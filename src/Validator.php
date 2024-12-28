<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

use EugeneErg\Assert\Rules\AbstractRule;
use EugeneErg\Assert\Rules\ARule;
use EugeneErg\Assert\Rules\BetweenRule;
use EugeneErg\Assert\Rules\CallableRule;
use EugeneErg\Assert\Rules\CallbackRule;
use EugeneErg\Assert\Rules\CountBetweenRule;
use EugeneErg\Assert\Rules\CountGreaterThanRule;
use EugeneErg\Assert\Rules\CountLessThanRule;
use EugeneErg\Assert\Rules\EachRule;
use EugeneErg\Assert\Rules\EmptyRule;
use EugeneErg\Assert\Rules\EqualRule;
use EugeneErg\Assert\Rules\GreaterThanRule;
use EugeneErg\Assert\Rules\IsRegularExpressionRule;
use EugeneErg\Assert\Rules\LengthBetweenRule;
use EugeneErg\Assert\Rules\LengthGreaterThanRule;
use EugeneErg\Assert\Rules\LengthLessThanRule;
use EugeneErg\Assert\Rules\LessThanRule;
use EugeneErg\Assert\Rules\MatchesRegularExpressionRule;
use EugeneErg\Assert\Rules\MultiRule;
use EugeneErg\Assert\Rules\NotRule;
use EugeneErg\Assert\Rules\NumericRule;
use EugeneErg\Assert\Rules\SubclassOfRule;
use EugeneErg\Assert\Rules\TypeRule;

final readonly class Validator
{
    private function __construct(private AbstractRule $rule)
    {
    }

    public static function create(AbstractRule $rule): self
    {
        return new self($rule);
    }

    public static function each(self $validator, bool $any = true): self
    {
        return new self(new EachRule(self::root(), $validator->rule, $any));
    }

    public static function all(self ...$validators): self
    {
        return new self(new MultiRule(false, ...array_map(static fn (self $validator) => $validator->rule, $validators)));
    }

    public static function any(self ...$validators): self
    {
        return new self(new MultiRule(true, ...array_map(static fn (self $validator) => $validator->rule, $validators)));
    }

    public static function not(self $validator): self
    {
        return new self(new NotRule($validator->rule));
    }

    public static function isIterable(): self
    {
        return new self(new TypeRule(self::root(), 'iterable'));
    }

    public static function isArray(): self
    {
        return new self(new TypeRule(self::root(), 'array'));
    }

    public static function isString(): self
    {
        return new self(new TypeRule(self::root(), 'string'));
    }

    public static function isFloat(): self
    {
        return new self(new TypeRule(self::root(), 'double'));
    }

    public static function isInteger(): self
    {
        return new self(new TypeRule(self::root(), 'integer'));
    }

    public static function isNumeric(): self
    {
        return new self(new NumericRule(self::root()));
    }

    public static function isObject(): self
    {
        return new self(new TypeRule(self::root(), 'object'));
    }

    public static function isA(string $class, bool $allowString = true): self
    {
        return new self(new ARule(self::root(), Argument::fromValue($class), $allowString));
    }

    public static function isSubclassOf(string $class, bool $allowString = true): self
    {
        return new self(new SubclassOfRule(self::root(), Argument::fromValue($class), $allowString));
    }

    public static function instanceOf(string|object $class): self
    {
        return new self(new TypeRule(self::root(), is_string($class) ? $class : $class::class));
    }

    public static function isCallable(): self
    {
        return new self(new CallableRule(self::root()));
    }

    /**
     * @param callable(bool $not): string $callback
     */
    public static function callback(callable $callback): self
    {
        return new self(new CallbackRule($callback));
    }

    public static function matchesRegularExpression(mixed $pattern): self
    {
        return new self(new MatchesRegularExpressionRule(self::root(), Argument::fromValue($pattern, 'pattern')));
    }

    public static function isRegularExpression(): self
    {
        return new self(new IsRegularExpressionRule(self::root()));
    }

    public static function equal(mixed $value, bool $strict = true): self
    {
        return new self(new EqualRule(self::root(), Argument::fromValue($value, 'value'), $strict));
    }

    public static function empty(): self
    {
        return new self(new EmptyRule(self::root()));
    }

    public static function greaterThan(mixed $value): self
    {
        return new self(new GreaterThanRule(
            self::root(),
            Argument::fromValue($value),
            false,
        ));
    }

    public static function greaterThanOrEqual(mixed $value): self
    {
        return new self(new GreaterThanRule(
            self::root(),
            Argument::fromValue($value),
            true,
        ));
    }

    public static function lessThan(mixed $value): self
    {
        return new self(new LessThanRule(
            self::root(),
            Argument::fromValue($value),
            false,
        ));
    }

    public static function lessThanOrEqual(mixed $value): self
    {
        return new self(new LessThanRule(
            self::root(),
            Argument::fromValue($value),
            true,
        ));
    }

    public static function between(mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new BetweenRule(
            root: self::root(),
            from: Argument::fromValue($from),
            to: Argument::fromValue($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public static function lengthGreaterThan(mixed $value): self
    {
        return new self(new LengthGreaterThanRule(
            self::root(),
            Argument::fromValue($value),
            false,
        ));
    }

    public static function lengthGreaterThanOrEqual(mixed $value): self
    {
        return new self(new LengthGreaterThanRule(
            self::root(),
            Argument::fromValue($value),
            true,
        ));
    }

    public static function lengthLessThan(mixed $value): self
    {
        return new self(new LengthLessThanRule(
            self::root(),
            Argument::fromValue($value),
            false,
        ));
    }

    public static function lengthLessThanOrEqual(mixed $value): self
    {
        return new self(new LengthLessThanRule(
            self::root(),
            Argument::fromValue($value),
            true,
        ));
    }

    public static function lengthBetween(mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new LengthBetweenRule(
            root: self::root(),
            from: Argument::fromValue($from),
            to: Argument::fromValue($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public static function countGreaterThan(mixed $value): self
    {
        return new self(new CountGreaterThanRule(
            self::root(),
            Argument::fromValue($value),
            false,
        ));
    }

    public static function countGreaterThanOrEqual(mixed $value): self
    {
        return new self(new CountGreaterThanRule(
            self::root(),
            Argument::fromValue($value),
            true,
        ));
    }

    public static function countLessThan(mixed $value): self
    {
        return new self(new CountLessThanRule(
            self::root(),
            Argument::fromValue($value),
            false,
        ));
    }

    public static function countLessThanOrEqual(mixed $value): self
    {
        return new self(new CountLessThanRule(
            self::root(),
            Argument::fromValue($value),
            true,
        ));
    }

    public static function countBetween(mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new CountBetweenRule(
            root: self::root(),
            from: Argument::fromValue($from),
            to: Argument::fromValue($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public function assert(mixed $value, bool $not = false, ?string $message = null): void
    {
        $result = $this->rule->validate(Data::create($value), $not);

        if ($result !== null) {
            throw new ValidationException($message ?? $result);
        }
    }

    public function validate(mixed $value, bool $not = false): bool
    {
        return $this->rule->validate(Data::create($value), $not) === null;
    }

    private static function root(): Promise
    {
        return new Promise();
    }
}
