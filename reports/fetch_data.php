<?php
require_once "config.php";

$order_id = $_GET['order_id'];

// Fetch order details
$sql_order = "SELECT o.id, o.customer_name, o.customer_address, o.created_at, SUM(oi.quantity * oi.unit_price) AS total_amount
              FROM orders o
              INNER JOIN order_items oi ON o.id = oi.order_id
              WHERE o.id = ?";
if ($stmt_order = $mysqli->prepare($sql_order)) {
    $stmt_order->bind_param("i", $order_id);
    if ($stmt_order->execute()) {
        $result_order = $stmt_order->get_result();
        if ($result_order->num_rows == 1) {
            $order = $result_order->fetch_assoc();
            $order['created_at'] = date("Y-m-d H:i:s", strtotime($order['created_at']));
        } else {
            echo json_encode(['error' => 'Order not found']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Error fetching order details']);
        exit;
    }
    $stmt_order->close();
} else {
    echo json_encode(['error' => 'Error preparing order query']);
    exit;
}

// Fetch order items
$sql_items = "SELECT product_name, quantity, unit_price, (quantity * unit_price) AS total
              FROM order_items
              WHERE order_id = ?";
$items = [];
if ($stmt_items = $mysqli->prepare($sql_items)) {
    $stmt_items->bind_param("i", $order_id);
    if ($stmt_items->execute()) {
        $result_items = $stmt_items->get_result();
        while ($row = $result_items->fetch_assoc()) {
            $items[] = $row;
        }
    } else {
        echo json_encode(['error' => 'Error fetching order items']);
        exit;
    }
    $stmt_items->close();
} else {
    echo json_encode(['error' => 'Error preparing order items query']);
    exit;
}

// Combine order details and items
$order['items'] = $items;
echo json_encode(['order' => $order, 'items' => $items]);

$mysqli->close();
?>
