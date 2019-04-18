<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Jeux;
use App\Form\CommentType;
use App\Form\JeuxType;
use App\Repository\JeuxRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index(JeuxRepository $repo)
    {
        $jeux=$repo->findAll();
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'jeux'=>$jeux
        ]);
    }
    /**
     * @Route("/",name="home")
     */
    public function home(){
        return $this->render('site/home.html.twig',['title'=>"bienvenu sur le site de flip-table",]);

    }
    /**
     * @Route("/site/create",name="site_create")
     * @Route("/site/{id}/edit",name="site_edit")
     */
    public function form(Jeux $jeux=null,Request $request,ObjectManager $manager ){

        if (!$jeux){
            $jeux=new Jeux();
        }


        $form=$this->createForm(JeuxType::class,$jeux);

        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid()){
            $manager->persist($jeux);
            $manager->flush();

            return $this->redirectToRoute('site_show',['id'=>$jeux->getId()]);
        }



        return $this->render('site/create.html.twig',['formjeux'=>$form->createView(),'editMode'=>$jeux->getId()!==null]);

    }


    /**
     * @Route("/site/{id}", name="site_show")
     */
    public function show(Jeux $jeux, Request $request,ObjectManager $manager){
        $comment= new Comment();

        $form= $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted()&& $form->isValid()){

            $comment->setCreatedAt(new \DateTime())
                    ->setJeux($jeux);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('site_show',['id'=>$jeux->getId()]);
        }

        return$this->render('site/show.html.twig',['jeu'=>$jeux, 'commentForm'=> $form->createView() ]);


    }
    /**
     * @Route("/carte",name="site_carte")
     */
    public function carteG(){

        return $this->render('site/carte.html.twig');

    }
/**
 * @Route("/email",name="mailer_email")
 */

public function reset(){
    return $this->render('mailer/request.html.twig');
}

}
