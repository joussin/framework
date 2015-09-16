<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class SecurityController extends AbstractController
{


    public  function indexAction(Request $request){



        return  $this->render("Security/index.html.twig");
    }
}