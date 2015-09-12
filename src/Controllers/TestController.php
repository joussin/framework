<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Src\Entities\Product;

final class TestController extends AbstractController
{


    public  function indexAction($name){


//        $product = new Product();
//        $product->setName('Stéphane');
//        $product->setDate(new \DateTime('now'));
//
//        $em = $this->getContainer()->get('doctrine')->getEntityManager();
//        $em->persist($product);
//        $em->flush();




        $link = $this->getContainer()->get('router')->generateUrl('route1',array('name'=> $name));



        return  $this->render("index.html.twig",
            array(
                'name'=> $name,
                'link'=>$link,
                'web_path'=>$this->getAssetDirectory()
            )
        );

    }





}