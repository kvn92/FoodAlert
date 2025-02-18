<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Form\DeleteFormType;

class DeleteService
{
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private FormFactoryInterface $formFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        FormFactoryInterface $formFactory
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->formFactory = $formFactory;
    }

    /**
     * Génère un formulaire de suppression pour une entité.
     */
    public function createDeleteForm(object $entity, string $csrfKey): FormInterface
    {
        return $this->formFactory->create(DeleteFormType::class, null, [
            'csrf_token_id' => $csrfKey . '_' . $entity->getId(),
        ]);
    }

    /**
     * Gère la suppression d'une entité avec vérification CSRF.
     */
    public function handleDelete(
        object $entity,
        Request $request,
        string $csrfKey,
        string $redirectRoute
    ): ?string {
        $form = $this->createDeleteForm($entity, $csrfKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            return $this->router->generate($redirectRoute);
        }

        return null;
    }
}


