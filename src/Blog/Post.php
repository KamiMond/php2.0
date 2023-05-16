<?php
namespace GeekBrains\LevelTwo\Blog;

class Post
{
    public function __construct (
            private UUID   $uuid,
            private User   $user,
            private string $title,
            private string $text,
    )
    {
    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUud(UUID $uuid): void
    {
        $this->uuid = $uuid;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * @return Post
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