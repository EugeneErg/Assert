<?php

declare(strict_types = 1);

namespace Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;
use EugeneErg\Assert\Rules\ARule;
use EugeneErg\Assert\ValidationException;
use PHPUnit\Framework\TestCase;

final class ARuleTest extends TestCase
{
    public function testValidateSuccessNull(): void
    {
        $actual = (new ARule(new Promise(), new Argument(TestCase::class), true))
            ->validate(Data::create(ARuleTest::class), false);

        $this->assertEquals(null, $actual);
    }

    public function testValidateSuccessException(): void
    {
        $actual = (new ARule(new Promise(), new Argument(ARuleTest::class), true))
            ->validate(Data::create(TestCase::class), false);

        $this->assertEquals(
            new ValidationException('"PHPUnit\Framework\TestCase" must be of type "Rules\ARuleTest"'),
            $actual,
        );
    }
}