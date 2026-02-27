<?php
namespace ZerolLka\Calculator\Controller;

use function ZerolLka\Calculator\View\showMessage;
use function cli\prompt;

function startGame(): void {
    showMessage("Добро пожаловать в игру 'Калькулятор'!");
    
    $expression = generateExpression();
    showMessage("Вычислите выражение: $expression");

    $answer = (int) prompt("Ваш ответ: ");

    $correct = eval("return $expression;");

    if ($answer === (int)$correct) {
        showMessage("Правильно!");
    } else {
        showMessage("Неправильно! Правильный ответ: $correct");
    }
}

// генерация случайного выражения с 4 числами и +, -, *
function generateExpression(): string {
    $ops = ['+', '-', '*'];
    $numbers = [];
    for ($i = 0; $i < 4; $i++) {
        $numbers[] = rand(1, 20);
    }
    return $numbers[0] . $ops[array_rand($ops)] .
           $numbers[1] . $ops[array_rand($ops)] .
           $numbers[2] . $ops[array_rand($ops)] .
           $numbers[3];
}
