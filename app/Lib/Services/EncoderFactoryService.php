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

class EncoderFactoryService extends EncoderFactory
{

    public function __construct($security_config)
    {
        $encoder['plaintext'] = new PlaintextPasswordEncoder();
        $encoder['sha512'] = new MessageDigestPasswordEncoder('sha512', false, 1);
        $encoders = array();
        foreach ($security_config['encoders'] as $prov => $enco) {
            $encoders[$prov] = $encoder[$enco];
        }
        return parent::__construct($encoders);
    }
}