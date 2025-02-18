<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EntityFormatterService
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Retourne l'image d'une entité ou une image par défaut.
     */
    public function getImagePath(?string $image, string $folder, string $defaultImage = 'default.jpg'): string
    {
        if ($image) {
            return $folder . $image;
        }

        return $folder . $defaultImage;
    }

    /**
     * Vérifie et retourne l'URL d'une vidéo ou null si elle est absente.
     */
    public function getVideoUrl(?string $videoUrl): ?string
    {
        return $videoUrl ?: null;
    }

    /**
     * Retourne le nom d'une entité, avec une valeur par défaut si vide.
     */
    public function formatName(?string $name, string $default = 'Nom inconnu'): string
    {
        return $name ?: $default;
    }

    public function formatDescription(?string $name, string $default = 'Nom inconnu'): string
    {
        return $name ?: $default;
    }

    public function formatQuota(?int $int, string $default = 'Nom inconnu'): int
    {
        return $int ?: $default;
    }
}
