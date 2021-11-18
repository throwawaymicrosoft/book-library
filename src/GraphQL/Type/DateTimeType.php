<?php

namespace App\GraphQL\Type;

use GraphQL\Language\AST\Node;

class DateTimeType
{
    /**
     * @return string
     */
    public static function serialize(\DateTimeInterface $value)
    {
        return $value->format('Y-m-d H:i:s');
    }

    /**
     * @param mixed $value
     *
     * @return \DateTimeInterface
     */
    public static function parseValue($value)
    {
        return new \DateTimeImmutable($value);
    }

    /**
     * @return \DateTimeInterface
     */
    public static function parseLiteral(Node $valueNode)
    {
        return new \DateTimeImmutable($valueNode->value);
    }
}
