<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;


#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route("/", name:"display_categorie")]
    public function index(ManagerRegistry $doctrine,CategorieRepository $repository): Response
    {
        $categories=$repository->findAll();
        return $this->render('categorie/index.html.twig', [
            'c'=>$categories
        ]);
    }


        #[Route("/add", name:"add_categorie", methods: ['GET', 'POST'])]
    public function addCategorie(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorie = new Categorie();
        
        $form = $this ->createForm(CategorieType::class,$categorie);
        $form->handleRequest($request);
        if($form ->isSubmitted()&& $form->isValid()){
            $em= $doctrine->getManager();
            $em->persist($categorie);//Add
            $em->flush();
            return $this -> redirectToRoute(route:'display_categorie');
        }
        return $this -> render('categorie/ajoutCategorie.html.twig' , ['f'=>$form->createView()]);
    }


        #[Route("/removeCategorie/{id}", name:"supp_categorie")]
    public function suppClassroom($id,CategorieRepository $r, ManagerRegistry $doctrine): Response
    {
        //récupérer la categorie à supprimer
        $categorie=$r->find($id);
        //Action suppression
        $em =$doctrine->getManager();
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('display_categorie',);
    }


            #[Route("/modifCategorie/{id}", name:"modif_categorie")]
    public function modifCategorie(Request $request, $id, ManagerRegistry $doctrine): Response
    {
        //récupérer le categorie à modifier
        $categorie = $doctrine->getRepository(Categorie::class)->find($id);
        $form = $this ->createForm(CategorieType::class,$categorie);
        $form->handleRequest($request);
        if($form ->isSubmitted()){
            $em= $doctrine->getManager();
            $em->flush();
            return $this -> redirectToRoute(route:'display_categorie');}
            if(!$categorie = $doctrine->getRepository(Categorie::class)->find($id)){
                return $this -> redirectToRoute(route:'error');
            }
        return $this -> render('categorie/ajoutCategorie.html.twig' , ['f'=>$form->createView()]);

    }


    
}
