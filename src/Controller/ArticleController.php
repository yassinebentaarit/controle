<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;

use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Twilio\TwiML\Voice\Client as VoiceClient;

class ArticleController extends AbstractController
{
    #[Route("/article", name:"display_article")]
    public function show(ArticleRepository $repository): Response
    {
        $articles=$repository->findAll();

        return $this->render('article/index.html.twig', [
            's'=>$articles
        ]);
        
}
    





        #[Route("/addArticle", name:"add_article")]
    public function addArticle(Request $request, ManagerRegistry $doctrine): Response
    {
        $article = new Article();
        $form = $this ->createForm(ArticleType::class,$article);
        $form->handleRequest ($request);
        if($form ->isSubmitted()&& $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($article);//Add
            $em->flush();
            return $this -> redirectToRoute(route:'display_article');
        }
        return $this -> render('article/ajoutArticle.html.twig' , ['f'=>$form->createView()]);
    }


        #[Route("/removeArticle/{id}", name:"supp_article")]
    public function suppressionArticle( $id, ArticleRepository $repository): Response
    {
        $article = $repository->find($id);
        $sms=$article->getId();
        
        return $this->redirectToRoute('send_sms', ['id' => $sms]);
        
    }


    #[Route("/modifArticle/{id}", name:"modif_article")]
    public function modifArticle(Request $request,$id,ManagerRegistry $doctrine,
    ArticleRepository $repository, EntityManagerInterface $em): Response
    {//récupérer le article à modifier
        $article = $repository->find($id);
        $form = $this ->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form ->isSubmitted() && $form->isValid()){
            $em= $doctrine->getManager();
            $em->flush();
            return $this -> redirectToRoute(route:'display_article');}
        if(!$article = $repository->find($id)){
            return $this -> redirectToRoute(route:'error');
        }
        return $this -> render('article/modifArticle.html.twig' , ['f' => $form->createView()]);
    }


    #[Route("/actualiser", name:"article_actualiser", methods: ['GET'])]
    public function actualiser(ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        // get the current date
        $today = new \DateTime();
        // get the articles with a date later than today
        $articles = $articleRepository->findByDateGreaterThan($today);
    
        // remove the articles with a date earlier than today
        foreach ($articleRepository->findByDateLessThan($today) as $article) {
            $entityManager->remove($article);
        }
        $entityManager->flush();
    
        return $this->render('article/index.html.twig', [
            's' => $articles,
        ]);
    }

    

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        
            return $this->render('home/index.html.twig', [
                'controller_name' => 'IndexController',
            ]);
        
    }


    #[Route("/Error", name:"error")]
    public function erreur(): Response
    {
        return $this -> render('Error/erreur.html.twig',[] );
    }


    #[Route('/sendSms/{id}', name: 'send_sms', requirements: ['id' => '\d+'])]
    public function sendSmsAction($id, ArticleRepository $repository, ManagerRegistry $doctrine )
    {
        $article = $repository->find($id);
        $nom=$article->getNomA();
        $categorie=$article->getCategorie();
        
        $sid = 'ACaa2926cb5d388a533cf59c4a84ef1478';
        $token = 'b1e245d1e54f6b9a4e00e45e11e7fd87';
        $twilioNumber = '+18123988042';
        $recipientNumber = '+21624485249';
        $message = "le medicament " . $nom . " du categorie ". $categorie." a ete supprimer!!!";

        $twilioClient = new Client($sid, $token);
        echo($twilioClient);
        $twilioClient->messages->create(
            $recipientNumber,
            [
                'from' => $twilioNumber,
                'body' => $message
            ]
        );
        $em= $doctrine->getManager();
        $em->remove($article);
        $em->flush();
        $this->addFlash(
            'info',
            'suppression avec succes'
        );
        return $this->redirectToRoute('display_article');
    }

    
//GN7hKJNFA8YDcsCTBkAc72eV_48uJ6GNdgQr0cqS
//twilio code
}