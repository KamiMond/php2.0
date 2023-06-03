<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests;

use GeekBrains\LevelTwo\Blog\Like;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteLikesRepositoryTest extends TestCase
{
    public function testItSavesLikeToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
                ':post_uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
                ':author_uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteLikesRepository($connectionStub);


        $user = new User(
            new UUID('a12bcd34-5e67-890f-b859-426614174000'),
            new Name('Ivan', 'Ivanov'),
            'login',
        );

        $post = new Post(
            new UUID('a12bcd34-5e67-890f-b859-426614174000'),
            $user,
            'Word',
            'Paragraph'
        );

        $repository->save(
            new Like(
                new UUID('a12bcd34-5e67-890f-b859-426614174000'),
                $post,
                $user,
            )
        );
    }
}
