<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Jeux;
use App\Form\CommentType;
use App\Form\JeuxType;
use App\Repository\JeuxRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('site/home.html.twig',['title'=>"bienvenu ",]);

    }
    /**
     * @Route("/site/create",name="site_create")
     * @Route("admin/{id}/edit",name="site_edit")
     */
    public function form(Jeux $jeux=null,Request $request,ObjectManager $manager ){

        if (!$jeux){
            $jeux=new Jeux();
        }
        $form=$this->createForm(JeuxType::class,$jeux);

        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid()){

            $manager->persist($jeux)
                ->setUser($this->getUser());
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
                    ->setJeux($jeux)
                    ->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('site_show',['id'=>$jeux->getId()]);
        }

        return$this->render('site/show.html.twig',['jeu'=>$jeux, 'commentForm'=> $form->createView() ]);


    } /**
 * @Route("/admin/{id}/suppcomment",name="admin_comment",methods={"DELETE"})
 */
    public function deletecom(Comment $comment=null,Request $request,ObjectManager $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($comment);

            $entityManager->flush();

        }
        return $this->redirectToRoute('site');
    }

    /**
     * @Route("/site/{id}/delete",name="site_delete")
     */
    //fonction de suppression d'article
    public function delete(Jeux $jeux = null, Request $request, ObjectManager $manager)
    {

        $form = $this->createFormBuilder($jeux)
            ->add('title')
            ->add('description')
            ->add('image')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->remove($jeux);
            $manager->flush();
            return $this->redirectToRoute('site', ['id' => $jeux->getId()]);
        }

        return $this->render('site/delete.html.twig', ['formJeux' => $form->createView()]);

    }


//fonction d'identification de l'admin
    /**
     * @Route("/admin",name="admin")
     */
    public function base()
    {

        return $this->render('admin/index.html.twig', ['controller_name' => 'AdminController',]);
    }





    /**
     * @Route("/email",name="mailer_email")
     */

    public function reset(){
        return $this->render('mailer/request.html.twig');


    }


}


