<?php
require_once "vendor/autoload.php";

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

define("DEV_MODE",true);
define("ROOT_PATH",__DIR__);
define("INSTALL_DIR","framework");


if(DEV_MODE){
    error_reporting(E_ALL);
    Debug::enable();
    ErrorHandler::register();
    ExceptionHandler::register();
}else{
    error_reporting(0);
}