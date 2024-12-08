<?php

namespace ShanjaGlinka\Interpre\Components;

use ShanjaGlinka\Interpre\Components\ErrorHandler;

class Parser
{
    /**
     * @var array List of tokens to parse.
     */
    private array $tokens;

    /**
     * @var int Current position in the tokens array.
     */
    private int $pos = 0;

    /**
     * @param string $code The input code to parse.
     */
    public function __construct(string $code)
    {
        $this->initLexicalHandler($code);
    }

    /**
     * Parses the tokens into an AST.
     *
     * @param string|null $code
     *
     * @return ASTNode 
     *
     * @throws \Exception
     */
    public function parse(?string $code = null): ASTNode
    {
        if ($code) {
            $this->initLexicalHandler($code);
        }

        $expressions = [];

        while ($this->hasMore()) {
            $expressions[] = $this->parseExpression();
        }

        // Return ASTNode of type 'program', in children all expressions. 
        // Need to set the starting point of the program
        return new ASTNode('program', null, $expressions);
    }

    /**
     * Initializes the lexical handler (tokenizer) with the given code.
     *
     * @param string $code
     *
     * @return self
     */
    private function initLexicalHandler(string $code): self
    {
        $lexer = new LexicalHandler($code);
        $this->tokens = $lexer->tokenize();
        return $this;
    }

    /**
     * Parses a single expression and returns its AST node.
     *
     * @return ASTNode
     *
     * @throws \Exception
     */
    private function parseExpression(): ASTNode
    {
        // If it is IDENTIFIER + '(', then a function call
        if ($this->check('IDENTIFIER')) {
            $identifierToken = $this->currentToken();
            $nextToken = $this->nextToken();

            if ($nextToken && $nextToken['type'] === 'LPAREN') {
                // Function call
                $this->consume('IDENTIFIER');
                $this->consume('LPAREN');
                $funcName = $identifierToken['value'];

                $params = [];
                // If the next token is not RPAREN, then there are parameters
                if (!$this->check('RPAREN')) {
                    $params[] = $this->parseExpression();

                    while ($this->check('COMMA')) {
                        $this->consume('COMMA');
                        $params[] = $this->parseExpression();
                    }
                }

                $this->consume('RPAREN');
                return new ASTNode('function', $funcName, $params);
            } else {
                ErrorHandler::throwError("Unexpected identifier without '()': " . $identifierToken['value']);
            }
        }


        // Constants


        if ($this->check('STRING')) {
            $token = $this->consume('STRING');
            return new ASTNode('string', $token['value']);
        }

        if ($this->check('NUMBER')) {
            $token = $this->consume('NUMBER');
            $value = strpos($token['value'], '.') !== false ? (float)$token['value'] : (int)$token['value'];
            return new ASTNode('number', $value);
        }


        if ($this->check('KEYWORD')) {
            $token = $this->consume('KEYWORD');
            switch ($token['value']) {
                case 'true':
                    return new ASTNode('keyword', true);
                case 'false':
                    return new ASTNode('keyword', false);
                case 'null':
                    return new ASTNode('keyword', null);
            }
        }

        ErrorHandler::throwError("Unexpected token: " . ($this->currentToken()['value'] ?? 'EOF'));
    }

    /**
     * Checks if the current token matches the expected type.
     *
     * @param string $type
     *
     * @return bool
     */
    private function check(string $type): bool
    {
        $token = $this->currentToken();
        return $token && $token['type'] === $type;
    }

    /**
     * Consumes the current token if it matches the expected type.
     *
     * @param string $type
     *
     * @return array
     *
     * @throws \Exception 
     */
    private function consume(string $type): array
    {
        $token = $this->currentToken();
        if (!$token || $token['type'] !== $type) {
            ErrorHandler::throwError("Expected token $type, got " . ($token['type'] ?? 'EOF'));
        }
        $this->pos++;
        return $token;
    }

    /**
     * Determines if there are more tokens to parse.
     *
     * @return bool
     */
    private function hasMore(): bool
    {
        return $this->pos < count($this->tokens);
    }

    /**
     * Retrieves the current token without advancing the position.
     *
     * @return array|null
     */
    private function currentToken(): ?array
    {
        return $this->tokens[$this->pos] ?? null;
    }

    /**
     * Retrieves the next token without advancing the position.
     *
     * @return array|null
     */
    private function nextToken(): ?array
    {
        return $this->tokens[$this->pos + 1] ?? null;
    }
}
