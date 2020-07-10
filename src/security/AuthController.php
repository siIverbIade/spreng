<?php

namespace Spreng\security;

use Spreng\http\Controller;
use Spreng\http\HttpSession;
use Spreng\http\ModelAndView;
use Spreng\http\ResponseBody;
use Spreng\system\log\Logger;
use Spreng\config\GlobalConfig;
use Spreng\connection\AuthConnPool;

/**
 * AuthController
 */
class AuthController extends Controller
{
    public static function login(HttpSession $hs): ModelAndView
    {
        $secConf = GlobalConfig::getSecurityConfig();

        return new ModelAndView(function () use ($secConf, $hs) {

            if (!$secConf->isEnabled() | SessionUser::getSessionToken() !== '') {
                $auth = new Autentication($hs);
                $authResult = $auth->try(new AuthConnPool());
                if ($authResult->isAuth()) {
                    HttpSession::echoRedirect($secConf->startFullUrl())();
                }
            }

            $l = (new LoginHandler)->getLoginModel();
            $l->username = $hs::username();
            $l->remember = $hs::remember() == '' ? '' : 'checked';
            $hs::clear();
            $l->auth_url = "." . $secConf->authUrl();
            $l->servermsg = isset($authResult) ? $authResult->getAuthMessage() : '';
            return $l;
        }, $secConf->loginUrl());
    }

    public static function checkCredentials(HttpSession $hs): ResponseBody
    {
        $secConf = GlobalConfig::getSecurityConfig();

        return new ResponseBody(function () use ($secConf, $hs) {
            $tryResult = (new Autentication($hs))->try(new AuthConnPool);
            if ($tryResult) {
                $hs::echoRedirect($secConf->startUrl())();
            } else {
                $hs::echoRedirect($secConf->loginUrl())();
            }
        }, $secConf->authUrl(), 'POST');
    }

    public static function logout(HttpSession $hs): ResponseBody
    {
        $secConf = GlobalConfig::getSecurityConfig();
        return new ResponseBody(function () use ($secConf, $hs) {
            session_unset();
            Logger::info('User ' . SessionUser::getUserName() . ' has logged out');
            SessionUser::clearCredentials();
            $hs::echoRedirect($secConf->loginUrl())();
        }, $secConf->logoutUrl());
    }
}
