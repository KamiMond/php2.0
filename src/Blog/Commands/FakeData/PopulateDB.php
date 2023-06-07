<?php

namespace GeekBrains\LevelTwo\Blog\Commands\FakeData;

use Faker\Generator;
use GeekBrains\LevelTwo\Blog\Comment;
use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Blog\Post;
use GeekBrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use GeekBrains\LevelTwo\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private Generator $faker
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Users number'
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Posts number'
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
// Создаём десять пользователей

        $userCount = (int)$input->getOption('users-number');
        $postCount = (int)$input->getOption('posts-number');
        if(empty($userCount) && empty($postCount)) {
            $output->writeln('Input count users and count posts');
            return Command::SUCCESS;
        }
        $users = [];
        for ($i = 0; $i < $userCount; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getLogin());
        }
// От имени каждого пользователя
// создаём по двадцать статей
        foreach ($users as $user) {
            for ($i = 0; $i < $postCount; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getHeaderText());
                $comment = $this->createFakeComment($user, $post);
            }
        }
        return Command::SUCCESS;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            new Name(
                $this->faker->firstName,
                $this->faker->lastName
            ),
            $this->faker->password,
        );

        $this->usersRepository->save($user);
        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
// Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
// Генерируем текст
            $this->faker->realText
        );
// Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeComment(User $author, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $post,
// Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true)
        );
// Сохраняем в репозиторий
        $this->commentsRepository->save($comment);
        return $comment;
    }
}