<!doctype html>
<html>
<head>
    <title>Calculator Game</title>
</head>
<body>
<h1>Калькулятор</h1>
<p>Введите ответ для выражения: <span id="expr"></span></p>
<input type="number" id="answer">
<button onclick="sendAnswer()">Отправить</button>
<p id="result"></p>

<script>
let gameId = null;
let currentExpr = null;

// создаём новую игру
fetch('/api/games', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({player: 'Igor'})
})
.then(r=>r.json()).then(r=> gameId = r.game_id);

function newExpression() {
    const a = Math.floor(Math.random()*50);
    const b = Math.floor(Math.random()*50);
    const c = Math.floor(Math.random()*50);
    const d = Math.floor(Math.random()*50);
    const ops = ['+','-','*'];
    currentExpr = `${a}${ops[Math.floor(Math.random()*3)]}${b}${ops[Math.floor(Math.random()*3)]}${c}${ops[Math.floor(Math.random()*3)]}${d}`;
    document.getElementById('expr').innerText = currentExpr;
}
newExpression();

function sendAnswer() {
    const answer = parseInt(document.getElementById('answer').value);
    fetch(`/api/step/${gameId}`, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({expression: currentExpr, answer: answer})
    }).then(r=>r.json())
      .then(r=>{
          if(r.correct_answer === answer){
              document.getElementById('result').innerText = 'Правильно!';
          } else {
              document.getElementById('result').innerText = 'Неправильно! Правильный ответ: '+r.correct_answer;
          }
          newExpression();
          document.getElementById('answer').value='';
      });
}
</script>
</body>
</html>