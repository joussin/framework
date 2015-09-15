<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

use Symfony\Component\Yaml\Parser;

class ParametersService{

    private $parameters;

    public function __construct(){

        $parser = new Parser();
        $this->parameters =  $parser->parse(file_get_contents(ROOT_PATH.'/src/config/parameters.yml'));

    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }







}