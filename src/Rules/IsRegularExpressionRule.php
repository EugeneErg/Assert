<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class IsRegularExpressionRule extends AbstractRule
{
    public function __construct(public Argument|Promise $root)
    {
        parent::__construct(new MultiRule(
            false,
            new TypeRule($root, 'string'),
            new CallbackRule(
                function (Data $data, bool $not, ?string $message) {
                    $root = $data->getArgument($data);
                    $regexpMessage = null;
                    $oldErrorHandler = set_error_handler(
                        function (int $level, string $message) use (&$regexpMessage) {
                            if ($level === E_WARNING && str_contains($message, 'preg_match')) {
                                $regexpMessage = $message;

                                return true;
                            }

                            return false;
                        },
                    );

                    try {
                        $result = preg_match($root->value, '');
                    } finally {
                        set_error_handler($oldErrorHandler);
                    }

                    return ($result === false) === $not
                        ? $message ?? ($root . 'is ' . ($not ? ' not' : '') . ' invalid' . ($regexpMessage === null ? '' : ': ' . $regexpMessage))
                        : null;
                },
            ),
        ));
    }
}