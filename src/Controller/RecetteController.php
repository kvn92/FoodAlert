<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\LikeRecette;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Recette;
use App\Entity\SauvergardeRecette;
use App\Entity\UserFollow;
use App\Form\CommentaireType;
use App\Form\RecetteType;
use App\Repository\CommentaireRepository;
use App\Repository\LikeRecetteRepository;
use App\Repository\RecetteRepository;
use App\Repository\SauvergardeRecetteRepository;
use App\Repository\UserFollowRepository;
use App\Repository\UserRepository;
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
    LikeRecetteRepository $likeRecetteRepository,
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
    $nnLike = $commentaireRepository->count(['recette'=>$recette]);

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

        return $this->redirectToRoute('recette.show', ['id' => $id]);
    }

    return $this->render('recette/show.html.twig', [
        'recette' => $recette,
        'form' => $form->createView(),
        'commentaires' => $commentaires,
        'nbCommentaire' => $nbCommentaire,
        'nbLike'=>$nnLike,
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

    #[Route('/{id}/like', name: 'like', methods: ['POST'])]
    public function toggleLike(
        int $id, 
        RecetteRepository $recetteRepository, 
        LikeRecetteRepository $likeRecetteRepository, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $recette = $recetteRepository->find($id);
        $user = $security->getUser();
    
        if (!$recette) {
            throw $this->createNotFoundException('Recette introuvable.');
        }
    
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour liker une recette.');
            return $this->redirectToRoute('recette.show', ['id' => $id]);
        }
    
        // Vérifier si l'utilisateur a déjà liké cette recette
        $like = $likeRecetteRepository->findOneBy(['user' => $user, 'recette' => $recette]);
    
        if ($like) {
            // Supprimer le like
            $entityManager->remove($like);
            $this->addFlash('success', 'Like retiré.');
        } else {
            // Ajouter un like
            $like = new LikeRecette();
            $like->setUser($user);
            $like->setRecette($recette);
            $like->setIsActive(true);
            $entityManager->persist($like);
            $this->addFlash('success', 'Recette likée.');
        }
    
        $entityManager->flush();
    
        return $this->redirectToRoute('recette.show', ['id' => $id]);
    }


    #[Route('/{id}/sauvegarder', name: 'sauvegarder', methods: ['POST'])]
    public function toggleSauvegarde(
        int $id, 
        RecetteRepository $recetteRepository, 
        SauvergardeRecetteRepository $sauvergardeRecetteRepository, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $recette = $recetteRepository->find($id);
        $user = $security->getUser();
    
        if (!$recette) {
            throw $this->createNotFoundException('Recette introuvable.');
        }
    
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour liker une recette.');
            return $this->redirectToRoute('recette.show', ['id' => $id]);
        }
    
        // Vérifier si l'utilisateur a déjà liké cette recette
        $sauvegarder = $sauvergardeRecetteRepository->findOneBy(['user' => $user, 'recette' => $recette]);
    
        if ($sauvegarder) {
            // Supprimer le like
            $entityManager->remove($sauvegarder);
            $this->addFlash('success', 'Like retiré.');
        } else {
            // Ajouter un like
            $sauvegarder = new SauvergardeRecette();
            $sauvegarder->setUser($user);
            $sauvegarder->setRecette($recette);
            $sauvegarder->setIsActive(true);
            $entityManager->persist($sauvegarder);
            $this->addFlash('success', 'Recette dans les favoris.');
        }
    
        $entityManager->flush();
    
        return $this->redirectToRoute('recette.show', ['id' => $id]);
    }

    #[Route('/{id}/following', name: 'following', methods: ['POST','GET'])]
public function toggleFollow(
    int $id, 
    UserRepository $userRepository, 
    UserFollowRepository $userFollowRepository, 
    EntityManagerInterface $entityManager,
    Security $security
): Response {
    $userToFollow = $userRepository->find($id);
    $user = $security->getUser();

    if (!$userToFollow) {
        throw $this->createNotFoundException('Utilisateur introuvable.');
    }

    if (!$user) {
        $this->addFlash('error', 'Vous devez être connecté pour suivre un utilisateur.');
        return $this->redirectToRoute('recette.show', ['id' => $id]);
    }

    // Vérifier si l'utilisateur suit déjà l'autre utilisateur
    $existingFollow = $userFollowRepository->findOneBy([
        'follower' => $user, 
        'following' => $userToFollow
    ]);

    if ($existingFollow) {
        // Supprimer le suivi
        $entityManager->remove($existingFollow);
        $this->addFlash('success', 'Vous ne suivez plus cet utilisateur.');
    } else {
        // Ajouter le suivi
        $newFollow = new UserFollow();
        $newFollow->setFollower($user);
        $newFollow->setFollowing($userToFollow);
        $entityManager->persist($newFollow);
        $this->addFlash('success', 'Vous suivez cet utilisateur.');
    }

    $entityManager->flush();

    return $this->redirectToRoute('recette.show', ['id' => $id]);
}

}   
