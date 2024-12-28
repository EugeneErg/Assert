<?php

declare(strict_types = 1);

namespace Tests\Validators;

use EugeneErg\Assert\ValidationException;
use EugeneErg\Assert\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
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

        Validator::isA($valueB, $allowString)->assert($valueA);

        $this->assertTrue(true);
    }

    public static function getIsAAssertData(): array
    {
        return [
            [
                ValidatorTest::class,
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
                ValidatorTest::class,
                true,
                '"PHPUnit\Framework\TestCase" must be of type "Rules\ValidatorTest"',
            ],
            [
                ValidatorTest::class,
                TestCase::class,
                false,
                '"Rules\ValidatorTest" must be of type object, string given',
            ],
        ];
    }
}