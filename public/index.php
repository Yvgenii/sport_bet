<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Пропуск кеша
?>

<?php
// Пример массива событий (можно заменить реальными данными из БД)
$events = [
    ['category' => 'Футбол', 'teams' => 'Ліверпуль vs Манчестер Сіті', 'time' => '20:00 Сьогодні'],
    ['category' => 'Футбол', 'teams' => 'Шахтер vs Оболонь', 'time' => '10:00 Завтра'],
    ['category' => 'Баскетбол', 'teams' => 'КТУ НАУ vs США', 'time' => '22:00 Сьогодні'],
    ['category' => 'Кіберспорт', 'teams' => 'Team Spirit vs NAVI', 'time' => '15:00 Сьогодні'],
    ['category' => 'Кіберспорт', 'teams' => 'FaZe Clan vs Astralis', 'time' => '19:00 Сьогодні'],
    ['category' => 'Волейбол', 'teams' => 'Греція vs Італія', 'time' => '20:00 Сьогодні'],
    ['category' => 'Футбол', 'teams' => 'Динамо Київ vs Заря', 'time' => '15:00 Сьогодні'],
    ['category' => 'Баскетбол', 'teams' => 'Лейкерс vs Бостон', 'time' => '18:00 Сьогодні'],
    ['category' => 'Теніс', 'teams' => 'Новак Джокович vs Рафаэль Надаль', 'time' => '16:00 Сьогодні'],
    ['category' => 'Волейбол', 'teams' => 'Україна vs США', 'time' => '17:00 Сьогодні'],
    ['category' => 'Баскетбол', 'teams' => 'Поколение чудес vs Сейрин', 'time' => '18:00 Сьогодні'],
    ['category' => 'Кіберспорт', 'teams' => 'Clan Piru vs Astralis', 'time' => '10:00 Завтра'],
];

$perPage = 6;
$totalPages = ceil(count($events) / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));

$start = ($page - 1) * $perPage;
$eventsOnPage = array_slice($events, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Букмекерская Контора</title>
    <link rel="stylesheet" href="styles.css">

</head>
<body class="light-theme">
    <!-- Остальной код страницы -->

   <div id="betModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeBetModal()">&times;</span>
        <h2>Зробити ставку</h2>
        <div class="bet-details">
            <p id="betDetails"></p>
        </div>
        <form id="betForm" class="bet-form">
            <div class="form-row">
                <label for="betAmount" class="bet-label">Сумма ставки:</label>
                <input type="number" id="betAmount" name="betAmount" placeholder="Введіть суму ставки" class="bet-input" min="1" required>
            </div>
            <button type="button" onclick="submitBet()" class="bet-submit">Підтвердити</button>
        </form>
    </div>
</div>


    <!-- Верхняя панель -->
    <header>
        <div class="top-bar">
            <div class="logo">
                <h1><a href="#" onclick="showHomePage()">Sport Bet</a></h1>
            </div>

            <!-- Верхняя навигационная панель -->
            <nav class="main-nav">
    <a href="rozdily/events.php">Спортивні події</a>
    <a href="rozdily/slots.php">Слоти</a>
    <a href="rozdily/mini-games.php">Міні ігри</a>
    <a href="rozdily/referral.php">Реферальна програма</a>
</nav>


<nav class="auth-links">
    <?php if (isset($_SESSION['user'])): ?>
        <!-- Пользователь вошел -->
        <span>Баланс: <?php echo htmlspecialchars($_SESSION['user']['balance']); ?>₴</span>
        <a href="profile.php">Профіль</a>
        <a href="logout.php">Вийти</a>
    <?php else: ?>
        <!-- Пользователь не вошел -->
        <a href="#" onclick="openLoginModal()">Вхід</a> | <a href="#" onclick="openRegisterModal()">Реєстрація</a>
    <?php endif; ?>
</nav>

        </div>
    </header>
    <!-- Главная страница -->
    <section id="home" class="main-content">
        <h2 id="welcome">Ласкаво просимо до Sport Bet</h2>
        <p id="betsText">Ставки на спорт із найкращими коефіцієнтами!</p>

        <!-- Блок с бонусом -->
<div class="bonus-container">
    <h1>ВІТАЛЬНИЙ БОНУС</h1>
    <p class="bonus-amount">до 5 000 ₴</p>
    <button id="claim-bonus" class="bonus-button">Забрать бонус</button>
</div>


        <!-- Секция популярных событий -->
        <section id="popular-events" class="events-section">
            <h2>Топ-події</h2>
            <div class="categories">
                <button class="category active" onclick="showTopEvent('all', this)">Все</button>
                <button class="category" onclick="showTopEvent('football', this)">Футбол</button>
                <button class="category" onclick="showTopEvent('basketball', this)">Баскетбол</button>
                <button class="category" onclick="showTopEvent('volleyball', this)">Волейбол</button>
                <button class="category" onclick="showTopEvent('tennis', this)">Теніс</button>
                <button class="category" onclick="showTopEvent('cybersport', this)">Кіберспорт</button>
            </div>


            <div class="events-grid">
            <?php foreach ($eventsOnPage as $event): ?>
                <div class="event-card">
                    <div class="event-header">
                        <span class="team"><?= htmlspecialchars($event['category']) ?></span>
                        <span class="time"><?= htmlspecialchars($event['time']) ?></span>
                    </div>
                    <div class="teams">
                        <span class="team"><?= htmlspecialchars($event['teams']) ?></span>
                    </div>
                    <div class="odds">
                        <button class="odd">1.8</button>
                        <button class="odd">2.3</button>
                        <button class="odd">3.0</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination" style="margin-top: 30px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
</section>
</section>


    <!-- Скрипт для переключения вида спорта и выделения кнопки -->
    <script>
        function showTopEvent(sport, button) {
            // Удаляем класс active у всех кнопок
            document.querySelectorAll('.category').forEach(btn => btn.classList.remove('active'));
            // Добавляем класс active к выбранной кнопке
            button.classList.add('active');

            const events = document.querySelectorAll('.event-card');
            // Скрываем все карточки
            events.forEach(event => event.style.display = 'none');

            // Показываем карточки в зависимости от выбранного вида спорта
            if (sport === 'all') {
                events.forEach(event => event.style.display = 'block');
            } else {
                const selectedEvents = document.querySelectorAll(`.${sport}`);
                if (selectedEvents.length > 0) {
                    selectedEvents.forEach(event => event.style.display = 'block');
                }
            }
        }
    </script>
    <!-- Добавьте этот блок перед закрывающим тегом body -->
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="styles.css">
    
</head>

       
<section id="news-ticker" class="news-section">
    <h2>Останні спортивні новини</h2>
    <div class="news-container">
        <div class="news-track">
            <div class="news-item">🎮 Євген Прайм зробив Рампагу на Венику.</div>
            <div class="news-item">⚽️ Ліверпуль переміг Манчестер Сіті з рахунком 3:1!</div>
            <div class="news-item">🎮 Тренер Team Spirit Денчик Небезпечний знову розповідає яких ідіотів набрали в команду</div>
            <div class="news-item">🏀 Лейкерс обіграли Бостон у напруженій грі 102:99.</div>
            <div class="news-item">🎾 Новак Джокович виграв ATP турнір у Лондоні.</div>
            <div class="news-item">🏒 СКА розгромив Динамо з рахунком 5:2 у КХЛ.</div>
            <div class="news-item">🎮 Воскреслав знову робить мінус 10 на гіракоптері</div>

        </div>
    </div>
</section>




    <section id="football-matches" class="matches-section" style="display: none;">
    <h2>Футбольніматчі з коефіцієнтами</h2>
     <div class="matches-container">
        <div class="match">
            <h3>Блю лок vs Cборной Японии</h3>
            <p>Дата: 20 ноября 2024</p>
            <p>Коефіцієнти: П1 - 1.80, Н - 3.60, П2 - 4.00</p>
            <button>Зробити ставку</button>
        </div>
        <div class="match">
            <h3>Динамо Київ vs Шахтер Донецк</h3>
            <p>Дата: 22 ноября 2024</p>
            <p>Коефіцієнти: П1 - 2.50, Н - 3.20, П2 - 2.80</p>
            <button>Зробити ставку</button>
        </div>
        <div class="match">
            <h3>Ліверпуль vs Манчестер Сіті</h3>
            <p>Дата: 25 ноября 2024</p>
            <p>Коефіцієнти: П1 - 2.70, Н - 3.50, П2 - 2.50</p>
            <button>Зробити ставку</button>
        </div>
        <div class="match">
            <h3>Реал Мадрид vs Барселона</h3>
                <p>Дата: 28 ноября 2024</p>
                <p>Коефіцієнти: П1 - 2.30, Н - 3.40, П2 - 2.90</p>
                <button>Зробити ставку</button>
        </div>
        <div class="match">
                <h3>ПСЖ vs Марсель</h3>
                <p>Дата: 30 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.60, Н - 3.80, П2 - 5.00</p>
                <button>Зробити ставку</button>
        </div>
     </div>
    </section>


    <section id="basketball-matches" class="matches-section" style="display: none;">
        <h2>Баскетбольніматчі з коефіцієнтами</h2>
        <div class="matches-container">
            <!-- Добавлено 12 матчей для баскетбола -->
            <div class="match">
                <h3>Поколение Чудес vs Старшая Школа Сейрин </h3>
                <p>Дата: 12 сентября 2024</p>
                <p>Коефіцієнти: П1 - 1.80, П2 - 2.10</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Лос-Анджелес Лейкерс vs Бостон Селтикс</h3>
                <p>Дата: 21 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.90, П2 - 2.10</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Бруклин Нетс vs Милуоки Бакс</h3>
                <p>Дата: 23 ноября 2024</p>
                <p>Коефіцієнти: П1 - 2.20, П2 - 1.80</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Голден Стэйт Уорриорз vs Майами Хит</h3>
                <p>Дата: 26 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.70, П2 - 2.30</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Чикаго Буллз vs Нью-Йорк Никс</h3>
                <p>Дата: 29 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.85, П2 - 2.15</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Филадельфия Севенти Сиксерс vs Торонто Рэпторс</h3>
                <p>Дата: 2 декабря 2024</p>
                <p>Коефіцієнти: П1 - 1.75, П2 - 2.25</p>
                <button>Зробити ставку</button>
            </div>
            <!-- Повторите блок еще 11 раз с разными данными -->
        </div>
    </section>

    <section id="volleyball-matches" class="matches-section" style="display: none;">
        <h2>Волейбольні матчі з коефіцієнтами</h2>
        <div class="matches-container">
            <!-- Добавлено 12 матчей для волейбола -->
            <div class="match">
                <h3>Урал Уфа vs Чикаго Буллз</h3>
                <p>Дата: 15 сентября 2024</p>
                <p>Коефіцієнти: П1 - 2.20, П2 - 1.80</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Динамо Київ vs Локомотив Новосибирск</h3>
                <p>Дата: 23 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.80, П2 - 2.20</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Зенит Казань vs Урал Уфа</h3>
                <p>Дата: 25 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.66, П2 - 2.40</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>zxcursed vs shadowraze</h3>
                <p>Дата: 23 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.60, П2 - 2.90</p>
                <button>Зробити ставку</button>
            </div><div class="match">
                <h3>Зенит Казань vs Чикаго</h3>
                <p>Дата: 25 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.60, П2 - 2.40</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
                <h3>Зенит  vs Казань </h3>
                <p>Дата: 25 ноября 2024</p>
                <p>Коефіцієнти: П1 - 1.60, П2 - 2.40</p>
                <button>Зробити ставку</button>
            </div>
            <!-- Повторите блок еще 11 раз с разными данными -->
        </div>
    </section>

    <section id="tennis-matches" class="matches-section" style="display: none;">
        <h2>Тенісні матчі з коефіцієнтами</h2>
        <div class="matches-container">

            <div class="match">
                <h3>Евгений Прайм vs Денис Омельченко</h3>
                <p>Дата: 18 сентября 2024</p>
                <p>Коефіцієнти: П1 - 1.70, П2 - 2.20</p>
                <button>Зробити ставку</button>
            </div>
            <div class="match">
            <h3>Новак Джокович vs Рафаэль Надаль</h3>
            <p>Дата: 20 ноября 2024</p>
            <p>Коефіцієнти: П1 - 1.85, П2 - 2.10</p>
            <button>Зробити ставку</button>
            </div>
        <div class="match">
            <h3>Даниил Медведев vs Александр Зверев</h3>
            <p>Дата: 26 ноября 2024</p>
            <p>Коефіцієнти: П1 - 1.95, П2 - 1.90</p>
            <button>Зробити ставку</button>
        </div>
         <div class="match">
            <h3>Потеряко Арсен vs Гордін Александр</h3>
            <p>Дата: 24 ноября 2024</p>
            <p>Коефіцієнти: П1 - 1.85, П2 - 2.00</p>
            <button>Зробити ставку</button>
            </div>
             <div class="match">
            <h3>Артем Апутевіч vs Єлдінов Дмитро</h3>
            <p>Дата: 26 ноября 2024</p>
            <p>Коефіцієнти: П1 - 1.65, П2 - 2.40</p>
            <button>Зробити ставку</button>
            </div>

        </div>
    </section>

    <section id="cybersport-matches" class="matches-section" style="display: none;">
        <h2>Кіберспортивні матчі з коефіцієнтами</h2>
        <div class="matches-container">

            <div class="match">
                <h3>United Dream vs Team Spirit Liquid </h3>
                <p>Дата: 2 декобря 2024</p>
                <p>Коефіцієнти: П1 - 1.50, П2 - 2.50</p>
                <button>Зробити ставку</button>
            </div>
                        <div class="match">
                <h3>United Dream vs Team Spirit Liquid </h3>
                <p>Дата: 4 декобря 2024</p>
                <p>Коефіцієнти: П1 - 1.50, П2 - 2.50</p>
                <button>Зробити ставку</button>
            </div>
                        <div class="match">
                <h3>United Dream vs Team Spirit Liquid </h3>
                <p>Дата: 5 декобря 2024</p>
                <p>Коефіцієнти: П1 - 1.50, П2 - 2.50</p>
                <button>Зробити ставку</button>
            </div>
                        <div class="match">
                <h3>United Dream vs Team Spirit Liquid </h3>
                <p>Дата: 7 декобря 2024</p>
                <p>Коефіцієнти: П1 - 1.50, П2 - 2.50</p>
                <button>Зробити ставку</button>
            </div>
                        <div class="match">
                <h3>United Dream vs Team Spirit Liquid </h3>
                <p>Дата: 13 декобря 2024</p>
                <p>Коефіцієнти: П1 - 1.50, П2 - 2.50</p>
                <button>Зробити ставку</button>
            </div>

        </div>
    </section>

    <!-- Модальные окна для входа и регистрации -->
    <div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Вход</h2>
        <form action="login.php" method="POST">
            <div class="form-row">
                <label for="username">Ім'я користувача:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-row">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-row">
                <button type="submit">Увійти</button>
            </div>
        </form>
    </div>
</div>


    <div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRegisterModal()">&times;</span>
        <h2>Регистрация</h2>
        <form action="register.php" method="POST">
            <div class="form-row">
                <label for="reg-username">Ім'я користувача:</label>
                <input type="text" id="reg-username" name="reg-username" required>
            </div>
            <div class="form-row">
                <label for="reg-password">Пароль:</label>
                <input type="password" id="reg-password" name="reg-password" required>
            </div>
            <div class="form-row">
                <label for="phone">Номер телефону:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-row" >
                <button type="submit">Регистрация</button>
            </div>
            

            <div class="alrReg">
            <p class="switch-login">Вже маете акаунт? <a href="#" onclick="switchToLogin()">Увійти</a></p>
            </div>
        </form>
    </div>
</div>


    <!-- Подвал сайта -->
    <footer>
        <p>&copy; 2025 Sport Bet. Усі права захищені.</p>
    </footer>



    <!-- Скрипты для переключения страниц и управления темой -->
    <script>


    function toggleTheme() {
        const body = document.body;
        if (body.classList.contains('light-theme')) {
            body.classList.remove('light-theme');
            body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.remove('dark-theme');
            body.classList.add('light-theme');
            localStorage.setItem('theme', 'light');
        }
    }

    // Устанавливаем тему при загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(savedTheme);
    });



        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
            closeRegisterModal();
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        function openRegisterModal() {
            document.getElementById('registerModal').style.display = 'block';
            closeLoginModal();
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').style.display = 'none';
        }

        function switchToLogin() {
            closeRegisterModal();
            openLoginModal();
        }

        function showHomePage() {
            document.getElementById('home').style.display = 'block';
            document.getElementById('football-matches').style.display = 'none';
            document.getElementById('basketball-matches').style.display = 'none';
            document.getElementById('volleyball-matches').style.display = 'none';
            document.getElementById('tennis-matches').style.display = 'none';
            document.getElementById('cybersport-matches').style.display = 'none';
        }

        function showFootballMatches() {
            showHomePage();
            document.getElementById('home').style.display = 'none';
            document.getElementById('football-matches').style.display = 'block';
        }

        function showBasketballMatches() {
            showHomePage();
            document.getElementById('home').style.display = 'none';
            document.getElementById('basketball-matches').style.display = 'block';
        }

        function showVolleyballMatches() {
            showHomePage();
            document.getElementById('home').style.display = 'none';
            document.getElementById('volleyball-matches').style.display = 'block';
        }

        function showTennisMatches() {
            showHomePage();
            document.getElementById('home').style.display = 'none';
            document.getElementById('tennis-matches').style.display = 'block';
        }

        function showCyberSportMatches() {
            showHomePage();
            document.getElementById('home').style.display = 'none';
            document.getElementById('cybersport-matches').style.display = 'block';
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('loginModal')) {
                closeLoginModal();
            }
            if (event.target === document.getElementById('registerModal')) {
                closeRegisterModal();
            }
        }
    </script>
    <script>
    const newsTrack = document.querySelector('.news-track');
    newsTrack.addEventListener('mouseenter', () => {
        newsTrack.style.animationPlayState = 'paused';
    });
    newsTrack.addEventListener('mouseleave', () => {
        newsTrack.style.animationPlayState = 'running';
    });
</script>
     <script>
    document.getElementById('claim-bonus').addEventListener('click', function () {
        fetch('/sport_bet/Private/bonus.php', { method: 'POST' })

        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const balanceElement = document.querySelector('.auth-links span');
                balanceElement.textContent = `Баланс: ${data.newBalance}₴`;
                alert('Бонус успешно добавлен!');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Ошибка при добавлении бонуса. Попробуйте снова.');
            console.error('Error:', error);
        });
    });
</script>
    <script>
    let currentBet = {};

    // Функция для открытия модального окна
    function openBetModal(eventName, betType, odds) {
        currentBet = { event: eventName, type: betType, odds: odds };
        document.getElementById('betDetails').innerText = `Событие: ${eventName}, Тип: ${betType}, Коэффициент: ${odds}`;
        document.getElementById('betModal').style.display = 'block';
    }

    // Функция для закрытия модального окна
    function closeBetModal() {
        document.getElementById('betModal').style.display = 'none';
    }

    // Функция для отправки ставки на сервер
  // Функция для отправки ставки на сервер
function submitBet() {
    const amount = document.getElementById('betAmount').value;

    if (amount <= 0) {
        alert('Введіть правильну суму ставки!');
        return;
    }

    console.log("Отправляем данные:", { ...currentBet, amount }); // Лог данных для проверки

    fetch('../Private/place_bet.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            ...currentBet,
            amount: amount,
        }),
    })
        .then(response => response.json())
        .then(data => {
            console.log("Ответ от сервера:", data); // Лог ответа от сервера

            if (data.success) {
                alert(`Ставка успішно зроблена! Можливий виграш: ${data.potentialWin} ₴`);
                closeBetModal();
                // Обновление баланса в интерфейсе
                const balanceElement = document.querySelector('.auth-links span');
                balanceElement.textContent = `Баланс: ${data.newBalance} ₴`;
            } else {
                alert(`Ошибка: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Помилка під час виконання запиту:', error);
            alert('Не вдалось зробити ставку. Спробуйте пізніше.');
        });
}


// Дополнительная функция для добавления ставки в историю
function addBetToHistory(eventName, betType, amount, potentialWin) {
    // Предполагается, что в DOM есть контейнер для истории ставок
    const historyContainer = document.getElementById('betHistory');
    if (!historyContainer) return;

    const betItem = document.createElement('div');
    betItem.className = 'bet-item';
    betItem.innerHTML = `
        <div><strong>Подія:</strong> ${eventName}</div>
        <div><strong>Тип:</strong> ${betType}</div>
        <div><strong>Ставка:</strong> ${amount}₴</div>
        <div><strong>Потенційний виграш:</strong> ${potentialWin}₴</div>
        <hr>
    `;

    historyContainer.prepend(betItem); // Добавляет новую ставку в начало списка
}


    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        const modal = document.getElementById('betModal');
        if (event.target === modal) {
            closeBetModal();
        }
    }
</script>

<!-- Кнопка переключения темы -->
<button id="theme-toggle" onclick="toggleTheme()">🌓Змінити тему</button>
<script>
  function toggleTheme() {
    const body = document.body;
    if (body.classList.contains('dark-theme')) {
      body.classList.remove('dark-theme');
      body.classList.add('light-theme');
      localStorage.setItem('theme', 'light');
    } else {
      body.classList.remove('light-theme');
      body.classList.add('dark-theme');
      localStorage.setItem('theme', 'dark');
    }
  }

  // Загружаем тему при открытии страницы
  document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.classList.add(savedTheme);
  });
</script>


</body>
</html>