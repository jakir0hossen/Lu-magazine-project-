<?php
session_start();
if(isset($_SESSION["user"])){
    header("Location: login.php");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
           <p class="text-white fs-2">Lu Magazine</p>
          
       </div> 

    
        
       <div class="col-md-6 right-box">
          <div class="row align-items-center">
                <div class="header-text mb-4">
                     <h2 class="">Hello,Again</h2>
                     <p>We are happy to have you back.</p>
                </div>
                <form action="registration.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Name">
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Email address">
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password">
                </div>
               
            
               
                <div class="input-group mb-3">
                    <button class="btn btn-lg btn-success w-100 fs-6">Login</button>
                </div>
                <div class="input-group mb-3">
                    <button class="btn btn-lg btn-light w-100 fs-6"><img src="login_registration/imagess/google.png" style="width:20px" class="me-2"><small>Sign In with Google</small></button>
                </div>
              
                <div class="input-group mb-3 d-flex justify-content-between">
                        
                     <div class="form-check-label text-secondary"><small>Already have an account?</small></div>
                    
                <div class="forgot">
                          <small><a href="login.php">Login here</a></small>
                    </div>
                    </form>
                </div>
          </div>
       </div> 

      </div>
    </div>

</body>
</html>


<?php
        if(isset($_POST["submit"])){
            $fullname = $_POST["fullname"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $confirm_password = $_POST["confirm_password"];

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();

            if(empty($fullname) OR empty($email) OR empty($password) OR empty($confirm_password)){
                array_push($errors, "All fields are required");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email is not valid");
            }
            if (strlen($password)<8) {
                array_push($errors, "Password must be at least 8 characters long");
            }
            if ($password !== $confirm_password) {
                array_push($errors,"Password does not match!");
            }

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount>0) {
                array_push($errors, "Email already exists!");
            }
            if (count($errors)>0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else{
                
                $sql = "INSERT INTO users(full_name, email, password) VALUES ( ? , ? , ? )";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if($prepareStmt){
                    mysqli_stmt_bind_param($stmt,"sss",$fullname,$email,$passwordHash);
                    mysqli_stmt_execute($stmt);
                    echo"<div class='alert alert-success'>You are registered successfully.</div>";
                    header("Location: login.php");
                }
                else{
                    die("Something went wrong");
                }
            }
        }
        ?>