<?php
require_once '../config/database.php';
$db = new Database();
$connection = $db->getConnection();

$query = "SELECT * FROM products ORDER BY id";
$stmt = $connection->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="shop.css">
    <link rel="shortcut icon" href="/images/master-logo.jpg" type="image/x-icon">
    <title>Рок-группа МАСТЕР</title>
</head>
<body>
    <header>
        <h1 style="text-align: center;">Магазин мерча МАСТЕР</h1>
    </header>

    <main>
        <h2 style="text-align: center;">Магазин мерча</h2>
        <h3 style="text-align: center;">Товары:</h3>
        <div class="product-container">
            <?php foreach ($products as $product): ?>
            <div class="product">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <p class="product-name"><?php echo htmlspecialchars($product['name']); ?></p>
                <span class="product-price"><?php echo number_format($product['price'], 0, '', ' '); ?> руб.</span>
                <p class="product-size">Размер: <?php echo htmlspecialchars($product['sizes']); ?></p>
                <button class="buy-btn" 
                        data-product-id="<?php echo $product['id']; ?>" 
                        onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo htmlspecialchars($product['sizes']); ?>')">
                    Купить
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Рок-группа МАСТЕР. Все права защищены.</p>
    </footer>

    <div id="cart">
        <h3>
            Корзина 
            <span class="cart-badge"></span>
            <button id="toggle-cart-button" class="cart-toggle-btn" onclick="toggleCart()">▼</button>
        </h3>
        <div id="cart-content">
            <div id="cart-items">
                <p class="empty-cart">Корзина пуста</p>
            </div>
            <div class="total-price" id="total-price"></div>
            <button id="checkout-button" class="checkout-btn" onclick="showOrderForm()" disabled>Оформить заказ</button>
        </div>
    </div>

    <div id="order-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeOrderForm()">&times;</span>
            <h2>Оформление заказа</h2>
            <form id="order-form">
                <div class="form-group">
                    <label for="customer-name">Имя *</label>
                    <input type="text" id="customer-name" name="customer_name" required>
                </div>
                
                <div class="form-group">
                    <label for="customer-email">Email *</label>
                    <input type="email" id="customer-email" name="customer_email" required>
                </div>
                
                <div class="form-group">
                    <label for="customer-phone">Телефон *</label>
                    <input type="tel" id="customer-phone" name="customer_phone" required>
                </div>
                
                <div class="form-group">
                    <label for="customer-address">Адрес доставки *</label>
                    <textarea id="customer-address" name="customer_address" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="customer-comment">Комментарий к заказу</label>
                    <textarea id="customer-comment" name="customer_comment" rows="3"></textarea>
                </div>
                
                <div class="order-items-section">
                    <h3>Товары в заказе:</h3>
                    <div id="order-items-list">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeOrderForm()">Отмена</button>
                    <button type="submit" class="submit-order-btn">Подтвердить заказ</button>
                </div>
            </form>
        </div>
    </div>

        <script>
        let cart = [];
        let cartVisible = true;

        function addToCart(productId, name, price, sizes) {
            const sizeArray = sizes.split(',').map(s => s.trim());
            let selectedSize = sizeArray[0];
            
            if (sizeArray.length > 1) {
                selectedSize = prompt(`Выберите размер для "${name}":\nДоступные размеры: ${sizes}`, sizeArray[0]);
                if (selectedSize === null) return;
                if (!sizeArray.includes(selectedSize)) {
                    alert(`Пожалуйста, выберите размер из доступных: ${sizes}`);
                    return;
                }
            }
            
            cart.push({ 
                id: productId, 
                name: name, 
                price: price, 
                sizes: sizes,
                selectedSize: selectedSize
            });
            updateCart();
            updateProductButtons();
            
            // Получаем кнопку через productId вместо event
            const button = document.querySelector(`.buy-btn[data-product-id="${productId}"]`);
            if (button) {
                button.textContent = 'Добавлено!';
                button.style.background = 'linear-gradient(135deg, #00cc00, #00ff00)';
                setTimeout(() => {
                    updateProductButtons(); // Возвращаем нормальное состояние через updateProductButtons
                }, 1000);
            }
        }

        function updateCart() {
            const cartItems = document.getElementById('cart-items');
            const totalPrice = document.getElementById('total-price');
            const checkoutButton = document.getElementById('checkout-button');
            const cartBadge = document.querySelector('.cart-badge');
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="empty-cart">Корзина пуста</p>';
                totalPrice.textContent = '';
                checkoutButton.disabled = true;
                cartBadge.textContent = '';
                return;
            }
            
            let itemsHTML = '';
            let total = 0;
            
            cart.forEach((item, index) => {
                total += item.price;
                itemsHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <span class="cart-item-name">${item.name}</span>
                            <span class="cart-item-size">Размер: ${item.selectedSize}</span>
                        </div>
                        <span class="cart-item-price">${item.price.toLocaleString('ru-RU')} руб.</span>
                        <button class="remove-btn" onclick="removeFromCart(${index})" title="Удалить">×</button>
                    </div>
                `;
            });
            
            cartItems.innerHTML = itemsHTML;
            totalPrice.textContent = `Итого: ${total.toLocaleString('ru-RU')} руб.`;
            checkoutButton.disabled = false;
            cartBadge.textContent = `(${cart.length})`;
        }

        function updateProductButtons() {
            const buttons = document.querySelectorAll('.buy-btn');
            buttons.forEach(button => {
                const productId = button.getAttribute('data-product-id');
                const isInCart = cart.some(item => item.id == productId);
                
                if (isInCart) {
                    button.textContent = 'Добавлено!';
                    button.style.background = 'linear-gradient(135deg, #00cc00, #00ff00)';
                    button.disabled = true;
                } else {
                    button.textContent = 'Купить';
                    button.style.background = 'linear-gradient(135deg, #8b0000, #cc0000)';
                    button.disabled = false;
                }
            });
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCart();
            updateProductButtons();
        }

        function toggleCart() {
            const cartContent = document.getElementById('cart-content');
            const toggleButton = document.getElementById('toggle-cart-button');
            
            cartVisible = !cartVisible;
            
            if (cartVisible) {
                cartContent.style.display = 'block';
                toggleButton.textContent = '▲';
            } else {
                cartContent.style.display = 'none';
                toggleButton.textContent = '▼';
            }
        }

        function showOrderForm() {
            if (cart.length === 0) {
                alert('Корзина пуста!');
                return;
            }
            
            const orderItemsList = document.getElementById('order-items-list');
            let itemsHTML = '';
            
            cart.forEach((item, index) => {
                itemsHTML += `
                    <div class="order-item">
                        <span class="order-item-name">${item.name}</span>
                        <span class="order-item-size">Размер: ${item.selectedSize}</span>
                        <span class="order-item-price">${item.price.toLocaleString('ru-RU')} руб.</span>
                    </div>
                `;
            });
            
            const total = cart.reduce((sum, item) => sum + item.price, 0);
            itemsHTML += `
                <div class="order-total">
                    <strong>Общая сумма: ${total.toLocaleString('ru-RU')} руб.</strong>
                </div>
            `;
            
            orderItemsList.innerHTML = itemsHTML;
            
            const modal = document.getElementById('order-modal');
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');
            
            requestAnimationFrame(() => {
                modal.classList.add('show');
            });
        }

        function closeOrderForm() {
            const modal = document.getElementById('order-modal');
            modal.classList.remove('show');
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
                document.getElementById('order-form').reset();
            }, 300);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeOrderForm();
            }
        });

        document.getElementById('order-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const orderData = {
                customer_name: formData.get('customer_name'),
                customer_email: formData.get('customer_email'),
                customer_phone: formData.get('customer_phone'),
                customer_address: formData.get('customer_address'),
                customer_comment: formData.get('customer_comment'),
                items: cart,
                total: cart.reduce((sum, item) => sum + item.price, 0)
            };
            
            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Спасибо за покупку! Ваш заказ №${data.order_id} оформлен. Мы свяжемся с вами в ближайшее время.`);
                    cart = [];
                    updateCart();
                    updateProductButtons();
                    closeOrderForm();
                } else {
                    alert('Ошибка при оформлении заказа: ' + data.message);
                }
            })
            .catch(error => {
                alert('Ошибка сети. Попробуйте еще раз.');
                console.error('Error:', error);
            });
        });

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('order-modal');
            if (e.target === modal) {
                closeOrderForm();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const products = document.querySelectorAll('.product');
            
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

            products.forEach(product => {
                product.style.opacity = "0";
                product.style.transform = "translateY(30px)";
                product.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                observer.observe(product);
            });

            toggleCart();
            updateProductButtons();
        });
    </script>
    <script src="../navigation.js"></script>
</body>
</html>