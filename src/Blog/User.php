<?php
namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;

    /**
     * Summary of User
     * @param UUID $uuid
     * @param Name $name
     * @param string $login
     */

    public function __construct(UUID $uuid, Name $name, string $username)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $username;
    }
    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name 
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function __toString(): string
    {
        return "Юзер $this->uuid с именем $this->name под логином $this->username" . PHP_EOL;
    }
}