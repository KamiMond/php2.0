<?php
namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Name;

class User
{
    /**
     * Summary of User
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     * @param string $hashedPassword
     */
    private UUID $uuid;
    private Name $name;
    private string $username;
    private string $hashedPassword;


    public function __construct(UUID $uuid, Name $name, string $username, string $hashedPassword)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $username;
        $this->hashedPassword = $hashedPassword;
    }
    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256',  $uuid . $password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    /**
     * @throws Exceptions\InvalidArgumentException
     */
    public static function createFrom(
        Name   $name,
        string $username,
        string $password
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password, $uuid),
        );
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

    public function hashedPassword(): string
    {
        return $this->hashedPassword ;
    }

    public function __toString(): string
    {
        return "Юзер $this->uuid с именем $this->name под логином $this->username" . PHP_EOL;
    }
}