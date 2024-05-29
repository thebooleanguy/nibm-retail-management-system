<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Suppliers</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Suppliers</h1>
        <form method="GET" class="mb-4">
            <div class="form-group">
                <label for="search">Search Suppliers:</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Enter supplier name or contact details">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <?php
        $conn = new mysqli('localhost', 'root', '', 'inventory_system');
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $search = '';
        if (isset($_GET['search'])) {
            $search = $conn->real_escape_string($_GET['search']);
        }

        $sql = "SELECT * FROM suppliers WHERE name LIKE '%$search%' OR contact_details LIKE '%$search%'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead class='thead-dark'><tr><th>Name</th><th>Contact Details</th><th>Email</th><th>Pricing Information</th><th>Description</th><th>Actions</th></tr></thead>";
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td><a href='https://wa.me/{$row['contact_details']}' target='_blank'><i class='fab fa-whatsapp'></i> {$row['contact_details']}</a></td>";
                echo "<td><a href='mailto:{$row['email']}'><i class='fas fa-envelope'></i> {$row['email']}</a></td>";
                echo "<td>{$row['pricing_info']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td>";
                echo "<a href='edit_supplier.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i> Edit</a> ";
                echo "<a href='delete_supplier.php?id={$row['id']}' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i> Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-info' role='alert'>No suppliers found.</div>";
        }

        $conn->close();
        ?>
        <a href="add_supplier.html" class="btn btn-primary mt-3"><i class="fas fa-plus"></i> Add New Supplier</a>
    </div>
</body>
</html>
