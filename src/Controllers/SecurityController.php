<?php
namespace Src\Controllers;

use App\Lib\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Firewall;


final class SecurityController extends AbstractController
{


    public  function loginAction(Request $request){

        $error = $this->getContainer()->get('session')->get('security_login_error');

        if($error!==NULL){
            $this->getContainer()->get('session')->remove('security_login_error');
        }

        return  $this->render("Security/login.html.twig",
            array(
                "error"=>$error
            )
        );
    }
    public  function logoutAction(Request $request){

        $this->getContainer()->get('session')->remove('security_token');
        return new Response();
    }
}