<?php
require_once '../config/database.php';
$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM gallery ORDER BY category, id";
$stmt = $connection->prepare($query);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedImages = [];
foreach ($images as $image) {
    $groupedImages[$image['category']][] = $image;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="gallery.css">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title>Рок-группа МАСТЕР</title>
</head>
<body>
    <header>
        <h1 style="text-align: center;">Галерея МАСТЕР</h1>
    </header>

    <main>
        <h2>Галерея</h2>

        <?php foreach ($groupedImages as $category => $categoryImages): ?>
        <section class="gallery-section">
            <h3>
                <?php 
                if ($category === 'life') echo 'Члены группы в обычной жизни:';
                elseif ($category === 'concerts') echo 'Концерты:';
                else echo htmlspecialchars(ucfirst($category)) . ':';
                ?>
            </h3>
            <div class="gallery">
                <?php foreach ($categoryImages as $image): ?>
                <div class="gallery-item">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($image['description']); ?>"
                         data-description="<?php echo htmlspecialchars($image['description']); ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
    </main>

    <footer>
       <p>&copy; <?php echo date('Y'); ?> Рок-группа МАСТЕР. Все права защищены.</p>
   </footer>

   <div id="lightbox" style="display: none;">
        <div class="lightbox-content">
            <span class="close">&times;</span>
            <div class="image-container">
                <button class="nav-btn prev">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <img src="" alt="">
                <button class="nav-btn next">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="image-info">
                <span class="image-description"></span>
                <div class="image-counter">
                    <span class="current">1</span> / <span class="total">0</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('.gallery-item img');
            const lightbox = document.getElementById('lightbox');
            let currentIndex = 0;
            const images = Array.from(galleryItems);

            function updateCounter() {
                lightbox.querySelector('.current').textContent = currentIndex + 1;
                lightbox.querySelector('.total').textContent = images.length;
            }

            galleryItems.forEach((img, index) => {
                img.addEventListener('click', () => {
                    currentIndex = index;
                    openLightbox();
                });
            });

            function openLightbox() {
                lightbox.style.display = 'flex';
                const currentImage = images[currentIndex];
                lightbox.querySelector('img').src = currentImage.src;
                lightbox.querySelector('.image-description').textContent = currentImage.getAttribute('data-description');
                updateCounter();
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                lightbox.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            function showImage(index) {
                currentIndex = index;
                const currentImage = images[currentIndex];
                lightbox.querySelector('img').src = currentImage.src;
                lightbox.querySelector('.image-description').textContent = currentImage.getAttribute('data-description');
                updateCounter();
            }

            function showNext() {
                currentIndex = (currentIndex + 1) % images.length;
                showImage(currentIndex);
            }

            function showPrev() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                showImage(currentIndex);
            }

            lightbox.querySelector('.close').addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', (e) => {
                if (e.target === lightbox) closeLightbox();
            });

            lightbox.querySelector('.prev').addEventListener('click', (e) => {
                e.stopPropagation();
                showPrev();
            });

            lightbox.querySelector('.next').addEventListener('click', (e) => {
                e.stopPropagation();
                showNext();
            });

            document.addEventListener('keydown', (e) => {
                if (lightbox.style.display === 'flex') {
                    if (e.key === 'Escape') closeLightbox();
                    if (e.key === 'ArrowLeft') showPrev();
                    if (e.key === 'ArrowRight') showNext();
                }
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "scale(1)";
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.gallery-item').forEach(item => {
                item.style.opacity = "0";
                item.style.transform = "scale(0.8)";
                item.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(item);
            });

            let touchStartX = 0;
            let touchEndX = 0;

            lightbox.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });

            lightbox.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });

            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;

                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        showNext();
                    } else {
                        showPrev();
                    }
                }
            }
        });
    </script>
    <script src="../navigation.js"></script>
</body>
</html>