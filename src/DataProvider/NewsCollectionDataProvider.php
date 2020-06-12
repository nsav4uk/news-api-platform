<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\{CollectionDataProviderInterface, RestrictedDataProviderInterface};
use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class NewsCollectionDataProvider
 * @package App\DataProvider
 */
final class NewsCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private EntityManagerInterface $em;
    private array $userRoles;

    /**
     * NewsCollectionDataProvider constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $storage
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $storage)
    {
        $this->em = $em;
        $this->userRoles = $storage->getToken()->getUser() instanceof UserInterface
            ? $storage->getToken()->getUser()->getRoles() : [];
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return News::class === $resourceClass;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @return iterable|object[]
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $state = in_array('ROLE_MANAGER', $this->userRoles, true) ? News::PENDING_APPROVAL : News::PUBLISHED;

        return $this->em->getRepository($resourceClass)->findBy(['status' => $state]);
    }
}
