<?php
namespace GeekBrains\LevelTwo\Person;

use \DateTimeImmutable;

class Person
{
    public function __construct
    (
        private Name $name,
        private DateTimeImmutable $registeredOn,
    ) {}
    public function __toString(): string
    {
        return $this->name . '(на сайте с ' . $this->registeredOn->format('Y-m-d') . ')';
    }
}