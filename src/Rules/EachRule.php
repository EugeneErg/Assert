<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Data;
use EugeneErg\Assert\Promise;

final readonly class EachRule extends AbstractRule
{
    public function __construct(public Promise $root, public AbstractRule $rule, public bool $any = true)
    {
        parent::__construct(new MultiRule(
            false,
            new IterableRule($root),
            new CallbackRule(function (Data $data, bool $not, ?string $message) {
                $children = $data->getByPromise($this->root)->getChildren();
                $rules = [];

                foreach ($children as $child) {
                    $rules[] = new CallbackRule(function (Data $data, bool $not, ?string $message) use ($child) {
                        return $this->rule->validate($child, $not, $message);
                    });
                }

                return (new MultiRule($this->any, ...$rules))->validate($data, $not, $message);
            }),
        ));
    }
}