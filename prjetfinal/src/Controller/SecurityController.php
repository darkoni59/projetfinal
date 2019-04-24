<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityController extends AbstractController
{
  /**
   * @Route("registration",name="security_registration")
   */
public function registration(Request $request,ObjectManager $manager,UserPasswordEncoderInterface $encoder){
    $user=new User();

    $form=$this->createForm(RegistrationType::class,$user);
    $form->handleRequest($request);
    if ($form->isSubmitted()&&$form->isValid()){
    $hash=$encoder->encodePassword($user,$user->getPassword());
    $user->setPassword($hash);
    $user->setRole('ROLE_USER');
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('security_login');

    }
    return $this->render("security/registration.html.twig",['form'=>$form->createView()]);
    }

    /**
     *@Route("/connexion",name="security_login")
     */

public function login(){

    return $this->render('security/login.html.twig');


}
/**
 *@Route("/deconnexion",name="security_logout")
 */
public function logout(){

    return $this->redirectToRoute("/");
}
/**
 * @Route("/profil",name="security_profil")
 */
public function profil(){

return $this->render('security/profil.html.twig');

}

}
