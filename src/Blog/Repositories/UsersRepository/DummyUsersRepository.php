<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\UsersRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUUID(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByLogin(string $login): User
    {
        return new User(UUID::random(), new Name("first", "last"), "user123", "123");
    }
}