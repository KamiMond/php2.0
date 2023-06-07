<?php

namespace GeekBrains\LevelTwo\Http\Actions\Auth;

use DateTimeImmutable;
use GeekBrains\LevelTwo\Blog\AuthToken;
use GeekBrains\LevelTwo\Blog\Exceptions\AuthException;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\Http\Actions\ActionInterface;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\http\Response;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->uuid(),
            (new DateTimeImmutable())->modify('+ 1 day')
        );
        $this->authTokensRepository->save($authToken);
        return new SuccessfulResponse([
            'token' => $authToken->getToken(),
        ]);
    }
}