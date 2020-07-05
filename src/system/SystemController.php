<?php

namespace Spreng\system;

use Spreng\system\System;
use Spreng\http\Controller;
use Spreng\http\HttpSession;
use Spreng\http\ModelAndView;
use Spreng\http\ResponseBody;
use Spreng\system\log\AdminPage;

/**
 * SystemController
 */
class SystemController extends Controller
{

    public function __construct()
    {
        $this->rootUrl = '/system';
        $this->require = ['MASTER'];
    }

    public static function message(): ResponseBody
    {
        return new ResponseBody(function () {
            return System::Message();
        }, '/message');
    }

    public static function admin(): ModelAndView
    {
        return new ModelAndView(function () {
            $admPage = new AdminPage;
            $admPage->refreshRate = HttpSession::name('refreshrate') == '' ? '' : (int) HttpSession::name('refreshrate');
            return $admPage;
        }, '/admin');
    }
}
