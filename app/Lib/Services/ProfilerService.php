<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 26/09/15
 * Time: 13:24
 */
namespace App\Lib\Services;



class ProfilerService{

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getToken(){

        return array(
            "session"=>$this->container->get('session')->get('security_token')->__toString(),
            "security.context"=>
                $this->container->get('security.context')->getSecurityContext()->getToken()!==NULL?$this->container->get('security.context')->getSecurityContext()->getToken()->__toString():NULL,
        );
    }

    public function getServices(){

        $services = $this->container->getServiceIds();
        foreach($services as $key=>$service){
            $services[$key] = array(
                "id" => $service,
                "instanciate" => $this->container->initialized($service)
            );
        }
        return $services;
    }
}