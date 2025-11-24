<?php
require_once '../config/database.php';
$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM concerts 
-- WHERE date >= CURDATE() ORDER BY date
";
$stmt = $connection->prepare($query);
$stmt->execute();
$concerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="concerts.css">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title>Рок-группа МАСТЕР</title>
</head>
<body>
   <header>
       <h1 style="text-align: center;">Расписание концертов МАСТЕР</h1>
    </header>

   <main>
       <h2 style="text-align: center;">Расписание концертов</h2>

    <div class="concerts-container">
        <?php if (empty($concerts)): ?>
            <div class="no-concerts">
                <h3>На данный момент запланированных концертов нет</h3>
                <p>Следите за обновлениями!</p>
            </div>
        <?php else: ?>
            <table class="concerts-table">
                <thead>
                    <tr>
                        <th>Город</th>
                        <th>Дата</th>
                        <th>Место проведения</th>
                        <th>Цена на билет</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($concerts as $concert): ?>
                    <tr>
                        <td data-label="Город"><?php echo htmlspecialchars($concert['city']); ?></td>
                        <td data-label="Дата"><?php echo date('d.m.Y', strtotime($concert['date'])); ?></td>
                        <td data-label="Место проведения"><?php echo htmlspecialchars($concert['venue']); ?></td>
                        <td data-label="Цена на билет"><?php echo number_format($concert['price'], 0, '', ' '); ?> руб.</td>
                        <td data-label="Действия">
                            <button class="buy-btn" onclick="showAlert('<?php echo htmlspecialchars($concert['city']); ?>', '<?php echo date('d.m.Y', strtotime($concert['date'])); ?>')">
                                Купить
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
   </main>

    <footer>
         <p>&copy; <?php echo date('Y'); ?> Рок-группа МАСТЕР. Все права защищены.</p>
     </footer>

    <script>
        function showAlert(city, date) {
            alert(`Спасибо за интерес к концерту в ${city} ${date}! Для покупки билетов свяжитесь с нами по телефону или через социальные сети.`);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.concerts-table tbody tr');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = "1";
                            entry.target.style.transform = "translateX(0)";
                        }, index * 100);
                    }
                });
            }, { threshold: 0.1 });

            rows.forEach(row => {
                row.style.opacity = "0";
                row.style.transform = "translateX(-20px)";
                row.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(row);
            });
        });
    </script>
    <script src="../navigation.js"></script>
</body>
</html>