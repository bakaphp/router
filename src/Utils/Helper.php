<?php

namespace Baka\Router\Utils;

class Helper {

    public static function trimSlahes(string $str): string
    {
        return trim($str,'/');
    }
}