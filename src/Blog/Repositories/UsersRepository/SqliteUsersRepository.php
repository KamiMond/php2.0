<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\UsersRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }
    public function save(User $user): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO users (firstName, lastName, uuid, login, password)
            VALUES (:firstName, :lastName, :uuid, :login, :password)
            ON CONFLICT (uuid) DO UPDATE SET firstName = :firstName, lastName = :lastName'
        );

        //            ON CONFLICT (uuid) DO UPDATE SET
//            firstName = :firstName,
//            lastName = :lastName
        $statement->execute([
            ':firstName' => $user->name()->first(),
            ':lastName' => $user->name()->last(),
            ':uuid' => $user->uuid(),
            ':login' => $user->getLogin(),
            ':password' => $user->hashedPassword()
        ]);
        $this->logger->info("User created: " . $user->getLogin());
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUUID(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );

        return $this->getUser($statement, (string)$uuid);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByLogin(string $login): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE login = :login'
        );
        $statement->execute([
            ':login' => $login,
        ]);

        return $this->getUser($statement, $login);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getUser(PDOStatement $statement, $findString): User
    {
        $statement->execute([(string)$findString]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException('Cannot get user:' . $findString);
        }
        return new User(
            new UUID((string)$result['uuid']),
            new Name($result['firstName'], $result['lastName']),
            (string)$result['login'],
            (string)$result['password']
        );
    }
}