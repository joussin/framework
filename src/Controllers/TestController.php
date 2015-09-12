<?php
namespace Src\Controllers;

use App\Lib\Controller;
use Src\Entities\Product;

final class TestController extends Controller
{


    public  function indexAction($name){

        echo  $this->getContainer()->get('myservice')->getStr();

        $product = new Product();
        $product->setName('Stéphane');
        $product->setDate(new \DateTime('now'));

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->persist($product);
        $em->flush();


        echo "<br>";
        echo "<br>";

        echo $this->getContainer()->get('router')->generateUrl('route1',array('name'=> $name));

        echo "<br>";
        echo "<br>";

        return  $this->render("index.html.twig",array('name'=> $name));

    }





}