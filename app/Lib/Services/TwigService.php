<?php
/**
 * Created by PhpStorm.
 * User: stef
 * Date: 12/09/15
 * Time: 16:06
 */
namespace App\Lib\Services;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

class TwigService{

    private $twig;

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {   if(DEV_MODE){
        $options = array(
            'cache' => false
        );
    }
    else{
        $options = array(
            'cache' => ROOT_PATH.'/app/cache/twig',
        );
    }

        $loader = new \Twig_Loader_Filesystem(
            array(
                ROOT_PATH.'/src/Views',
                ROOT_PATH.'/app/views',
                ROOT_PATH.'/app/views/form'
            )
        );
        $this->twig = new \Twig_Environment($loader, $options);

        //form layout
        $formEngine = new TwigRendererEngine(array('form_div_layout.html.twig'));
        $formEngine->setEnvironment($this->twig);

        //security
        $csrfSecret = md5(rand(0,10000000000000000));
        $session = new Session();
        $csrfProvider = new SessionCsrfProvider($session, $csrfSecret);
        $this->twig->addExtension(
            new FormExtension(new TwigRenderer($formEngine, $csrfProvider))
        );

        //translator
        $translator = new Translator('en');
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource(
            'xlf',
            ROOT_PATH.'app/views/form/translation/messages.en.xlf',
            'en'
        );
        $this->twig->addExtension(new TranslationExtension($translator));
        return $this->twig;
    }



}