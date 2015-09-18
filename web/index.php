<?php

require_once "../vendor/autoload.php";

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use App\Lib\Controller\FrontalController;



define("REWRITE_MODE",false);
define("DEV_MODE",true);

define("ROOT_PATH",__DIR__.'/../');
define("HOSTNAME","localhost");
define("INSTALL_DIR","framework");




//pour le router entre autres, acces aux assets etc..
if(!REWRITE_MODE)
    define("WEB_PATH","http://".HOSTNAME."/".INSTALL_DIR."/web");
else
    define("WEB_PATH","http://".HOSTNAME);


if(DEV_MODE){
    error_reporting(E_ALL);
    Debug::enable();
    ErrorHandler::register();
    ExceptionHandler::register();
}else{
    error_reporting(0);
}


$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(ROOT_PATH.'/app/config'));
$loader->load('services.yml');
$loader = new YamlFileLoader($container, new FileLocator(ROOT_PATH.'/src/config'));
$loader->load('services.yml');
$container->compile();



$controllerFrontal = new FrontalController();
