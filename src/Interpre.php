<?php

namespace ShanjaGlinka\Interpre;

use ShanjaGlinka\Interpre\Components\FunctionRegistry;
use ShanjaGlinka\Interpre\Components\Interpreter;
use ShanjaGlinka\Interpre\Components\Parser;
use ShanjaGlinka\Interpre\Components\StandardFunctions;
use ShanjaGlinka\Interpre\Interfaces\InterpreInterface;

final class Interpre implements InterpreInterface
{
    /**
     * @var Interpre|null Singleton instance of the Interpre class.
     */
    private static ?Interpre $instance = null;

    /**
     * @var array Registered components.
     */
    private array $components = [];

    /**
     * @var InterpreterModeEnum|null Current interpreter mode.
     */
    private ?InterpreterModeEnum $currentMode = null;

    /**
     * Private constructor to prevent external instantiation.
     */
    private function __construct()
    {
        $this->boot();
    }

    /**
     * Disabled cloning.
     */
    private function __clone() {}

    /**
     * Singleton instance.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Sets the interpreter mode.
     *
     * @param InterpreterModeEnum $mode
     *
     * @return self
     *
     * @throws \Exception 
     */
    public function setMode(InterpreterModeEnum $mode): self
    {
        if (!isset($this->components[$mode->value])) {
            throw new \Exception("Component '{$mode->value}' is not registered.");
        }

        $this->currentMode = $mode;
        return $this;
    }

    /**
     * Registers a component with a function callable.
     *
     * @param string $name
     * @param callable $factory
     * 
     * @return self
     */
    public function registerFunc(string $name, callable $function): self
    {
        FunctionRegistry::register($name, $function);
        return $this;
    }

    /**
     * Executes code according to the current mode.
     *
     * @param mixed $arg The code or input to be executed.
     *
     * @return mixed The result of the execution.
     *
     * @throws \Exception If no mode is set.
     */
    public function execute(mixed $arg): mixed
    {
        if ($this->currentMode === null) {
            throw new \Exception("Interpreter mode is not set.");
        }

        return match ($this->currentMode) {
            InterpreterModeEnum::TEXT_INTERPRETER_MODE => $this->executeTextInterpreter((string)$arg),
            InterpreterModeEnum::PARSER_MODE => $this->executeParse((string)$arg),
        };
    }

    /**
     * Registers a component for a given mode.
     *
     * @param string $name
     * @param callable $factory
     * 
     * @return void
     */
    private function register(string $name, callable $factory): void
    {
        $this->components[$name] = $factory;
    }

    /**
     * Retrieves the component for a given mode.
     *
     * @param InterpreterModeEnum $mode
     *
     * @return mixed Registered component.
     *
     * @throws \Exception
     */
    private function getComponent(InterpreterModeEnum $mode): mixed
    {
        $name = $mode->value;

        if (!isset($this->components[$name])) {
            throw new \Exception("Component '{$name}' is not registered.");
        }

        return ($this->components[$name])();
    }

    /**
     * Executes code in TEXT_INTERPRETER_MODE.
     *
     * @param string $arg
     *
     * @return mixed The result of interpretation.
     *
     * @throws \Exception
     */
    private function executeTextInterpreter(string $arg): mixed
    {
        if (!file_exists($arg)) {
            throw new \Exception("File '$arg' not found.");
        }

        $code = file_get_contents($arg);
        if (empty(trim($code))) {
            throw new \Exception("Input code is empty or invalid.");
        }

        return $this->executeParse($code);
    }

    /**
     * Parses code into an AST and executes it using the TEXT_INTERPRETER_MODE component.
     *
     * @param string $code
     *
     * @return mixed The execution result.
     */
    private function executeParse(string $code): mixed
    {
        $parser = new Parser($code);
        $ast = $parser->parse();

        $interpreter = $this->getComponent(InterpreterModeEnum::TEXT_INTERPRETER_MODE);
        return $interpreter->execute($ast);
    }

    /**
     * Boots the interpreter by registering standard functions and default components.
     *
     * @return void
     */
    private function boot(): void
    {
        // Register standard functions
        StandardFunctions::register();

        // Register the text interpreter component
        $this->register(InterpreterModeEnum::TEXT_INTERPRETER_MODE->value, fn() => new Interpreter());

        // Register the parser component
        $this->register(InterpreterModeEnum::PARSER_MODE->value, fn() => new Parser(''));
    }
}
