<?php

namespace App\EventListener;

use App\Entity\Post;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PostSlugListener
{
    private EntityManagerInterface $em;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Generate slug on prePersist (New Post)
     *
     * @param Post $post
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function prePersist(Post $post, LifecycleEventArgs $args): void
    {
        $this->generateSlug($post);
    }

    public function preUpdate(Post $post, LifecycleEventArgs $args): void
    {
        // Access the UnitOfWork through the EntityManager
        $uow = $this->em->getUnitOfWork();

        // Get the original data for the Post entity
        $originalData = $uow->getOriginalEntityData($post);

        // Check if the title has changed, regenerate the Slug
        if (isset($originalData['title']) && $originalData['title'] !== $post->getTitle()) {
            $this->generateSlug($post);
        }
    }

    /**
     * Generate Slug
     *
     * @param Post $post
     *
     * @return void
     */
    private function generateSlug(Post $post): void
    {
        if (empty($post->getSlug())) { // Only generate if slug is not set
            $slugify = new Slugify();
            $post->setSlug($slugify->slugify($post->getTitle())); // Generate slug from title
        }
    }
}