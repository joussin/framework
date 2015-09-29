<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

use Symfony\Component\Yaml\Parser;


class SecurityParametersService extends \ArrayObject
{

    public function __construct()
    {
        $parser = new Parser();
        $datas = $parser->parse(file_get_contents(ROOT_PATH . '/app/config/security.yml'));
        parent::__construct($datas);
    }



}