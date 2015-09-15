<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Demo;


use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('task', 'text', array(
                'required' => false,
//                'constraints' => new NotBlank(),
            ))
            ->add('dueDate', 'date')
            ->getForm();



         $form->submit($request->request->get($form->getName()));

        if ($form->isValid()) {
            echo 'FORM VALID';
             $data = $form->getViewData();
             var_dump($data);
        }


        return  $this->render("Demo/index.html.twig",
            array(
                 'name'=>$name,
                 'link'=>$link,
                'form' => $form->createView(),
            )
        );
    }
}