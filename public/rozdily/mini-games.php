<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Міні ігри</title>
  <style>
.fixed-home-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #e60073;
  color: white;
  padding: 10px 20px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
  font-size: 16px;
  opacity: 0.6;
  transition: opacity 0.3s ease, box-shadow 0.3s ease;
  z-index: 9999;
}

.fixed-home-button:hover {
  opacity: 1;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

    body {
      background-color: #f9f9f9;
      font-family: Arial, sans-serif;
      padding: 20px;
      margin: 0;
    }

    h1 {
      text-align: center;
      color: #333;
    }

    .games-menu {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 15px;
      margin: 30px 0;
    }

    .game-button {
      padding: 10px 20px;
      background-color: #e60073;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.2s;
    }

    .game-button:hover,
    .game-button.active {
      background-color: #cc005f;
    }

    #game-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      min-height: 150px;
      font-size: 18px;
      text-align: center;
    }
  </style>
      <!-- Верхняя панель -->
      <header>
        <div class="top-bar">
            <div class="logo">
                <h1><a href="../index.php">Sport Bet</a>
                </h1>
            </div>

            <!-- Верхняя навигационная панель -->
            <nav class="main-nav">
            <a href="../rozdily/events.php">Спортивні події</a>
            <a href="../rozdily/slots.php">Слоти</a>
            <a href="../rozdily/mini-games.php">Міні ігри</a>
            <a href="../rozdily/referral.php">Реферальна програма</a>
</nav>


<nav class="auth-links">
    <?php if (isset($_SESSION['user'])): ?>
        <!-- Пользователь вошел -->
        <span>Баланс: <?php echo htmlspecialchars($_SESSION['user']['balance']); ?>₴</span>
        <a href="../profile.php">Профіль</a>
        <a href="../logout.php">Вийти</a>
    <?php else: ?>
        <!-- Пользователь не вошел -->
        <a href="#" onclick="openLoginModal()">Вхід</a> | <a href="#" onclick="openRegisterModal()">Реєстрація</a>
    <?php endif; ?>
</nav>

        </div>
    </header>
    <link rel="stylesheet" href="../styles.css">

</head>
<body>

<h1>🎮 Міні ігри</h1>

<div class="games-menu">
  <button class="game-button" onclick="loadGame('airplane', this)">✈️ Самолетик</button>
  <button class="game-button" onclick="loadGame('coin', this)">🪙 Монетка</button>
  <button class="game-button" onclick="loadGame('roulette', this)">🎡 Рулетка</button>
  <button class="game-button" onclick="loadGame('guess', this)">🔢 Угадай число</button>
  <button class="game-button" onclick="loadGame('stairs', this)">🪜 Лесенка</button> //УБРАТЬ!!!
</div>

<div id="game-container">
  <p>Оберіть гру, щоб почати грати.</p>
</div>

<a href="../index.php" class="fixed-home-button">🏠 Повернутися на головну</a>

<script>
function loadGame(game, button = null) {
  localStorage.setItem('lastGame', game); // запоминаем игру

  fetch(`games/${game}.php`)
    .then(res => res.text())
    .then(html => {
      const container = document.getElementById('game-container');
      container.innerHTML = html;

      // Удаляем старые .active
      document.querySelectorAll('.game-button').forEach(btn => btn.classList.remove('active'));
      if (button) button.classList.add('active');

      // Подключаем скрипты из загруженного HTML (если есть)
      const scripts = container.querySelectorAll('script');
      scripts.forEach(script => {
        const newScript = document.createElement('script');
        if (script.src) {
          newScript.src = script.src;
        } else {
          newScript.textContent = script.textContent;
        }
        document.body.appendChild(newScript);
        script.remove();
      });
    })
    .catch(() => {
      document.getElementById('game-container').innerHTML = '<p style="color:red;">Помилка завантаження гри.</p>';
    });
}

// Загружаем последнюю открытую игру (если была)
const last = localStorage.getItem('lastGame');
if (last) {
  setTimeout(() => {
    const btn = document.querySelector(`.game-button[onclick*="${last}"]`);
    loadGame(last, btn);
  }, 100); // даём странице подгрузиться
}
</script>



</body>
</html>
