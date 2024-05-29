<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$product_name = $description = $category = $price = "";
$product_name_err = $description_err = $category_err = $price_err = "";

// Get the product ID from the URL
$product_id = isset($_GET["id"]) ? $_GET["id"] : null;

// Fetch the product details
if ($product_id) {
    $sql = "SELECT * FROM products WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $product = $result->fetch_assoc();
                $product_name = $product["name"];
                $description = $product["description"];
                $category = $product["category"];
                $price = $product["price"];
            } else {
                echo "<script>Swal.fire('Error', 'Product not found.', 'error');</script>";
                exit();
            }
        } else {
            echo "<script>Swal.fire('Error', 'Error executing query.', 'error');</script>";
            exit();
        }
    } else {
        echo "<script>Swal.fire('Error', 'Error preparing query.', 'error');</script>";
        exit();
    }
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate product name
    if (empty(trim($_POST["product_name"]))) {
        $product_name_err = "Please enter a product name.";
    } else {
        $product_name = trim($_POST["product_name"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate category
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please enter a category.";
    } else {
        $category = trim($_POST["category"]);
    }

    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter the price.";
    } elseif (!is_numeric($_POST["price"])) {
        $price_err = "Please enter a valid price.";
    } else {
        $price = trim($_POST["price"]);
    }

    // Check input errors before updating in database
    if (empty($product_name_err) && empty($description_err) && empty($category_err) && empty($price_err)) {
        // Prepare an update statement
        $sql = "UPDATE products SET name = ?, description = ?, category = ?, price = ? WHERE id = ?";
        
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssi", $param_name, $param_description, $param_category, $param_price, $param_id);
            
            // Set parameters
            $param_name = $product_name;
            $param_description = $description;
            $param_category = $category;
            $param_price = $price;
            $param_id = $product_id;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                echo "<script>
                        Swal.fire({
                            title: 'Success!',
                            text: 'Product updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'view_products.php';
                            }
                        });
                      </script>";
            } else {
                echo "<script>Swal.fire('Error', 'Something went wrong. Please try again later.', 'error');</script>";
            }
            
            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .wrapper {
            width: 600px;
            margin: 0 auto;
            padding-top: 50px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="mb-4">Edit Product</h2>
        <p>Please fill this form to update the product details.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $product_id; ?>" method="post">
            <div class="form-group">
                <label for="product_name"><i class="fas fa-tag"></i> Product Name</label>
                <input type="text" name="product_name" class="form-control <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $product_name; ?>">
                <span class="invalid-feedback"><?php echo $product_name_err;?></span>
            </div>    
            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err;?></span>
            </div>
            <div class="form-group">
                <label for="category"><i class="fas fa-list-alt"></i> Category</label>
                <input type="text" name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $category; ?>">
                <span class="invalid-feedback"><?php echo $category_err;?></span>
            </div>
            <div class="form-group">
                <label for="price"><i class="fas fa-dollar-sign"></i> Price</label>
                <input type="text" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                <span class="invalid-feedback"><?php echo $price_err;?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="view_products.php" class="btn btn-secondary ml-2">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
