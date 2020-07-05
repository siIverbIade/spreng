<?php

declare(strict_types=1);

namespace Spreng\system\utils;

/**
 * TextUtils
 */
class TextUtils
{
    public static function countLines(string $input, int $strPos): int
    {
        return substr_count($input, PHP_EOL);
    }

    public static function rBlankLines(string $str): string
    {
        $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
        return $str;
    }

    public static function rDotLine(string $str): string
    {
        $str = str_replace("\n. ", "", $str);
        return $str;
    }
}
