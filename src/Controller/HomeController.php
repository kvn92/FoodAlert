<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(RecetteRepository $recetteRepository ,StatsService $statsService): Response
    {
        $recettesDerniereSortie =  $recetteRepository->findBy(['isActive'=>true],['createAt'=>'DESC'],4);
        $stats = $statsService->getEntityStats(Recette::class);
        
        $recettesPopulaires = $recetteRepository->findMostLikedRecette();
        


        return $this->render('home/index.html.twig', [
            'stats'=>$stats,
            'recettesDerniereSortie'=>$recettesDerniereSortie,
            'recettesPopulaires'=>$recettesPopulaires
          
        ]);
    }
}
