<?php

namespace ShanjaGlinka\Interpre\Components;

use ShanjaGlinka\Interpre\Components\ErrorHandler;

class LexicalHandler
{
    /**
     * @var int Current position in input string.
     */
    private int $pos = 0;
    /**
     * @var int The total length of input string.
     */
    private int $length;


    /**
     * @param string $input Input string to tokenize.
     */
    public function __construct(
        private string $input,
    ) {
        $this->length = strlen($input);
    }

    /**
     * Tokenizes the input string into an array of tokens.
     *
     * @return array The array of tokens.
     *
     * @throws \Exception
     */
    public function tokenize(): array
    {
        $tokens = [];

        while (!$this->eof()) {
            $this->skipWhitespaceAndComments();

            if ($this->eof()) {
                break;
            }

            $char = $this->peek();

            // Primitive tokens
            if ($char === '(') {
                $tokens[] = ['type' => 'LPAREN', 'value' => '('];
                $this->advance();
                continue;
            }
            if ($char === ')') {
                $tokens[] = ['type' => 'RPAREN', 'value' => ')'];
                $this->advance();
                continue;
            }
            if ($char === ',') {
                $tokens[] = ['type' => 'COMMA', 'value' => ','];
                $this->advance();
                continue;
            }
            if ($char === '"') {
                $tokens[] = ['type' => 'STRING', 'value' => $this->readString()];
                continue;
            }

            // numbers
            if (ctype_digit($char)) {
                $tokens[] = ['type' => 'NUMBER', 'value' => $this->readNumber()];
                continue;
            }

            // Identifiers, Keywords
            if (ctype_alpha($char) || $char === '_' || $char === '.') {
                $identifier = $this->readIdentifier();
                if (in_array($identifier, ['true', 'false', 'null'])) {
                    $tokens[] = ['type' => 'KEYWORD', 'value' => $identifier];
                } else {
                    $tokens[] = ['type' => 'IDENTIFIER', 'value' => $identifier];
                }
                continue;
            }

            ErrorHandler::throwError("Unexpected character '$char'", $this->input, $this->pos);
        }

        return $tokens;
    }

    /**
     * Skips whitespace and comments in the input string.
     *
     * @return void
     */
    private function skipWhitespaceAndComments(): void
    {
        while (!$this->eof()) {
            if (ctype_space($this->peek())) {
                $this->advance();
                continue;
            }

            // Processing comments: if we meet "//, "skip to the end of the line
            if ($this->peek() === '/' && $this->peekNext() === '/') {
                $this->advance(); // '/'
                $this->advance(); // '/'
                while (!$this->eof() && $this->peek() !== "\n" && $this->peek() !== "\r") {
                    $this->advance();
                }
                continue;
            }

            break;
        }
    }

    /**
     * Reads a string token, handling escape characters.
     *
     * @return string The parsed string value.
     *
     * @throws \Exception
     */
    private function readString(): string
    {
        $this->advance(); // Нужно пропустить начальную кавычку

        $value = '';

        while (!$this->eof()) {
            $char = $this->peek();
            // dd($char); 
            if ($char === '\\') {
                // Possible escape character
                $this->advance(); // пропускаем '\\'

                if ($this->eof()) {
                    // dd('here-3');
                    ErrorHandler::throwError("Unterminated string due to trailing backslash", $this->input, $this->pos);
                }
                $nextChar = $this->peek();
                // Handler some common escape sequences
                if ($nextChar === '"' || $nextChar === '\\') {
                    // dd('here-1', $char);
                    $value .= $nextChar;
                    $this->advance();
                }
                // Something wrong after '\\'
                else {
                    // dd('here-2', $char);
                    $value .= $nextChar;
                    // dd($value); // Need an Exception?

                    $this->advance();
                }
            } elseif ($char === '"') {
                // !!EOF
                $this->advance();
                return $value;
            } else {
                $value .= $char;
                $this->advance();
            }
        }

        ErrorHandler::throwError("Unterminated string", $this->input, $this->pos);
    }

    /**
     * Reads a number token.
     *
     * @return string
     */
    private function readNumber(): string
    {
        $start = $this->pos;
        while (!$this->eof() && (ctype_digit($this->peek()) || $this->peek() === '.')) {
            $this->advance();
        }
        return substr($this->input, $start, $this->pos - $start);
    }

    /**
     * Reads an identifier or keyword token.
     *
     * @return string
     */
    private function readIdentifier(): string
    {
        $start = $this->pos;
        while (!$this->eof() && (ctype_alnum($this->peek()) || $this->peek() === '.' || $this->peek() === '_')) {
            $this->advance();
        }
        return substr($this->input, $start, $this->pos - $start);
    }

    /**
     * Peeks at the current character without advancing the position.
     *
     * @return string
     */
    private function peek(): string
    {
        return $this->pos < $this->length ? $this->input[$this->pos] : '';
    }

    /**
     * Peeks at the next character without advancing the position.
     *
     * @return string
     */
    private function peekNext(): string
    {
        return $this->pos + 1 < $this->length ? $this->input[$this->pos + 1] : '';
    }

    /**
     * Advances the current position by one.
     *
     * @return void
     */
    private function advance(): void
    {
        $this->pos++;
    }

    /**
     * Checks if the end of the input string has been reached.
     *
     * @return bool
     */
    private function eof(): bool
    {
        return $this->pos >= $this->length;
    }
}
