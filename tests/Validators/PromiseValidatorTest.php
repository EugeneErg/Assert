<?php

declare(strict_types = 1);

namespace Tests\Validators;

use EugeneErg\Assert\Promise;
use EugeneErg\Assert\PromiseValidator;
use EugeneErg\Assert\ValidationException;
use PHPUnit\Framework\TestCase;

final class PromiseValidatorTest extends TestCase
{
    /**
     * @dataProvider getIsAAssertData
     */
    public function testIsAAssert(mixed $valueA, mixed $valueB, bool $allowString, mixed $data, ?string $expected): void
    {
        if ($expected !== null) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage($expected);
        }

        PromiseValidator::isA($valueA, $valueB, $allowString)->assert($data);

        $this->assertTrue(true);
    }

    public static function getIsAAssertData(): array
    {
        return [
            [
                new Promise(0, 'object'),
                new Promise(0, 'class'),
                true,
                ['object' => PromiseValidatorTest::class, 'class' => TestCase::class],
                null,
            ],
            [
                new Promise(0, 'object'),
                new Promise(0, 'class'),
                true,
                ['object' => new self('name'), 'class' => TestCase::class],
                null,
            ],
            [
                new Promise(0, 'object'),
                new Promise(0, 'class'),
                true,
                ['object' => TestCase::class, 'class' => PromiseValidatorTest::class],
                'object must be of type class',
            ],
            [
                new Promise(0, 'object'),
                new Promise(0, 'class'),
                true,
                ['object' => TestCase::class, 'class' => new self('name')],
                'class must be of type string, object given',
            ],
            [
                new Promise(0, 'object'),
                new Promise(0, 'class'),
                false,
                ['object' => PromiseValidatorTest::class, 'class' => TestCase::class],
                'object must be of type object, string given',
            ],


            [
                PromiseValidatorTest::class,
                TestCase::class,
                true,
                null,
                null,
            ],
            [
                new self('name'),
                TestCase::class,
                true,
                null,
                null,
            ],
            [
                TestCase::class,
                PromiseValidatorTest::class,
                true,
                null,
                '"PHPUnit\Framework\TestCase" must be of type "Rules\PromiseValidatorTest"',
            ],
            [
                TestCase::class,
                new self('name'),
                true,
                null,
                'Rules\PromiseValidatorTest(...) must be of type string, object given',
            ],
            [
                PromiseValidatorTest::class,
                TestCase::class,
                false,
                null,
                '"Rules\PromiseValidatorTest" must be of type object, string given',
            ],
        ];
    }
}