<?php

declare(strict_types = 1);

namespace Tests\Validators;

use EugeneErg\Assert\MultipleValidator;
use EugeneErg\Assert\ValidationException;
use PHPUnit\Framework\TestCase;

final class MultipleValidatorTest extends TestCase
{
    /**
     * @dataProvider getIsAAssertData
     */
    public function testIsAAssert(mixed $valueA, mixed $valueB, bool $allowString, ?string $expected): void
    {
        if ($expected !== null) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage($expected);
        }

        MultipleValidator::isA($valueA, $valueB, $allowString)->assert();

        $this->assertTrue(true);
    }

    public static function getIsAAssertData(): array
    {
        return [
            [
                MultipleValidatorTest::class,
                TestCase::class,
                true,
                null,
            ],
            [
                new self('name'),
                TestCase::class,
                true,
                null,
            ],
            [
                TestCase::class,
                MultipleValidatorTest::class,
                true,
                'value must be of type "Rules\MultipleValidatorTest"',
            ],
            [
                TestCase::class,
                new self('name'),
                true,
                'Rules\MultipleValidatorTest(...) must be of type string, object given',
            ],
            [
                MultipleValidatorTest::class,
                TestCase::class,
                false,
                'value must be of type object, string given',
            ],
        ];
    }
}