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


        $formFactory = Forms::createFormFactoryBuilder()->getFormFactory();

        $form = $formFactory->createBuilder()
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->getForm();


        $link = $this->getContainer()->get('router')->generateUrl('route_1',array('name'=> $name));

        return  $this->render("Demo/index.html.twig",
            array(
                'name'=> $name,
                'link'=>$link,
                'form' => $form->createView(),
            )
        );

    }





}