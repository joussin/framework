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

        return ($this->container->get('session')->get('security_token')!=null)?$this->container->get('session')->get('security_token')->__toString():"pas de token de sesion";
    }

    public function getServices(){


        $servicesStr = "";
        $services = $this->container->getServiceIds();

        foreach($services as $key=>$service){

            $services[$key] = array(
                "id" => $service,
                "instanciate" => $this->container->initialized($service)
            );

            $instanciated = $this->container->initialized($service);
            if($instanciated)$servicesStr .= sprintf("<li><font color='green'>%s</font></li>",$service);
            else$servicesStr .= sprintf("<li><font color='red'>%s</font></li>",$service);

        }
        return "<ul>".$servicesStr."</ul>";
    }

    public function showProfiler(){

        echo "<div style='position: absolute;bottom:0;left:0;background-color: #f5f5f5'>";
        echo "<div id='btn' onclick='document.getElementById(\"content\").style.display=\"block\"'><strong>INFO</strong>";
        echo "</div>";
        echo "<div id='content' style='display: none'>";
        echo ($this->getServices());
        echo ($this->getToken());
        echo "</div>";
        echo "</div>";
    }

}