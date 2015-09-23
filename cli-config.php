<?php
//php vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:update --force

require_once "vendor/autoload.php";


//Doctrine initialisation
$paths = array(__DIR__."/src/Entities");

$parser = new \Symfony\Component\Yaml\Parser();
$parameters =  $parser->parse(file_get_contents(__DIR__.'/app/config/parameters.yml'));


// the connection configuration
$dbParams = array(
    'driver'   =>$parameters['db']['driver'],
    'user'     => $parameters['db']['user'],
    'password' => $parameters['db']['password'],
    'dbname'   =>$parameters['db']['dbname'],
    'charset'   => $parameters['db']['charset']
);

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, true);
$entity_manager = \Doctrine\ORM\EntityManager::create($dbParams, $config);


return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entity_manager);