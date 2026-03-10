<?php
session_start();
require_once __DIR__ . '/../../Private/db.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT referral_code, referred_by FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$referralCode = $user['referral_code'] ?? 'N/A';
$referredByCode = $user['referred_by'] ?? null;

$referrals = [];
$refCount = 0;
$totalBonus = 0;

if ($referralCode) {
    $stmt = $pdo->prepare("SELECT username, created_at FROM users WHERE referred_by = :referral_code");
    $stmt->execute(['referral_code' => $referralCode]);
    $referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $refCount = count($referrals);
    $totalBonus = $refCount * 1000;
}

$referrerName = null;
if ($referredByCode) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE referral_code = :code");
    $stmt->execute(['code' => $referredByCode]);
    $referrer = $stmt->fetch(PDO::FETCH_ASSOC);
    $referrerName = $referrer['username'] ?? null;
}

$canEnterCode = !$user['referred_by']; // если пользователь не вводил промокод
$codeSubmitMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enter_referral_code'])) {
    $enteredCode = trim($_POST['enter_referral_code']);

    // Проверим, существует ли код и он не наш
    $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = :code AND id != :my_id");
    $stmt->execute(['code' => $enteredCode, 'my_id' => $userId]);
    $referrer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($referrer) {
        // Обновляем нашу запись — кто пригласил и бонус
        $stmt = $pdo->prepare("UPDATE users SET referred_by = :code, balance = balance + 1000 WHERE id = :my_id");
        $stmt->execute(['code' => $enteredCode, 'my_id' => $userId]);

        // Рефереру бонус
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + 1000 WHERE id = :ref_id");
        $stmt->execute(['ref_id' => $referrer['id']]);

        // Обновим в сессии и переменных
        $_SESSION['user']['balance'] += 1000;
        $user['referred_by'] = $enteredCode;
        $canEnterCode = false;
        $codeSubmitMessage = "🎉 Бонус нараховано! Ви ввели код: $enteredCode";
    } else {
        $codeSubmitMessage = "❌ Невірний код або це ваш власний код.";
    }
}


?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Реферальна програма</title>
  <link rel="stylesheet" href="../styles.css">
  
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;

    }
    .referral-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .referral-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .referral-code {
      text-align: center;
      font-size: 22px;
      margin-bottom: 20px;
    }
    .referral-code span {
      font-weight: bold;
      color: #e60073;
    }
    .referral-bonus {
      display: flex;
      justify-content: space-around;
      background: #f1f1f1;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
    }
    .referral-step {
      text-align: center;
      margin-bottom: 30px;
    }
    .referral-step h3 {
      margin-bottom: 10px;
      color: #333;
    }
    .user-list {
      margin-top: 20px;
    }
    .user-list table {
      width: 100%;
      border-collapse: collapse;
    }
    .user-list th, .user-list td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
    }
    .user-list th {
      background-color: #eee;
    }
    .referred-by {
      margin-top: 20px;
      background: #e7f6ff;
      padding: 15px;
      border-left: 5px solid #2196F3;
    }
    .copy-btn {
      display: inline-block;
      padding: 5px 10px;
      background: #e60073;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 10px;
    }
    .referral-step input {
  padding: 8px;
  width: 250px;
  margin-right: 10px;
  border-radius: 4px;
  border: 1px solid #ccc;
}
.referral-step button {
  padding: 8px 15px;
  background: #e60073;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.back-home-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #e60073;
  color: white;
  padding: 10px 16px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: background 0.3s;
  z-index: 999;
}

.back-home-btn:hover {
  background-color: #c20064;
}

.step-grid {
  display: flex;
  gap: 20px;
  justify-content: space-between;
  margin-top: 30px;
  flex-wrap: nowrap; /* чтобы все шаги были в один ряд */
}

.step-box {
  background: #f1f1f1;
  border-radius: 10px;
  padding: 20px;
  flex: 1;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  transition: transform 0.3s;
}

.step-box:hover {
  transform: translateY(-5px);
}

.step-box img {
  max-width: 100px; /* делаем изображение больше */
  margin-bottom: 15px;
}

.step-box h4 {
  margin-bottom: 10px;
}


  </style>
</head>
<body>

<header>
        <div class="top-bar">
            <div class="logo">
                <h1><a href="../index.php" onclick="showHomePage()">Sport Bet</a></h1>
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



  <div class="referral-container">
    <div class="referral-header">
      <h1>🎁 Реферальна програма</h1>
      <p>Запрошуй друзів і отримуй бонуси!</p>
    </div>

    <div class="referral-code">
      Твій реферальний код: <span id="myCode"><?= htmlspecialchars($referralCode) ?></span>
      <button class="copy-btn" onclick="copyCode()">Скопіювати</button>
    </div>

    <div class="referral-bonus">
      <div>
        🤑 <strong>Ти отримуєш:</strong><br>+1000 на баланс
      </div>
      <div>
        🎁 <strong>Друг отримує:</strong><br>+1000 на старт
      </div>
    </div>

    <div class="referral-step">
  <h3>Як це працює?</h3>
  <div class="step-grid">
    <div class="step-box">
      <img src="../images/step1.png" alt="Крок 1">
      <h4>1. Запроси друзів</h4>
      <p>Поділись своїм кодом з друзями або в соцмережах.</p>
    </div>
    <div class="step-box">
      <img src="../images/step2.png" alt="Крок 2">
      <h4>2. Вони реєструються</h4>
      <p>Твої друзі вводять твій код під час реєстрації.</p>
    </div>
    <div class="step-box">
      <img src="../images/step3.png" alt="Крок 3">
      <h4>3. Отримайте бонуси</h4>
      <p>Ти та твій друг одразу отримуєте по 1000!</p>
    </div>
  </div>
</div>

    <?php if ($canEnterCode): ?>
  <div class="referral-step">
    <h3>🔑 Введіть реферальний код</h3>
    <form method="POST">
      <input type="text" name="enter_referral_code" placeholder="Наприклад: REF123456" required>
      <button type="submit">Отримати бонус</button>
    </form>
  </div>
<?php endif; ?>

<?php if ($codeSubmitMessage): ?>
  <div class="referral-step" style="color: green; font-weight: bold;">
    <?= htmlspecialchars($codeSubmitMessage) ?>
  </div>
<?php endif; ?>

    <div class="referral-step">
      <h3>📊 Статистика</h3>
      <p>Запрошено користувачів: <strong><?= $refCount ?></strong></p>
      <p>Отримано бонусів: <strong><?= $totalBonus ?></strong></p>
    </div>

    <?php if ($refCount > 0): ?>
    <div class="user-list">
      <h3>Зареєстровані за твоїм кодом</h3>
      <table>
        <tr>
          <th>Користувач</th>
          <th>Дата реєстрації</th>
          <th>Бонус</th>
        </tr>
        <?php foreach ($referrals as $ref): ?>
        <tr>
          <td><?= htmlspecialchars($ref['username']) ?></td>
          <td><?= htmlspecialchars($ref['created_at']) ?></td>
          <td>+1000</td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <?php else: ?>
    <div class="user-list">
      <h3>Зареєстровані за твоїм кодом</h3>
      <p>Ніхто ще не зареєструвався за вашим кодом.</p>
    </div>
    <?php endif; ?>

    <?php if ($referredByCode && $referrerName): ?>
    <div class="referred-by">
      Ви ввели промокод: <strong><?= htmlspecialchars($referredByCode) ?></strong><br>
      Вас запросив: <strong><?= htmlspecialchars($referrerName) ?></strong>
    </div>
    <?php endif; ?>
  </div>

<script>
function copyCode() {
  const code = document.getElementById('myCode').innerText;
  navigator.clipboard.writeText(code).then(() => {
    alert('Код скопійовано: ' + code);
  });
}
</script>
<a href="../index.php" class="back-home-btn">🏠 Повернутися на головну</a>

</body>
</html>


