<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Demo;


use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;


use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

final class DemoController extends AbstractController
{


    public  function indexAction($name){

//        echo $this->getContainer()->get('demoservice')->getStr();
//
//        $product = new Demo();
//        $product->setName('Stéphane');
//        $product->setDate(new \DateTime('now'));
//
//        $em = $this->getContainer()->get('doctrine')->getEntityManager();
//        $em->persist($product);
//        $em->flush();







        $defaultFormTheme = 'form_div_layout.html.twig';
        $vendorDir = realpath(ROOT_PATH. '/vendor');
        $vendorTwigBridgeDir = $vendorDir . '/symfony/twig-bridge';
        $viewsDir = realpath(ROOT_PATH . 'src/Views');

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array(
            $viewsDir,
            $vendorTwigBridgeDir . '/Resources/views/Form',
        )));
        $formEngine = new TwigRendererEngine(array($defaultFormTheme));
        $formEngine->setEnvironment($twig);


        $csrfSecret = "1";
        $session = new Session();
        $csrfProvider = new SessionCsrfProvider($session, $csrfSecret);
        $twig->addExtension(
            new FormExtension(new TwigRenderer($formEngine, $csrfProvider))
        );



        $translator = new Translator('en');
        $translator->addLoader('xlf', new XliffFileLoader());
        $translator->addResource(
            'xlf',
            ROOT_PATH.'src/translation/messages.en.xlf',
            'en'
        );
        $twig->addExtension(new TranslationExtension($translator));






        $formFactory = Forms::createFormFactoryBuilder()->getFormFactory();
        $form = $formFactory->createBuilder()
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->getForm();

        echo $twig->render('Demo/new.html.twig', array(
            'form' => $form->createView(),
        ));


return new Response("lk,m");



//        $link = $this->getContainer()->get('router')->generateUrl('route_1',array('name'=> $name));
//
//        return  $this->render("Demo/index.html.twig",
//            array(
//                'name'=> $name,
//                'link'=>$link
//            )
//        );

    }





}