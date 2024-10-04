<?php
require('db_connection_mysqli.php');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

$query = "SELECT * FROM clothes";
$result = mysqli_query($dbc, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg" style="background-color: skyblue;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Clothing Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Add Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Existing Clothing Products</h2>
        <a href="products.php" class="btn btn-primary mb-3">Add New Product</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Clothing Name</th>
                    <th>Description</th>
                    <th>Quantity Available</th>
                    <th>Price</th>
                    <th>Material</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Brand</th>
                    <th>Added By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ClothingName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Material']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Size']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Color']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Brand']) . "</td>"; // Added Brand
                        echo "<td>" . htmlspecialchars($row['ProductAddedBy']) . "</td>";
                        echo "<td>
                                <a href='edit.php?clothingId=" . $row['ClothingID'] . "' class='btn btn-edit btn-sm'><i class='fas fa-pencil-alt'></i> Edit</a>
                                <a href='delete.php?delete_id=" . $row['ClothingID'] . "' class='btn btn-remove btn-sm' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash-alt'></i> Remove</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <footer class="text-center text-lg-start mt-5" style="background-color: skyblue;">
        <div class="text-center p-3" style="background-color: skyblue;">
            © 2024 Clothing Store. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
