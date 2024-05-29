<?php
require_once "config.php";

// Fetch products and their stock levels
$sql = "SELECT p.id, p.name, p.category, s.quantity, s.low_stock_alert FROM products p
        LEFT JOIN stock s ON p.id = s.product_id";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Product Inventory</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Low Stock Alert</th>
            </tr>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$row["name"]."</td>
                <td>".$row["category"]."</td>
                <td>".$row["quantity"]."</td>
                <td>".$row["low_stock_alert"]."</td>
              </tr>";
        
        // Check if stock is below low stock alert level and notify
        if ($row["quantity"] < $row["low_stock_alert"]) {
            echo "<tr><td colspan='5'><strong>Low Stock Alert: ".$row["name"]." is running low on stock!</strong></td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "No products found.";
}

$mysqli->close();
?>
