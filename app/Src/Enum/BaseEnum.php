<?php

namespace App\Src\Enum;

use InvalidArgumentException;

trait BaseEnum
{
    public function value(): int|string
    {
        return $this->value ?? $this->name;
    }

    public static function get(string|int $value): static
    {
        foreach (self::cases() as $case) {
            if ($case->value() ===  $value) {
                return $case;
            }
        }

        throw new InvalidArgumentException(
            'Value ' . $value . ' not found in ' . self::class
        );
    }

    public static function getList(): array|static
    {
        return self::cases();
    }
}
