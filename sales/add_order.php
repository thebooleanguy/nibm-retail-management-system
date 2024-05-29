<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$customer_id = 1;
$customer_name = $customer_email = $customer_contact = "";
$product_name = $quantity = $unit_price = $total = "";
$customer_name_err = $customer_email_err = $customer_contact_err = "";
$product_name_err = $quantity_err = $unit_price_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate customer details
    $customer_name = trim($_POST["customer_name"]);
    $customer_email = trim($_POST["customer_email"]);
    $customer_contact = trim($_POST["customer_contact"]);

    if (empty($customer_name)) {
        $customer_name_err = "Customer Name is required.";
    }
    if (empty($customer_email)) {
        $customer_email_err = "Customer Email is required.";
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $customer_email_err = "Invalid email format.";
    }
    if (empty($customer_contact)) {
        $customer_contact_err = "Customer Contact is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $customer_contact)) {
        $customer_contact_err = "Invalid contact number.";
    }

    // If no errors, check if the customer already exists
    if (empty($customer_name_err) && empty($customer_email_err) && empty($customer_contact_err)) {
        $sql = "SELECT id FROM customers WHERE email = ? OR contact_number = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ss", $customer_email, $customer_contact);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($customer_id);
                    $stmt->fetch();
                    // Debugging output
                    echo "Customer exists with ID: $customer_id<br>";
                } else {
                    // Insert new customer
                    $sql_insert = "INSERT INTO customers (name, email, contact_number) VALUES (?, ?, ?)";
                    if ($stmt_insert = $mysqli->prepare($sql_insert)) {
                        $stmt_insert->bind_param("sss", $customer_name, $customer_email, $customer_contact);
                        if ($stmt_insert->execute()) {
                            $customer_id = $stmt_insert->insert_id;
                            // Debugging output
                            echo "New customer inserted with ID: $customer_id<br>";
                        } else {
                            die("Error: Could not execute customer insertion.");
                        }
                        $stmt_insert->close();
                    } else {
                        die("Error: Could not prepare customer insertion.");
                    }
                }
                $stmt->close();
            } else {
                die("Error: Could not execute customer selection.");
            }
        } else {
            die("Error: Could not prepare customer selection.");
        }
    }

    // Validate and process each product item
    $order_items = [];
    $product_names = $_POST["product_name"];
    $quantities = $_POST["quantity"];
    $unit_prices = $_POST["unit_price"];
    $totals = $_POST["total"];

    for ($i = 0; $i < count($product_names); $i++) {
        $product_name = trim($product_names[$i]);
        $quantity = trim($quantities[$i]);
        $unit_price = trim($unit_prices[$i]);
        $total = trim($totals[$i]);

        if (empty($product_name)) {
            $product_name_err = "Product Name is required.";
        }
        if (empty($quantity)) {
            $quantity_err = "Quantity is required.";
        } elseif (!ctype_digit($quantity)) {
            $quantity_err = "Quantity must be a positive integer.";
        }
        if (empty($unit_price)) {
            $unit_price_err = "Unit Price is required.";
        } elseif (!is_numeric($unit_price) || $unit_price <= 0) {
            $unit_price_err = "Unit Price must be a positive number.";
        }
        if (empty($product_name_err) && empty($quantity_err) && empty($unit_price_err)) {
            $order_items[] = [
                "product_name" => $product_name,
                "quantity" => $quantity,
                "unit_price" => $unit_price,
                "total" => $total
            ];
        }
    }

    if (count($order_items) == 0) {
        die("Error: At least one order item is required.");
    }

    // Prepare SQL statements
    $sql_order = "INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)";
    $sql_order_item = "INSERT INTO order_items (order_id, product_name, quantity, unit_price, total) VALUES (?, ?, ?, ?, ?)";

    if ($stmt_order = $mysqli->prepare($sql_order)) {
        if ($stmt_order_item = $mysqli->prepare($sql_order_item)) {
            $param_customer_id = $customer_id;
            $param_total_amount = $_POST["total_amount"];
            $stmt_order->bind_param("ii", $param_customer_id, $param_total_amount);
            if ($stmt_order->execute()) {
                $order_id = $stmt_order->insert_id;
                $stmt_order_item->bind_param("isdds", $order_id, $param_product_name, $param_quantity, $param_unit_price, $param_total);
                foreach ($order_items as $item) {
                    $param_product_name = $item["product_name"];
                    $param_quantity = $item["quantity"];
                    $param_unit_price = $item["unit_price"];
                    $param_total = $item["total"];
                    if (!$stmt_order_item->execute()) {
                        die("Error: Executing statement for order items.");
                    }
                }
                $stmt_order_item->close();
            } else {
                die("Error: Executing statement for orders.");
            }
            $stmt_order->close();
        } else {
            die("Error: Cannot prepare statement for order items.");
        }
    } else {
        die("Error: Cannot prepare statement for orders.");
    }

    $mysqli->close();

    echo "<h2>Order successfully placed.</h2>";
    echo "<a href='view_orders.php' class='btn btn-primary'>View Orders</a>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Add Order</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Customer Details</h2>
            <div class="form-group">
                <label for="customer_name">Name:</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="form-group">
                <label for="customer_email">Email:</label>
                <input type="email" class="form-control" id="customer_email" name="customer_email" required>
            </div>
            <div class="form-group">
                <label for="customer_contact">Contact Number:</label>
                <input type="text" class="form-control" id="customer_contact" name="customer_contact" required>
            </div>

            <h2>Order Items</h2>
            <table class="table table-bordered" id="order_items">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" name="product_name[]" required></td>
                        <td><input type="number" class="form-control" name="quantity[]" min="1" required onchange="calculateRow(1)"></td>
                        <td><input type="number" step="0.01" class="form-control" name="unit_price[]" min="0" required onchange="calculateRow(1)"></td>
                        <td><input type="text" class="form-control" name="total[]" readonly></td>
                        <td><button type="button" class="btn btn-danger" onclick="deleteRow(this)">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" onclick="addRow()">Add Item</button>

            <div class="form-group mt-3">
                <label for="total_amount">Total Amount:</label>
                <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
            </div>
            <button type="submit" class="btn btn-success">Place Order</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function addRow() {
            var table = document.getElementById("order_items").getElementsByTagName('tbody')[0];
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
            var cell1 = row.insertCell(0);
            var element1 = document.createElement("input");
            element1.type = "text";
            element1.className = "form-control";
            element1.name = "product_name[]";
            element1.required = true;
            cell1.appendChild(element1);

            var cell2 = row.insertCell(1);
            var element2 = document.createElement("input");
            element2.type = "number";
            element2.className = "form-control";
            element2.name = "quantity[]";
            element2.min = "1";
            element2.required = true;
            element2.onchange = function() { calculateRow(rowCount + 1); };
            cell2.appendChild(element2);

            var cell3 = row.insertCell(2);
            var element3 = document.createElement("input");
            element3.type = "number";
            element3.className = "form-control";
            element3.name = "unit_price[]";
            element3.min = "0";
            element3.step = "0.01";
            element3.required = true;
            element3.onchange = function() { calculateRow(rowCount + 1); };
            cell3.appendChild(element3);

            var cell4 = row.insertCell(3);
            var element4 = document.createElement("input");
            element4.type = "text";
            element4.className = "form-control";
            element4.name = "total[]";
            element4.readOnly = true;
            cell4.appendChild(element4);

            var cell5 = row.insertCell(4);
            var element5 = document.createElement("button");
            element5.type = "button";
            element5.className = "btn btn-danger";
            element5.innerHTML = "Remove";
            element5.onclick = function() { deleteRow(this); };
            cell5.appendChild(element5);
        }

        function deleteRow(button) {
            var row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
            calculateTotal();
        }

        function calculateRow(row) {
            var quantity = $('input[name="quantity[]"]').eq(row - 1).val();
            var unit_price = $('input[name="unit_price[]"]').eq(row - 1).val();
            var total = quantity * unit_price;
            $('input[name="total[]"]').eq(row - 1).val(total.toFixed(2));
            calculateTotal();
        }

        function calculateTotal() {
            var total_amount = 0;
            $('input[name="total[]"]').each(function() {
                total_amount += parseFloat($(this).val()) || 0;
            });
            $('#total_amount').val(total_amount.toFixed(2));
        }
    </script>
</body>
</html>

