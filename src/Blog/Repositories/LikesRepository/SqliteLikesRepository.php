<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\LikesRepository;

use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Like;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid,post_uuid,author_uuid) VALUES (:uuid,:post_uuid,:author_uuid)'
        );

        $statement->execute([
            ':uuid' => $like->getUuid(),
            ':post_uuid' => $like->getPost()->getUuid(),
            ':author_uuid' => $like->getUser()->uuid()
        ]);
    }

    /**
     * @throws LikeNotFoundException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByPostUuid(Post $uuid): Like
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid'
        );
        $statement->execute([
            ':post_uuid' => (string)$uuid,
        ]);

        return $this->getLike($statement, $uuid);
    }

    /**
     * @throws LikeNotFoundException
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getLike(PDOStatement $statement, string $likeUuId): Like
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new LikeNotFoundException(
                "Cannot find like: $likeUuId"
            );
        }

        $postRepository = new SqlitePostsRepository($this->connection);
        $post = $postRepository->get(new UUID($result['post_uuid']));


        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Like(
            new UUID($result['uuid']),
            $post,
            $user
        );
    }
}