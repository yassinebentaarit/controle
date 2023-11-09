<?php

namespace App\Controller;

use App\Entity\RDV;
use App\Form\RDVType;
use App\Repository\RDVRepository;
use App\Repository\RelationnelRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/RDV')]
class RDVController extends AbstractController
{

    #[Route('/', name: 'app_RDV_index', methods: ['GET'])]
    public function index(RDVRepository $RDVRepository): Response
    {
        return $this->render('rdv/index.html.twig', [
            'RDVs' => $RDVRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_RDV_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RDVRepository $RDVRepository, Security $security): Response
    {
        $RDV = new RDV();
        $form = $this->createForm(RDVType::class, $RDV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $RDVRepository->save($RDV, true);
            $this->addFlash(
                'info',
                'Added Successfully'
            );
            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('RDV/newFront.html.twig', [
            'RDV' => $RDV,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_RDV_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RDV $RDV, RDVRepository $RDVRepository, Security $security): Response
    {

       
            $RDV = new RDV();            
                $form = $this->createForm(RDVType::class, $RDV);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $RDVRepository->save($RDV, true);
                    $this->addFlash(
                        'info-edit',
                        'Updated Successfully'
                    );

                    return $this->redirectToRoute('app_RDV_index', [], Response::HTTP_SEE_OTHER);
                }

                return $this->renderForm('RDV/editFront.html.twig', [
                    'RDV' => $RDV,
                    'form' => $form,
                ]);}
    

    #[Route("/delete/{id}", name: "app_RDV_delete")]
    public function delete(Request $request, ManagerRegistry $doctrine, RDVRepository $repository, $id)
    {
        $RDV = $repository->find($id);
        $entityyManager = $doctrine->getManager();
        $entityyManager->remove($RDV);
        $entityyManager->flush();
        $this->addFlash(
            'info-delete',
            'Deleted Successfully'
        );
        return $this->redirectToRoute('app_RDV_index', [], Response::HTTP_SEE_OTHER);
}
    #[Route("/rdv/search_recc", name: "search_RDV", methods: "GET")]

    public function search_rec(Request $request, NormalizerInterface $Normalizer, RDVRepository $RDVRepository): Response
    {

        $requestString = $request->get('searchValue');
        $requestString3 = $request->get('orderid');

        $rdv = $RDVRepository->findRDV($requestString, $requestString3);
        $jsoncontentc = $Normalizer->normalize($rdv, 'json', ['groups' => 'posts:read']);
        $jsonc = json_encode($jsoncontentc);
        if ($jsonc == "[]") {
            return new Response(null);
        } else {
            return new Response($jsonc);
        }
    }
}
