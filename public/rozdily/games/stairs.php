<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Гра "Сходи"</title>
  <style>
    body {
      background-color: #f9f9f9;
      font-family: Arial, sans-serif;
    }
    .game-wrapper {
      display: flex;
      justify-content: center;
      gap: 40px;
      padding: 40px;
    }
    .sidebar {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 260px;
      text-align: center;
    }
    .sidebar label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #1a1a1a;
    }
    .sidebar input {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .quick-buttons {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 5px;
      margin-bottom: 15px;
    }
    .quick-buttons button {
      padding: 6px 10px;
      background-color: #ff0080;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }
    .stones-section label {
      font-size: 20px;
      font-weight: bold;
      display: block;
      margin-bottom: 8px;
      color: #1a1a1a;
    }
    .stone-select {
      display: flex;
      justify-content: center;
      gap: 6px;
      flex-wrap: wrap;
      margin-bottom: 10px;
    }
    .stone-button {
      background: transparent;
      border: 1px solid #666;
      border-radius: 6px;
      color: #333;
      padding: 6px 10px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.2s ease;
    }
    .stone-button.active {
      background: #dbeafe;
      color: #000;
      border-color: #dbeafe;
    }
    .play-btn {
      width: 100%;
      padding: 10px;
      font-weight: bold;
      color: white;
      background-color: #ff0080;
      border: none;
      border-radius: 8px;
      margin-top: 10px;
      cursor: pointer;
      font-size: 16px;
    }
    .main-game {
      max-width: 500px;
      text-align: center;
    }
    .main-game h2 {
      margin-bottom: 10px;
      font-size: 24px;
      color: #6b3000;
    }
    .main-game p {
      color: #444;
      font-size: 16px;
      margin-bottom: 20px;
    }
    .ladder-board {
      background: #1a1a40;
      padding: 16px;
      border-radius: 12px;
      display: flex;
      flex-direction: column-reverse;
      justify-content: space-between;
      align-items: flex-start;
      height: 420px;
      width: 100%;
      max-width: 420px;
      overflow: hidden;
      box-sizing: border-box;
      margin: 0 auto;
      position: relative;
    }
    .ladder-row {
      display: flex;
      gap: 4px;
      justify-content: flex-start;
      align-items: center;
      flex-shrink: 0;
      width: 100%;
    }
    .row-coef {
      width: 55px;
      height: 24px;
      background-color: #3a3c5e;
      color: #fff;
      font-size: 13px;
      font-weight: bold;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0.9;
      flex-shrink: 0;
    }
    .ladder-cell {
      width: 20px;
      height: 20px;
      background-color: #2b2d55;
      border-radius: 4px;
      transition: 0.2s;
      position: relative;
    }
    .ladder-cell.active-cell {
      background-color: #3347b1 !important;
    }
    .ladder-cell.stone.visible {
      background-color: #ff4c4c !important;
    }
    .ladder-cell.stone.visible::after {
      content: "\1F4A3";
      position: absolute;
      top: 0;
      left: 4px;
      font-size: 14px;
    }
    .player {
      width: 18px;
      height: 18px;
      background-image: url('../images/player.png');
      background-size: contain;
      background-repeat: no-repeat;
      position: absolute;
      transition: top 0.3s, left 0.3s;
      z-index: 10;
    }
    .collect-btn {
      width: 100%;
      padding: 10px;
      font-weight: bold;
      color: white;
      background-color: #1a1a40;
      border: none;
      border-radius: 8px;
      margin-top: 10px;
      cursor: pointer;
      font-size: 16px;
    }
    .play-btn.playing {
      opacity: 0.5;
    }
  </style>
</head>
<body>
<div class="game-wrapper">
  <div class="sidebar">
    <label for="ladder-bet">💰Сума ставки</label>
    <input type="number" id="ladder-bet" step="0.1" min="1" value="1.00">
    <div class="quick-buttons">
      <button onclick="modifyBet(0.1)">+0.1</button>
      <button onclick="modifyBet(0.5)">+0.5</button>
      <button onclick="modifyBet(1)">+1</button>
      <button onclick="modifyBet(5)">+5</button>
      <button onclick="multiplyBet(2)">x2</button>
      <button onclick="multiplyBet(0.5)">½</button>
    </div>
    <div class="stones-section">
      <label>☁️ Кількість каменів</label>
      <div class="stone-select" id="stone-select"></div>
    </div>
    <button id="play-button" class="play-btn">Грати</button>
    <button id="collect-button" class="collect-btn">Забрати</button>
  </div>
  <div class="main-game">
    <h2>🪜 Гра "Сходи"</h2>
    <p>Зробіть ставку та почніть підйом! Обирайте сходинку, рухайтесь вгору і забирайте виграш до вибуху!</p>
    <div class="ladder-board" id="ladder-board"></div>
  </div>
</div>
<script>
let selectedStones = 4;
let currentRowIndex = 0;
let rows = [];
let currentPlayerCell = null;
let currentBet = 0;
let balance = parseFloat(localStorage.getItem('user_balance')) || 100;

function updateBalanceDisplay() {
  fetch('../Private/get_balance.php')
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const headerBalance = document.querySelector('.header-balance');
        if (headerBalance) {
          headerBalance.textContent = data.balance.toFixed(2) + '€';
        }
      }
    });
}


const coefSets = {
  1: ['x1', 'x1.06', 'x1.12', 'x1.19', 'x1.27', 'x1.36', 'x1.46', 'x1.58', 'x1.73', 'x2.71'],
  2: ['x1.06', 'x1.18', 'x1.33', 'x1.5', 'x1.72', 'x1.98', 'x2.31', 'x2.73', 'x3.28', 'x8.6'],
  3: [' x1.12  ', ' x1.33  ', ' x2.98 ', ' x3.79 ', ' x4.92 ', ' x6.56 ', ' x9.03 ', ' x12.89 ', ' x19.34 ', ' x30.94'],
  4: ['x1.15', 'x1.33', 'x1.55', 'x3.37', 'x4.6', 'x6.44', 'x13.95', 'x21.92', 'x36.53', 'x65.75 '],
  5: ['x1.23', ' x1.72 ', 'x2.38', ' x3.37 ', ' x7.36 ', ' x18.6 ', 'x31.88', 'x58.45', 'x116.9', 'x263.01 '],
  6: ['x1.36', 'x2.98', 'x4.6', 'x7.36', 'x12.26', 'x21.46','x92.34', 'x175.34', 'x438.36', 'x1.32k'],
  7: ['x1.46', ' x3.79 ', ' x6.44 ', ' x11.44 ', ' x42.92 ', ' x223.16 ', ' x613.7 ', ' x2.05k ', ' x9.21k ', 'x73.64k']
};

function modifyBet(amount) {
  const input = document.getElementById('ladder-bet');
  input.value = (parseFloat(input.value || 0) + amount).toFixed(2);
}
function multiplyBet(factor) {
  const input = document.getElementById('ladder-bet');
  input.value = (parseFloat(input.value || 0) * factor).toFixed(2);
}

function generateLadderBoard() {
  const board = document.getElementById('ladder-board');
  board.innerHTML = '';
  rows = [];
  const totalRows = 10;
  const baseCellCount = 17;
  const coefs = coefSets[selectedStones] || [];
  for (let i = 0; i < totalRows; i++) {
    const row = document.createElement('div');
    row.classList.add('ladder-row');
    const coef = document.createElement('div');
    coef.classList.add('row-coef');
    coef.textContent = coefs[i] || '';
    row.appendChild(coef);
    const cellCount = baseCellCount - i;
    const cells = [];
    for (let j = 0; j < cellCount; j++) {
      const cell = document.createElement('div');
      cell.classList.add('ladder-cell');
      cell.dataset.row = i;
      row.appendChild(cell);
      cells.push(cell);
    }
    const shuffled = [...cells].sort(() => 0.5 - Math.random());
    for (let k = 0; k < selectedStones && k < shuffled.length; k++) {
      shuffled[k].classList.add('stone');
    }
    board.appendChild(row);
    rows.push({ el: row, cells });
  }
  currentRowIndex = 0;
  showRow(0);
}

function showRow(rowIndex) {
  rows.forEach((row, i) => {
    row.cells.forEach(cell => {
      cell.removeEventListener('click', handleCellClick);
      cell.classList.remove('active-cell');
      if (i === rowIndex) {
        cell.classList.add('active-cell');
        cell.addEventListener('click', handleCellClick);
      }
    });
  });
}

function handleCellClick(e) {
  const cell = e.currentTarget;
  const rowIndex = parseInt(cell.dataset.row);
  document.querySelectorAll('.player').forEach(p => p.remove());
  const player = document.createElement('div');
  player.className = 'player';
  cell.appendChild(player);
  const currentRow = rows[rowIndex];
  currentRow.cells.forEach(c => {
    if (c.classList.contains('stone')) {
      c.classList.add('visible');
    }
  });
  if (cell.classList.contains('stone')) {
    alert("💥 Ви натрапили на камінь! Гру завершено.");
    return;
  }
  currentRowIndex++;
  if (currentRowIndex < rows.length) {
    showRow(currentRowIndex);
  } else {
    alert("🎉 Ви виграли гру!");
  }
}

document.getElementById('play-button').addEventListener('click', () => {
  const bet = parseFloat(document.getElementById('ladder-bet').value);
  if (bet > balance) {
    alert('Недостатньо коштів.');
    return;
  }
  currentBet = bet;
  balance -= currentBet;
  updateBalanceDisplay();
  document.getElementById('play-button').classList.add('playing');
  generateLadderBoard();
});

document.getElementById('collect-button').addEventListener('click', () => {
  const coefs = coefSets[selectedStones];
  const coef = parseFloat(coefs[currentRowIndex - 1]?.replace('x', '') || 1);
  const winAmount = currentBet * coef;
  balance += winAmount;
  updateBalanceDisplay();
  alert(`🎉 Ви забрали виграш: ${winAmount.toFixed(2)}€`);
});

for (let i = 1; i <= 7; i++) {
  const btn = document.createElement('button');
  btn.className = 'stone-button';
  btn.textContent = i;
  if (i === selectedStones) btn.classList.add('active');
  btn.onclick = () => {
    selectedStones = i;
    document.querySelectorAll('.stone-button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    generateLadderBoard();
  };
  document.getElementById('stone-select').appendChild(btn);
}

updateBalanceDisplay();
generateLadderBoard();

document.getElementById('play-button').addEventListener('click', () => {
  const bet = parseFloat(document.getElementById('ladder-bet').value);
  if (bet <= 0) return;

  fetch('../update_balance.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=bet&amount=${bet}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      currentBet = bet;
      document.getElementById('play-button').classList.add('playing');
      updateBalanceDisplay();
      generateLadderBoard();
    } else {
      alert(data.message || 'Помилка списання коштів.');
    }
  });
});
document.getElementById('collect-button').addEventListener('click', () => {
  const coefs = coefSets[selectedStones];
  const coef = parseFloat(coefs[currentRowIndex - 1]?.replace('x', '') || 1);

  if (!currentBet || !coef || currentRowIndex === 0) {
    alert("😢 Спочатку потрібно зробити ставку та піднятися хоча б на одну сходинку.");
    return;
  }

  const winAmount = (currentBet * coef).toFixed(2); // сумма в грн, с двумя знаками
  fetch('../update_balance.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=win&amount=${winAmount}`
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert(`🎉 Ви забрали виграш: ${winAmount}₴`);
      updateBalanceDisplay();
    } else {
      alert(data.message || 'Помилка зарахування виграшу.');
    }
  });
});

</script>
</body>
</html>