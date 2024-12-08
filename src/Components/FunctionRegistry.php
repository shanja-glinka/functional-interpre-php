<?php

namespace ShanjaGlinka\Interpre\Components;

class FunctionRegistry
{
    /**
     * @var array Functions list
     */
    private static array $functions = [];

    /**
     * Registers a new function in the registry.
     * 
     * @param string $name
     * @param callable $function
     * 
     * @return void
     */
    public static function register(string $name, callable $function): void
    {
        self::$functions[$name] = $function;
    }

    /**
     * Calls a registered function by names.
     * 
     * @param string $name
     * @param array $args
     * 
     * @return mixed Function call results
     */
    public static function call(string $name, array $args): mixed
    {
        if (!isset(self::$functions[$name])) {
            ErrorHandler::throwError("Function '$name' not found");
        }
        return call_user_func_array(self::$functions[$name], $args);
    }
}
