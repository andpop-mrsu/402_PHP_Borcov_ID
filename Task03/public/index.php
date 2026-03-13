<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$db = new PDO('sqlite:' . __DIR__ . '/../db/games.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player TEXT,
    created_at TEXT
)
");

$db->exec("
CREATE TABLE IF NOT EXISTS steps (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    game_id INTEGER,
    expression TEXT,
    answer INTEGER,
    correct_answer INTEGER,
    result TEXT,
    created_at TEXT
)
");

$app->get('/', function ($request, $response) {

    $response->getBody()->write("Slim Calculator API работает");

    return $response;
});


$app->get('/expression', function ($request, $response) {

    $ops = ['+','-','*'];

    $a = rand(1,20);
    $b = rand(1,20);
    $c = rand(1,20);
    $d = rand(1,20);

    $expression =
        $a.$ops[array_rand($ops)].
        $b.$ops[array_rand($ops)].
        $c.$ops[array_rand($ops)].
        $d;

    $response->getBody()->write(json_encode([
        "expression"=>$expression
    ]));

    return $response->withHeader('Content-Type','application/json');
});


$app->get('/games', function ($request, $response) use ($db) {

    $stmt = $db->query("SELECT * FROM games");

    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($games));

    return $response->withHeader('Content-Type','application/json');
});


$app->get('/games/{id}', function ($request, $response, $args) use ($db) {

    $id = $args['id'];

    $stmt = $db->prepare("SELECT * FROM steps WHERE game_id=?");
    $stmt->execute([$id]);

    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($steps));

    return $response->withHeader('Content-Type','application/json');
});


$app->post('/games', function ($request, $response) use ($db) {

    $data = json_decode($request->getBody(), true);

    $player = $data['player'];

    $stmt = $db->prepare("
        INSERT INTO games (player,created_at)
        VALUES (?,datetime('now'))
    ");

    $stmt->execute([$player]);

    $id = $db->lastInsertId();

    $response->getBody()->write(json_encode([
        "game_id"=>$id
    ]));

    return $response->withHeader('Content-Type','application/json');
});


$app->post('/step/{id}', function ($request, $response, $args) use ($db) {

    $gameId = $args['id'];

    $data = json_decode($request->getBody(), true);

    $expression = $data['expression'];
    $answer = (int)$data['answer'];

    $correct = eval("return $expression;");

    $result = ($answer == $correct) ? "correct" : "wrong";

    $stmt = $db->prepare("
        INSERT INTO steps
        (game_id,expression,answer,correct_answer,result,created_at)
        VALUES (?,?,?,?,?,datetime('now'))
    ");

    $stmt->execute([
        $gameId,
        $expression,
        $answer,
        $correct,
        $result
    ]);

    $response->getBody()->write(json_encode([
        "result"=>$result,
        "correct_answer"=>$correct
    ]));

    return $response->withHeader('Content-Type','application/json');
});


$app->run();