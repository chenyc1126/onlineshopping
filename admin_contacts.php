<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `message` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_contacts.php');
}


if(isset($_POST['update_message'])){

   $message_update_id = $_POST['message_id'];
   $update_message = $_POST['update_contact'];
   mysqli_query($conn, "UPDATE `message` SET status = '$update_message' WHERE id = '$message_update_id'") or die('query failed');
   echo '<script>alert("訊息狀態已更新!")</script>';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>訊息</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="icon" href="images/favicon.ico" />
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="messages">

   <h1 class="title"> 收到的訊息 </h1>

   <div class="box-container">
   <?php
      $select_message = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
      if(mysqli_num_rows($select_message) > 0){
         while($fetch_message = mysqli_fetch_assoc($select_message)){
      
   ?>
   <div class="box">
      <p> user id : <span><?php echo $fetch_message['user_id']; ?></span> </p>
      <p> 姓名 : <span><?php echo $fetch_message['name']; ?></span> </p>
      <p> 號碼 : <span><?php echo $fetch_message['number']; ?></span> </p>
      <p> email : <span><?php echo $fetch_message['email']; ?></span> </p>
      <p> 訊息 : <span><?php echo $fetch_message['message']; ?></span> </p>
      
      <form action="" method="post">
            <input type="hidden" name="message_id" value="<?php echo $fetch_message['id']; ?>">
            <select name="update_contact">
               <option value="" selected disabled><?php echo $fetch_message['status']; ?></option>
               <option value="尚未回覆">尚未回覆</option>
               <option value="已經回覆">已經回覆</option>
            </select>
            <input type="submit" value="更新" name="update_message" class="option-btn">
      <a href="admin_contacts.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('刪除訊息?');" class="delete-btn">刪除此訊息</a>
      </form>
      <style>
         .messages .box-container .box form {
            text-align: center;
          /* 臨時的紅色邊框 */
         }
         
         .messages .box-container .box form select{
            border-radius: .5rem;
            margin:.5rem 0;
            width: 100%;
            background-color: var(--light-bg);
            border:var(--border);
            padding:1.2rem 1.4rem;
            font-size: 1.8rem;
            color:var(--black);
         }
         </style>

   </div>
   <?php
      };
   }else{
      echo '<p class="empty">還沒有收到任何訊息!</p>';
   }
   ?>
   </div>

</section>









<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
