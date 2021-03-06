/** Default landing page for HolyHope */
<?php
  include "functions.php";
  session_start(); /** Start a session so that $_SESSION[] can be accessed to prove if the user has logged in. */
  if(isset($_POST['submit'])){ /** If the user has previously submit thier login details and they now need authenticating run assign the following variables */
    $username=$_POST['username'];
    $password=$_POST['password'];

  if (loginAutentication($username, $password)){ /** Check login details against database hashed values */
  $_SESSION['username'] = $username; /** Assign a SESSION variable 'username' that can be used to prove the user has previously logged in */
  header('location:home.php');
  }
  else{
    
    echo"<h3>Login Failed Please enter the correct login detail <br> Hint - check whatsapp" . get_password_hint($username) . "</h3>";
  }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <title>Login | Page</title>
</head>

<body>

    <form name="loginauth" method="post">
        <div class="formcontainer">
        <h3>Login Form</h3>
            <label for="uname"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="username">

            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" required>
        
            <button type="submit" name="submit">Login</button>
        </div>
        
    </form>
</body>
</html>
