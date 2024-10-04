<?php
require('db_connection_mysqli.php');

// Initialize variables to hold form values and error messages
$clothingId = $clothingName = $description = $quantity = $price = $brand = $material = $size = $color = "";
$clothingNameErr = $descriptionErr = $quantityErr = $priceErr = $brandErr = $materialErr = $sizeErr = $colorErr = "";

// Check if the clothing ID is provided
if (isset($_GET['clothingId'])) {
    $clothingId = $_GET['clothingId'];

    // Fetch existing clothing details from the database
    $query = "SELECT * FROM clothes WHERE ClothingID = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, 'i', $clothingId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If the clothing item is found, populate the form fields
    if ($row = mysqli_fetch_assoc($result)) {
        $clothingName = $row['ClothingName'];
        $description = $row['Description'];
        $quantity = $row['Quantity'];
        $price = $row['Price'];
        $brand = $row['Brand'];
        $material = $row['Material'];
        $size = $row['Size'];
        $color = $row['Color'];
    } else {
        echo "Clothing item not found!";
        exit;
    }
}

// Function to sanitize form inputs
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate form inputs after submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Clothing Name
    if (empty($_POST["clothingName"])) {
        $clothingNameErr = "Clothing Name is required";
    } else {
        $clothingName = cleanInput($_POST["clothingName"]);
        if (!preg_match("/^[a-zA-Z0-9 ]*$/", $clothingName)) {
            $clothingNameErr = "Only letters, numbers, and white space allowed";
        }
    }

    // Validate Description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = cleanInput($_POST["description"]);
    }

    // Validate Quantity (must be a positive integer)
    if (empty($_POST["quantity"])) {
        $quantityErr = "Quantity is required";
    } else {
        $quantity = cleanInput($_POST["quantity"]);
        if (!filter_var($quantity, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $quantityErr = "Quantity must be a positive integer";
        }
    }

    // Validate Price (must be a positive decimal number)
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = cleanInput($_POST["price"]);
        if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
            $priceErr = "Price must be a positive number";
        }
    }

    // Validate Brand
    if (empty($_POST["brand"])) {
        $brandErr = "Brand is required";
    } else {
        $brand = cleanInput($_POST["brand"]);
    }

    // Validate Material
    if (empty($_POST["material"])) {
        $materialErr = "Material is required";
    } else {
        $material = cleanInput($_POST["material"]);
    }

    // Validate Size
    if (empty($_POST["size"])) {
        $sizeErr = "Size is required";
    } else {
        $size = cleanInput($_POST["size"]);
    }

    // Validate Color (only letters allowed)
    if (empty($_POST["color"])) {
        $colorErr = "Color is required";
    } else {
        $color = cleanInput($_POST["color"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $color)) {
            $colorErr = "Only letters and white space allowed for color";
        }
    }

    // If all validations pass, proceed with form submission
    if (empty($clothingNameErr) && empty($descriptionErr) && empty($quantityErr) && empty($priceErr) &&
        empty($brandErr) && empty($materialErr) && empty($sizeErr) && empty($colorErr)) {

        // Clean inputs
        $clothingName_clean = prepare_string($dbc, $clothingName);
        $description_clean = prepare_string($dbc, $description);
        $quantity_clean = prepare_string($dbc, $quantity);
        $price_clean = prepare_string($dbc, $price);
        $brand_clean = prepare_string($dbc, $brand);
        $material_clean = prepare_string($dbc, $material);
        $size_clean = prepare_string($dbc, $size);
        $color_clean = prepare_string($dbc, $color);

        // Update data in the database
        $updateQuery = "UPDATE clothes SET ClothingName=?, Description=?, Quantity=?, Price=?, Brand=?, Material=?, Size=?, Color=? WHERE ClothingID=?";
        $updateStmt = mysqli_prepare($dbc, $updateQuery);

        // Bind parameters
        mysqli_stmt_bind_param($updateStmt, 'ssssssssi', $clothingName_clean, $description_clean, $quantity_clean, 
                               $price_clean, $brand_clean, $material_clean, $size_clean, $color_clean, $clothingId);

        // Execute the statement
        $result = mysqli_stmt_execute($updateStmt);

        if ($result) {
            header("Location: index.php"); // Redirect on success to refresh the page
            exit;
        } else {
            echo "<br>Some error in updating the data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Clothing Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Clothing Product</h2>
        <form action="edit.php?clothingId=<?php echo $clothingId; ?>" method="POST">
            <div class="mb-3">
                <label for="clothingName" class="form-label">Clothing Name</label>
                <input type="text" class="form-control" id="clothingName" name="clothingName" value="<?php echo $clothingName; ?>">
                <span class="text-danger"><?php echo $clothingNameErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" id="description" name="description" value="<?php echo $description; ?>">
                <span class="text-danger"><?php echo $descriptionErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $quantity; ?>">
                <span class="text-danger"><?php echo $quantityErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $price; ?>">
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
        <option value="" disabled>Select Material</option>
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
            <button type="submit" class="btn btn-primary">Update Product</button>
           

            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
