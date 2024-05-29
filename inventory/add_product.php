<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$product_name = $description = $category = $price = "";
$product_name_err = $description_err = $category_err = $price_err = "";

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

    // Check input errors before inserting in database
    if (empty($product_name_err) && empty($description_err) && empty($category_err) && empty($price_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO products (name, description, category, price) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_name, $param_description, $param_category, $param_price);
            
            // Set parameters
            $param_name = $product_name;
            $param_description = $description;
            $param_category = $category;
            $param_price = $price;
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to product list page
                header("location: view_products.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
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
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        <h2 class="mb-4">Add Product</h2>
        <p>Please fill this form to add a product.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
