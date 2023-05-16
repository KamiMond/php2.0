<?php

namespace GeekBrains\LevelTwo\Blog\UnitTests\Commands;

use GeekBrains\LevelTwo\Blog\Commands\Arguments;
use GeekBrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
    /**
     * @throws ArgumentsException
     */
    public function testItReturnsArgumentsValueByName(): void
    {
        // Подготовка
        $arguments = new Arguments(['some_key' => 'some_value']);
        // Действие
        $value = $arguments->get('some_key');
        // Проверка
        $this->assertEquals('some_value', $value);
        $this->assertSame('some_value', $value);
        $this->assertIsString($value);
    }
    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        // Подготовка
        $arguments = new Arguments([]);
        // Действие
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage("No such argument: some_key");
        // Проверка
        $arguments->get('some_key');
    }

    /**
     * @dataProvider argumentsProvider
     * @throws ArgumentsException
     */
    public function testItConvertsArgumentsToStrings($inputValue, $expectedValue): void
    {
        // Подготовка
        $arguments = new Arguments(['some_key' => $inputValue]);
        // Действие
        $value = $arguments->get('some_key');
        // Проверка
        $this->assertEquals($expectedValue, $value);
    }

    public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'],
            [' some_string', 'some_string'],
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }
}