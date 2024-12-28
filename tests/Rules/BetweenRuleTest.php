<?php

declare(strict_types = 1);

namespace Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;
use EugeneErg\Assert\Rules\BetweenRule;
use PHPUnit\Framework\TestCase;

final class BetweenRuleTest extends TestCase
{
    public function testValidateSuccessNull()
    {
        $actual = (new BetweenRule(new Promise(), new Argument(TestCase::class), true))
            ->validate(Data::create(ARuleTest::class), false);

        $this->assertEquals(null, $actual);
    }
}