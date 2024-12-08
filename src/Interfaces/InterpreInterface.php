<?php

namespace ShanjaGlinka\Interpre\Interfaces;

interface InterpreInterface
{
    /**
     * Singleton instance.
     *
     * @return self
     */
    public static function getInstance(): self;

    /**
     * Sets the interpreter mode.
     *
     * @param \ShanjaGlinka\Interpre\InterpreterModeEnum $mode
     *
     * @return self
     */
    public function setMode(\ShanjaGlinka\Interpre\InterpreterModeEnum $mode): self;

    /**
     * Registers a component with a function callable.
     *
     * @param string $name
     * @param callable $factory
     * 
     * @return self
     */
    public function registerFunc(string $name, callable $function): self;

    /**
     * Executes code according to the current mode.
     *
     * @param mixed $arg The code or input to be executed.
     *
     * @return mixed The result of the execution.
     */
    public function execute(mixed $arg): mixed;
}
