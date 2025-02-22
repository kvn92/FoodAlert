<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Service\DeleteService;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('administrateur/recettes',name:'administrateur.')]
final class AdminRecetteController extends AbstractController
{
   
    #[Route('', name:'recettes',methods:['GET','POST'])]
    public function listeRecette(RecetteRepository $recetteRepository, StatsService $statsService, DeleteService $deleteService): Response
    {
        $titre = 'Recettes';
        $titrePage = strtoupper($titre);

        $recettes = $recetteRepository->findBy(['isActive'=>true],['createAt' => 'DESC']);
        $stats = $statsService->getEntityStats(Recette::class); 


        $deleteForms = [];
        foreach ($recettes as $v) { // Ne pas écraser la variable $viande
            $deleteForms[$v->getId()] = $deleteService
                ->createDeleteForm($v, 'delete_utlisateurs')
                ->createView();
        }
        return $this->render('admin/liste-recette.html.twig', [
            'recettes'=>$recettes,
            'stats'=>$stats,
            'titre'=>$titre,
            'titrePage'=> $titrePage,
            'deleteForms' => $deleteForms,
        ]);
    }

    #[Route('/validation', name:'recettes.validation',methods:['GET','POST'])]
    public function recettesValid(RecetteRepository $recetteRepository, StatsService $statsService,DeleteService $deleteService ): Response{
        $titrePage = 'Validation Recettes';
        $recetteValidations = $recetteRepository->findBy(['isActive'=>false],['createAt'=>'DESC']);
        $stats = $statsService->getEntityStats(Recette::class);


        $deleteForms = [];
        foreach ($recetteValidations as $v) { // Ne pas écraser la variable $viande
            $deleteForms[$v->getId()] = $deleteService
                ->createDeleteForm($v, 'delete_recettes')
                ->createView();
        }


        return $this->render('validations/recette-validation.html.twig',[
            'recettes'=>$recetteValidations,
            'titrePage'=>$titrePage,
            'stats'=>$stats,
            'deleteForms' => $deleteForms,

        ]);



    }


    #[Route('{id}/delete', name:'recettes.delete',methods:['GET','POST'])]
    public function delete(Recette $recette, Request $request, DeleteService $deleteService): Response
    {
        $wordEntity = 'recette';
        $redirect = $deleteService->handleDelete($recette, $request, 'delete_'.$wordEntity, $wordEntity.'.index');
    
        if ($redirect) {
            $this->addFlash('success', ' supprimée avec succès.');
            return $this->redirect($redirect);
        }
    
        $this->addFlash('error', 'Échec de la suppression.');
        return $this->redirectToRoute($wordEntity.'.index');
    }




}
