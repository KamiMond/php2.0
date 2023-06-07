<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\UUID;
use PDO;
use Psr\Log\LoggerInterface;


class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Comment $comment): void
    {
        $this->logger->info("Create comment command started");
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, authorUuid, postUuid, comment)
            VALUES (:uuid, :authorUuid, :postUuid, :comment)'
        );
        $statement->execute([
            ':uuid' => $comment->uuid(),
            ':authorUuid' => $comment->getAuthor()->uuid(),
            ':postUuid' => $comment->getPost()->uuid(),
            ':comment' => $comment->getComment()
        ]);
        $this->logger->info("Comment created: " . $comment->uuid());
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid '
        );
        $statement->execute([
            ':uuid' => $uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    /**
     * @throws PostNotFoundException
     * @throws CommentNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getPost(\PDOStatement $statement, $commentUuid): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Cannot get comment: $commentUuid");
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuid"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->getByUUID(new UUID($result['authorUuid']));
        $postRepository = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postRepository->getByUUID(new UUID($result['postUuid']));

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['comment']
        );
    }
    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}