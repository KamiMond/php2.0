<?php

namespace GeekBrains\LevelTwo\Blog;

class LikePost extends Like
{
    private UUID $postUuid;
    public function __construct(
        UUID $uuid,
        UUID $userUuid,
        UUID $postUuid
    )
    {
        parent::__construct($uuid, $userUuid);
        $this->postUuid = $postUuid;
    }

    /**
     * @return UUID
     */
    public function getPostUuid(): UUID
    {
        return $this->postUuid;
    }

    /**
     * @param UUID $postUuid
     */
    public function setPostUuid(UUID $postUuid): void
    {
        $this->postUuid = $postUuid;
    }

    public function __toString()
    {
        return $this->userUuid . " поставил лайк посту: " . $this->postUuid . PHP_EOL;
    }
}