<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commentaire', name: 'admin.')]
final class AdminCommentaireController extends AbstractController
{
   
    #[Route('', name: 'commentaire')]
    public function index(): Response
    {
        return $this->render('admin_commentaire/index.html.twig', [
            'controller_name' => 'AdminCommentaireController',
        ]);
    }
}
