<?php
namespace GeekBrains\LevelTwo\Person;

use \DateTimeImmutable;

class Person
{
    private Name $name;
    private DateTimeImmutable $registratedOn;

    public function __construct(Name $name, DateTimeImmutable $registratedOn)
    {
        $this->name = $name;
        $this->registratedOn = $registratedOn;
    }

    public function __toString()
    {
        return $this->name . " зарегистрирован на сайте с " . $this->registratedOn->format('Y-m-d');
    }
}