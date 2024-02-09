<?php
// src/EventListener/PostListener.php

namespace App\EventListener;

use App\Entity\Post;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsEventListener(event: 'doctrine.orm.pre_flush')]
final class PostListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function onDoctrineOrmPreFlush(PreFlushEventArgs $event): void
    {
        $entityManager = $event->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Post) {
                if (!$entity->getSlug()) {
                    // Utilisation de l'AsciiSlugger pour générer le slug
                    $asciiSlugger = new AsciiSlugger();
                    $slug = $asciiSlugger->slug($entity->getTitle())->lower();
                    $entity->setSlug($slug);
                }
            }
        }
    }
}


