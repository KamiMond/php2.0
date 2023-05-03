<?php
namespace Geekbrains\Leveltwo\Blog;

use Geekbrains\Leveltwo\Person\Name;

class User
{
    private int $id;
    private Name $name;
    private string $login;

    /**
     * Summary of User
     * @param int $id
     * @param Name $name
     * @param string $login
     */

    public function __construct(int $id, Name $name, string $login)
    {
        $this->id = $id;
        $this->name = $name;
        $this->login = $login;
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
    public function login(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function __toString(): string
    {
        return "Юзер $this->id с именем $this->name с логином $this->login" . PHP_EOL;
    }
}