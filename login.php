<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    require_once "database.php";

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user["id"];
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Something went wrong!";
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
    <title>Login</title>
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
                        <h2>Hello, Again</h2>
                        <p>Sign in for existing users</p>
                    </div>
                    <form action="login.php" method="post">
                        <div class="input-group mb-3">
                            <input type="text" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" required>
                        </div>
                        <div class="input-group mb-3">
                            <button type="submit" name="login" class="btn btn-lg btn-success w-100 fs-6">Login</button>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <div class="input-group mb-3 d-flex justify-content-between">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="formCheck">
                                <label for="formCheck" class="form-check-label text-secondary"><small>Remember Me</small></label>
                            </div>
                            <div class="forgot">
                                <small><a href="forgot_password.php">Forgot Password?</a></small>
                            </div>
                        </div>
                        <div class="input-group mb-3 d-flex justify-content-between">
                            <div class="form-check-label text-secondary"><small>Don't have an account?</small></div>
                            <div class="forgot">
                                <small><a href="registration.php">Sign Up</a></small>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</body>
</html>
