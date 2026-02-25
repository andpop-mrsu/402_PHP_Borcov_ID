<?php

namespace ZerolLka\Calculator\View;

use function cli\line;

function showWelcome(): void
{
    line("Добро пожаловать в игру Калькулятор!");
    line("Вычислите значение выражения.");
}