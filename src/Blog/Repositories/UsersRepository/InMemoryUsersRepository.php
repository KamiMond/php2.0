<?php
namespace GeekBrains\LevelTwo\Blog\Repositories\UsersRepository;

use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;


class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];
    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param UUID $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function getByUUID(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if((string)$user->getId() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        foreach ($this->users as $user) {
            if((string)$login === (string)$login) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $login");
    }
}