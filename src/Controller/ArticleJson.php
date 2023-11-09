<?php
namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;


    class ArticleJson extends AbstractController
    {
    
    
    #[Route("/articleJSON", name:"display_articleJSON")]
    public function index(ArticleRepository $repository,NormalizerInterface $normalizer)
    {
        $articles = $repository->findAll();
        $jsonContent = $normalizer->normalize($articles,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("/addArticleJSON", name:"add_articleJSON")]
    public function addArticle(Request $request, ManagerRegistry $doctrine,EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $article = new Article();
        $article->setNomA($request->get('nom_a'));
        $article->setCategorie($request->get('categorie'));
        $article->setQuantite($request->get('quantite'));
        $article->setDate($request->get('date'));
        $entityManager->persist($article);
        $entityManager->flush();


        $jsonContent = $normalizer->normalize($article,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }


    #[Route("/removeArticleJSON/{id}", name:"supp_articleJSON")]
    public function suppressionArticle(Article $article, ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $em->remove($article);
        $em->flush();

        $response['success'] = true;
        $response['message'] = 'L\'article a été supprimé avec succès.';

        return new JsonResponse($response);
    }


#[Route("/modifArticleJSON/{id}", name:"modif_articleJSON")]
public function modifArticle(Request $request, $id, ManagerRegistry $doctrine, ArticleRepository $repository, EntityManagerInterface $em): JsonResponse
{
    $article = $repository->find($id);
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $response['success'] = true;
        $response['message'] = 'L\'article a été modifié avec succès.';

        return new JsonResponse($response);
    }

    if (!$article = $repository->find($id)) {
        $response['success'] = false;
        $response['message'] = 'L\'article demandé n\'existe pas.';

        return new JsonResponse($response);
    }

    $response['success'] = false;
    $response['message'] = 'Le formulaire de modification n\'est pas valide.';

    return new JsonResponse($response);
}


}