<?php

namespace Spreng\security;

use Spreng\http\Controller;
use Spreng\http\ModelAndView;
use Spreng\config\GlobalConfig;
use Spreng\connection\AuthConnPool;
use Spreng\http\HttpSession;
use Spreng\http\ResponseBody;
use Spreng\system\log\Logger;

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
                if ($auth->try(new AuthConnPool())) {
                    HttpSession::echoRedirect($secConf->startFullUrl())();
                }
            }
            $l = (new LoginHandler)->getLoginModel();
            $l->username = $hs::username();
            $l->remember = $hs::remember() == '' ? '' : 'checked';
            $hs::clear();
            $l->auth_url = "." . $secConf->authUrl();
            $l->servermsg = Autentication::getAuthMessage();
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
