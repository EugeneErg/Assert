<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

use EugeneErg\Assert\Rules\ARule;
use EugeneErg\Assert\Rules\BetweenRule;
use EugeneErg\Assert\Rules\CallableRule;
use EugeneErg\Assert\Rules\CallbackEach;
use EugeneErg\Assert\Rules\CallbackRule;
use EugeneErg\Assert\Rules\CountBetweenRule;
use EugeneErg\Assert\Rules\CountGreaterThanRule;
use EugeneErg\Assert\Rules\CountLessThanRule;
use EugeneErg\Assert\Rules\EmptyRule;
use EugeneErg\Assert\Rules\EqualRule;
use EugeneErg\Assert\Rules\IsRegularExpressionRule;
use EugeneErg\Assert\Rules\IterableRule;
use EugeneErg\Assert\Rules\LengthBetweenRule;
use EugeneErg\Assert\Rules\LengthGreaterThanRule;
use EugeneErg\Assert\Rules\LengthLessThanRule;
use EugeneErg\Assert\Rules\LessThanRule;
use EugeneErg\Assert\Rules\NumericRule;
use EugeneErg\Assert\Rules\MatchesRegularExpressionRule;
use EugeneErg\Assert\Rules\AbstractRule;
use EugeneErg\Assert\Rules\MultiRule;
use EugeneErg\Assert\Rules\EachRule;
use EugeneErg\Assert\Rules\NotRule;
use EugeneErg\Assert\Rules\SubclassOfRule;
use EugeneErg\Assert\Rules\TypeRule;

final readonly class PromiseValidator
{
    private function __construct(public AbstractRule $rule)
    {
    }

    public static function create(AbstractRule $rule): self
    {
        return new self($rule);
    }

    public static function each(Promise $value, self $validator, bool $all = true): self
    {
        return new self(new EachRule($value, $validator->rule, $all));
    }

    public static function callbackEach(mixed $value, callable $validator, bool $all = true): self
    {
        return new self(new CallbackEach(Argument::fromValue($value, 'value'), $validator, $all));
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

    public static function isIterable(mixed $value): self
    {
        return new self(new IterableRule(self::toArgument($value)));
    }

    public static function isArray(mixed $value): self
    {
        return new self(new TypeRule(self::toArgument($value), 'array'));
    }

    public static function isString(mixed $value): self
    {
        return new self(new TypeRule(self::toArgument($value), 'string'));
    }

    public static function isFloat(mixed $value): self
    {
        return new self(new TypeRule(self::toArgument($value), 'double'));
    }

    public static function isInteger(mixed $value): self
    {
        return new self(new TypeRule(self::toArgument($value), 'integer'));
    }

    public static function isNumeric(mixed $value): self
    {
        return new self(new NumericRule(self::toArgument($value)));
    }

    public static function isObject(mixed $value): self
    {
        return new self(new TypeRule(self::toArgument($value), 'object'));
    }

    public static function isA(mixed $value, mixed $class, bool $allowString = true): self
    {
        return new self(new ARule(self::toArgument($value), self::toArgument($class), $allowString));
    }

    public static function isSubclassOf(mixed $value, mixed $class, bool $allowString = true): self
    {
        return new self(new SubclassOfRule(self::toArgument($value), self::toArgument($class), $allowString));
    }

    public static function instanceOf(mixed $value, string|object $class): self
    {
        return new self(new TypeRule(self::toArgument($value), is_object($class) ? $class::class : $class));
    }

    public static function isCallable(mixed $value): self
    {
        return new self(new CallableRule(self::toArgument($value)));
    }

    /**
     * @param callable(bool $not): string $callback
     */
    public static function callback(callable $callback): self
    {
        return new self(new CallbackRule($callback));
    }

    public static function matchesRegularExpression(mixed $value, mixed $pattern): self
    {
        return new self(new MatchesRegularExpressionRule(
            self::toArgument($value, 'value'),
            self::toArgument($pattern, 'pattern'),
        ));
    }

    public static function isRegularExpression(mixed $value): self
    {
        return new self(new IsRegularExpressionRule(self::toArgument($value, 'value')));
    }

    public static function equal(mixed $valueA, mixed $valueB, bool $strict = true): self
    {
        return new self(new EqualRule(
            self::toArgument($valueA, 'valueA'),
            self::toArgument($valueB, 'valueB'),
            $strict,
        ));
    }

    public static function empty(mixed $value): self
    {
        return new self(new EmptyRule(self::toArgument($value, 'value')));
    }

    public static function greaterThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LessThanRule(
            root: self::toArgument($valueB),
            value: self::toArgument($valueA),
            equal: false,
        ));
    }

    public static function greaterThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LessThanRule(
            root: self::toArgument($valueB),
            value: self::toArgument($valueA),
            equal: true,
        ));
    }

    public static function lessThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LessThanRule(
            root: self::toArgument($valueA),
            value: self::toArgument($valueB),
            equal: false,
        ));
    }

    public static function lessThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LessThanRule(
            root: self::toArgument($valueA),
            value: self::toArgument($valueB),
            equal: true,
        ));
    }

    public static function between(mixed $value, mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new BetweenRule(
            root: self::toArgument($value),
            from: self::toArgument($from),
            to: self::toArgument($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public static function lengthGreaterThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthGreaterThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            false,
        ));
    }

    public static function lengthGreaterThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthGreaterThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            true,
        ));
    }

    public static function lengthLessThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthLessThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            false,
        ));
    }

    public static function lengthLessThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthLessThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            true,
        ));
    }

    public static function lengthBetween(mixed $value, mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new LengthBetweenRule(
            root: self::toArgument($value),
            from: self::toArgument($from),
            to: self::toArgument($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public static function countGreaterThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountGreaterThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            false,
        ));
    }

    public static function countGreaterThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountGreaterThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            true,
        ));
    }

    public static function countLessThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountLessThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            false,
        ));
    }

    public static function countLessThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountLessThanRule(
            self::toArgument($valueA),
            self::toArgument($valueB),
            true,
        ));
    }

    public static function countBetween(mixed $value, mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new CountBetweenRule(
            root: self::toArgument($value),
            from: self::toArgument($from),
            to: self::toArgument($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public function assert(mixed $data = null, bool $not = false, ?string $message = null): void
    {
        $result = $this->rule->validate(Data::create($data), $not);

        if ($result !== null) {
            throw new ValidationException($message ?? $result);
        }
    }

    public function validate(mixed $data = null, bool $not = false): bool
    {
        return $this->rule->validate(Data::create($data), $not) === null;
    }

    private static function toArgument(mixed $value, ?string $name = null): Argument|Promise
    {
        return $value instanceof Promise ? $value : Argument::fromValue($value, $name);
    }
}
