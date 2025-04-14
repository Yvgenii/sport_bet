<?php
session_start();
require_once __DIR__ . '/../../Private/db.php';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Спортивні події</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<header>
<h1 class="page-title">Спортивні події</h1>

<div class="sports-container">
    <a href="#" class="sport-card" onclick="loadMatches('Футбол')"><span>⚽ Футбол</span></a>
    <a href="#" class="sport-card" onclick="loadMatches('Баскетбол')"><span>🏀 Баскетбол</span></a>
    <a href="#" class="sport-card" onclick="loadMatches('Бейсбол')"><span>⚾ Бейсбол</span></a>
    <a href="#" class="sport-card" onclick="loadMatches('Хокей')"><span>🏒 Хокей</span></a>
    <a href="#" class="sport-card" onclick="loadMatches('Гольф')"><span>🏌️ Гольф</span></a>
    <a href="#" class="sport-card" onclick="loadMatches('Теніс')"><span>🎾 Теніс</span></a>
    <a href="#" class="sport-card" onclick="loadMatches('Регбі')"><span>🏉 Регбі</span></a>
</div>

<div id="matches-container">
    <p>Оберіть вид спорту для перегляду матчів.</p>
</div>

</header>

<main></main>

<footer>
    <a href="../index.php">🏠 Повернутися на головну</a>
</footer>
<style>
footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #2c2c2c;
    color: white;
    padding: 10px 0;
    text-align: center;
    z-index: 999;
}
footer a {
    color: white;
    text-decoration: none;
}
</style>

<!-- Модальне вікно для ставки -->
<div id="betModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeBetModal()">&times;</span>
    <h2>Зробити ставку</h2>
    <p id="betInfo"></p>
    <label for="betAmount">Сума ставки:</label>
    <input type="number" id="betAmount" min="1" required>
    <button onclick="submitBet()">Підтвердити</button>
  </div>
</div>

<script>
let currentEvent = '', currentType = '', currentOdds = 0;

function openBetModal(event, type, odds) {
    currentEvent = event;
    currentType = type;
    currentOdds = odds;
    document.getElementById('betInfo').innerText = `Подія: ${event}, Тип: ${type}, Коефіцієнт: ${odds}`;
    document.getElementById('betModal').style.display = 'block';
}

function closeBetModal() {
    document.getElementById('betModal').style.display = 'none';
}

function submitBet() {
    const amount = document.getElementById('betAmount').value;
    fetch('../../Private/place_bet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event: currentEvent, type: currentType, odds: currentOdds, amount })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Ставка успішна! Можливий виграш: ' + data.potentialWin);
            closeBetModal();
        } else {
            alert('Помилка: ' + data.message);
        }
    })
    .catch(() => alert('Помилка при надсиланні запиту.'));
}

function loadMatches(sport) {
    fetch('load_matches.php?sport=' + encodeURIComponent(sport))
        .then(response => response.text())
        .then(data => {
            document.getElementById('matches-container').innerHTML = data;
        })
        .catch(error => console.error('🔴 Помилка завантаження матчів:', error));
}
</script>

<link rel="stylesheet" href="../styles.css">
<style>
.modal {
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
}
.modal-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 20px;
  width: 90%;
  max-width: 400px;
  border-radius: 8px;
  text-align: center;
}
.close {
  float: right;
  font-size: 28px;
  cursor: pointer;
}
</style>

</body>
</html>
