<?php
session_start();
?>
<div id="airplane-game">
  <h2>✈️ Самолетик</h2>
  <p>Зробіть ставку і натисніть <strong>"Старт"</strong>. Заберіть гроші до того, як самоліт вибухне!</p>

  <div class="bet-panel">
    <input type="number" id="bet-amount" placeholder="Ставка (грн)" min="1">
    <button onclick="startPreparation()">Старт</button>
    <button onclick="cashOut()" disabled id="cashout-btn">Забрати</button>
  </div>

  <div id="airplane-visual">
    <div id="plane">✈️</div>
    <div id="multiplier">x1.00</div>
  </div>

  <div id="history">
    <h4>Останні ігри</h4>
    <ul id="history-list"></ul>
  </div>
</div>

<style>
  #airplane-game {
    max-width: 600px;
    margin: auto;
    background: #f9f9f9;
    color: #333;
    padding: 30px;
    border-radius: 10px;
    font-family: Arial, sans-serif;
    text-align: center;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
  }

  .bet-panel input {
    padding: 8px;
    width: 150px;
    margin-right: 10px;
  }

  .bet-panel button {
    padding: 8px 14px;
    background: #e60073;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
  }

  .bet-panel button:disabled {
    background: grey;
    cursor: default;
  }

  #airplane-visual {
    margin-top: 30px;
    position: relative;
    height: 200px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
  }

  #plane {
    font-size: 40px;
    position: absolute;
    left: 10px;
    top: 80%;
    transition: top 0.3s linear;
  }

  #multiplier {
    font-size: 36px;
    font-weight: bold;
    margin-top: 20px;
  }

  #history {
    margin-top: 30px;
    text-align: left;
  }

  #history h4 {
    margin-bottom: 10px;
  }

  #history-list {
    list-style: none;
    padding: 0;
    font-size: 16px;
  }

  #history-list li {
    margin-bottom: 5px;
  }
</style>

<script>
let multiplier = 1.00;
let interval = null;
let exploded = false;
let cashoutDone = false;
let plane = null;
let planePosition = 80;
let currentBet = 0;

function startPreparation() {
  const bet = parseFloat(document.getElementById('bet-amount').value);
  if (!bet || bet <= 0) {
    alert('Введіть дійсну суму ставки');
    return;
  }

  currentBet = bet;

  fetch('../../Private/place_airplane_bet.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ amount: bet })
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) {
      alert(data.message);
      return;
    }

    document.getElementById('cashout-btn').disabled = true;
    document.getElementById('multiplier').innerHTML = '🕓 Підготовка до злету...';

    setTimeout(() => {
      startFlight();
    }, 10000);
  });
}

function startFlight() {
  document.getElementById('cashout-btn').disabled = false;
  multiplier = 1.00;
  exploded = false;
  cashoutDone = false;
  plane = document.getElementById('plane');
  plane.style.display = 'block';
  planePosition = 80;

  const status = document.getElementById('multiplier');
  const explodeAt = Math.random() * 5 + 3;

  interval = setInterval(() => {
    multiplier += 0.1;
    status.textContent = `x${multiplier.toFixed(2)}`;
    planePosition -= 1;
    plane.style.top = `${planePosition}%`;
  }, 300);

  setTimeout(() => {
    if (!cashoutDone) {
      clearInterval(interval);
      exploded = true;
      status.innerHTML = '💥 Вибух!';
      plane.style.display = 'none';
      document.getElementById('cashout-btn').disabled = true;
      addToHistory(`❌ Вибух на x${multiplier.toFixed(2)}`);

      // Сохраняем проигрыш
      saveResult(false, 0);
    }
  }, explodeAt * 1000);
}

function cashOut() {
  if (exploded || cashoutDone) return;

  cashoutDone = true;
  clearInterval(interval);
  const winnings = (currentBet * multiplier).toFixed(2);

  document.getElementById('multiplier').innerHTML = `💰 Ви забрали <strong>${winnings} грн</strong>!`;
  document.getElementById('plane').style.display = 'none';
  document.getElementById('cashout-btn').disabled = true;
  addToHistory(`✅ Забрано x${multiplier.toFixed(2)} → ${winnings} грн`);

  // Сохраняем выигрыш
  saveResult(true, winnings);
}

function addToHistory(entry) {
  const list = document.getElementById('history-list');
  const li = document.createElement('li');
  li.textContent = entry;
  list.prepend(li);
  while (list.children.length > 5) list.removeChild(list.lastChild);
}

function saveResult(won, winnings) {
  fetch('../../Private/save_airplane_result.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      amount: currentBet,
      multiplier: multiplier.toFixed(2),
      winnings: winnings,
      won: won
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success && data.newBalance) {
      const balanceSpan = document.querySelector('nav.auth-links span');
      if (balanceSpan) {
        balanceSpan.textContent = `Баланс: ${data.newBalance}₴`;
      }
    }
  });
}
function loadHistoryFromServer() {
  fetch('../../Private/load_airplane_history.php')
    .then(res => res.json())
    .then(history => {
      const list = document.getElementById('history-list');
      list.innerHTML = '';
      history.forEach(item => {
        const li = document.createElement('li');
        if (item.won) {
          li.textContent = `✅ Забрано x${parseFloat(item.multiplier).toFixed(2)} → ${parseFloat(item.winnings).toFixed(2)} грн`;
        } else {
          li.textContent = `❌ Вибух на x${parseFloat(item.multiplier).toFixed(2)}`;
        }
        list.appendChild(li);
      });
    });
}

// Загружаем историю при загрузке
document.addEventListener('DOMContentLoaded', loadHistoryFromServer);

</script>
