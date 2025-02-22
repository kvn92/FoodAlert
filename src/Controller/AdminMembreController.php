<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('administrateur/membres',name:'administrateur.')]
final class AdminMembreController extends AbstractController
{
    #[Route('', name:'membre',methods:['GET','POST'])]
    public function listeMembre(UserRepository $userRepository, StatsService $statsService): Response
    { $titre = 'Membres';
        $titrePage = strtoupper($titre);

        $users = $userRepository->findAll();
        $stats = $statsService->getEntityStats(User::class); 


        return $this->render('admin/liste-membre.html.twig', [
            'membres'=>$users,
            'stats'=>$stats,
            'titre'=>$titre,
            'titrePage'=> $titrePage,
        ]);
    }


    #[Route('/new',name:'membres.new',methods:['POST','GET'])]
    public function new(Request $request ,EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response{

        $user =  new User();
        $form = $this->createForm(UserType::class, $user); 

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
            $plainPassword = $form->get('password')->getData();
            if($plainPassword){
                $hashedPassword = $passwordHasher->hashPassword($user,$plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','Ajouter');
            return $this->redirectToRoute('administrateur.membre');
        }

        return $this->render('admin/membres/new.html.twig',[
            'form'=>$form->createView(),
        ]);
    }




    #[Route('/{id}', name:'membres.show', methods:['GET'],requirements:['id'=>'\d+'])]


        public function show(UserRepository $userRepository,int $id ):Response {

            $user = $userRepository->find($id);

            return $this->render('admin/show-membre.html.twig',[
            
            'user'=>$user
            ]);
        }




        #[Route('/membres/{id}/edit', name: 'membres.edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
        public function edit(Request $request, EntityManagerInterface $entityManager, User $user, UserPasswordHasherInterface $passwordHasher): Response
        {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                // Récupérer le champ `password` sans écraser l'ancien si vide
                $plainPassword = $form->get('password')->getData();
        
                if ($plainPassword) { // Vérifier si l'utilisateur a saisi un mot de passe
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }
        
                $entityManager->persist($user);
                $entityManager->flush();
        
                $this->addFlash('success', 'Membre mis à jour avec succès.');
        
                return $this->redirectToRoute('administrateur.membre');
            }
        
            return $this->render('admin/membres/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        



    #[Route('{id}/toggle-statut',name:'toggle_statut')]

    public function toggle(): Response {

       return $this->redirectToRoute('administrateur.membre');
    }
    }



