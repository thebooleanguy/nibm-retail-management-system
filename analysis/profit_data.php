<?php
require_once "config.php";

$sql = "SELECT 
            p.name AS product_name,
            SUM(s.quantity) AS total_quantity_sold,
            SUM(s.total_price) AS total_revenue,
            SUM(s.quantity * p.cost_price) AS total_cost,
            (SUM(s.total_price) - SUM(s.quantity * p.cost_price)) AS total_profit
        FROM 
            sales s
        JOIN 
            products p ON s.product_id = p.id
        GROUP BY 
            p.name
        ORDER BY 
            total_profit DESC";

$result = $mysqli->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$mysqli->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
