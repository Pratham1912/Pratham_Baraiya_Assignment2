<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Function to sanitize input
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Initialize variables and error messages
$clothingName = $description = $quantity = $price = $brand = $material = $size = $color = "";
$clothingNameErr = $descriptionErr = $quantityErr = $priceErr = $brandErr = $materialErr = $sizeErr = $colorErr = "";
$productAddedBy = "Admin";  

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Clothing Name
    if (empty($_POST["clothingName"])) {
        $clothingNameErr = "Clothing Name is required";
    } else {
        $clothingName = cleanInput($_POST["clothingName"]);
    }

    // Validate Description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = cleanInput($_POST["description"]);
    }

    // Validate Quantity
    if (empty($_POST["quantity"])) {
        $quantityErr = "Quantity is required";
    } elseif (!is_numeric($_POST["quantity"]) || intval($_POST["quantity"]) < 0) {
        $quantityErr = "Quantity must be a non-negative number";
    } else {
        $quantity = cleanInput($_POST["quantity"]);
    }

    // Validate Price
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } elseif (!is_numeric($_POST["price"]) || floatval($_POST["price"]) < 0) {
        $priceErr = "Price must be a non-negative number";
    } else {
        $price = cleanInput($_POST["price"]);
    }

    // Validate Brand
    if (!empty($_POST["brand"])) {
        $brand = cleanInput($_POST["brand"]);
        if (strlen($brand) > 100) {
            $brandErr = "Brand must not exceed 100 characters";
        }
    } else {
        $brand = "";  // Optional, so set to empty if not provided
    }

    // Validate Material
    if (empty($_POST["material"])) {
        $materialErr = "Material is required"; // Set error if no material is selected
    } else {
        $material = cleanInput($_POST["material"]);
    }

    // Validate Size
    if (empty($_POST["size"])) {
        $sizeErr = "Size is required"; // Set error if no size is selected
    } else {
        $size = cleanInput($_POST["size"]);
    }

    // Validate Color
    if (empty($_POST["color"])) {
        $colorErr = "Color is required";
    } else {
        $color = cleanInput($_POST["color"]);
    }

    // If no errors, insert the data into the database
    if (empty($clothingNameErr) && empty($descriptionErr) && empty($quantityErr) && empty($priceErr) && empty($brandErr) && empty($materialErr) && empty($sizeErr) && empty($colorErr)) {
        $stmt = $dbc->prepare("INSERT INTO clothes (ClothingName, Description, Quantity, Price, Brand, Material, Size, Color, ProductAddedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissssss", $clothingName, $description, $quantity, $price, $brand, $material, $size, $color, $productAddedBy);

        if ($stmt->execute()) {
            // Redirect to the index page after successful insert
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Clothing Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <h2>Add New Clothing Product</h2>
    <form action="products.php" method="POST">
        <div class="mb-3">
            <label for="clothingName" class="form-label">Clothing Name</label>
            <input type="text" class="form-control" id="clothingName" name="clothingName" value="<?php echo $clothingName; ?>">
            <span class="text-danger"><?php echo $clothingNameErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"><?php echo $description; ?></textarea>
            <span class="text-danger"><?php echo $descriptionErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="text" class="form-control" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
            <span class="text-danger"><?php echo $quantityErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" id="price" name="price" value="<?php echo $price; ?>">
            <span class="text-danger"><?php echo $priceErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $brand; ?>">
            <span class="text-danger"><?php echo $brandErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="material" class="form-label">Material</label>
            <select name="material" id="material" class="form-select">
                <option value="">Select Material</option>
                <option value="Cotton" <?php if ($material == 'Cotton') echo 'selected'; ?>>Cotton</option>
                <option value="Denim" <?php if ($material == 'Denim') echo 'selected'; ?>>Denim</option>
                <option value="Polyester" <?php if ($material == 'Polyester') echo 'selected'; ?>>Polyester</option>
                <option value="Silk" <?php if ($material == 'Silk') echo 'selected'; ?>>Silk</option>
                <option value="Wool" <?php if ($material == 'Wool') echo 'selected'; ?>>Wool</option>
                <option value="Fleece" <?php if ($material == 'Fleece') echo 'selected'; ?>>Fleece</option>
                <option value="Linen" <?php if ($material == 'Linen') echo 'selected'; ?>>Linen</option>
                <option value="Leather" <?php if ($material == 'Leather') echo 'selected'; ?>>Leather</option>
            </select>
            <span class="text-danger"><?php echo $materialErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="size" class="form-label">Size</label>
            <select class="form-select" id="size" name="size">
                <option value="" selected disabled>Select Size</option>
                <option value="S" <?php if ($size == 'S') echo 'selected'; ?>>Small (S)</option>
                <option value="M" <?php if ($size == 'M') echo 'selected'; ?>>Medium (M)</option>
                <option value="L" <?php if ($size == 'L') echo 'selected'; ?>>Large (L)</option>
                <option value="XL" <?php if ($size == 'XL') echo 'selected'; ?>>Extra Large (XL)</option>
                <option value="XXL" <?php if ($size == 'XXL') echo 'selected'; ?>>Double Extra Large (XXL)</option>
            </select>
            <span class="text-danger"><?php echo $sizeErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">Color</label>
            <input type="text" class="form-control" id="color" name="color" value="<?php echo $color; ?>">
            <span class="text-danger"><?php echo $colorErr; ?></span>
        </div>

        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
