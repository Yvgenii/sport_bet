<?php
session_start();
?>

<div class="game-wrapper">
  <div class="game-left-panel">
    <h3>Сума ставки</h3>
    <input type="number" id="bet-amount" value="1.00" step="0.1" min="1">
    <div class="quick-buttons">
      <button onclick="multiplyBet(2)">x2</button>
      <button onclick="multiplyBet(0.5)">1/2</button>
    </div>
    <div class="add-buttons">
      <button onclick="addToBet(0.1)">+0.1</button>
      <button onclick="addToBet(0.5)">+0.5</button>
      <button onclick="addToBet(1)">+1</button>
      <button onclick="addToBet(5)">+5</button>
      <button onclick="addToBet(20)">+20</button>
    </div>
    <h4>Виберіть кількість ступенів</h4>
    <div class="level-buttons">
      <button onclick="selectLevel(1)">1</button>
      <button onclick="selectLevel(2)">2</button>
      <button onclick="selectLevel(3)">3</button>
      <button onclick="selectLevel(4)">4</button>
      <button onclick="selectLevel(5)">5</button>
    </div>
    <button class="play-button" onclick="startGame()">Грати</button>
  </div>

  <div class="game-area" id="game-area">
    <!-- steps and rocks will render here -->
  </div>
</div>

<style>
body {
  font-family: Arial;
  background: #121522;
  color: white;
}
.game-wrapper {
  display: flex;
  justify-content: center;
  gap: 40px;
  padding: 40px;
}
.game-left-panel {
  width: 260px;
  background: #f1f1f1;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 10px #000;
}
input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border: none;
  border-radius: 4px;
  font-size: 16px;
}
.quick-buttons button, .add-buttons button, .level-buttons button {
  margin: 4px;
  padding: 6px 10px;
  background: #e60073;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.play-button {
  margin-top: 20px;
  width: 100%;
  padding: 10px;
  background: #00cc66;
  border: none;
  font-weight: bold;
  font-size: 18px;
  border-radius: 6px;
  cursor: pointer;
}
.game-area {
  background: #181b2d;
  width: 500px;
  height: 500px;
  position: relative;
  border-radius: 10px;
  overflow: hidden;
}
.step {
  width: 50px;
  height: 20px;
  background: #555;
  border: 1px solid #aaa;
  position: absolute;
  bottom: 0;
  border-radius: 4px;
}
.rock {
  width: 20px;
  height: 20px;
  background: gray;
  border-radius: 50%;
  position: absolute;
  animation: fall 2s linear forwards;
}
@keyframes fall {
  0% { top: 0; }
  100% { top: 480px; }
}
</style>

<script>
let bet = 1;
let selectedLevel = 1;

function addToBet(amount) {
  const input = document.getElementById('bet-amount');
  bet = parseFloat(input.value) + amount;
  if (bet < 1) bet = 1;
  input.value = bet.toFixed(2);
}

function multiplyBet(factor) {
  const input = document.getElementById('bet-amount');
  bet = parseFloat(input.value) * factor;
  if (bet < 1) bet = 1;
  input.value = bet.toFixed(2);
}

function selectLevel(level) {
  selectedLevel = level;
  console.log("Selected level: ", level);
}

function startGame() {
  const gameArea = document.getElementById('game-area');
  gameArea.innerHTML = '';

  for (let i = 0; i < 5; i++) {
    const step = document.createElement('div');
    step.classList.add('step');
    step.style.left = (i * 100 + 20) + 'px';
    step.dataset.index = i;
    gameArea.appendChild(step);
  }

  const targetIndex = Math.floor(Math.random() * 5);
  const targetStep = document.querySelector(.step[data-index='${targetIndex}']);
  targetStep.style.background = '#00cc66';

  setTimeout(() => {
    for (let i = 0; i < selectedLevel + 2; i++) {
      const rock = document.createElement('div');
      rock.classList.add('rock');
      rock.style.left = (Math.floor(Math.random() * 5) * 100 + 35) + 'px';
      gameArea.appendChild(rock);
    }
  }, 1000);
}
</script>