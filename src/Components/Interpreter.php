<?php

namespace ShanjaGlinka\Interpre\Components;

use ShanjaGlinka\Interpre\Components\ASTNode;
use ShanjaGlinka\Interpre\Components\FunctionRegistry;
use ShanjaGlinka\Interpre\Components\ErrorHandler;

class Interpreter
{
    /**
     * Executes the given AST node.
     *
     * @param ASTNode $node AST node to execute.
     *
     * @return mixed The result of the execution.
     *
     * @throws \Exception
     */
    public function execute(ASTNode $node): mixed
    {
        return $this->evalNode($node);
    }

    /**
     * Recursively evaluates an AST node.
     *
     * @param ASTNode $node
     *
     * @return mixed The result of the evaluation.
     *
     * @throws \Exception 
     */
    private function evalNode(ASTNode $node): mixed
    {
        switch ($node->type) {
            case 'program':
                // Execute all expressions in sequence, the result of the last is the final result
                $result = null;
                foreach ($node->children as $child) {
                    $result = $this->evalNode($child);
                }
                return $result;

            case 'function':
                // Evaluate all child nodes as arguments
                $args = [];
                foreach ($node->children as $child) {
                    $args[] = $this->evalNode($child);
                }
                return FunctionRegistry::call($node->value, $args);

            case 'string':
            case 'number':
            case 'keyword':
                // Return the literal value
                return $node->value;

            default:
                // Throw an error for unknown node types
                ErrorHandler::throwError("Unknown AST node type '{$node->type}'");
        }
    }
}
