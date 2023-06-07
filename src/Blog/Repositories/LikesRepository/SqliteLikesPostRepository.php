<?php

namespace GeekBrains\LevelTwo\Blog\Repositories\LikesRepository;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeAlreadyExistsException;
use GeekBrains\LevelTwo\Blog\Exceptions\LikeNotFoundException;
use GeekBrains\LevelTwo\Blog\LikePost;
use GeekBrains\LevelTwo\Blog\UUID;
use PDO;
use Psr\Log\LoggerInterface;

class SqliteLikesPostRepository implements LikesPostRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function save(LikePost $like): void
    {
        $this->logger->info("Like start create");
        $statement = $this->connection->prepare(
            'INSERT INTO post_likes (uuid, postUuid, userUuid) VALUES (:uuid, :postUuid, :userUuid)');

        $statement->execute([
            ":uuid" => (string)$like->getUuid(),
            ":postUuid" => (string)$like->getPostUuid(),
            ":userUuid" => (string)$like->getUserUuid(),
        ]);
        $this->logger->info("Like created:" . $like->getUuid());

    }

    /**
     * @throws InvalidArgumentException
     * @throws LikeNotFoundException
     */
    public function getByPostUUID(UUID $postUuid): array
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post_likes WHERE postUuid = :postUuid"
        );
        $statement->execute([
            ":postUuid" => (string)$postUuid,
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!$result) {
            $this->logger->warning("Cannot get likes: $postUuid");
            throw new LikeNotFoundException(
                'No likes to post with uuid = : ' . $postUuid
            );
        }

        $likes = [];
        foreach ($result as $like) {
            $likes[] = new LikePost(
                uuid: new UUID($like['uuid']),
                userUuid: new UUID($like['userUuid']),
                postUuid: new UUID($like['postUuid']),
            );
        }
        return $likes;
    }
    public function delete(UUID $uuid): void
    {
        // TODO: Implement delete() method.
    }

    /**
     * @throws LikeAlreadyExistsException
     */
    public function checkUserLikeForPostExists(string $postUuid, UUID $userUuid): void
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM post_likes WHERE  userUuid = :userUuid AND postUuid = :postUuid"
        );
        $statement->execute([
            ":postUuid" => (string)$postUuid,
            ":userUuid" => (string)$userUuid
        ]);

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExistsException(
                'The users like for this post already exists'
            );
        }
    }
}