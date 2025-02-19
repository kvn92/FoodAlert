<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use App\Entity\User;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use App\Service\DeleteService;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin',name:'admin.')]
final class AdminController extends AbstractController
{
    #[Route('/admins', name: 'app_admin',methods:['GET','POST'])]
    public function index(UserRepository $userRepository, RecetteRepository $recetteRepository, StatsService $statsService): Response
    {
        $statsRecette = $statsService->getEntityStats(Recette::class); 
        $statsMembre = $statsService->getEntityStats(User::class);
        $statsCommentaire = $statsService->getEntityStats(Commentaire::class);
        return $this->render('admin/index.html.twig', [
            'statsRecette' => $statsRecette,
            'statsMembre'=>$statsMembre,
            'statsCommentaire'=>$statsCommentaire

        ]);
    }

    #[Route('/dashboard', name: 'dashboard',methods:['GET','POST'])]
    public function dashboard(UserRepository $userRepository, RecetteRepository $recetteRepository, StatsService $statsService): Response
    {
        $statsRecette = $statsService->getEntityStats(Recette::class); 
        $statsMembre = $statsService->getEntityStats(User::class);
        $statsCommentaire = $statsService->getEntityStats(Commentaire::class);

        return $this->render('admin/dashboard.html.twig', [
            'statsRecette' => $statsRecette,
            'statsMembre'=>$statsMembre,
            'statsCommentaire'=>$statsCommentaire
        ]);
    }

    #[Route('/recettes', name:'recettes',methods:['GET','POST'])]
    public function listeRecette(RecetteRepository $recetteRepository, StatsService $statsService, DeleteService $deleteService): Response
    {
        $titre = 'Recettes';
        $titrePage = strtoupper($titre);

        $recettes = $recetteRepository->findAll();
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

    #[Route('/membres', name:'membres',methods:['GET','POST'])]
    public function listeMembre(UserRepository $userRepository, StatsService $statsService): Response
    { $titre = 'Membre';
        $titrePage = strtoupper($titre);

        $users = $userRepository->findUsersByRole();
        $stats = $statsService->getEntityStats(User::class); 


        $deleteForms = [];
        foreach ($users as $v) { // Ne pas écraser la variable $viande
            $deleteForms[$v->getId()] = $deleteService
                ->createDeleteForm($v, 'delete_utlisateurs')
                ->createView();
        }
        return $this->render('admin/liste-recette.html.twig', [
            'users'=>$users,
            'stats'=>$stats,
            'titre'=>$titre,
            'titrePage'=> $titrePage,
            'deleteForms' => $deleteForms,
        ]);
    }

    #[Route('/recettes/validation', name:'recettes.validation',methods:['GET','POST'])]

    public function recettesValid(): Response{

        return $this->render('admin/recetteValidation.htm.twig',[

        ]);
    }

    #[Route('/commentaires/validation', name:'commentaires.validation',methods:['GET','POST'])]

    public function commentaireValid(): Response{


        return $this->render('admin/recetteValidation.htm.twig',[

        ]);
    }
}
