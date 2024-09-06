<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reset_password"])) {
    $token = $_POST["token"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        require_once "database.php";

        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $passwordHash = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $passwordHash, $token);
            $stmt->execute();

            $success = "Your password has been reset successfully.";
        } else {
            $error = "Invalid or expired token.";
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
    <title>Reset Password</title>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-12 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h2>Reset Password</h2>
                        <p>Enter your new password below.</p>
                    </div>
                    <form action="reset_password.php" method="post">
                        <input type="hidden" name="token" value="<?= $_GET['token'] ?>">
                        <div class="input-group mb-3">
                            <input type="password" name="new_password" class="form-control form-control-lg bg-light fs-6" placeholder="New Password" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="confirm_password" class="form-control form-control-lg bg-light fs-6" placeholder="Confirm Password" required>
                        </div>
                        <div class="input-group mb-3">
                            <button type="submit" name="reset_password" class="btn btn-lg btn-success w-100 fs-6">Reset Password</button>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</body>
</html>
