<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Demo;


use Src\Form\DemoType;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class DemoController extends AbstractController
{


    public  function indexAction($name, Request $request){


//        echo $this->getContainer()->get('demoservice')->getStr();
        $link = $this->getContainer()->get('router')->generateUrl('route_1', array('name'=>$name));
        $em = $this->getContainer()->get('doctrine')->getEntityManager();



        $demo = new Demo();
        $demo->setName("name de test");
        $demo->setDate(new \DateTime('now'));

        $date = $demo->getDate()->format("Y-m-d H:i:s");


        $demoType =new DemoType();

        $formFactory = Forms::createFormFactoryBuilder()->getFormFactory();
        $form = $formFactory->createBuilder($demoType,$demo)->getForm();


         $form->submit($request->request->get($form->getName()));

        if ($request->isMethod('POST')) {
        if ($form->isValid()) {

            $demo = $form->getViewData();
            $em->persist($demo);
            $em->flush();

        }}


        $demoRepository = $em->getRepository('Src\Entities\Demo');
        $demos = $demoRepository->findAll();




        return  $this->render("Demo/index.html.twig",
            array(
                 'name'=>$name,
                 'link'=>$link,
                'form' => $form->createView(),
                'demos' => $demos,
            )
        );
    }
}