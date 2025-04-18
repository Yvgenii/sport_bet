<?php
session_start();
?>

<div class="coin-wrapper">
  <div class="bet-sidebar">
    <h3>💰 Сума ставки</h3>
    <input type="number" id="bet-amount" placeholder="Ставка (грн)" min="1" step="0.1">
    <div class="quick-controls">
      <button onclick="multiplyBet(2)">x2</button>
      <button onclick="multiplyBet(0.5)">1/2</button>
    </div>
    <div class="quick-add">
      <button onclick="addToBet(0.1)">+0.1</button>
      <button onclick="addToBet(0.5)">+0.5</button>
      <button onclick="addToBet(1)">+1</button>
      <button onclick="addToBet(5)">+5</button>
      <button onclick="addToBet(20)">+20</button>
      <button onclick="addToBet(100)">+100</button>
      <button onclick="addToBet(500)">+500</button>
    </div>

    <div class="sidebar-buttons">
      <button onclick="startCoinGame()" id="play-btn">Грати</button>
      <button onclick="cashOut()" disabled id="cashout-btn">Забрати</button>
    </div>
  </div>

  <div id="stairs-game">
    <h2>🪜 Лестница</h2>
    <p>Зробіть ставку та виберіть місце по якому Ви хочете залізти. Якщо вгадаєте де нема бомби — множник зростає, і ви зможете забрати гроші!</p>

    <div class="bet-panel">
        <table>
        </table>
    </div>

    

<style>
.coin-wrapper {
  display: flex;
  justify-content: center;
  gap: 40px;
  padding: 60px;
}

.bet-sidebar {
  width: 220px;
  background: #f1f1f1;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  font-family: Arial, sans-serif;
  text-align: center;
}

.bet-sidebar input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  font-size: 16px;
}

.quick-controls button,
.quick-add button,
.sidebar-buttons button {
  margin: 4px 4px;
  padding: 6px 10px;
  font-size: 14px;
  background-color: #e60073;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.quick-add {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
  margin-bottom: 12px;
}

.sidebar-buttons {
  margin-top: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.quick-controls {
  margin-bottom: 10px;
}













#play-btn:disabled {
  opacity: 0.5;
  pointer-events: none;
}


</style>

<script>
let multipliers = [1.9, 3.8, 7.6, 15.2, 30.4, 60.8, 121.6, 243.2, 486.4, 972.8];
let currentRound = 0;
let currentBet = 0;
let currentChoice = '';
let gameActive = false;

function multiplyBet(factor) {
  const input = document.getElementById('bet-amount');
  const current = parseFloat(input.value) || 0;
  input.value = (current * factor).toFixed(2);
}

function addToBet(amount) {
  const input = document.getElementById('bet-amount');
  const current = parseFloat(input.value) || 0;
  input.value = (current + amount).toFixed(2);
}

function selectCoin(element) {
  document.querySelectorAll('.coin-option').forEach(el => el.classList.remove('selected'));
  element.classList.add('selected');
  currentChoice = element.dataset.choice;
}

function startCoinGame() {
  const bet = parseFloat(document.getElementById('bet-amount').value);
  if (!bet || bet <= 0) {
    alert('Введіть дійсну суму ставки');
    return;
  }
  if (!currentChoice) {
    alert('Оберіть сторону монети');
    return;
  }
  currentBet = bet;
  currentRound = 0;
  gameActive = true;
  document.getElementById('cashout-btn').disabled = false;
  document.getElementById('play-btn').disabled = true;
  document.getElementById('round-info').textContent = 'Раунд: 0';
  document.getElementById('multiplier-info').textContent = 'Коефіцієнт: x0';
  fetch('../../Private/place_coin_bet.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ amount: bet })
  }).then(res => res.json())
    .then(data => {
      if (!data.success) {
        alert(data.message);
        gameActive = false;
        document.getElementById('play-btn').disabled = false;
      } else {
        nextCoinRound();
      }
    });
  renderMultiplierTrack(currentRound);
}

function nextCoinRound() {
  if (!gameActive || currentRound >= multipliers.length) return;
  document.getElementById('next-round-btn').style.display = 'none';
  const result = Math.random() < 0.5 ? 'heads' : 'tails';
  const coin = document.getElementById('coin-flip');
  coin.textContent = result === 'heads' ? '🪙 Орел' : '🪙 Решка';
  if (result === currentChoice) {
    currentRound++;
    document.getElementById('round-info').textContent = `Раунд: ${currentRound}`;
    document.getElementById('multiplier-info').textContent = `Коефіцієнт: x${multipliers[currentRound - 1]}`;
    renderMultiplierTrack(currentRound);
    if (currentRound < multipliers.length) {
      document.getElementById('next-round-btn').style.display = 'inline-block';
    } else {
      cashOut();
    }
  } else {
    gameActive = false;
    document.getElementById('cashout-btn').disabled = true;
    document.getElementById('multiplier-info').textContent = `💥 Ви програли!`;
    renderMultiplierTrack(currentRound);
    document.getElementById('play-btn').disabled = false;
  }
}

function cashOut() {
  if (!gameActive || currentRound === 0) return;
  gameActive = false;
  const winnings = (currentBet * multipliers[currentRound - 1]).toFixed(2);
  document.getElementById('multiplier-info').innerHTML = `💰 Ви забрали <strong>${winnings} грн</strong>`;
  document.getElementById('cashout-btn').disabled = true;
  document.getElementById('play-btn').disabled = false;
  fetch('../../Private/save_coin_result.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      winnings,
      multiplier: multipliers[currentRound - 1],
      amount: currentBet,
      won: true
    })
  });
}

function renderMultiplierTrack(currentRound = -1) {
  const track = document.getElementById('multiplier-track');
  track.innerHTML = '';
  multipliers.forEach((mult, index) => {
    const el = document.createElement('div');
    el.className = 'multiplier-step';
    if (index < currentRound) {
      el.classList.add('passed');
      el.innerHTML = '✅<div>x' + mult + '</div>';
    } else if (index === currentRound) {
      el.classList.add('current');
      el.innerHTML = 'x' + mult;
    } else {
      el.innerHTML = '<div>x' + mult + '</div>';
    }
    track.appendChild(el);
  });
}

renderMultiplierTrack();
</script>
