<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests;

use GeekBrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\LevelTwo\Blog\Comment;
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
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot find comment: a12bcd34-5e67-890f-b859-426614174000');
        $this->expectException(CommentNotFoundException::class);
        $repository->get(new UUID('a12bcd34-5e67-890f-b859-426614174000'));
    }

    public function testItSavesCommentToDatabase(): void
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
                ':text' => 'Sentence',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentsRepository($connectionStub);


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
            new Comment(
                new UUID('a12bcd34-5e67-890f-b859-426614174000'),
                $post,
                $user,
                'Sentence'
            )
        );
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
            'post_uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
            'author_uuid' => 'a12bcd34-5e67-890f-b859-426614174000',
            'text' => 'Sentence',
            'title' => 'Word',
            'text' => 'Paragraph',
            'username' => 'login',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub);
        $comment = $commentRepository->get(new UUID('a12bcd34-5e67-890f-b859-426614174000'));

        $this->assertSame('a12bcd34-5e67-890f-b859-426614174000', (string)$comment->getUuid());
    }
}