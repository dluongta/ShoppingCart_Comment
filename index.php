<?php
ob_start();
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
   $_SESSION['user_id'] = 1;
   header('location:index.php');
}


if ($user_id == 1) {
   $message[] = 'You need logging';
   ob_end_flush();
}

if ($user_id == 1 && isset($_POST['checkout'])) {
   header('location:login.php');
}


if (isset($_GET['logout'])) {
   unset($user_id);
   session_destroy();
   header('location:login.php');
}
;

if (isset($_POST['add_to_cart'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if (mysqli_num_rows($select_cart) > 0) {
      $message[] = 'product already added to cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
      $message[] = 'product added to cart!';
   }

}
;

if (isset($_POST['update_cart'])) {
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'cart quantity updated successfully!';
}

if (isset($_GET['remove'])) {
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
   header('location:index.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shopping Cart</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php
   if (isset($message)){
      if (is_array($message) || is_object($message))
      {
       foreach ($message as $onMessage) {
       echo '<div class="message" onclick="this.remove();">' . $onMessage . '</div>';
       }
      }
   }
   ?>
   <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
      <div class="container-fluid">
         <a class="navbar-brand" href="index.php">Cart System</a>

         <ul class="navbar-nav me-auto">
            <li class="nav-item">
               <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" href="comment.php">Comment</a>
            </li>

         </ul>

      </div>
      </div>
   </nav>

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
            <a href="index.php?logout=<?php echo $user_id; ?>"
               onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
         </div>

      </div>
      <div class="container">
         <form>
            <div class="row">
               <div class="col">
                  <label for="">Start Price</label>
                  <input type="text" name="start_price" value="<?php if (isset($_GET['start_price'])) {
                     echo $_GET['start_price'];
                  } else {
                     echo "0";
                  } ?>" class="form-control">
               </div>
               <div class="col">
                  <label for="">End Price</label>
                  <input type="text" name="end_price" value="<?php if (isset($_GET['end_price'])) {
                     echo $_GET['end_price'];
                  } else {
                     echo "2000";
                  } ?>" class="form-control">
               </div>
               <div class="col">
                  <label for="">Filter Price</label> <br />
                  <button type="submit" class="option-btn ">Filter</button>
               </div>
            </div>
         </form>
      </div>

      <div class="products">

         <h1 class="heading">Our Products</h1>

         <div class="box-container">

            <?php
            if (isset($_GET['start_price']) && isset($_GET['end_price'])) {
               $startprice = $_GET['start_price'];
               $endprice = $_GET['end_price'];

               $select_product = mysqli_query($conn, "SELECT * FROM `products` WHERE price BETWEEN $startprice AND $endprice") or die('query failed');
            } else {
               $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            }

            if (mysqli_num_rows($select_product) > 0) {
               while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                  ?>
                  <form method="post" class="box" action="">
                     <img src="images/<?php echo $fetch_product['image']; ?>" alt="">
                     <div class="name">
                        <?php echo $fetch_product['name']; ?>
                     </div>
                     <div class="price">
                        <?php echo $fetch_product['price']; ?> VND
                     </div>
                     <input type="number" min="1" name="product_quantity" value="1">
                     <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                     <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                     <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                     <input type="submit" value="add to cart" name="add_to_cart" class="btn">
                  </form>
                  <?php
               }
               ;
            }
            ;
            ?>

         </div>

      </div>

      <div class="shopping-cart">

         <h1 class="heading">shopping cart</h1>

         <table>
            <thead>
               <th>image</th>
               <th>name</th>
               <th>price</th>
               <th>quantity</th>
               <th>total price</th>
               <th>action</th>
            </thead>
            <tbody>
               <?php
               $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $grand_total = 0;
               if (mysqli_num_rows($cart_query) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
                     ?>
                     <tr>
                        <td><img src="images/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                        <td>
                           <?php echo $fetch_cart['name']; ?>
                        </td>
                        <td>$
                           <?php echo $fetch_cart['price']; ?>/-
                        </td>
                        <td>
                           <form action="" method="post">
                              <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                              <input type="number" min="1" name="cart_quantity"
                                 value="<?php echo $fetch_cart['quantity']; ?>">
                              <input type="submit" name="update_cart" value="update" class="option-btn">
                           </form>
                        </td>
                        <td>$
                           <?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-
                        </td>
                        <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn"
                              onclick="return confirm('remove item from cart?');">remove</a></td>
                     </tr>
                     <?php
                     $grand_total += $sub_total;
                  }
               } else {
                  echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">no item added</td></tr>';
               }
               ?>
               <tr class="table-bottom">
                  <td colspan="4">Total :</td>
                  <td> 
                     <?php echo $grand_total; ?> VND
                  </td>
                  <td><a href="index.php?delete_all" onclick="return confirm('delete all from cart?');"
                        class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">delete all</a></td>
               </tr>
            </tbody>
         </table>
            <form method="post" >
         <button name="checkout" class="cart-btn">
            <a href="index.php?#" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">proceed to checkout</a>
         </button>
            </form>

      </div>

   </div>
   <footer class="footer">
      <div class="container">
         <div class="row">
            <div class="col-md-12">
               <h5 class="d-flex align-items-center justify-content-center"><i class="fa fa-user"></i> LUEN2003</h5>
               <div class="row">
                  <div class="col-6 d-flex align-items-center justify-content-center">
                     <ul class="list-unstyled">
                        <li><a href="#">Product</a></li>
                        <li><a href="#">Benefits</a></li>
                        <li><a href="#">Partners</a></li>
                        <li><a href="#">Team</a></li>
                     </ul>
                  </div>
                  <div class="col-6 d-flex align-items-center justify-content-center">
                     <ul class="list-unstyled">
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Support</a></li>
                        <li><a href="#">Legal Terms</a></li>
                        <li><a href="#">About</a></li>
                     </ul>
                  </div>
               </div>
               <ul class="nav d-flex align-items-center justify-content-center ">
                  <li class="nav-item"><a href="" class="nav-link "><i class="fa fa-facebook fa-lg"></i></a></li>
                  <li class="nav-item"><a href="" class="nav-link"><i class="fa fa-twitter fa-lg"></i></a></li>
                  <li class="nav-item"><a href="" class="nav-link"><i class="fa fa-github fa-lg"></i></a></li>
                  <li class="nav-item"><a href="" class="nav-link"><i class="fa fa-instagram fa-lg"></i></a></li>
               </ul>
               <br>
            </div>
         </div>
      </div>
   </footer>

</body>

</html>