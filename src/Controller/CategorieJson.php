<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategorieJson extends AbstractController
{
    #[Route("/categorieJSON", name:"display_categorieJSON")]
    public function index(CategorieRepository $repository): JsonResponse
    {
        $categories = $repository->findAll();
        $data = [];
    
        foreach ($categories as $categorie) {
            $data[] = $categorie->__toString();
        }
    
        return new JsonResponse($data);
    }

    #[Route("/AddCategorieJSON", name:"add_categorieJSON")]
    public function addCategorie(Request $request, ManagerRegistry $doctrine,EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $categorie = new Categorie();
        $categorie->setLibelle($request->get('libelle'));
        $entityManager->persist($categorie);
        $entityManager->flush();

        $jsonContent = $normalizer->normalize($categorie,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    
    }
    
    #[Route("/removeCategorieJSON/{id}", name:"supp_categorieJSON")]
public function suppressionCategorie(Categorie $categorie, ManagerRegistry $doctrine): JsonResponse
{
    $em = $doctrine->getManager();
    $em->remove($categorie);
    $em->flush();

    $response['success'] = true;
    $response['message'] = 'La categorie a étée supprimée avec succès.';

    return new JsonResponse($response);
}

#[Route("/modifCategorieJSON/{id}", name:"modif_categorieJSON")]
public function modifCategorie(Request $request, $id, ManagerRegistry $doctrine, CategorieRepository $repository, EntityManagerInterface $em): JsonResponse
{
    $categorie = $repository->find($id);
    $form = $this->createForm(CategorieType::class, $categorie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $response['success'] = true;
        $response['message'] = 'La categorie a été modifié avec succès.';

        return new JsonResponse($response);
    }

    if (!$categorie = $repository->find($id)) {
        $response['success'] = false;
        $response['message'] = 'La categorie demandée n\'existe pas.';

        return new JsonResponse($response);
    }

    $response['success'] = false;
    $response['message'] = 'Le formulaire de modification n\'est pas valide.';

    return new JsonResponse($response);
}


}