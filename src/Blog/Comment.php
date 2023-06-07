<?php

namespace GeekBrains\LevelTwo\Blog;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private User $author,
        private Post $post,
        private string $comment,
    )
    {
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
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function __toString()
    {
        return $this->author . 'оставил комментарий ' . $this->comment . 'к посту: ' . $this->post->getAuthor() . '.' . PHP_EOL;
    }
}