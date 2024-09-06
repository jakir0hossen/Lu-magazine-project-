<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required";
    }
    
    // Email Validation for cse_2112020022@lus.ac.bd format
    if (!preg_match("/^([a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|[a-z]{3}_[0-9]{9}@lus\.ac\.bd))$/", $email)) {
        $errors[] = "Email must be in the format: cse_2112020022@lus.ac.bd";
    }

    // Password validation for at least 8 characters, with at least one uppercase, one lowercase, and one number
    if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, and one number";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Password does not match!";
    }

    require_once "database.php";
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists!";
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $fullname, $email, $passwordHash);
            $stmt->execute();
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="loginSignup.css">
    <title>Register</title>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: rgb(1, 102, 1);">
                <div class="featured-image mb-3">
                    <img src="images/logo.png" class="img-fluid" style="width: 250px;">
                </div>
                <p class="text-white fs-2">LU Magazine</p>
            </div> 
            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2 class="">Hello, User</h2>
                        <p>Create an account for new users </p>
                    </div>
                    <form action="registration.php" method="post">
                        <div class="input-group mb-3">
                            <input type="text" name="fullname" class="form-control form-control-lg bg-light fs-6" placeholder="Name" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="confirm_password" class="form-control form-control-lg bg-light fs-6" placeholder="Confirm Password" required>
                        </div>
                        <div class="input-group mb-3">
                            <button type="submit" name="submit" class="btn btn-lg btn-success w-100 fs-6">Register</button>
                        </div>
                        <?php if (!empty($errors)): ?>
                            <?php foreach ($errors as $error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="input-group mb-3 d-flex justify-content-between">
                            <div class="form-check-label text-secondary"><small>Already have an account?</small></div>
                            <div class="forgot">
                                <small><a href="login.php">Login here</a></small>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</body>
</html>
