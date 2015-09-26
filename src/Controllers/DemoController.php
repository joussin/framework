<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Demo;


use Src\Form\DemoType;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

final class DemoController extends AbstractController
{


    /**
     * @param $name
     * @param Request $request
     * @return Response
     */
    public  function indexAction($name, Request $request){


//        echo $this->getContainer()->get('demoservice')->getStr();
        $link = $this->getContainer()->get('router')->generateUrl('route_1', array('name'=>$name));
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $demo = new Demo();
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



    public  function testPerfAction($templating,Request $request){



         if($templating == "twig"){


            return  $this->render("Demo/testPerf.html.twig",
                array(
                    'demos'=>array('demo')
                )
            );
        }

        elseif($templating=="php") {


            $test = $this->getCurrentUser();

            return $this->renderPhp("../src/Views/Demo/testPerf.html.php",
                array(
                    'demos'=>array('demo')
                )
            );

        }

    }

    public  function secured1Action(){

        return new Response("secured page 1");
    }
    public  function secured2Action(){


        return new Response("secured page 2");
    }

}