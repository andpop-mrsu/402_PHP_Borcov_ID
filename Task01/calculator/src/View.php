<?php
namespace ZerolLka\Calculator\View;

use function cli\line;

function showMessage(string $msg): void {
    line($msg);
}
