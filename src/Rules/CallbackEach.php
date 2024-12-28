<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Argument;
use EugeneErg\Assert\Data;

final readonly class CallbackEach extends AbstractRule
{
    /** @var callable */
    public mixed $validator;

    public function __construct(public Argument $root, callable $validator, public bool $any = true)
    {
        $this->validator = $validator;
        parent::__construct(new MultiRule(
            false,
            new IterableRule($root),
            new CallbackRule(function (Data $data, bool $not, ?string $message) use ($root, $validator, $any) {
                $validators = [];

                foreach ($root->value as $key => $item) {
                    $validators[] = $validator(Argument::fromValue($item, 'value[' . $key . ']'));
                }

                return (new MultiRule($any, ...$validators))->validate($data, $not, $message);
            })
        ));
    }
}