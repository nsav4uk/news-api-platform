<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\News;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class NewsPersister
 * @package App\DataPersister
 */
class NewsPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private ?User $user;

    /**
     * NewsPersister constructor.
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
        return $data instanceof News;
    }

    /**
     * @param News $data
     * @return object|void
     */
    public function persist($data)
    {
        if ($data->getStatus() === News::PENDING_APPROVAL) {
            $data->setAuthor($this->user);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param News $data
     */
    public function remove($data): void
    {
        $data->setAuthor(null);
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
