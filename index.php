<?php
require_once 'config/database.php';
$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM band_members ORDER BY id";
$stmt = $connection->prepare($query);
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title>Рок-группа МАСТЕР</title>
</head>
<body>
    <header>
        <h1 style="text-align: center;">Рок-группа МАСТЕР</h1>
    </header>

    <main>
        <h1 style="text-align: center;">О группе</h1>
        <h2 style="text-align: center;">Дата создания: 1989<br><br>Страна: Россия</h2>
        <h3 style="text-align: center;">Состав группы:</h3><br>
        <ul class="band-members">
            <?php foreach ($members as $member): ?>
            <li>
                <div>
                    <img src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                    <p><?php echo htmlspecialchars($member['name']); ?></p>
                    <p><?php echo htmlspecialchars($member['role']); ?></p>
                    <p><?php echo htmlspecialchars($member['years_active']); ?></p>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </main>

    <section>
        <div>
            <h1>От «Арии» до мировой славы: эволюция группы «Мастер»</h1>
            Рок-группа из Москвы, играющая в жанрах трэш-метал и хеви-метал. Основана в 1986 году Аликом Грановским и Андреем Большаковым после их ухода из группы «Ария».
            <p>Ключевые этапы развития группы:</p>
            <ul>
                <li>1987 год - выход первого альбома «Мастер» тиражом более миллиона экземпляров</li>
                <li>1989 год - релиз альбома «С петлёй на шее» (тираж вдвое больше первого альбома)</li>
                <li>1991-1995 - период работы над англоязычными альбомами, включая «Talk of the Devil»</li>
                <li>1996-2000 - выход альбома «Песни мёртвых», смена вокалиста</li>
                <li>2000 год - релиз альбома «Лабиринт» с новым вокалистом Алексеем Кравченко (Lexx)</li>
            </ul>
            За время существования группа пережила множество изменений в составе, но всегда сохраняла свою уникальность благодаря лидеру и бессменному участнику Алику Грановскому. Тематика текстов «Мастера» изначально носила остросоциальный характер, что способствовало быстрой популярности группы в перестроечные годы.

            <p>Группа активно гастролировала как по СССР/России, так и за рубежом, участвовала в крупных фестивалях, включая «Монстры рока СССР».</p>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Рок-группа МАСТЕР. Все права защищены.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.band-members li').forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(card);
            });
        });
    </script>
    <script src="../navigation.js"></script>
</body>
</html>