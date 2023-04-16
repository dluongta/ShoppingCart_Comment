<?php
ob_start();
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($user_id)) {
   $_SESSION['user_id'] = 1;
   header('location:comment.php');
}


if ($user_id == 1) {
   $message[] = 'You need logging';
   ob_end_flush();
}

if ($user_id == 1 && isset($_POST['post_comment'])) {
    header('location:login.php');
 }

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:login.php');
}
;
if ($user_id != 1 && isset($_POST['post_comment'])) {

    $postMessage = $_POST['message'];
    $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
    }
    ;

    $sql = "INSERT INTO comment_db (name, message)
    VALUES ( '$fetch_user[name]', '$postMessage')";

    if ($conn->query($sql) === TRUE) {
        echo "";
    } else {
        echo "Error: " . $sql . "<br>" . $conn-> error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Page</title>
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
        <div class="container mt-3">
            <form action="" method="post" class="form">
                <div class="row mb-3">
                    <textarea name="message" cols="30" rows="10" class="message form-control"
                        placeholder="Message"></textarea>
                    <br>
                </div>
                <button type="submit" class="btn" name="post_comment">Post Comment</button>
            </form>
        </div>
        <div class="container">
            <?php

            $sql = "SELECT * FROM `comment_db` ORDER BY time DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    ?>
                    <div class="card">
                        <h3>
                            <?php echo $row['name']; ?>
                        </h3>
                        <p>
                            <?php echo $row['message']; ?>
                        </p>
                    </div>
                <?php }
            } ?>
        </div>
    </div>

    </div>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="d-flex align-items-center justify-content-center"><i class="fa fa-user"></i> LUEN2003</h5>
                    <div class="row ">
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