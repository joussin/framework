<?php
namespace Src\Controllers;
//namespace App\Lib\Security;

use App\Lib\Controller\AbstractController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Src\Entities\User;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;


final class SecurityController extends AbstractController
{


    public  function loginAction(){

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
    public  function logoutAction(){

        $this->getContainer()->get('session')->remove('security_token');
        return new Response();
    }

    public  function registerAction(Request $request){

        $error ="";

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $user = new User();
        $form = $formFactory->createBuilder("form",$user)
            ->add('username',null, array(
                'constraints' => array(new Length(
                    array(
                        'min'        => 6,
                        'max'        => 10,
                        'minMessage' => 'Votre pseudo doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre pseudo ne peut pas être plus long que {{ limit }} caractères',
                    )
                ),
                    new NotBlank(
                        array(
                            "message"=>"Votre pseudo ne doit pas être vide"
                        )
                    )
                )
            ))
            ->add('password',"password", array(
                'constraints' => array(new Length(
                    array(
                        'min'        => 6,
                        'minMessage' => 'Votre pseudo doit faire au moins {{ limit }} caractères',
                    )
                ),
                    new NotBlank(
                        array(
                            "message"=>"Votre mot de passe ne doit pas être vide"
                        )
                    )
                )
            ))
            ->getForm();



        if ($request->isMethod('POST')) {

            $form->submit($request->request->get($form->getName()));

            if ($form->isValid()) {

                $user = $form->getData();

                $salt = uniqid();
                $user->setSalt($salt);

                $encoder = $this->getContainer()->get('encoder.factory')->getEncoder($user);
                $encodedPassword = $encoder->encodePassword($user->getPassword(), $salt);
                $user->setPassword($encodedPassword);

                try{
                    $em->persist($user);
                    $em->flush();
                }
                catch(\Exception $e){
                    $error = "Username déjà utilisé";
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