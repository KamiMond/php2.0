<?php
namespace GeekBrains\LevelTwo\Person;

class Name
{
    private string $firstName;
    private string $lastName;

    public function __construct(string $firstName, string $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName 
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName 
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
    public function __toString()
    {
        return $this->firstName . " " . $this->lastName;
    }
}