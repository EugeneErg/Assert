<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class MatchesRegularExpressionRule extends AbstractRule
{
    public function __construct(public Argument|Promise $root, public Argument|Promise $pattern)
    {
        parent::__construct(new MultiRule(
            false,
            new TypeRule($root, 'string'),
            new IsRegularExpressionRule($pattern),
            new CallbackRule(function (Data $data, bool $not, ?string $message) {
                $root = $data->getArgument($this->root);
                $pattern = $data->getArgument($this->pattern);

                return (preg_match($pattern->value, $root->value) === 1) === $not
                    ? $message ?? ($root . ($not ? ' not' : '') . ' must match pattern' . $pattern)
                    : null;
            }),
        ));
    }
}