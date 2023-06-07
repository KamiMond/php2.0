<?php
namespace GeekBrains\LevelTwo\Blog;

class Post
{
    public function __construct(
        private UUID $uuid,
        private User $author,
        private string $headerText,
        private string $text,
    )
    {
    }
    public function __toString(): string
    {
        return $this->author . ' пишет: ' . $this->text;
    }

    public function getHeaderText(): string
    {
        return $this->headerText;
    }

    /**
     * @param string $headerText
     */
    public function setHeaderText(string $headerText): void
    {
        $this->headerText = $headerText;
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
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }
}