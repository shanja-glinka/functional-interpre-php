<?php

namespace ShanjaGlinka\Interpre\Components;

use ShanjaGlinka\Interpre\Components\ErrorHandler;

class StandardFunctions
{
    /**
     * Registers a set of standard functions into the FunctionRegistry.
     *
     * @return void
     *
     * @throws \Exception
     */
    public static function register(): void
    {
        // Предполагается что args передан глобально
        global $args;
        if (!isset($args)) {
            $args = [];
        }

        FunctionRegistry::register('bk.action.core.GetArg', function ($argIndex) use (&$args) {
            return $args[$argIndex] ?? null;
        });

        FunctionRegistry::register('bk.action.array.Make', function (...$elements) {
            return $elements;
        });

        FunctionRegistry::register('bk.action.map.Make', function ($keys, $values) {
            return array_combine($keys, $values);
        });

        FunctionRegistry::register('bk.action.string.JsonEncode', function ($value) {
            return json_encode($value);
        });

        FunctionRegistry::register('bk.action.string.Concat', function ($str1, $str2) {
            return $str1 . $str2;
        });

        FunctionRegistry::register('bk.action.core.SetVar', function ($varName, $value) {
            VariableStore::set($varName, $value);
            return $value;
        });

        FunctionRegistry::register('bk.action.core.GetVar', function ($varName) {
            return VariableStore::get($varName);
        });

        // Additional functions
        FunctionRegistry::register('math.add', fn($a, $b) => $a + $b);
        FunctionRegistry::register('math.sub', fn($a, $b) => $a - $b);
        FunctionRegistry::register('math.mul', fn($a, $b) => $a * $b);
        FunctionRegistry::register('math.div', fn($a, $b) => $b != 0 ? $a / $b : ErrorHandler::throwError('Division by zero'));
    }
}
