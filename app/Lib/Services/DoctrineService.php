<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class DoctrineService{

    private $entity_manager;

    public function __construct($parameters){

        //Doctrine initialisation
        $paths = array(ROOT_PATH."/src/Entities");

        // the connection configuration
        $dbParams = array(
            'driver'   =>$parameters->getParameters()['db']['driver'],
            'user'     => $parameters->getParameters()['db']['user'],
            'password' => $parameters->getParameters()['db']['password'],
            'dbname'   =>$parameters->getParameters()['db']['dbname'],
            'charset'   => $parameters->getParameters()['db']['charset']
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, DEV_MODE);
        $this->entity_manager = EntityManager::create($dbParams, $config);

    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entity_manager;
    }
}