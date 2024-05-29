<?php
// Include config file
require_once "config.php";

// Fetch sales data with product details
$sql = "SELECT p.name, p.category, s.quantity, s.total_price, p.cost_price, (s.total_price - (s.quantity * p.cost_price)) AS profit
        FROM sales s
        JOIN products p ON s.product_id = p.id
        ORDER BY s.sale_date DESC";
$result = $mysqli->query($sql);

// Calculate total revenue and total profit
$total_revenue = 0;
$total_profit = 0;
$category_profit = [];
$product_profit = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_revenue += $row['total_price'];
        $total_profit += $row['profit'];

        // Calculate profit by category
        if (!isset($category_profit[$row['category']])) {
            $category_profit[$row['category']] = 0;
        }
        $category_profit[$row['category']] += $row['profit'];

        // Calculate profit by product
        if (!isset($product_profit[$row['name']])) {
            $product_profit[$row['name']] = 0;
        }
        $product_profit[$row['name']] += $row['profit'];
    }
}

arsort($category_profit);
arsort($product_profit);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit Analysis</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-chart-line"></i> Profit Analysis</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-dollar-sign"></i> Revenue & Profit</h4>
                    </div>
                    <div class="card-body">
                        <p>Total Revenue: $<?php echo number_format($total_revenue, 2); ?></p>
                        <p>Total Profit: $<?php echo number_format($total_profit, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-tags"></i> Most Profitable Categories</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($category_profit as $category => $profit): ?>
                                <li class="list-group-item">
                                    <?php echo $category; ?> - $<?php echo number_format($profit, 2); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-tag"></i> Most Profitable Products</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($product_profit as $product => $profit): ?>
                                <li class="list-group-item">
                                    <?php echo $product; ?> - $<?php echo number_format($profit, 2); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
