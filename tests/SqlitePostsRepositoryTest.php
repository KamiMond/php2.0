<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
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
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot find post: a12bcd34-5e67-890f-b859-426614174000');
        $this->expectException(PostNotFoundException::class);
        $repository->get(new UUID('a12bcd34-5e67-890f-b859-426614174000'));
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
                ':author_uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
                ':title' => 'Word',
                ':text' => 'Paragraph',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostsRepository($connectionStub);


        $user = new User(
            new UUID('a12bcd34-5e67-890f-b859-426614174000'),
            new Name('Ivan', 'Ivanov'),
            'login',
        );

        $repository->save(
            new Post(
                new UUID('a12bcd34-5e67-890f-b859-426614174000'),
                $user,
                'Word',
                'Paragraph'
            )
        );
    }

    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
            'author_uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
            'title' => 'Word',
            'text' => 'Paragraph',
            'username' => 'login',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub);
        $post = $postRepository->get(new UUID('a12bcd34-5e67-890f-b859-426614174000'));

        $this->assertSame('a12bcd34-5e67-890f-b859-426614174000', (string)$post->getUuid());
    }
}