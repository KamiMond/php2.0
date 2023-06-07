<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\PostsRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostRepositoryException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;


class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Post $post): void
    {
        $this->logger->info("Create post command started");
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, authorUuid, headerText, text)
            VALUES (:uuid, :authorUuid, :headerText, :text)'
        );
        $statement->execute([
            ':uuid' => $post->uuid(),
            ':authorUuid' => $post->getAuthor()->uuid(),
            ':headerText' => $post->getHeaderText(),
            ':text' => $post->getText(),
        ]);
        $this->logger->info("Post created: $post");
    }

    /**
     * @throws PostNotFoundException|InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getByUUID(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getPost(PDOStatement $statement, $postUuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Cannot get post: $postUuid");
            throw new PostNotFoundException(
                "Cannot find post: $postUuid"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->getByUUID(new UUID($result['authorUuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['headerText'],
            $result['text']
        );
    }

    /**
     * @throws PostRepositoryException
     */
    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = :uuid'
            );
            $statement->execute([
                ':uuid' => (string)$uuid
            ]);
        } catch (\PDOException $e) {
            throw new PostRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }
}