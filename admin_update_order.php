<?php

include 'config.php';

ob_start();
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   $_SESSION['user_id'] = 1;
   header('location:index.php');
}

$select_user_is_admin = mysqli_query($conn, "SELECT is_admin FROM `user_info` WHERE id = '$user_id'") or die('query failed');

if (mysqli_num_rows($select_user_is_admin) > 0) {
   $row = mysqli_fetch_assoc($select_user_is_admin);
   if ($row['is_admin'] == 0) {
      header('location:index.php');
   }
}

$id = $_GET['edit'];
 if (isset($_POST['order_paid'])) {
   mysqli_query($conn, "UPDATE `order` SET paid = TRUE WHERE id = '$id';") or die('query failed');
 } else {
   mysqli_query($conn, "UPDATE `order` SET paid = FALSE WHERE id = '$id';") or die('query failed');

 }
 if (isset($_POST['order_delivered'])) {
   mysqli_query($conn, "UPDATE `order` SET delivered = TRUE WHERE id = '$id';") or die('query failed');
 } else {
   mysqli_query($conn, "UPDATE `order` SET delivered = FALSE WHERE id = '$id';") or die('query failed');

 }

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="css/style1.css">
   
</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '<span class="message">' . $message . '</span>';
      }
   }
   ?>

   <div class="container">


      <div class="admin-product-form-container centered">


            <form action="" method="post" enctype="multipart/form-data" id="order_form">
               <h3 class="title">update order</h3>
               <label for="order_paid" class="text">paid</label>
               <input type="checkbox" name="order_paid" class="order_paid" />
               <br>
               <label for="order_delivered" class="text">delivered</label>
               <input type="checkbox" name="order_delivered" class="order_delivered" />
               <input type="submit" value="update order" name="update_order" class="btn" id = "update_order">
               <a href="admin_page.php" class="btn">Go Back!</a>
            </form>

      </div>

   </div>

</body>

</html>