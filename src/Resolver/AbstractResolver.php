<?php

namespace App\Resolver;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractResolver implements ResolverInterface
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function entityManager(): Registry
    {
        return $this->container->get('doctrine');
    }
}
