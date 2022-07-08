<?php

namespace App\Src\Enum;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait BaseEnum
{
    public function value(): int|string
    {
        return $this->value ?? $this->name;
    }

    /**
     * @throws HttpException
     */
    public static function get(string|int $value): static
    {
        foreach (self::cases() as $case) {
            if ($case->value() ===  $value) {
                return $case;
            }
        }

        throw new HttpException(
            Response::HTTP_NOT_FOUND,
            'Value ' . $value . ' not found in ' . self::class
        );
    }

    public static function getList(): array|static
    {
        return self::cases();
    }
}
