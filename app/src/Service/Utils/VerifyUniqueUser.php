<?php


namespace App\Service\Utils;


use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class VerifyUniqueUser
{
    private UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    function verifyUniqueUser(string $identifier, object $user): bool
    {
        try {
            return $this->userProvider->loadUserByIdentifier($identifier) === $user;
        } catch (UserNotFoundException $e) {
            return true;
        }
    }
}