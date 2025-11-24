<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['customer_name']) || empty($input['customer_email']) || empty($input['customer_phone']) || empty($input['customer_address'])) {
        echo json_encode(['success' => false, 'message' => 'Все обязательные поля должны быть заполнены']);
        exit;
    }
    
    if (!filter_var($input['customer_email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Некорректный email адрес']);
        exit;
    }
    
    $db = new Database();
    $connection = $db->getConnection();
    
    try {
        $connection->beginTransaction();
        
        $query = "INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, customer_comment, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $connection->prepare($query);
        $stmt->execute([
            $input['customer_name'],
            $input['customer_email'],
            $input['customer_phone'],
            $input['customer_address'],
            $input['customer_comment'] ?? '',
            $input['total']
        ]);
        $order_id = $connection->lastInsertId();
        
        $query = "INSERT INTO order_items (order_id, product_id, quantity, size) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        
        foreach ($input['items'] as $item) {
            $stmt->execute([
                $order_id, 
                $item['id'], 
                1,
                $item['selectedSize']
            ]);
        }
        
        $connection->commit();
                
        echo json_encode(['success' => true, 'order_id' => $order_id]);
        
    } catch (Exception $e) {
        $connection->rollBack();
        error_log("Order processing error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Произошла ошибка при обработке заказа: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}
?>