<?php

ob_start();
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
   $_SESSION['user_id'] = 1;
   header('location:checkout.php');
}


if ($user_id == 1) {
   $message[] = 'You need logging';
   ob_end_flush();
}

if ($user_id == 1 && isset($_POST['order_btn'])) {
   header('location:login.php');
}

if ($user_id != 1 && isset($_POST['order_btn'])) {

   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $address = $_POST['address'];

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'");
   $price_total = 0;
   if (mysqli_num_rows($cart_query) > 0) {
      while ($product_item = mysqli_fetch_assoc($cart_query)) {
         $product_name[] = $product_item['name'] . ' (' . $product_item['quantity'] . ') ';
         $product_price = number_format($product_item['price'] * $product_item['quantity']);
         $price_total += $product_price;
      }
      ;
   }
   ;

   $total_product = implode(', ', $product_name);
   $detail_query = mysqli_query($conn, "INSERT INTO `order`(user_id,name, number, email, method, address, total_products, total_price) VALUES('$user_id','$name','$number','$email','$method','$address','$total_product','$price_total')") or die('query failed');

   if ($cart_query && $detail_query) {
      echo "
      <div class='order-message-container'>
      <div class='message-container'>
         <h3>thank you for shopping!</h3>
         <div class='order-detail'>
            <span>" . $total_product . "</span>
            <span class='total'> total : " . $price_total . " VND  </span>
         </div>
         <div class='customer-details'>
            <p> your name : <span>" . $name . "</span> </p>
            <p> your number : <span>" . $number . "</span> </p>
            <p> your email : <span>" . $email . "</span> </p>
            <p> your address : <span>" . $address . "</span> </p>
            <p> your payment mode : <span>" . $method . "</span> </p>
            <p>Pay when product arrives</p>
         </div>
            <a href='index.php' class='btn'>Continue Shopping</a>
         </div>
      </div>
      ";
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php
   if (isset($message)) {
      if (is_array($message) || is_object($message)) {
         foreach ($message as $onMessage) {
            echo '<div class="message" onclick="this.remove();">' . $onMessage . '</div>';
         }
      }
   }
   ?>
   <header className='header'>
      <div className='container flex'>
         <div className='nav'>
            <ul className="nav-links">
               <li>
                  <a href='index.php'> Home </a>
               </li>

               <li>
                  <a href='comment.php'> Comment </a>
               </li>
               <div class='nav-items-icon'>
                  <li>
                     <?php
                     $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
                     if (mysqli_num_rows($select_user) > 0) {
                        $fetch_user = mysqli_fetch_assoc($select_user);
                     }
                     ;
                     ?>
                     <div class='text'>

                        <p> Username : <span>
                              <?php echo $fetch_user['name']; ?>
                           </span> </p>
                     </div>
                  </li>
                  <li>
                     <a href="login.php">Login</a>
                  </li>

                  <li>
                     <a href="register.php">Sign Up</a>
                  </li>
                  <li>
                     <a href="index.php?logout=<?php echo $user_id; ?>">Logout</a>
                  </li>
               </div>

            </ul>
         </div>

         </button>

      </div>

   </header>

   <div class="container">
      <div class="user-profile">

         <?php
         $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
         if (mysqli_num_rows($select_user) > 0) {
            $fetch_user = mysqli_fetch_assoc($select_user);
         }
         ;
         ?>

         <p> username : <span>
               <?php echo $fetch_user['name']; ?>
            </span> </p>
         <div class="flex">
            <a href="login.php" class="btn">login</a>
            <a href="register.php" class="option-btn">register</a>
            <a href="index.php?logout=<?php echo $user_id; ?>" class="delete-btn">logout</a>
         </div>

      </div>
   </div>

   <div class="container">

      <section class="checkout-form">

         <h1 class="heading">Complete Your Order</h1>

         <form action="" method="post">

            <div class="display-order">
               <?php
               $select_cart = mysqli_query($conn, "SELECT * FROM `cart`  WHERE user_id = '$user_id'");
               $total = 0;
               $grand_total = 0;
               if (mysqli_num_rows($select_cart) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                     $total_price = number_format($fetch_cart['price'] * $fetch_cart['quantity']);
                     $grand_total = $total += $total_price;
                     ?>
                     <span>
                        <?= $fetch_cart['name']; ?>(
                        <?= $fetch_cart['quantity']; ?>)
                     </span>
                     <?php
                  }
               } else {
                  echo "<div class='display-order'><span>Your cart is empty!</span></div>";
               }
               ?>
               <span class="grand-total"> grand total :
                  <?= $grand_total; ?>VND
               </span>
            </div>

            <div class="flex">
               <div class="inputBox">
                  <span>your name</span>
                  <input type="text" placeholder="enter your name" name="name" required>
               </div>
               <div class="inputBox">
                  <span>your number</span>
                  <input type="number" placeholder="enter your number" name="number" required>
               </div>
               <div class="inputBox">
                  <span>your email</span>
                  <input type="email" placeholder="enter your email" name="email" required>
               </div>
               <div class="inputBox">
                  <span>payment method</span>
                  <select name="method">
                     <option value="cash on delivery" selected>cash on devlivery</option>
                     <option value="credit cart">credit cart</option>
                  </select>
               </div>
               <div class="inputBox">
                  <span>address </span>
                  <input type="text" placeholder="your address" name="address" required>
               </div>


            </div>
            <input type="submit" value="order now" name="order_btn" class="btn">
         </form>

      </section>

   </div>


</body>

</html>