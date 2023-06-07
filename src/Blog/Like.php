<?php

namespace GeekBrains\LevelTwo\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
        protected UUID $userUuid
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
     * @param UUID $userUuid
     */
    public function setUserUuid(UUID $userUuid): void
    {
        $this->userUuid = $userUuid;
    }

    /**
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }
}