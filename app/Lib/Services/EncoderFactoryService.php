<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

class EncoderFactoryService{

    private $encoderFactory;

    public function __construct($security_config){

        $security_config = $security_config->getParameters();

        $encoder['plaintext'] = new PlaintextPasswordEncoder();
        $encoder['sha512'] = new MessageDigestPasswordEncoder('sha512',false,1);
        $encoders = array();
        foreach($security_config['encoders'] as $prov => $enco){
            $encoders[$prov] = $encoder[$enco];
        }
        $this->encoderFactory = new EncoderFactory($encoders);
    }

    /**
     * @return EncoderFactory
     */
    public function getEncoderFactory()
    {
        return $this->encoderFactory;
    }
}