<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Demo;

final class DemoController extends AbstractController
{


    public  function indexAction($name){


        $product = new Demo();
        $product->setName('Stéphane');
        $product->setDate(new \DateTime('now'));

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->persist($product);
        $em->flush();


        $link = $this->getContainer()->get('router')->generateUrl('route1',array('name'=> $name));

        return  $this->render("Demo/index.html.twig",
            array(
                'name'=> $name,
                'link'=>$link
            )
        );

    }





}