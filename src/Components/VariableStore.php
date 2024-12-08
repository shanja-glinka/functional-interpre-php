<?php

namespace ShanjaGlinka\Interpre\Components;

class VariableStore
{
    private static array $variables = [];

    /**
     * Sets a variable in the store.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public static function set(string $name, mixed $value): void
    {
        self::$variables[$name] = $value;
    }

    /**
     * Retrieves a variable from the store.
     *
     * @param string $name
     *
     * @return mixed The
     *
     * @throws \Exception
     */
    public static function get(string $name): mixed
    {
        if (!array_key_exists($name, self::$variables)) {
            ErrorHandler::throwError("Undefined variable '$name'");
        }
        return self::$variables[$name];
    }
}
