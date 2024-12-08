# Interpre

Interpreter of a Functional Language.

**Languages:** [Русский](README.ru.md) | [English](README.md)

## Requirements

- **Docker**: Ensure Docker is installed on your computer.

## Launch

A code testing and validation application is available at [http://localhost:8011](http://localhost:8011).

## Installation

Execute the following commands from the project root:

```sh
docker network create interpre_network
docker compose up -d
docker compose run php composer install
```

## Application Grammar

```
<program> ::= <expression>

<expression> ::= <function_call> | <constant>

<function_call> ::= <function_name> '(' <expression> ')' | <function_name> '(' <expression> (',' <function_parameters>) ')'

<function_name> ::= string

<function_parameters> ::= <expression> | <expression> ',' <function_parameters>

<constant> ::= 'true' | 'false' | 'null' | <string> | <number>

<string> ::= '""' | '"' [a-zA-Z_][a-zA-Z0-9_.] '"'

<number> ::= <integer> | <float>

<integer> ::= digit | digit <integer>

<float> ::= <integer> '.' <integer>

<comment> ::= "\\"
```

## Example Program

```
bk.action.string.JsonEncode(
  bk.action.map.Make(
    bk.action.array.Make("message"),
    bk.action.array.Make(
      bk.action.string.Concat(
        "Hello, ",
        bk.action.core.GetArg(0)
      )
    )
  )
)
```

## How It Works

1. **Lexical Analysis**:
   - `LexicalHandler` breaks the source code into a sequence of tokens.
   
2. **Parsing**:
   - `Parser` converts tokens into an Abstract Syntax Tree (AST) according to the grammatical rules.
   
3. **Interpretation**:
   - `Interpreter` traverses the AST and performs corresponding actions using registered functions in `FunctionRegistry`.

### Parsing Steps

1. **Tokenization:**

  `LexicalHandler` breaks the code into tokens:

```php
  [  
      ['type' => 'IDENTIFIER', 'value' => 'bk.action.string.JsonEncode'],
      ['type' => 'LPAREN', 'value' => '('],
      ['type' => 'IDENTIFIER', 'value' => 'bk.action.map.Make'],
      ['type' => 'LPAREN', 'value' => '('],
      ['type' => 'IDENTIFIER', 'value' => 'bk.action.array.Make'],
      ['type' => 'LPAREN', 'value' => '('],
      ['type' => 'STRING', 'value' => 'message'],
      ['type' => 'RPAREN', 'value' => ')'],
      ['type' => 'COMMA', 'value' => ','],
      ['type' => 'IDENTIFIER', 'value' => 'bk.action.array.Make'],
      ['type' => 'LPAREN', 'value' => '('],
      ['type' => 'IDENTIFIER', 'value' => 'bk.action.string.Concat'],
      ['type' => 'LPAREN', 'value' => '('],
      ['type' => 'STRING', 'value' => 'Hello, '],
      ['type' => 'COMMA', 'value' => ','],
      ['type' => 'IDENTIFIER', 'value' => 'bk.action.core.GetArg'],
      ['type' => 'LPAREN', 'value' => '('],
      ['type' => 'NUMBER', 'value' => '0'],
      ['type' => 'RPAREN', 'value' => ')'],
      ['type' => 'RPAREN', 'value' => ')'],
      ['type' => 'RPAREN', 'value' => ')'],
      ['type' => 'RPAREN', 'value' => ')'],
  ]
```
    
2. **Parsing:**

  `Parser` builds an AST (Abstract Syntax Tree):

```
  ASTNode('program', null, [
    ASTNode('function', 'bk.action.string.JsonEncode', [
      ASTNode('function', 'bk.action.map.Make', [
        ASTNode('function', 'bk.action.array.Make', ['message']),
        ASTNode('function', 'bk.action.array.Make', [
          ASTNode('function', 'bk.action.string.Concat', [
            'Hello, ',
            ASTNode('function', 'bk.action.core.GetArg', [0])
          ])
        ])
      ])
    ])
  ])
```
    
3. **Execution:**

  `Interpreter` traverses the AST and executes functions as defined in `FunctionRegistry`.
