<?php

include 'config.php';

if (isset($_POST['submit'])) {

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

   $select = mysqli_query($conn, "SELECT * FROM `user_info` WHERE name = '$name' AND password = '$pass'") or die('query failed');

   if (mysqli_num_rows($select) > 0) {
      $message[] = 'user already exist!';
   } else {
      mysqli_query($conn, "INSERT INTO `user_info`(name,  password) VALUES('$name', '$pass')") or die('query failed');
      $message[] = 'registered successfully!';
      header('location:login.php');
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Page</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
      }
   }
   ?>

   <div class="form-container">

      <form action="" method="post">
         <h3>Sign Up</h3>
         <input type="text" name="name" required placeholder="enter username" class="box">
         <input type="password" name="password" required placeholder="enter password" class="box">
         <input type="password" name="cpassword" required placeholder="confirm password" class="box">
         <input type="submit" name="submit" class="btn" value="register now">
         <p>Already have an account? <a href="login.php">Log in</a></p>
      </form>

   </div>

</body>

</html>