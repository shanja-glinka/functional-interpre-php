<?php

namespace ShanjaGlinka\Interpre\Components;

class ErrorHandler
{
    /**
     * Throws an exception with an error message and optional code context.
     *
     * @param string $message
     * @param string $code
     * @param int $pos
     *
     * @return void
     */
    public static function throwError(string $message, string $code = "", int $pos = 0): void
    {
        if ($pos > 0 && $code) {
            $context = substr($code, max(0, $pos - 10), 20);
            $pointer = str_repeat(" ", $pos - max(0, $pos - 10)) . "^";
            throw new \Exception("Error: $message\nContext: $context\n$pointer");
        }

        throw new \Exception("Error: $message\nContext: No code context available.");
    }
}
