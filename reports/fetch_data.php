<?php
// fetch_data.php
require_once 'config.php';

// Fetch order data
$order_id = $_GET['order_id'];
$order_query = "SELECT orders.id, customers.name AS customer_name, customers.address AS customer_address, orders.total_amount, orders.created_at
                FROM orders
                JOIN customers ON orders.customer_id = customers.id
                WHERE orders.id = ?";
$order_stmt = $mysqli->prepare($order_query);
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order_data = $order_result->fetch_assoc();

// Fetch order items
$items_query = "SELECT product_name, quantity, unit_price, total FROM order_items WHERE order_id = ?";
$items_stmt = $mysqli->prepare($items_query);
$items_stmt->bind_param('i', $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$order_items = $items_result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['order' => $order_data, 'items' => $order_items]);
?>

<?php
// fetch_stock.php
require_once 'config.php';

// Fetch stock data
$stock_query = "SELECT products.name, products.description, stock.quantity, stock.created_at, stock.updated_at
                FROM stock
                JOIN products ON stock.product_id = products.id";
$stock_result = $mysqli->query($stock_query);
$stock_data = $stock_result->fetch_all(MYSQLI_ASSOC);

echo json_encode($stock_data);
?>
