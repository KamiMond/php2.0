<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests;

use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
// Тест, проверяющий, что SQLite-репозиторий бросает исключение,
// когда запрашиваемый пользователь не найден
    public function testItThrowsAnExceptionWhenUserNotFound(): void
{
    // Создаем стаб подключение
    $connectionStub = $this->createStub(PDO::class);
    //  Передаём в репозиторий stub подключение
    $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());
    //  Создаём стаб запроса
    $statementStub = $this->createStub(PDOStatement::class);
    // Stub подключение будет возвращать другой стаб - стаб запроса - при вызове метода prepare
    $connectionStub->method('prepare')->willReturn($statementStub);
    // Стаб запроса будет возвращать false при вызове метода fetch
    $statementStub->method('fetch')->willReturn(false);

    $this->expectException(UserNotFoundException::class);
    $this->expectExceptionMessage('Cannot get user:Ivan');
    $repository->getByLogin('Ivan');
}

    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesUserToDatabase(): void
{
    // 2. Создаём стаб подключения
    $connectionStub = $this->createStub(PDO::class);
    // 4. Создаём мок запроса, возвращаемый стабом подключения
    $statementMock = $this->createMock(PDOStatement::class);
    // 5. Описываем ожидаемое взаимодействие
    // нашего репозитория с моком запроса
    $statementMock
        ->expects($this->once()) // Ожидаем, что будет вызван один раз
        ->method('execute') // метод execute
        ->with([ // с единственным аргументом - массивом
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':login' => 'ivan123',
            ':firstName' => 'Ivan',
            ':lastName' => 'Nikitin',
            ':password' => '123'
        ]);
    // 3. При вызове метода prepare стаб подключения
    // возвращает мок запроса
    $connectionStub->method('prepare')->willReturn($statementMock);
    // 1. Передаём в репозиторий стаб подключения
    $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());
    // Вызываем метод сохранения пользователя
    $repository->save(
        new User( // Свойства пользователя точно такие,
        // как и в описании мока
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('Ivan', 'Nikitin'),
            'ivan123',
            '123'
        )
    );
}
}