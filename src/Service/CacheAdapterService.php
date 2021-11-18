<?php

namespace App\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheAdapterService
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function guardKey(string $key): string
    {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $key);
    }

    /**
     * @return mixed
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $key, callable $function)
    {
        return $this->container->get('cache.app')->get($this->guardKey($key), function (ItemInterface $item) use ($function) {
            $item->expiresAfter((int) $this->container->getParameter('cache.ttl'));

            return $function();
        });
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(string $key): void
    {
        $this->container->get('cache.app')->delete($this->guardKey($key));
    }
}
