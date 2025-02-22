<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use App\Entity\User;
use App\Repository\CommentaireRepository;
use App\Repository\RecetteRepository;
use App\Repository\UserRepository;
use App\Service\DeleteService;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin',name:'admin.')]
final class AdminController extends AbstractController
{
   

    

   

    
 
    #[Route('/commentaires/validation', name:'commentaires.validation',methods:['GET','POST'])]
    public function commentaireValid(CommentaireRepository $commentaireRepository, StatsService $statsService): Response{


        $titrePage = 'Validation des commentaires';
        $commentaire = $commentaireRepository->findBy(['isActive'=>false],['createAt'=>'DESC']);
        $stats = $statsService->getEntityStats(Commentaire::class);


        return $this->render('validations/commentaire-validation.html.twig',[
        'titrePage'=>$titrePage,
        'commentaires' => $commentaire,
        'stats'=>$stats
        ]);
    }

    #[Route('/commentaires/{id}', name:'commentaire.show',methods:['GET','POST'])]
    public function show(CommentaireRepository $commentaireRepository, StatsService $statsService): Response{


        $titrePage = 'Validation des commentaires';
        $commentaire = $commentaireRepository->findBy(['isActive'=>false],['createAt'=>'DESC']);
        $stats = $statsService->getEntityStats(Commentaire::class);


        return $this->render('commentaire/show.html.twig',[
        'titrePage'=>$titrePage,
        'commentaires' => $commentaire,
        'stats'=>$stats
        ]);


    }


    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]    
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

        $deleteForms = [];
        foreach ($recetteValidations as $v) { // Ne pas écraser la variable $viande
            $deleteForms[$v->getId()] = $deleteService
                ->createDeleteForm($v, 'delete_recettes')
                ->createView();
        }
        return $this->render("");
    }

}
