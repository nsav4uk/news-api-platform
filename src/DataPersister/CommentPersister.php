<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentPersister
 * @package App\DataPersister
 */
class CommentPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private ?User $user;

    /**
     * CommentPersister constructor.
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $storage
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $storage)
    {
        $this->entityManager = $entityManager;
        $this->user = $storage->getToken()->getUser() instanceOf UserInterface ? $storage->getToken()->getUser() : null;
    }

    public function supports($data): bool
    {
        return $data instanceof Comment;
    }

    /**
     * @param Comment $data
     * @return object|void
     */
    public function persist($data)
    {
        $data->setSender($this->user);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param Comment $data
     */
    public function remove($data): void
    {
        $data->setSender(null);
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
