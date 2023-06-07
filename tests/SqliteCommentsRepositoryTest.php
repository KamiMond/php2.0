<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests;

use GeekBrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':authorUuid' => '123e4567-e89b-12d3-a456-426614174001',
            ':postUuid' => '123e4567-e89b-12d3-a456-426614174002',
            ':comment' => 'Комментарий'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $author = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174001'),
            new Name('Иван', 'Никитин'),
            'admin',
            '123'
        );
        $post = new Post(
            new UUID('123e4567-e89b-12d3-a456-426614174002'),
            $author,
            'Заголовок',
            'Текст'
        );

        $repositoryComment = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $repositoryComment->save(new Comment(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            $author,
            $post,
            'Комментарий'
        ));
    }


    /**
     * @throws PostNotFoundException
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repositoryComment = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find comment: 123e4567-e89b-12d3-a456-426614174000');
        $repositoryComment->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }
}