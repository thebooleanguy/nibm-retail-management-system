<?php
// Include config file
require_once "config.php";

// Fetch sales data
$sql = "SELECT DATE(order_date) as date, SUM(total_amount) as total_sales FROM orders GROUP BY DATE(order_date)";
$result = $mysqli->query($sql);

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = $row;
}

echo json_encode($sales_data);

$mysqli->close();
?>
