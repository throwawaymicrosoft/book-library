<?php

namespace App\GraphQL\Mutation;

use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractMutation implements MutationInterface, ResolverInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function getDoctrine(): EntityManagerInterface
    {
        return $this->container->get('doctrine')->getManager();
    }
}
