# PHP Spreng
JAVA's Spring Framework Inspired Engine for PHP Development 


![Build Status](https://api.travis-ci.org/siIverbIade/spreng.svg?branch=master&status=unknown)
![Issues](https://img.shields.io/github/issues/siIverbIade/spreng)
![Forks](https://img.shields.io/github/forks/siIverbIade/spreng)
![Stars](https://img.shields.io/github/stars/siIverbIade/spreng)
![License]()
![Version](https://img.shields.io/badge/version-v1.0.2--beta-blueviolet)
 ### Requirements:
 - PHP 7.2
 - [Apache](https://httpd.apache.org/) 2.4.xx
 - [Composer](https://getcomposer.org/download/) version 1.10.x
 - psr-4 compliance (your composer.json should look like this)
 ```json
  {
    ...
    "require" : {
      "suzano/spreng" : "^1.0.0-beta"
    },
    "autoload" : {
      "psr-4" : {
        "AppName" : "folder/"
      }
    }
    ...
  }
 ```
 ### Installation
 Install dependency. 
 
 ```sh
 $ composer require suzano/spreng
 ```
 
Obs: Note that it will install also *twig/twig*, *firebase/php-jwt* and *monolog/monolog* as third-party.
 
Create a new file index.php in your root folder
 ```php
 <?php

use Spreng\MainApp;

require_once 'vendor/autoload.php';

MainApp::init();
  ```

Once you run localhost Spreng will create:
 - /.htaccess and /application.json config files.
 - /folder source folder.
 - /folder/MyFirstController.php sample ready to work.
If everything went ok you should see the following message on your browser:

![N|Init](https://i.ibb.co/Vw846P6/Screenshot-2.png)
 
After clicking refresh a Hello World page will be loaded.

![N|Hello World](https://i.ibb.co/dJtsXDV/Screenshot-1.png)

