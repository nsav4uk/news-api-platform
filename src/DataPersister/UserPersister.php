<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use App\Entity\User;
use App\Exception\InvalidRoleException;
use App\Exception\UsernameNotAvailableException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserDataPersister
 * @package App\DataPersister
 */
class UserPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $encoder;

    /**
     * UserDataPersister constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @throws UsernameNotAvailableException
     * @throws InvalidRoleException
     */
    public function persist($data): void
    {
        foreach ($data->getRoles() as $role)
        {
            if (!in_array($role, ['ROLE_MANAGER', 'ROLE_USER'], true))
            {
                throw new InvalidRoleException('Role should be ROLE_MANAGER or ROLE_USER');
            }
        }

        $data->setPassword($this->encoder->encodePassword($data, $data->getPassword()));
        $this->entityManager->persist($data);
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new UsernameNotAvailableException(sprintf('Username %s not available', $data->getUsername()));
        }
    }

    /**
     * @param User $data
     */
    public function remove($data): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
