<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const USERNAME = 'admin';
    public const PASSWORD = 'admin';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('admin');
        $user->setEmail('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEnabled(true);
        $password = $this->passwordEncoder->encodePassword($user, self::PASSWORD);
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
