<?php
require('db_connection_mysqli.php');

// Initialize variables
$username = $password = "";
$usernameErr = $passwordErr = "";

// Function to sanitize form inputs
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Validate form inputs after submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    // If all validations pass, proceed with registration
    if (empty($usernameErr) && empty($passwordErr)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $query = "INSERT INTO admin (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($dbc, $query);

        // Check if the statement was prepared successfully
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $username, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php"); // Redirect to login page
                exit;
            } else {
                echo "Error executing statement: " . mysqli_stmt_error($stmt);
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($dbc);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Clothing Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6dd5ed, #2193b0); /* Gradient background */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: dance 1.5s ease-in-out infinite alternate;
        }
        @keyframes dance {
            0% { transform: translateY(0); }
            25% { transform: translateY(-5px); }
            50% { transform: translateY(5px); }
            75% { transform: translateY(-3px); }
            100% { transform: translateY(0); }
        }
        .btn-primary {
            background: linear-gradient(90deg, #ff6a00, #ee0979); /* Gradient for button */
            border: none;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #ee0979, #ff6a00); /* Reverse gradient on hover */
        }
        .text-danger {
            font-size: 0.875em;
        }
        .form-label {
            color: #ff6a00; /* Color for labels */
        }
        .form-control {
            border: 2px solid #ff6a00; /* Border color for inputs */
        }
        .form-control:focus {
            border-color: #ee0979; /* Focus color for inputs */
            box-shadow: 0 0 5px rgba(238, 9, 121, 0.5); /* Shadow on focus */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4 text-black">Admin Registration</h2>
                <form method="POST" action="register.php" class="form-container">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>">
                        <span class="text-danger"><?php echo $usernameErr; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password">
                        <span class="text-danger"><?php echo $passwordErr; ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
