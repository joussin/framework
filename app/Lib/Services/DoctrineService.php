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

    private $parameters;

    public function __construct($parameters){
        $this->parameters =$parameters;
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        //Doctrine initialisation
        $paths = array(ROOT_PATH."/src/Entities");

        // the connection configuration
        $dbParams = array(
            'driver'   =>$this->parameters->getParameters()['db']['driver'],
            'user'     => $this->parameters->getParameters()['db']['user'],
            'password' => $this->parameters->getParameters()['db']['password'],
            'dbname'   =>$this->parameters->getParameters()['db']['dbname'],
            'charset'   => $this->parameters->getParameters()['db']['charset']
        );


        $config = Setup::createAnnotationMetadataConfiguration($paths, DEV_MODE);
        $this->entity_manager = EntityManager::create($dbParams, $config);
        return $this->entity_manager;
    }
}