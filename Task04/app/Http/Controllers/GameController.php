<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    // Получить все игры
    public function index()
    {
        $games = DB::table('games')->get();
        return response()->json($games);
    }

    // Получить шаги игры по ID
    public function show($id)
    {
        $steps = DB::table('steps')->where('game_id', $id)->get();
        return response()->json($steps);
    }

    // Создать новую игру
    public function store(Request $request)
    {
        $gameId = DB::table('games')->insertGetId([
            'player' => $request->player,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['game_id' => $gameId]);
    }

    // Добавить шаг (вычисление) к игре
    public function addStep(Request $request, $id)
    {
        $correct = eval("return {$request->expression};"); // вычисляем правильный ответ
        DB::table('steps')->insert([
            'game_id' => $id,
            'expression' => $request->expression,
            'answer' => $request->answer,
            'correct_answer' => $correct,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['status' => 'ok', 'correct_answer' => $correct]);
    }
}