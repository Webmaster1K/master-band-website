<?php
require_once '../config/database.php';
$db = new Database();
$connection = $db->getConnection();

$album_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

$query = "SELECT * FROM albums WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->execute([$album_id]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    header("Location: albums.php");
    exit;
}

$query = "SELECT * FROM tracks WHERE album_id = ? ORDER BY id";
$stmt = $connection->prepare($query);
$stmt->execute([$album_id]);
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="albums.css">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title><?php echo htmlspecialchars($album['header']); ?></title>
</head>
<body>
    <header>
        <h1 style="text-align: center;" id="album-header"><?php echo htmlspecialchars($album['header']); ?></h1>
    </header>

    <main>
        <h2 style="text-align: center;" id="album-title"><?php echo htmlspecialchars($album['title']); ?></h2>
        <div class="album-cover">
            <img id="album-image" src="<?php echo htmlspecialchars($album['image_path']); ?>" alt="Обложка альбома <?php echo htmlspecialchars($album['title']); ?>">
        </div>
        <div class="album-description">
            <p id="album-description"><?php echo htmlspecialchars($album['description']); ?></p>
        </div>

        <h3>Песни:</h3>
        <ul class="songs-list" id="songs-list">
            <?php foreach ($tracks as $track): ?>
            <li class="song-item">
                <div class="song-content">
                    <div class="song-title"><?php echo htmlspecialchars($track['title']); ?></div>
                    <button class="play-song-btn" data-src="<?php echo htmlspecialchars($track['file_path']); ?>" data-title="<?php echo htmlspecialchars($track['title']); ?>">
                        <span class="play-icon">▶</span> Воспроизвести
                    </button>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="back-button">
            <button onclick="location.href='albums.php'">Назад к альбомам</button>
        </div>
    </main>

    <div id="mini-player" class="mini-player">
        <button id="close-player" class="close-btn">×</button>
        <div class="player-content">
            <div class="track-info">
                <span id="current-track">Выберите трек для воспроизведения</span>
                <span id="current-album"></span>
            </div>
            
            <div class="player-controls">
                <div class="control-buttons">
                    <button id="prev-btn" class="control-btn" disabled>⏮</button>
                    <button id="play-pause-btn" class="control-btn" disabled>▶</button>
                    <button id="next-btn" class="control-btn" disabled>⏭</button>
                </div>
                
                <div class="volume-control">
                    <span>🔊</span>
                    <input type="range" id="volume-slider" class="volume-slider" min="0" max="100" value="50">
                </div>
            </div>
            
            <div class="progress-control">
                <span id="current-time">-:--</span>
                <input type="range" id="progress-bar" class="progress-bar" min="0" max="100" value="0" disabled>
                <span id="duration">-:--</span>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Рок-группа МАСТЕР. Все права защищены.</p>
    </footer>

    <script>
        let currentAudio = null;
        let currentAlbum = "<?php echo htmlspecialchars($album['title']); ?>";
        let currentTrackIndex = 0;
        let isPlaying = false;
        let tracks = [];

        document.addEventListener('DOMContentLoaded', function() {
            const trackElements = document.querySelectorAll('.play-song-btn');
            tracks = Array.from(trackElements).map(btn => ({
                src: btn.getAttribute('data-src'),
                title: btn.getAttribute('data-title')
            }));

            initializePlayer();
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = "1";
                            entry.target.style.transform = "translateY(0)";
                        }, index * 50);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.song-item').forEach(item => {
                item.style.opacity = "0";
                item.style.transform = "translateY(20px)";
                item.style.transition = "opacity 0.4s ease, transform 0.4s ease";
                observer.observe(item);
            });

            const albumDesc = document.querySelector('.album-description');
            if (albumDesc) {
                albumDesc.style.opacity = "0";
                albumDesc.style.transform = "translateY(20px)";
                albumDesc.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(albumDesc);
            }
        });

        function initializePlayer() {
            const playButtons = document.querySelectorAll('.play-song-btn');
            
            playButtons.forEach((button, index) => {
                button.addEventListener('click', function() {
                    playTrack(index);
                });
            });

            document.getElementById('play-pause-btn').addEventListener('click', togglePlayPause);
            document.getElementById('prev-btn').addEventListener('click', playPrevTrack);
            document.getElementById('next-btn').addEventListener('click', playNextTrack);
            document.getElementById('close-player').addEventListener('click', resetPlayer);
            
            document.getElementById('volume-slider').addEventListener('input', function() {
                if (currentAudio) {
                    currentAudio.volume = this.value / 100;
                }
            });
            
            document.getElementById('progress-bar').addEventListener('input', function() {
                if (currentAudio) {
                    const seekTime = (this.value / 100) * currentAudio.duration;
                    currentAudio.currentTime = seekTime;
                }
            });
        }

        function playTrack(index) {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
            
            currentAudio = new Audio(tracks[index].src);
            currentTrackIndex = index;
            
            currentAudio.addEventListener('loadedmetadata', function() {
                updateDuration();
                enableControls(true);
            });
            
            currentAudio.addEventListener('timeupdate', function() {
                updateProgress();
            });
            
            currentAudio.addEventListener('ended', function() {
                playNextTrack();
            });
            
            currentAudio.play().then(() => {
                isPlaying = true;
                
                updateMiniPlayer(tracks[index].title, currentAlbum);
                updatePlayPauseButton();
            }).catch(error => {
                console.error("Ошибка воспроизведения:", error);
                alert("Не удалось воспроизвести трек. Проверьте путь к файлу.");
            });
        }

        function enableControls(enabled) {
            document.getElementById('play-pause-btn').disabled = !enabled;
            document.getElementById('prev-btn').disabled = !enabled;
            document.getElementById('next-btn').disabled = !enabled;
            document.getElementById('progress-bar').disabled = !enabled;
        }

        function resetPlayer() {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
            
            isPlaying = false;
            currentTrackIndex = 0;
            
            document.getElementById('current-track').textContent = "Выберите трек для воспроизведения";
            document.getElementById('current-album').textContent = "";
            document.getElementById('current-time').textContent = "-:--";
            document.getElementById('duration').textContent = "-:--";
            document.getElementById('progress-bar').value = 0;
            
            enableControls(false);
            updatePlayPauseButton();
        }

        function updateMiniPlayer(trackName, albumName) {
            document.getElementById('current-track').textContent = trackName;
            document.getElementById('current-album').textContent = albumName;
        }

        function togglePlayPause() {
            if (currentAudio) {
                if (isPlaying) {
                    currentAudio.pause();
                } else {
                    currentAudio.play();
                }
                isPlaying = !isPlaying;
                updatePlayPauseButton();
            }
        }

        function updatePlayPauseButton() {
            const button = document.getElementById('play-pause-btn');
            button.textContent = isPlaying ? '⏸' : '▶';
        }

        function playNextTrack() {
            if (currentTrackIndex < tracks.length - 1) {
                playTrack(currentTrackIndex + 1);
            } else {
                resetPlayer();
            }
        }

        function playPrevTrack() {
            if (currentTrackIndex > 0) {
                playTrack(currentTrackIndex - 1);
            }
        }

        function updateProgress() {
            if (currentAudio && currentAudio.duration) {
                const progress = (currentAudio.currentTime / currentAudio.duration) * 100;
                document.getElementById('progress-bar').value = progress;
                
                document.getElementById('current-time').textContent = formatTime(currentAudio.currentTime);
            }
        }

        function updateDuration() {
            if (currentAudio && currentAudio.duration) {
                document.getElementById('duration').textContent = formatTime(currentAudio.duration);
            }
        }

        function formatTime(seconds) {
            if (isNaN(seconds)) return '-:--';
            
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        }
    </script>
    <script src="../navigation.js"></script>
</body>
</html>