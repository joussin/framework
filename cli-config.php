<?php
//php vendor/doctrine/orm/bin/doctrine.php orm:schema-tool:update --force

require_once "vendor/autoload.php";

//pour le cli tools de doctrine
$entityManager = $container['entity.manager'] ;
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);