<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class VirtualApiUser implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return 'virtual';
    }

    public function eraseCredentials(): bool
    {
        return true;
    }
}
