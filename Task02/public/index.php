<?php
declare(strict_types=1);

// Путь к базе SQLite
$dbPath = __DIR__ . '/../db/game.sqlite';
$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Создаём таблицу игр, если ещё нет
$db->exec("
CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player TEXT,
    expression TEXT,
    answer INTEGER,
    created_at TEXT
)
");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player = trim($_POST['player']);
    $expression = $_POST['expression']; // берём выражение из скрытого поля
    $answer = (int) $_POST['answer'];

    // вычисляем правильный ответ
    $correctAnswer = eval("return $expression;");

    // сохраняем в базу данных
    $stmt = $db->prepare("
        INSERT INTO games (player, expression, answer, created_at)
        VALUES (:player, :expression, :answer, datetime('now'))
    ");
    $stmt->execute([
        'player' => $player,
        'expression' => $expression,
        'answer' => $answer
    ]);

    // формируем сообщение
    $message = $answer === (int)$correctAnswer
        ? 'Правильно!'
        : 'Неправильно. Правильный ответ: ' . $correctAnswer;

    // после отправки создаём новое выражение для следующей попытки
    $expression = generateExpression();
} else {
    // GET-запрос — генерируем первый пример
    $expression = generateExpression();
}

// Функция генерации случайного арифметического выражения
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
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор</title>
</head>
<body>
<h1>Игра «Калькулятор»</h1>

<p>Вычислите выражение: <strong><?= htmlspecialchars($expression) ?></strong></p>

<?php if ($message): ?>
    <p><strong><?= htmlspecialchars($message) ?></strong></p>
<?php endif; ?>

<form method="post">
    <label>
        Имя:
        <input type="text" name="player" required>
    </label><br><br>

    <label>
        Ответ:
        <input type="number" name="answer" required>
    </label><br><br>

    <!-- скрытое поле с текущим выражением -->
    <input type="hidden" name="expression" value="<?= htmlspecialchars($expression) ?>">

    <button type="submit">Отправить</button>
</form>
</body>
</html>