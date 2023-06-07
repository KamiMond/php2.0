<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests\Commands;


use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use GeekBrains\LevelTwo\Blog\UnitTests\DummyLogger;
use GeekBrains\LevelTwo\Blog\Commands\Arguments;
use GeekBrains\LevelTwo\Blog\Commands\Users\CreateUser;
use GeekBrains\LevelTwo\Blog\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use GeekBrains\LevelTwo\Blog\Exceptions\CommandException;
use GeekBrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\DummyUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testItSavesUserToRepository(): void
    {
        $usersRepository =  new class implements UsersRepositoryInterface {
// В этом свойстве мы храним информацию о том,
// был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
// Запоминаем, что метод save был вызван
                $this->called = true;
            }
            public function getByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
// Этого метода нет в контракте UsersRepositoryInterface,
// но ничто не мешает его добавить.
// С помощью этого метода мы можем узнать,
// был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }

        };

        $command = new CreateUser(
            $usersRepository
        );
        $command->run(
            new ArrayInput([
                'login' => 'Админ',
                'password' => '123',
                'firstName' => 'Иван',
                'lastName' => 'Никитин',
            ]),
            new NullOutput()
        );
        $this->assertTrue($usersRepository->wasCalled());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresLastNameNew(): void
    {
// Тестируем новую команду
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );
// Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);
// .. и его сообщение
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "lastName").'
        );
// Запускаем команду методом run вместо handle
        $command->run(
// Передаём аргументы как ArrayInput,
// а не Arguments
// Сами аргументы не меняются
            new ArrayInput([
                'login' => 'админ',
                'password' => '123',
                'firstName' => 'Иван',
            ]),
// Передаём также объект,
// реализующий контракт OutputInterface
// Нам подойдёт реализация,
// которая ничего не делает
            new NullOutput()
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresPasswordNew(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "password").'
        );
        $command->run(
            new ArrayInput([
                'login' => 'админ',
                'lastName' => 'Никитин',
                'firstName' => 'Иван',
            ]),
            new NullOutput()
        );
    }
    public function testItRequiresFirstNameNew(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "firstName, password").'
        );
        $command->run(
            new ArrayInput([
                'login' => 'админ',
                'lastName' => 'Никитин'
            ]),
            new NullOutput()
        );
    }

    /**
     * @throws CommandException
     * @throws InvalidArgumentException
     */
    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'login' => 'Ivan123',
            'firstName' => 'Ivan',
            'lastName' => 'Никитин'
        ]));
    }

    /**
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды зависимость - UsersRepositoryInterface и logger
        $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'password' => '123',
        ]));
    }


    // Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function getByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя

    /**
     * @throws CommandException
     */
    public function testItRequiresLastName(): void
    {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: lastName');
        $command->handle(new Arguments([
            'login' => 'Ivan',
            // Нам нужно передать имя пользователя,
            // чтобы дойти до проверки наличия фамилии
            'firstName' => 'Ivan',
            'password' => '123'
        ]));
    }
    // Тест проверяет, что команда действительно требует имя пользователя

    /**
     * @throws CommandException
     * @throws InvalidArgumentException
     */
    public function testItRequiresFirstName(): void
    {
// Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: firstName');
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'password' => '123'
        ]));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории

    /**
     * @throws CommandException
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     */
    public function testItSavesUserToRepositoryOld(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }
            public function getByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
        // Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository, new DummyLogger());
        // Запускаем команду
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
            'password' => '123'
        ]));
        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

}