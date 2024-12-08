# Interpre

Интерпретатор функционального языка.

**Языки:** [Русский](README.ru.md) | [English](README.md)

## Требования

- **Docker**: Убедитесь, что Docker установлен на вашем компьютере.

## Установка

Выполните следующие команды из корня проекта:

```sh
docker network create interpre_network
docker compose up -d
docker compose run php composer install
```

## Запуск

Приложение для тестирования и проверки кода доступно по адресу [http://localhost:8011](http://localhost:8011).


## Грамматика приложения

```
<программа> ::= <выражение>

<выражение> ::= <вызов_функции> | <константа>

<вызов_функции> ::= <имя_функции> '(' <выражение> ')' | <имя_функции> '(' <выражение> (',' <параметры_функции>) ')'

<имя_функции> ::= строка

<параметры_функции> ::= <выражение> | <выражение> ',' <параметры_функции>

<константа> ::= 'true' | 'false' | 'null' | <строка> | <число>

<строка> ::= '""' | '"' [a-zA-Z_][a-zA-Z0-9_.] '"'

<число> ::= <целое_число> | <вещественное_число>

<целое_число> ::= цифра | цифра <целое_число>

<вещественное_число> ::= <целое_число> '.' <целое_число>

<комментарий> ::= "\\"
```


## Пример программы

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

## Как это работает

1. **Лексический анализ**:
   - `LexicalHandler` разбивает исходный код на последовательность токенов.
   
2. **Синтаксический анализ**:
   - `Parser` преобразует токены в абстрактное синтаксическое дерево (AST) согласно грамматическим правилам.
   
3. **Интерпретация**:
   - `Interpreter` обходит AST и выполняет соответствующие действия, используя зарегистрированные функции в `FunctionRegistry`.



### Шаги парсинга

1. **Токенизация:**

  `LexicalHandler` разбивает код на токены:

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

2. **Парсинг:**

  `Parser` строит AST (Абстрактное Синтаксическое Дерево):

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

3. **Выполнение:**

  `Interpreter` обходит AST и выполняет функции согласно их определению в `FunctionRegistry`.
