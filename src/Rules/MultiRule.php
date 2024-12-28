<?php

declare(strict_types = 1);

namespace EugeneErg\Assert\Rules;

use EugeneErg\Assert\Data;

final readonly class MultiRule extends AbstractRule
{
    /** @var AbstractRule[] */
    public array $rules;

    public function __construct(public bool $any, AbstractRule ...$rules)
    {
        $this->rules = $rules;
        parent::__construct(new CallbackRule(function (Data $data, bool $not, ?string $message) {
            if ($not === $this->any) {
                foreach ($this->rules as $item) {
                    $result = $item->validate($data, $not, $message);

                    if ($result !== null) {
                        return $result;
                    }
                }

                return null;
            }

            foreach ($this->rules as $item) {
                $result = $item->validate($data, $not, $message);

                if ($result === null) {
                    return null;
                }
            }

            return $message ?? 'Any rule must be followed.';
        }));
    }
}