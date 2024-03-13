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

if(isset($_POST['add_product'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_FILES['product_image']['name'];
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
   $product_image_folder = 'images/'.$product_image;

   if(empty($product_name) || empty($product_price) || empty($product_image)){
      $message[] = 'please fill out all';
   }else{
      $insert = "INSERT INTO products(name, price, image) VALUES('$product_name', '$product_price', '$product_image')";
      $upload = mysqli_query($conn,$insert);
      if($upload){
         move_uploaded_file($product_image_tmp_name, $product_image_folder);
         $message[] = 'new product added successfully';
      }else{
         $message[] = 'could not add the product';
      }
   }

};

if(isset($_GET['delete'])){
   $id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `products` WHERE id = $id");
   header('location:admin_page.php');
};

if(isset($_GET['deleteOrder'])){
   $id = $_GET['deleteOrder'];
   mysqli_query($conn, "DELETE FROM `order` WHERE id = $id");
   header('location:admin_page.php');
};

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin page</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

   <link rel="stylesheet" href="css/style1.css">

</head>
<body>

<?php

if(isset($message)){
   foreach($message as $message){
      echo '<span class="message">'.$message.'</span>';
   }
}

?>
   
<div class="container">

   <div class="admin-product-form-container">

      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
         <h3>Add A New Product</h3>
         <input type="text" placeholder="enter product name" name="product_name" class="box">
         <input type="number" placeholder="enter product price" name="product_price" class="box">
         <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
         <input type="submit" class="btn" name="add_product" value="add product">
      </form>

   </div>

   <?php

   $select = mysqli_query($conn, "SELECT * FROM products");
   
   ?>
   <div class="product-display">
      <table class="product-display-table">
         <thead>
         <tr>
            <th>product image</th>
            <th>product name</th>
            <th>product price</th>
            <th>action</th>
         </tr>
         </thead>
         <?php while($row = mysqli_fetch_assoc($select)){ ?>
         <tr>
            <td><img src="images/<?php echo $row['image']; ?>" height="100" alt=""></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['price']; ?> VND </td>
            <td>
               <a href="admin_update.php?edit=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-edit"></i> edit </a>
               <a href="admin_page.php?delete=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-trash"></i> delete </a>
            </td>
         </tr>
      <?php } ?>
      </table>
   </div>

   <h3>Manage Order</h3>
   <?php

   $select_ = mysqli_query($conn, "SELECT * FROM `order`");
   
   ?>

   <div class="product-display">
      <table class="product-display-table">
         <thead>
         <tr>
            <th>name</th>
            <th>total product</th>
            <th>price</th>
            <th>time ordered</th>
            <th>delivered</th>
            <th>paid</th>
            <th>action</th>
         </tr>
         </thead>
         <?php while($row = mysqli_fetch_assoc($select_)){ ?>
         <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['total_products']; ?>  </td>
            <td><?php echo $row['total_price']; ?> VND </td>
            <td><?php echo $row['time_order']; ?></td>
            <td><?php if ($row['delivered'] == 0) { echo '❌';} else {echo '✅';} ?> </td>
            <td><?php if ($row['paid'] == 0) { echo '❌';} else {echo '✅';} ?> </td>
            <td>
               <a href="admin_update_order.php?edit=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-edit"></i> edit </a>
               <a href="admin_page.php?deleteOrder=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-trash"></i> delete </a>
            </td>
         </tr>
      <?php } ?>
      </table>
   </div>

</div>


</body>
</html>