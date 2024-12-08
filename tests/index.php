<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/larapack/dd/src/helper.php';

use ShanjaGlinka\Interpre\Components\FunctionRegistry;
use ShanjaGlinka\Interpre\Components\Interpreter;
use ShanjaGlinka\Interpre\Components\Parser;
use ShanjaGlinka\Interpre\Components\StandardFunctions;
use ShanjaGlinka\Interpre\Interpre;
use ShanjaGlinka\Interpre\InterpreterModeEnum;


global $args, $filesExecutePath;
$args = ['ZERO_INDEX_ARG', 'SECOND_INDEX_ARG'];
$filesExecutePath = 'tests/execute';

// User functions
Interpre::getInstance()
    ->registerFunc('my.custom.func', function ($arg1, $arg2) {
        return $arg1 . 'my.custom.func' . $arg2;
    })
    ->registerFunc('print', function (...$args) {
        array_walk($args, fn($arg) => print($arg));
    });


// echo '<pre>';

$fileTests = [
    'execute.me.constants.txt' => 'Константы',
    'execute.me.hard.txt' => 'Проверка Чего-то невозможного',
    'execute.me.concat.txt' => 'Конкатенация строк',
    'execute.me.json.txt' => 'Отработка JSON',
    'execute.me.math.sum.txt' => 'Отработка математической функции сложения',
    'execute.me.vars.txt' => 'Вызов функций присвоения',
    'execute.my-custom.txt' => 'Вызов кастомной функции',
    'execute.me.assignments.txt' => 'Пример интерпретации последовательного набор выражений:'
];

$parserTests = [];
foreach ($fileTests as $file => $header) {
    $filePath = __DIR__ . '/../' . "/{$filesExecutePath}/{$file}";
    if (!file_exists($filePath)) {
        continue;
    }

    $parserTests[$header] = [
        'file' => $filePath,
        'code' => file_get_contents($filePath)
    ];
}



function callTest(string $header, ?string $file, mixed $code = null)
{
    global $filesExecutePath;

    echo "<hr>";
    if ($file) {
        echo "<h2>Файл: <code>{$file}</code></h2>";
    }
    echo "<h3><strong>Тест:</strong> {$header}</h3>";
    echo "<h3>Код:</h3>";
    echo "<pre style='background:#f4f4f4;padding:10px;border:1px solid #ccc;'>";

    if ($file) {
        $filePath = __DIR__ . '/../' . "/{$filesExecutePath}/{$file}";
        if (!file_exists($filePath)) {
            echo "Файл не найден: $filePath";
            echo "</pre>";
            return;
        }
    }

    if (!$code) {
        $code = file_get_contents($filePath);
    }
    echo htmlspecialchars($code);
    echo "</pre>";

    echo "<h3>Результат:</h3>";
    try {
        $result = Interpre::getInstance()->execute($file !== null ? "{$filesExecutePath}/{$file}" : $code);
        echo "<pre style='background:#e5f7e5;padding:10px;border:1px solid #ccc;'>";
        echo htmlspecialchars(is_array($result) ? json_encode($result) : (string) $result);
    } catch (Exception $e) {
        echo "<pre style='background:#f7e5e5;padding:10px;border:1px solid #ccc;'>";
        echo "Ошибка выполнения: " . htmlspecialchars($e->getMessage());
    }
    echo "</pre>";
}

echo "<html><body><h1>Результаты тестирования</h1>";

if (isset($_GET['execute'])) {

    require_once __DIR__ . '/parser-interpreter.php';

    callTest('Пользовательский ввод', null, $_GET['execute']);
} else if ($_GET['mode'] == InterpreterModeEnum::TEXT_INTERPRETER_MODE->value) {
    require_once __DIR__ . '/files-interpreter.php';

    foreach ($fileTests as $file => $header) {
        callTest($header, $file);
    }
} else if ($_GET['mode'] == InterpreterModeEnum::PARSER_MODE->value) {
    require_once __DIR__ . '/parser-interpreter.php';

    foreach ($parserTests as $header => $data) {
        callTest($header, null, $data['code']);
    }
} else {
    echo 'hi';
}

echo "</body></html>";
