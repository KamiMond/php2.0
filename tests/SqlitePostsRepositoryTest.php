<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests;

use Exception;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':authorUuid' => '123e4567-e89b-12d3-a456-426614174001',
            ':headerText' => 'Заголовок',
            ':text' => 'Текст',
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repositoryPost = new SqlitePostsRepository($connectionStub, new DummyLogger());
        $repositoryPost->save(new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new User(new UUID('123e4567-e89b-12d3-a456-426614174001'),
                    new Name('Иван', 'Никитин'),
                    'Admin', '123'),
                'Заголовок',
                'Текст')
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repositoryPost = new SqlitePostsRepository($connectionStub, new DummyLogger());
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 123e4567-e89b-12d3-a456-426614174000');
        $repositoryPost->getByUUID(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }

}