<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentaireController extends AbstractController
{
    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }


    #[Route('commentaire/{id}/delete', name: 'commentaire_delete')]
    public function deleter(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    #[Route('commentaire/{id}/delete', name: 'commentaire.toggle_statut')]
    public function toggle(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    #[Route('commentaire/{id}', name: 'commentaire.show')]
    public function show(): Response
    {
        return $this->render('commentaire/show.html.twig', [
            
        ]);
    }
}
