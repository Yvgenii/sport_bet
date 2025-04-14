<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php"); // Перенаправление, если пользователь не вошел
    exit();
}

// Данные пользователя из сессии
$userId = (int)$_SESSION['user']['id'];
$balance = (float)$_SESSION['user']['balance'];
$username = htmlspecialchars($_SESSION['user']['username']);
$role = htmlspecialchars($_SESSION['user']['role']); // Получаем роль пользователя

require '../Private/db.php';

try {
    // Получение данных пользователя из базы
    $stmt = $pdo->prepare("SELECT username, phone, created_at, balance FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Получение истории ставок
    $stmt = $pdo->prepare("SELECT event_name, bet_type, amount, potential_win, created_at
                           FROM bets
                           WHERE user_id = :user_id
                           ORDER BY created_at DESC");
    $stmt->execute(['user_id' => $userId]);
    $bets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("<p>Ошибка: " . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профіль користувача</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .profile-details, .bet-history {
            margin-bottom: 20px;
        }
        .profile-details table, .bet-history table {
            width: 100%;
            border-collapse: collapse;
        }
        .profile-details th, .profile-details td, .bet-history th, .bet-history td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .profile-details th, .bet-history th {
            background: #333;
            color: #fff;
        }
        .edit-button, .admin-button, .back-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #007BFF;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .edit-button:hover, .admin-button:hover, .back-button:hover {
            background: #0056b3;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="profile-container">
    <div class="profile-header">
        <h1>Ласкаво просимо <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <p>Дата реєстрації: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
    </div>

    <div class="profile-details">
        <h2>Ваші дані:</h2>
        <table>
            <tr>
                <th>Ім'я користувача</th>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
            </tr>
            <tr>
                <th>Телефон</th>
                <td><?php echo htmlspecialchars($user['phone'] ?? 'Не вказано'); ?></td>
            </tr>
            <tr>
            <th>Введений реферальний код</th>
<td>
  <?php
    if (!empty($user['referred_by'])) {
        echo htmlspecialchars($user['referred_by']);
    } else {
        echo 'Немає коду';
    }
  ?>
</td>


            <tr>
                <th>Баланс</th>
                <td><?php echo htmlspecialchars($user['balance']); ?> ₴</td>
            </tr>
        </table>
        <button class="edit-button" onclick="openEditProfileModal()">Редагувати дані</button>
        </div>

    <!-- Добавляем кнопку для администратора -->
    <?php if ($role === 'admin'): ?>
        <a href="admin.php" class="admin-button">Перейти до панелі адміністратора</a>
    <?php endif; ?>

    <div class="bet-history">
        <h2>Історія ставок:</h2>
        <?php if ($bets): ?>
            <table>
                <tr>
                    <th>Подія</th>
                    <th>Тип ставки</th>
                    <th>Сума</th>
                    <th>Потенційний виграш</th>
                    <th>Дата</th>
                </tr>
                <?php foreach ($bets as $bet): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bet['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($bet['bet_type']); ?></td>
                        <td><?php echo htmlspecialchars($bet['amount']); ?> ₴</td>
                        <td><?php echo htmlspecialchars($bet['potential_win']); ?> ₴</td>
                        <td><?php echo date('d.m.Y H:i', strtotime($bet['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Ви ще не зробили жодної ставки.</p>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" class="back-button">Повернутися на головну</a>
    </div>
</div>


<!-- Модальне вікно -->
<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditProfileModal()">&times;</span>
        <h2>Редагування профілю</h2>
        <form id="editProfileForm">
            <div class="form-group">
                <label>Ім'я користувача:</label>
                <input type="text" name="username" id="editUsername" required>
            </div>

            <div class="form-group">
                <label>Телефон:</label>
                <input type="text" name="phone" id="editPhone">
            </div>

            <button type="button" onclick="saveProfileChanges()" class="btn">Зберегти</button>
        </form>
    </div>
</div>

<script>
function openEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'flex';

    // Заполняем поля текущими значениями из профиля
    document.getElementById('editUsername').value = "<?php echo htmlspecialchars($user['username']); ?>";
    document.getElementById('editPhone').value = "<?php echo htmlspecialchars($user['phone']); ?>";
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'none';
}

// Закрытие при клике вне модального окна
window.onclick = function(event) {
    let modal = document.getElementById('editProfileModal');
    if (event.target === modal) {
        closeEditProfileModal();
    }
};

// Функция сохранения изменений (AJAX-запрос)
function saveProfileChanges() {
    let username = document.getElementById('editUsername').value;
    let phone = document.getElementById('editPhone').value;

    fetch('update_profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username: username, phone: phone })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Дані успішно оновлено!");
            location.reload(); // Перезагрузка страницы для обновления данных
        } else {
            alert("Помилка: " + data.message);
        }
    })
    .catch(error => console.error('Помилка:', error));
}

</script>


</body>
</html>
