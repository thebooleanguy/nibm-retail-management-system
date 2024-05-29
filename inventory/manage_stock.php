<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$product_id = $quantity = "";
$product_id_err = $quantity_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate product details
    $product_id = trim($_POST["product_id"]);
    $quantity = trim($_POST["quantity"]);

    // Validate product id
    if (empty($product_id)) {
        $product_id_err = "Please select a product.";
    }

    // Validate quantity
    if (empty($quantity)) {
        $quantity_err = "Please enter the quantity.";
    } elseif (!ctype_digit($quantity) || $quantity <= 0) {
        $quantity_err = "Quantity must be a positive integer.";
    }

    // Check input errors before updating the database
    if (empty($product_id_err) && empty($quantity_err)) {

        // Update stock table
        $sql = "UPDATE stock SET quantity = ? WHERE product_id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ii", $param_quantity, $param_product_id);

            // Set parameters
            $param_quantity = $quantity;
            $param_product_id = $product_id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to manage stock page
                header("location: manage_stock.php");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}

// Fetch all products
$sql_products = "SELECT id, name FROM products";
$result_products = $mysqli->query($sql_products);

// Fetch stock information
$sql_stock = "SELECT s.id, p.name, s.quantity
              FROM stock s
              JOIN products p ON s.product_id = p.id";
$result_stock = $mysqli->query($sql_stock);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4"><i class="fas fa-boxes"></i> Manage Stock</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-plus-circle"></i> Add / Update Stock Details</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="product_id"><i class="fas fa-box"></i> Product:</label>
                                <select name="product_id" id="product_id" class="form-control <?php echo (!empty($product_id_err)) ? 'is-invalid' : ''; ?>">
                                    <option value="">Select a product</option>
                                    <?php
                                    if ($result_products->num_rows > 0) {
                                        while ($row = $result_products->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $product_id_err; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="quantity"><i class="fas fa-sort-numeric-up"></i> Quantity:</label>
                                <input type="number" class="form-control <?php echo (!empty($quantity_err)) ? 'is-invalid' : ''; ?>" id="quantity" name="quantity" min="1" value="<?php echo $quantity; ?>">
                                <span class="invalid-feedback"><?php echo $quantity_err; ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-list"></i> Current Stock Details</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-box"></i> Product Name</th>
                                    <th><i class="fas fa-sort-numeric-up"></i> Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result_stock->num_rows > 0) {
                                    while ($row = $result_stock->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>" . $row['quantity'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>No stock details found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
