<?php
namespace Src\Controllers;
//namespace App\Lib\Security;

use App\Lib\Controller\AbstractController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Src\Entities\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Validator\Constraints\Email;
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
        $r =new Response("logout");
        $r->headers->clearCookie('security_token');
        return $r;
    }

    public function confirmAction($token){

        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $user_repository = $em->getRepository('Src\Entities\User');

        $user = $user_repository->findOneBy(array('validation_token'=>$token));

        if($user!=null){

            $user->setEnabled(1);
            $em->persist($user);
            $em->flush();
        }

        return new Response();
    }

    public  function registerAction(Request $request){

        $error ="";

        $em = $this->getContainer()->get('doctrine')->getEntityManager();


        $user = new User();
        $form = $this->getContainer()->get('form.factory')->getFormFactory()
            ->createBuilder("form",$user)
            ->add('username',null, array(
                'constraints' => array(new Length(
                    array(
                        'min'        => 2,
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
            ->add('email',null, array(
                'constraints' => array(
                    new Email(
                        array(
                            'message'=> "email invalide"
                        )
                    ),
                    new NotBlank(
                        array(
                            "message"=>"Votre email ne doit pas être vide"
                        )
                    )
                )
            ))
            ->add('password',"password", array(
                'constraints' => array(new Length(
                    array(
                        'min'        => 5,
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

                $encoder = $this->getContainer()->get('encoder.factory')->getEncoderFactory()->getEncoder($user);
                $encodedPassword = $encoder->encodePassword($user->getPassword(), $salt);
                $user->setPassword($encodedPassword);

                $user->setValidationToken(md5($user->getUsername()));

                try{
                    $em->persist($user);
                    $em->flush();
                }
                catch(\Exception $e){
                    $error = $e->getMessage();
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