<?php
require_once '../config/database.php';
$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM albums ORDER BY release_year";
$stmt = $connection->prepare($query);
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="albums.css">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title>Рок-группа МАСТЕР</title>
</head>
<body>
    <header>
        <h1 style="text-align: center;">Альбомы МАСТЕР</h1>
    </header>

    <main>
        <h2 style="text-align: center;">Музыкальные альбомы</h2>

        <div class="album-carousel">
            <?php foreach ($albums as $album): ?>
            <div class="album" onclick="loadAlbum(<?php echo $album['id']; ?>)">
                <div class="album-image">
                    <img src="<?php echo htmlspecialchars($album['image_path']); ?>" alt="<?php echo htmlspecialchars($album['title']); ?>">
                </div>
                <p><?php echo htmlspecialchars($album['title']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p style="text-align: center;">&copy; <?php echo date('Y'); ?> Рок-группа МАСТЕР. Все права защищены.</p>
    </footer>

    <script>
        function loadAlbum(albumId) {
            sessionStorage.setItem('selectedAlbumId', albumId);
            window.location.href = 'album.php?id=' + albumId;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const albums = document.querySelectorAll('.album');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = "1";
                            entry.target.style.transform = "translateY(0)";
                        }, index * 100);
                    }
                });
            }, { threshold: 0.1 });

            albums.forEach(album => {
                album.style.opacity = "0";
                album.style.transform = "translateY(30px)";
                album.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(album);
            });
        });
    </script>
    <script src="../navigation.js"></script>
</body>
</html>