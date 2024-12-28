<?php

declare(strict_types = 1);

namespace EugeneErg\Assert;

use EugeneErg\Assert\Rules\AbstractRule;
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
use EugeneErg\Assert\Rules\GreaterThanRule;
use EugeneErg\Assert\Rules\IsRegularExpressionRule;
use EugeneErg\Assert\Rules\IterableRule;
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

final readonly class MultipleValidator
{
    private function __construct(public AbstractRule $rule)
    {
    }

    public static function create(AbstractRule $rule): self
    {
        return new self($rule);
    }

    /**
     * @param callable(Argument $root): self $validator
     */
    public static function each(mixed $value, callable $validator, bool $any = false): self
    {
        return new self(new CallbackEach(Argument::fromValue($value, 'value'), $validator, $any));
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

    public static function type(mixed $value, string $type): self
    {
        return new self(new TypeRule(Argument::fromValue($value), $type));
    }

    public static function isIterable(mixed $value): self
    {
        return new self(new IterableRule(Argument::fromValue($value, 'value')));
    }

    public static function isArray(mixed $value): self
    {
        return new self(new TypeRule(Argument::fromValue($value, 'value'), 'array'));
    }

    public static function isString(mixed $value): self
    {
        return new self(new TypeRule(Argument::fromValue($value, 'value'), 'string'));
    }

    public static function isFloat(mixed $value): self
    {
        return new self(new TypeRule(Argument::fromValue($value, 'value'), 'double'));
    }

    public static function isInteger(mixed $value): self
    {
        return new self(new TypeRule(Argument::fromValue($value, 'value'), 'integer'));
    }

    public static function isNumeric(mixed $value): self
    {
        return new self(new NumericRule(Argument::fromValue($value, 'value')));
    }

    public static function isObject(mixed $value): self
    {
        return new self(new TypeRule(Argument::fromValue($value, 'value'), 'object'));
    }

    public static function isA(mixed $value, mixed $class, bool $allowString = true): self
    {
        return new self(new ARule(Argument::fromValue($value, 'value'), Argument::fromValue($class), $allowString));
    }

    public static function isSubclassOf(mixed $value, mixed $class, bool $allowString = true): self
    {
        return new self(new SubclassOfRule(Argument::fromValue($value, 'value'), Argument::fromValue($class), $allowString));
    }

    public static function instanceOf(mixed $value, object|string $class): self
    {
        return new self(new TypeRule(Argument::fromValue($value, 'value'), is_object($class) ? $class::class : $class));
    }

    public static function isCallable(mixed $value): self
    {
        return new self(new CallableRule(Argument::fromValue($value, 'value')));
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
            Argument::fromValue($value, 'value'),
            Argument::fromValue($pattern),
        ));
    }

    public static function isRegularExpression(mixed $value): self
    {
        return new self(new IsRegularExpressionRule(Argument::fromValue($value, 'value')));
    }

    public static function equal(mixed $valueA, mixed $valueB, bool $strict): self
    {
        return new self(new EqualRule(Argument::fromValue($valueA, 'value'), Argument::fromValue($valueB), $strict));
    }

    public static function empty(mixed $value): self
    {
        return new self(new EmptyRule(Argument::fromValue($value, 'value')));
    }

    public static function greaterThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new GreaterThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            false,
        ));
    }

    public static function greaterThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new GreaterThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            true,
        ));
    }

    public static function lessThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LessThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            false,
        ));
    }

    public static function lessThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LessThanRule(
            Argument::fromValue($valueA, 'actual'),
            Argument::fromValue($valueB),
            true,
        ));
    }

    public static function between(mixed $value, mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new BetweenRule(
            root: Argument::fromValue($value),
            from: Argument::fromValue($from),
            to: Argument::fromValue($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public static function lengthGreaterThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthGreaterThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            false,
        ));
    }

    public static function lengthGreaterThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthGreaterThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            true,
        ));
    }

    public static function lengthLessThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthLessThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            false,
        ));
    }

    public static function lengthLessThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new LengthLessThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            true,
        ));
    }

    public static function lengthBetween(mixed $value, mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new LengthBetweenRule(
            root: Argument::fromValue($value),
            from: Argument::fromValue($from),
            to: Argument::fromValue($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    public static function countGreaterThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountGreaterThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            false,
        ));
    }

    public static function countGreaterThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountGreaterThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            true,
        ));
    }

    public static function countLessThan(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountLessThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            false,
        ));
    }

    public static function countLessThanOrEqual(mixed $valueA, mixed $valueB): self
    {
        return new self(new CountLessThanRule(
            Argument::fromValue($valueA, 'value'),
            Argument::fromValue($valueB),
            true,
        ));
    }

    public static function countBetween(mixed $value, mixed $from, mixed $to, bool $fromEqual = true, bool $toEqual = true): self
    {
        return new self(new CountBetweenRule(
            root: Argument::fromValue($value),
            from: Argument::fromValue($from),
            to: Argument::fromValue($to),
            fromEqual: $fromEqual,
            toEqual: $toEqual,
        ));
    }

    /**
     * @throws ValidationException
     */
    public function assert(bool $not = false, ?string $message = null): void
    {
        $result = $this->rule->validate(Data::create(null), $not);

        if ($result !== null) {
            throw new ValidationException($message ?? $result);
        }
    }

    public function validate(bool $not = false): bool
    {
        return $this->rule->validate(Data::create(null), $not) === null;
    }
}
