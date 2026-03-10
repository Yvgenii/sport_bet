<?php
session_start();
?>

<div class="roulette-wrapper">
  <h2 class="title">🎡 Рулетка</h2>
  <div class="roulette-container">
    <div class="betting-panel">
      <div class="bet-header">
        <button onclick="setMin()">Мін</button>
        <button onclick="multiplyBet(2)">x2</button>
        <button onclick="multiplyBet(0.5)">1/2</button>
        <button onclick="betAll()">На все</button>
      </div>
      <div class="bet-amount-selector">
        <button onclick="addToBet(-1)">-</button>
        <input type="number" id="bet-amount" value="1" min="1" max="10000">
        <button onclick="addToBet(1)">+</button>
      </div>
      <div class="bet-places">
        <p>Ставка (грн):</p>
        <div class="bet-options">
          <button class="bet-red" onclick="placeBet('red')">0.00 x2</button>
          <button class="bet-green" onclick="placeBet('green')">0.00 x14</button>
          <button class="bet-black" onclick="placeBet('black')">0.00 x2</button>
        </div>
      </div>
    </div>

    <div class="roulette-wheel">
      <img src="../images/roulette-wheel.png" alt="Рулетка" class="wheel-image" id="wheel">
      <div class="indicator"></div>
      <button onclick="spinRoulette()" class="spin-btn">Крутити</button>
    </div>
  </div>
</div>

<style>
.roulette-wrapper {
  padding: 40px;
  font-family: Arial, sans-serif;
  text-align: center;
  color: #333;
}
.title {
  font-size: 28px;
  margin-bottom: 20px;
}
.roulette-container {
  display: flex;
  justify-content: center;
  gap: 50px;
  flex-wrap: wrap;
}
.betting-panel {
  background: #f1f1f1;
  padding: 20px;
  border-radius: 12px;
  width: 280px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.bet-header button,
.bet-amount-selector button {
  padding: 6px 12px;
  margin: 4px;
  font-size: 14px;
  border: none;
  border-radius: 6px;
  background-color: #ccc;
  cursor: pointer;
}
.bet-amount-selector {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 0;
}
.bet-amount-selector input {
  width: 60px;
  text-align: center;
  margin: 0 5px;
  padding: 5px;
}
.bet-options {
  display: flex;
  justify-content: space-around;
  margin-top: 10px;
}
.bet-red {
  background: #e60000;
  color: white;
  padding: 10px;
  border-radius: 6px;
}
.bet-green {
  background: #00cc66;
  color: white;
  padding: 10px;
  border-radius: 6px;
}
.bet-black {
  background: #333;
  color: white;
  padding: 10px;
  border-radius: 6px;
}
.roulette-wheel {
  position: relative;
  width: 300px;
  height: 300px;
}
.wheel-image {
  width: 100%;
  transition: transform 5s ease-out;
}
.indicator {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0; 
  height: 0;
  border-left: 12px solid transparent;
  border-right: 12px solid transparent;
  border-bottom: 20px solid gold;
  z-index: 10;
}
.spin-btn {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #e60073;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}
</style>

<script>
let betAmount = 1;
let wheel = document.getElementById('wheel');

function addToBet(amount) {
  const input = document.getElementById('bet-amount');
  let current = parseFloat(input.value) || 0;
  current += amount;
  if (current < 1) current = 1;
  input.value = current;
  betAmount = current;
}

function multiplyBet(factor) {
  const input = document.getElementById('bet-amount');
  let current = parseFloat(input.value) || 0;
  current *= factor;
  input.value = current.toFixed(2);
  betAmount = current;
}

function setMin() {
  document.getElementById('bet-amount').value = 1;
  betAmount = 1;
}

function betAll() {
  document.getElementById('bet-amount').value = 10000;
  betAmount = 10000;
}

function placeBet(color) {
  alert(`Ставка ${betAmount} грн на ${color}`);
}

function spinRoulette() {
  const deg = Math.floor(720 + Math.random() * 720); // вращение от 2 до 4 кругов
  wheel.style.transform = `rotate(${deg}deg)`;
  // Тут можно добавить вычисление победного сектора и показ результата
}
</script>