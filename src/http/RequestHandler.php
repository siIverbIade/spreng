<?php

declare(strict_types=1);

namespace Spreng\http;

use Exception;
use Spreng\http\Resolver;
use Spreng\http\HttpResponse;
use Spreng\http\ModelAndView;
use Spreng\http\ResponseBody;
use Spreng\system\log\Logger;
use Spreng\config\GlobalConfig;
use Spreng\security\Autentication;
use Spreng\security\AuthController;
use Spreng\system\SystemController;
use Spreng\system\loader\ClassHandler;
use Spreng\system\collections\ControllerList;

/**
 * RequestHandler
 */
class RequestHandler
{
    private $session;
    private $controllers;
    private $classes;
    private $auth;

    public function __construct(HttpSession $session)
    {
        $this->session = $session;
        $this->auth = new Autentication($this->session);
        $this->controllers = new ControllerList();
        $gc = new GlobalConfig;
        $this->classes = $gc::getAllImplementationsOf($gc::getHttpConfig()->getControllersPath(), Controller::class);
        $this->registerAll();
    }

    private function fullUrl($url): string
    {
        return $this->session::rootUrl() . $url;
    }

    private function registerAll()
    {
        $this->controllers->add(new AuthController);
        $this->controllers->add(new SystemController);
        foreach ($this->classes as $class => $parentClass) {
            try {
                $this->controllers->add(new $class);
            } catch (Exception $e) {
            }
        }
    }

    public function processRequest()
    {
        foreach ($this->controllers->getAll() as $ctl) {
            $ctlShifted = $ctl->getFn();
            foreach ($ctlShifted as $name) {
                $arg = [];
                $classMethodArgs = ClassHandler::getFunctionArgsTypes(get_class($ctl), $name);
                //Logger::debug($classMethodArgs, true);
                foreach ($classMethodArgs as $type) {
                    $arg[] = new $type;
                }
                //$response = $ctl->{$name}($this->session);
                $response = $ctl->{$name}(self::arg(0, $arg), self::arg(1, $arg), self::arg(2, $arg));
                $fullUrl = $this->fullUrl($ctl->getRootUrl() . $response->url());

                //Logger::console_log("full = " . $fullUrl . "</br>");
                //Logger::console_log("root = " . $this->session->rootRequest() . "</br>");

                if ($fullUrl == $this->session->rootRequest()) {

                    if ($response->method() !== $this->session::method()) {
                        $this->resolveRequest(new Resolver('', 405));
                    }

                    if (!$this->auth->handleAuth(array_merge(array_diff($ctl->getRequiredPermissions(), $response->permissions()), array_diff($response->permissions(), $ctl->getRequiredPermissions())))) {
                        if ($this->auth->getUserCredentials() == null) {
                            $this->redirectRequest(GlobalConfig::getSecurityConfig()->loginFullUrl());
                        } else {
                            $this->resolveRequest(new Resolver('', 403));
                        }
                    }

                    if ($response instanceof ResponseBody) {
                        $this->resolveRequest($this->handleRest($response));
                    } elseif ($response instanceof ModelAndView) {
                        $this->resolveRequest($this->handleMvc($response));
                    } elseif ($response instanceof HttpResponse) {
                        $this->resolveRequest($this->handleResponse($response));
                    }
                }
            }
        }
        if ($this->auth->getUserCredentials() == null) $this->redirectRequest(GlobalConfig::getSecurityConfig()->loginFullUrl());
        $this->resolveRequest(new Resolver('', 404, $response->headers()));
    }

    private static function arg(int $n, array $arg)
    {
        return isset($arg[$n]) ? $arg[$n] : '';
    }

    private function handleResponse(HttpResponse $response)
    {
        $url = '';
        try {
            $echo = $response->response()();
            $url = $response->redirectUrl();
        } catch (Exception $e) {
            Logger::error($e->getMessage() . ' -> ' .  $e->getTraceAsString());
            $this->session::throwHttpCode(500)();
        }

        if ($url) $this->redirectRequest($url);

        return new Resolver($echo, $response->httpcode(), $response->headers());
    }

    private function handleRest(ResponseBody $response)
    {
        $url = '';
        try {
            $response->processResponse();
            $url = $response->redirectUrl();
        } catch (Exception $e) {
            Logger::error($e->getMessage() . ' -> ' . $e->getTraceAsString());
            $this->session::throwHttpCode(500)();
        }

        if ($url) $this->redirectRequest($url, $response->getObjResponse());

        return new Resolver($response->encodedResponse(), $response->httpcode(), $response->headers());
    }

    private function handleMvc(ModelAndView $model)
    {
        $url = '';
        try {
            $model->processResponse();
            $url = $model->redirectUrl();
        } catch (Exception $e) {
            Logger::error($e->getMessage() . ' -> ' .  $e->getTraceAsString());
            $this->session::throwHttpCode(500)();
        }

        if ($url) $this->redirectRequest($url);

        return new Resolver($model->render(), $model->httpcode(), $model->headers());
    }

    private function redirectRequest(string $url, $data = null)
    {
        ($data == null | $data == []) ? $query = '' : $query = '?' . http_build_query($data);
        header("Location: $url" . $query);
        $this->resolveRequest();
    }

    public function postData(HttpResponse $response)
    {
        $url = $response->url();

        try {
            $data = $response->response()();
        } catch (Exception $e) {
            Logger::error($e->getMessage() . ' -> ' . $e->getTraceAsString());
            $this->resolveRequest(new Resolver('', 500, $response->headers()));
        }

        $cURLConnection = curl_init($url);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $this->resolveRequest(new Resolver($apiResponse, 200, $response->headers()));
    }

    private function resolveRequest(Resolver $resolver = null)
    {
        if ($resolver !== null) {
            http_response_code($resolver->httpCode);
            if ($resolver->httpCode >= 400) {
                exit;
            }

            foreach ($resolver->headers as $header) {
                header($header);
            }

            echo $resolver->echo;
        }
        exit;
    }
}
