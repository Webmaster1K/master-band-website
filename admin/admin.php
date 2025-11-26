<?php
session_start();

require_once '../config/database.php';

// Конфигурация администратора
$admin_username = 'admin';
$admin_password = 'master123';

$is_logged_in = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_logged_in = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $is_logged_in = true;
    } else {
        $error = "Неверный логин или пароль!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['MainMaster'])) {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

function uploadImage($file, $productId) {
    $uploadDir = '../images/products/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }
    
    if ($file['size'] > $maxSize) {
        return null;
    }
    
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'product_' . $productId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return 'images/products/' . $fileName;
    }
    
    return null;
}

function deleteOldImage($imagePath) {
    if ($imagePath && file_exists('../' . $imagePath) && !str_contains($imagePath, '0001.jfif') && !str_contains($imagePath, 'shirt-') && !str_contains($imagePath, 'other-')) {
        unlink('../' . $imagePath);
    }
}

$editing_product = null;
if (isset($_GET['edit_product'])) {
    $edit_id = sanitize($_GET['edit_product']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$edit_id]);
    $editing_product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($is_logged_in) {
    if (isset($_POST['add_product'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $price = (float) str_replace(',', '.', $_POST['price']);
        $sizes = sanitize($_POST['sizes']);
        
        if ($price <= 0 || $price > 99999.99) {
            $error = "Цена должна быть в диапазоне от 0.01 до 99999.99";
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, sizes, image_path) VALUES (?, ?, ?, ?, '')");
            $stmt->execute([$name, $description, $price, $sizes]);
            
            $productId = $pdo->lastInsertId();
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = uploadImage($_FILES['image'], $productId);
                
                if ($imagePath) {
                    $stmt = $pdo->prepare("UPDATE products SET image_path = ? WHERE id = ?");
                    $stmt->execute([$imagePath, $productId]);
                }
            }
            
            header('Location: admin.php?section=products');
            exit;
        }
    }
    
    if (isset($_POST['update_product'])) {
        $id = sanitize($_POST['id']);
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $price = (float) str_replace(',', '.', $_POST['price']); 
        $sizes = sanitize($_POST['sizes']);
        
        if ($price <= 0 || $price > 99999.99) {
            $error = "Цена должна быть в диапазоне от 0.01 до 99999.99";
        } else {
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $currentImage = $stmt->fetchColumn();
            
            $imagePath = $currentImage;
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                deleteOldImage($currentImage);
                $imagePath = uploadImage($_FILES['image'], $id);
            }
            
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, sizes = ?, image_path = ? WHERE id = ?");
            if ($stmt->execute([$name, $description, $price, $sizes, $imagePath, $id])) {
                header('Location: admin.php?section=products&updated=' . $id);
                exit;
            }
        }
    }
    
    if (isset($_GET['delete_product'])) {
        $id = sanitize($_GET['delete_product']);
        
        $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $imagePath = $stmt->fetchColumn();
        
        deleteOldImage($imagePath);
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: admin.php?section=products');
        exit;
    }
    
    if (isset($_GET['delete_image'])) {
        $id = sanitize($_GET['delete_image']);
        
        $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $imagePath = $stmt->fetchColumn();
        
        deleteOldImage($imagePath);
        
        $stmt = $pdo->prepare("UPDATE products SET image_path = '' WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: admin.php?section=products');
        exit;
    }
    
    if (isset($_POST['update_order_status'])) {
        $order_id = sanitize($_POST['order_id']);
        $status = sanitize($_POST['status']);
        
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
    }
    
    $products_stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $orders_stmt = $pdo->query("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ' шт., размер: ', oi.size, ')') SEPARATOR '; ') as items_info,
               COUNT(oi.id) as items_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        GROUP BY o.id
        ORDER BY o.order_date DESC
    ");
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
}

$active_section = $_GET['section'] ?? 'products';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title>Админ-панель - Master</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Master - Админ-панель</h1>
    </header>

    <main>
        <?php if (!$is_logged_in): ?>
            <div class="login-form">
                <h2>Вход в админ-панель</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Логин:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <button type="submit" name="login" class="btn">Войти</button>
                        <a href="../index.php" class="btn btn-secondary" style="padding: 11px 30px;">На главную</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-container">
                <div class="admin-header">
                    <h1>Панель управления</h1>
                    <a href="?logout" class="logout-btn">Выйти</a>
                </div>

                <div class="admin-nav">
                    <button onclick="showSection('products')" class="<?php echo $active_section === 'products' ? 'active' : ''; ?>">Товары</button>
                    <button onclick="showSection('orders')" class="<?php echo $active_section === 'orders' ? 'active' : ''; ?>">Заказы</button>
                    <button onclick="showSection('add-product')" class="<?php echo $active_section === 'add-product' ? 'active' : ''; ?>">Добавить товар</button>
                </div>

                <div id="products" class="admin-section admin-panel <?php echo $active_section === 'products' ? 'active' : ''; ?>">
                    <?php if (isset($_GET['updated'])): ?>
                        <div class="success-message">
                            Товар успешно обновлен!
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="section-header">
                        <h2>Управление товарами</h2>
                        <div class="search-box">
                            <input type="text" id="product-search" placeholder="Поиск товаров..." class="search-input">
                            <span class="search-icon">🔍</span>
                        </div>
                    </div>
                    
                    <div class="results-info">
                        <span id="product-results-count"><?php echo count($products); ?> товаров</span>
                    </div>
                    
                    <?php if (!empty($products)): ?>
                        <div class="products-grid" id="products-grid">
                            <?php foreach ($products as $product): ?>
                            <div class="product-card" data-search="<?php echo htmlspecialchars(strtolower($product['name'] . ' ' . $product['description'] . ' ' . $product['price'] . ' ' . $product['sizes'])); ?>">
                                <div class="product-image">
                                    <?php if (!empty($product['image_path'])): ?>
                                        <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <div class="image-actions">
                                            <a href="?delete_image=<?php echo $product['id']; ?>" class="btn-image-delete" onclick="return confirm('Удалить изображение?')">×</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="no-image">Нет изображения</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <div class="product-details">
                                        <span class="price"><?php echo number_format($product['price'], 2, '.', ' '); ?> руб.</span>
                                        <span class="sizes">Размеры: <?php echo htmlspecialchars($product['sizes']); ?></span>
                                    </div>
                                    <div class="product-actions">
                                        <a href="?section=add-product&edit_product=<?php echo $product['id']; ?>" class="btn-edit">Редактировать</a>
                                        <a href="?delete_product=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Удалить товар?')">Удалить</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">Товаров пока нет</div>
                    <?php endif; ?>
                </div>

                <div id="orders" class="admin-section admin-panel <?php echo $active_section === 'orders' ? 'active' : ''; ?>">
                    <div class="section-header">
                        <h2>Управление заказами</h2>
                        <div class="search-box">
                            <input type="text" id="order-search" placeholder="Поиск заказов..." class="search-input">
                            <span class="search-icon">🔍</span>
                        </div>
                    </div>
                    
                    <div class="filters-row">
                        <div class="filter-group">
                            <label>Статус:</label>
                            <select id="status-filter" class="filter-select">
                                <option value="all">Все статусы</option>
                                <option value="pending">Ожидание</option>
                                <option value="confirmed">Подтвержден</option>
                                <option value="shipped">Отправлен</option>
                                <option value="delivered">Доставлен</option>
                            </select>
                        </div>
                        <div class="results-info">
                            <span id="order-results-count"><?php echo count($orders); ?> заказов</span>
                        </div>
                    </div>
                    
                    <?php if (!empty($orders)): ?>
                        <div class="orders-grid" id="orders-grid">
                            <?php foreach ($orders as $order): ?>
                            <div class="order-card" 
                                 data-search="<?php echo htmlspecialchars(strtolower(
                                     $order['id'] . ' ' . 
                                     $order['customer_name'] . ' ' . 
                                     $order['customer_email'] . ' ' . 
                                     $order['customer_phone'] . ' ' . 
                                     $order['customer_address'] . ' ' . 
                                     $order['items_info'] . ' ' . 
                                     $order['total_amount'] . ' ' . 
                                     $order['status']
                                 )); ?>"
                                 data-status="<?php echo $order['status']; ?>">
                                <div class="order-header">
                                    <h3>Заказ #<?php echo $order['id']; ?></h3>
                                    <span class="order-date"><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></span>
                                </div>
                                
                                <div class="order-customer">
                                    <div class="customer-info">
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                        <div class="contact-info">
                                            <span>📧 <?php echo htmlspecialchars($order['customer_email']); ?></span>
                                            <span>📞 <?php echo htmlspecialchars($order['customer_phone']); ?></span>
                                        </div>
                                        <div class="customer-address">
                                            <strong>Адрес:</strong> <?php echo htmlspecialchars($order['customer_address']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="order-items">
                                    <h4>Товары (<?php echo $order['items_count']; ?>):</h4>
                                    <p><?php echo htmlspecialchars($order['items_info'] ?? 'Нет информации о товарах'); ?></p>
                                </div>
                                
                                <div class="order-footer">
                                    <div class="order-total">
                                        <strong>Итого: <?php echo number_format($order['total_amount'], 2, '.', ' '); ?> руб.</strong>
                                    </div>
                                    <div class="order-status">
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="status-select status-<?php echo $order['status']; ?>">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Ожидание</option>
                                                <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Подтвержден</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Отправлен</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Доставлен</option>
                                            </select>
                                            <input type="hidden" name="update_order_status">
                                        </form>
                                    </div>
                                </div>
                                
                                <?php if (!empty($order['customer_comment'])): ?>
                                <div class="order-comment">
                                    <strong>Комментарий:</strong> <?php echo htmlspecialchars($order['customer_comment']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">Заказов пока нет</div>
                    <?php endif; ?>
                </div>

                <div id="add-product" class="admin-section admin-panel <?php echo $active_section === 'add-product' ? 'active' : ''; ?>">
                    <h2 id="form-title"><?php echo $editing_product ? 'Редактировать товар' : 'Добавить новый товар'; ?></h2>
                    <?php if ($editing_product): ?>
                        <div class="edit-mode-indicator">Режим редактирования</div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" id="product-form">
                        <input type="hidden" id="product_id" name="id" value="<?php echo $editing_product['id'] ?? ''; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Название товара:</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($editing_product['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Цена (руб.):</label>
                                <input type="number" id="price" name="price" step="0.01" min="0.01" max="99999.99" value="<?php echo $editing_product['price'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Описание:</label>
                            <textarea id="description" name="description" rows="4" class="admin-textarea" required><?php echo htmlspecialchars($editing_product['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="sizes">Доступные размеры (через запятую):</label>
                            <input type="text" id="sizes" name="sizes" placeholder="S, M, L, XL" value="<?php echo htmlspecialchars($editing_product['sizes'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Изображение товара:</label>
                            <input type="file" id="image" name="image" accept="image/*" class="file-input">
                            <div class="image-preview" id="image-preview">
                                <?php if ($editing_product && !empty($editing_product['image_path'])): ?>
                                    <div class="current-image">
                                        <img src="../<?php echo htmlspecialchars($editing_product['image_path']); ?>" alt="Текущее изображение">
                                        <span>Текущее изображение</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <?php if ($editing_product): ?>
                                <button type="submit" name="update_product" class="btn btn-primary" id="submit-btn">Обновить товар</button>
                                <a href="?section=products" class="btn btn-secondary">Отмена</a>
                            <?php else: ?>
                                <button type="submit" name="add_product" class="btn btn-primary" id="submit-btn">Добавить товар</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Master. Все права защищены.</p>
    </footer>

    <script>
        function showSection(sectionId) {
            history.pushState(null, null, '?section=' + sectionId);
            
            document.querySelectorAll('.admin-panel').forEach(section => {
                section.classList.remove('active');
            });
            
            document.querySelectorAll('.admin-nav button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(sectionId).classList.add('active');
            document.querySelector(`.admin-nav button[onclick="showSection('${sectionId}')"]`).classList.add('active');
        }

        function resetForm() {
            document.getElementById('product-form').reset();
            document.getElementById('product_id').value = '';
            document.getElementById('form-title').textContent = 'Добавить новый товар';
            document.getElementById('submit-btn').textContent = 'Добавить товар';
            document.getElementById('submit-btn').name = 'add_product';
            document.getElementById('image-preview').innerHTML = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const preview = document.getElementById('image-preview');
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const currentImage = preview.querySelector('.current-image');
                            if (currentImage) {
                                currentImage.remove();
                            }
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            preview.appendChild(img);
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            const productSearch = document.getElementById('product-search');
            const productCards = document.querySelectorAll('.product-card');
            const productResultsCount = document.getElementById('product-results-count');
            
            if (productSearch) {
                productSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleCount = 0;
                    
                    productCards.forEach(card => {
                        const searchData = card.getAttribute('data-search');
                        const matches = searchData.includes(searchTerm);
                        
                        if (matches) {
                            card.style.display = 'block';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    productResultsCount.textContent = `${visibleCount} товаров` + (searchTerm ? ` по запросу "${searchTerm}"` : '');
                });
            }
            
            const orderSearch = document.getElementById('order-search');
            const statusFilter = document.getElementById('status-filter');
            const orderCards = document.querySelectorAll('.order-card');
            const orderResultsCount = document.getElementById('order-results-count');
            
            function filterOrders() {
                const searchTerm = orderSearch.value.toLowerCase().trim();
                const statusValue = statusFilter.value;
                let visibleCount = 0;
                
                orderCards.forEach(card => {
                    const searchData = card.getAttribute('data-search');
                    const cardStatus = card.getAttribute('data-status');
                    const matchesSearch = searchData.includes(searchTerm);
                    const matchesStatus = statusValue === 'all' || cardStatus === statusValue;
                    
                    if (matchesSearch && matchesStatus) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                let statusText = '';
                if (statusValue !== 'all') {
                    statusText = ` со статусом "${statusFilter.options[statusFilter.selectedIndex].text}"`;
                }
                
                orderResultsCount.textContent = `${visibleCount} заказов` + statusText + (searchTerm ? ` по запросу "${searchTerm}"` : '');
            }
            
            if (orderSearch && statusFilter) {
                orderSearch.addEventListener('input', filterOrders);
                statusFilter.addEventListener('change', filterOrders);
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section) {
                showSection(section);
            }
        });
    </script>
</body>
</html>