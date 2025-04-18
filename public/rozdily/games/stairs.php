<div class="game-wrapper">
  <div class="sidebar">
    <label for="ladder-bet">💰Сума ставки</label>
    <input type="number" id="ladder-bet" placeholder="Ставка (грн)" step="0.1" min="1" value="1.00">

    <div class="quick-buttons">
      <button onclick="multiplyBet(2)">x2</button>
      <button onclick="multiplyBet(0.5)">1/2</button>
      <button onclick="addToBet(0.1)">+0.1</button>
      <button onclick="addToBet(0.5)">+0.5</button>
      <button onclick="addToBet(1)">+1</button>
      <button onclick="addToBet(5)">+5</button>
      <button onclick="addToBet(20)">+20</button>
      <button onclick="addToBet(100)">+100</button>
      <button onclick="addToBet(500)">+500</button>
    </div>

    <div class="stones-section">
      <label>☁️ Кількість каменів</label>
      <div class="stone-select" id="stone-select"></div>
    </div>

    <button class="play-btn">Грати</button>
    <button class="collect-btn">Забрати</button>
  </div>

  <div class="main-game">
    <h2>🪜 Гра "Сходи"</h2>
    <p>Зробіть ставку та почніть підйом! Обирайте сходинку, рухайтесь вгору і забирайте виграш до вибуху!</p>

    <div class="ladder-board" id="ladder-board"></div>
  </div>
</div>

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

.stones-section {
  margin-bottom: 15px;
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

.play-btn, .collect-btn {
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
.collect-btn {
  background-color: #1a1a40;
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
  position: relative; /* Добавлено */
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
}


.player {
  position: absolute;
  width: 18px;
  height: 18px;
  background-image: url('../images/player.png');
  background-size: contain;
  background-repeat: no-repeat;
  z-index: 2;
  transition: top 0.3s, left 0.3s;
  left: 0;
  top: 0;
}


.ladder-cell {
  width: 20px;
  height: 20px;
  background-color: #2b2d55;
  border-radius: 4px;
  transition: 0.2s;
  position: relative;
}

</style>

<script>
function multiplyBet(factor) {
  const input = document.getElementById('ladder-bet');
  input.value = (parseFloat(input.value || 0) * factor).toFixed(2);
}
function addToBet(amount) {
  const input = document.getElementById('ladder-bet');
  input.value = (parseFloat(input.value || 0) + amount).toFixed(2);
}

const stoneContainer = document.getElementById('stone-select');
let selectedStones = 4;

const coefSets = {
  1: ['x1', 'x1.06', 'x1.12', 'x1.19', 'x1.27', 'x1.36', 'x1.46', 'x1.58', 'x1.73', 'x2.71'],
  2: ['x1.06', 'x1.18', 'x1.33', 'x1.5', 'x1.72', 'x1.98', 'x2.31', 'x2.73', 'x3.28', 'x8.6'],
  3: ['x1.1', 'x1.23', 'x1.42', 'x1.68', 'x1.96', 'x2.33', 'x2.89', 'x3.64', 'x4.5', 'x15.4'],
  4: ['x1.15', 'x1.33', 'x1.55', 'x1.84', 'x2.17', 'x2.6', 'x3.16', 'x3.94', 'x4.92', 'x27.81'],
  5: ['x1.23', 'x1.46', 'x1.72', 'x2.04', 'x2.43', 'x2.95', 'x3.68', 'x4.74', 'x6.26', 'x51.42'],
  6: ['x1.33', 'x1.6', 'x1.92', 'x2.29', 'x2.78', 'x3.41', 'x4.34', 'x5.71', 'x7.68', 'x203.6'],
  7: ['x1.46', 'x2.31', 'x3.79', 'x6.44', 'x11.44', 'x21.46', 'x42.92', 'x92.98', 'x223.16', 'x73.64k']
};


let currentPlayerCell = null;

function addPlayerTo(cell) {
  if (currentPlayerCell) {
    currentPlayerCell.innerHTML = '';
  }

  const player = document.createElement('div');
  player.className = 'player';
  cell.appendChild(player);
  currentPlayerCell = cell;
}

function handleCellClick(e) {
  const cell = e.currentTarget;
  addPlayerTo(cell);
}

// ⚠️ ТОЛЬКО ОДНА ВЕРСИЯ этой функции:
function generateLadderBoard() {
  const board = document.getElementById('ladder-board');
  board.innerHTML = '';

  const totalRows = 10;
  const bottomCells = 17;
  const coefs = coefSets[selectedStones] || [];

  for (let r = 0; r < totalRows; r++) {
    const row = document.createElement('div');
    row.classList.add('ladder-row');

    const coef = document.createElement('div');
    coef.classList.add('row-coef');
    coef.textContent = coefs[r] || '';
    row.appendChild(coef);

    const cellCount = bottomCells - r;
    for (let c = 0; c < cellCount; c++) {
      const cell = document.createElement('div');
      cell.classList.add('ladder-cell');
      cell.addEventListener('click', handleCellClick);
      row.appendChild(cell);

    }

    board.appendChild(row);
  }

  // Добавляем игрока слева снизу
const firstRow = board.querySelector('.ladder-row');
const firstCell = firstRow.querySelector('.ladder-cell');
const player = document.createElement('div');
player.className = 'player';
player.id = 'player';
board.appendChild(player);

// Позиционируем игрока слева внизу
const cellRect = firstCell.getBoundingClientRect();
const boardRect = board.getBoundingClientRect();
player.style.left = (firstCell.offsetLeft - 20) + 'px';
player.style.top = firstCell.offsetTop + 'px';


}

// Создание кнопок выбора камней
for (let i = 1; i <= 7; i++) {
  const btn = document.createElement('button');
  btn.className = 'stone-button' + (i === selectedStones ? ' active' : '');
  btn.textContent = i;
  btn.onclick = () => {
    selectedStones = i;
    document.querySelectorAll('.stone-button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    generateLadderBoard(); // обновляем поле
  };
  stoneContainer.appendChild(btn);
}

// 👇 Инициализация
generateLadderBoard();
</script>
