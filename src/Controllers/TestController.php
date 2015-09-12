<?php
namespace Src\Controllers;

use App\Lib\Controller;
use Src\Entities\Product;
use Symfony\Component\HttpFoundation\Request;

final class TestController extends Controller
{


    public  function indexAction($id, Request $request){

        echo  $this->getContainer()->get('myservice')->getStr();

        $product = new Product();
        $product->setName('Stéphane');
        $product->setDate(new \DateTime('now'));

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->persist($product);
        $em->flush();


          return  $this->render("index.html.twig",array('name'=> $id));

    }





}