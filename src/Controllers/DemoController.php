<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Demo;


use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;


final class DemoController extends AbstractController
{


    public  function indexAction($name, Request $request){


//        echo $this->getContainer()->get('demoservice')->getStr();
//
//        $product = new Demo();
//        $product->setName('Stéphane');
//        $product->setDate(new \DateTime('now'));
//
//        $em = $this->getContainer()->get('doctrine')->getEntityManager();
//        $em->persist($product);
//        $em->flush();

        $link = $this->getContainer()->get('router')->generateUrl('route_1', array('name'=>$name));


        $formFactory = Forms::createFormFactoryBuilder()->getFormFactory();

        $form = $formFactory->createBuilder()
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->getForm();

//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            echo 'FORM VALID';
//            $data = $form->getData();
//            var_dump($data);
//        }


        return  $this->render("Demo/index.html.twig",
            array(
                 'name'=>$name,
                 'link'=>$link,
                'form' => $form->createView(),
            )
        );

    }





}