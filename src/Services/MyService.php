<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 06/09/15
 * Time: 16:46
 */
namespace Src\Services;

class MyService{

    public $str ;


    public function __construct($str){
        $this->str = $str;
    }

    public function getStr(){

        return $this->str;
    }

}