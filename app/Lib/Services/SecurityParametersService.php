<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

use Symfony\Component\Yaml\Parser;

class SecurityParametersService{

    private $parameters;

    public function __construct(){
        $parser = new Parser();
        $this->parameters =  $parser->parse(file_get_contents(ROOT_PATH.'/app/config/security.yml'));
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function get($param_name)
    {
        return (isset($this->parameters[$param_name]))?$this->parameters[$param_name]:NULL;
    }
}