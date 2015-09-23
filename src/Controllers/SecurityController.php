<?php
namespace Src\Controllers;
//namespace App\Lib\Security;

use App\Lib\Controller\AbstractController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Src\Entities\User;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Forms;
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

    public  function registerAction(Request $request){

        $error ="";

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $user = new User();

        $formFactory = Forms::createFormFactoryBuilder()->getFormFactory();

         $form = $formFactory->createBuilder('form',$user)
        ->add('username',"text")
        ->add('password',"text")
            ->getForm();

        $form->submit($request->request->get($form->getName()));

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {

                $user = $form->getData();

                $encoder = $this->getContainer()->get('encoder.factory')->getEncoder($user);
                $encodedPassword = $encoder->encodePassword($user->getPassword(), '');
                $user->setPassword($encodedPassword);
                try{
                    $em->persist($user);
                    $em->flush();
                }
                catch(\Exception $e){
                    $error = $e->getMessage();
//                    $error = "Username déjà utilisé";
                }


            }
        }



        return  $this->render("Security/register.html.twig",
            array(
                "error"=>$error,
                "form"=>$form->createView()
            )
        );
    }



}