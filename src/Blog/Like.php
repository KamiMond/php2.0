<?php

namespace GeekBrains\LevelTwo\Blog;

class Like
{
    public function __construct (
        private UUID $uuid,
        private Post $post,
        private User $user,
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

    public function __toString()
    {
        return $this->user . " поставил лайк посту: " . $this->post->getTitle() . PHP_EOL;
    }

}