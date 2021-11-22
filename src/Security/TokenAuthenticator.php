<?php

namespace App\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    public const TOKEN_NAME = 'X-API-TOKEN';

    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::TOKEN_NAME);
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get(self::TOKEN_NAME);
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if ($this->parameterBag->get('api.token') === $credentials) {
            return new VirtualApiUser();
        }

        return null;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => 'Invalid api token',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
