<?php

namespace App\Controller;

use App\Entity\Commentaire;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recette;
use App\Form\CommentaireType;
use App\Form\RecetteType;
use App\Repository\CommentaireRepository;
use App\Repository\RecetteRepository;
use App\Service\DeleteService;
use App\Service\StatsService;
use App\Service\StatusToggleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/recette',name:'recette.')]
class RecetteController extends AbstractController
{

    #[Route('', name:'index',methods:['GET','POST'])]
    public function index(RecetteRepository $recetteRepository, StatsService $statsService, DeleteService $deleteService):Response{

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
        return $this->render('recette/index.html.twig',[
            'recettes'=>$recettes,
            'stats'=>$stats,
            'titre'=>$titre,
            'titrePage'=> $titrePage,
            'deleteForms' => $deleteForms,

        ]);
    
}

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function newRecette(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté avec Security
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter une recette.');
        }

        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette->setUser($user); // Associer la recette à l'utilisateur connecté
            /** @var UploadedFile|null $file */
            $file = $form->get('photo')->getData();
            if ($file) {
                $fileName = uniqid().'.'.$file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads/recettes';
                $file->move($destination, $fileName);
                $recette->setPhoto($fileName);
            }

            $entityManager->persist($recette);
            $entityManager->flush();

            $this->addFlash('success', 'Recette ajoutée avec succès.');
            return $this->redirectToRoute('recette.index');
        }

        return $this->render('recette/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'show', methods: ['GET','POST'], requirements: ['id' => '\d+'])]
public function show(
    Request $request,
    RecetteRepository $recetteRepository,
    CommentaireRepository $commentaireRepository,
    Security $security,
    EntityManagerInterface $entityManager,
    int $id
): Response {
    $recette = $recetteRepository->find($id);
    if (!$recette) {
        throw $this->createNotFoundException('Recette introuvable.');
    }

    $commentaires = $commentaireRepository->findBy(['recette' => $recette], ['createAt' => 'DESC']);
    $nbCommentaire = $commentaireRepository->count(['recette' => $recette]);

    $commentaire = new Commentaire();
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour commenter.');
            return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion
        }

        // Associer l'utilisateur et la recette au commentaire
        $commentaire->setRecette($recette)->setUser($user);

        $entityManager->persist($commentaire);
        $entityManager->flush();
        $this->addFlash('success', 'Commentaire ajouté avec succès.');

        return $this->redirectToRoute('show', ['id' => $id]);
    }

    return $this->render('recette/show.html.twig', [
        'recette' => $recette,
        'form' => $form->createView(),
        'commentaires' => $commentaires,
        'nbCommentaire' => $nbCommentaire,
    ]);
}










    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Recette $recette, Security $security ): Response
    {
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour Modifier une recette.');
        }


        $anciennePhoto = $recette->getPhoto();
        $form = $this->createForm(RecetteType::class, $recette, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('photo')->getData();
            if ($file) {
                $fileName = uniqid().'.'.$file->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads/recettes';

                // Supprimer l'ancienne photo si elle existe
                if ($anciennePhoto) {
                    $oldFilePath = $destination.'/'.$anciennePhoto;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                try {
                    $file->move($destination, $fileName);
                    $recette->setPhoto($fileName);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                    return $this->redirectToRoute('index.edit', ['id' => $recette->getId()]);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Discipline mise à jour avec succès !');
            return $this->redirectToRoute('recette.index');
        }

        return $this->render('recette/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/toggle_statut', name: 'toggle_statut', methods: ['GET'],requirements: ['id' => '\d+'])]
    public function toggleStatus(Recette $recette, StatusToggleService $statusToggleService): Response
    {
        // Basculer le statut
        $statusToggleService->toggleStatus($recette, 'isActive');

        // Rediriger vers la indexe des catégories
        return $this->redirectToRoute('recette.index');
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
    }
}
