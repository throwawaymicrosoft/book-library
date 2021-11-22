<?php

namespace App\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheAdapter
{
    private CacheInterface $cache;
    private ParameterBagInterface $parameterBag;

    public function __construct(CacheInterface $cache, ParameterBagInterface $parameterBag)
    {
        $this->cache = $cache;
        $this->parameterBag = $parameterBag;
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
        return $this->cache->get($this->guardKey($key), function (ItemInterface $item) use ($function) {
            $item->expiresAfter((int) $this->parameterBag->get('cache.ttl'));

            return $function();
        });
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(string $key): void
    {
        $this->cache->delete($this->guardKey($key));
    }
}
