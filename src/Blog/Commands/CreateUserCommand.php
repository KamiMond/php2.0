<?php

namespace GeekBrains\LevelTwo\Blog\Commands;

use GeekBrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use GeekBrains\LevelTwo\Blog\Exceptions\CommandException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $login = $arguments->get('login');

        if ($this->userExists($login)) {
            $this->logger->warning("User already exists: $login");
            throw new CommandException("User already exists: $login");
        }
        $user = User::createFrom(
            $login,
            new Name(
                $arguments->get('firstName'),
                $arguments->get('lastName'),
            ),
            $arguments->get('password')
        );
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        $this->logger->info("User created: " . $user->getLogin());
    }
    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByLogin($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }

}