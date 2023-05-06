<?php
namespace GeekBrains\LevelTwo\Blog;

class Post
{
    private int $id;
    private User $user;
    private string $text;

    public function __construct(int $id, User $user, string $text)
    {
        $this->id = $id;
        $this->text = $text;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): Post
    {
        $this->text = $text;
        return $this;
    }

    public function __toString()
    {
        return $this->user . " написал: " . PHP_EOL . $this->text . PHP_EOL;
    }
}