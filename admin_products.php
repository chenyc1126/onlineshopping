<?php
# 1027 fix : category,book_type,type 在更新時沒有顯示的問題、按不到按鈕的問題
# 1027 fix : 庫存量需大於0
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_product'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $author = $_POST['author'];
   $edition = $_POST['edition'];
   $book_type = $_POST['book_type'];
   $type = $_POST['type'];
   $category = $_POST['category'];
   $inventory = $_POST['inventory'];
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select_product_name = mysqli_query($conn, "SELECT name FROM `products` WHERE name = '$name'") or die('query failed');

   if(mysqli_num_rows($select_product_name) > 0){
      $message[] = 'product name already added';
   }else{
      $add_product_query = mysqli_query($conn, "INSERT INTO `products`(name, price, author, edition, type, category,book_type, inventory, image) VALUES('$name', '$price', '$author', '$edition', '$type', '$category' ,'$book_type','$inventory', '$image')") or die('query failed');
      
      if($add_product_query){
         if($image_size > 2000000){
            $message[] = 'image size is too large';
         }else{
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'product added successfully!';
         }
      }else{
         $message[] = 'product could not be added!';
      }
   }
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT image FROM `products` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   mysqli_query($conn, "DELETE FROM `products` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_products.php');
}

if(isset($_POST['update_product'])){

   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_price = $_POST['update_price'];
   $update_author = $_POST['update_author'];
   $update_edition = $_POST['update_edition'];
   $update_book_type = $_POST['update_book_type'];
   $update_type = $_POST['update_type'];
   $update_category = $_POST['update_category'];
   $update_inventory = $_POST['update_inventory'];
 

   mysqli_query($conn, "UPDATE `products` SET name = '$update_name', price = '$update_price', author='$update_author', edition='$update_edition', type='$update_type', 
   category='$update_category', book_type='$update_book_type',inventory='$update_inventory' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = 'uploaded_img/'.$update_image;
   $update_old_image = $_POST['update_old_image'];

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'image file size is too large';
      }else{
         mysqli_query($conn, "UPDATE `products` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('uploaded_img/'.$update_old_image);
      }
   }

   header('location:admin_products.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- product CRUD section starts  -->

<section class="add-products">

   <h1 class="title">書籍總覽</h1>

   <!-- 新增商品  -->
   <form action="" method="post" enctype="multipart/form-data">
      <h3>新增商品</h3>
      <input type="text" name="name" class="box" placeholder="輸入產品名稱" required>
      <input type="number" min="0" name="price" class="box" placeholder="輸入產品價格" required>
      <input type="text" name="author" class="box" placeholder="輸入作者" required>
      <input type="text" name="edition" class="box" placeholder="輸入版本(僅輸入數字)" required>
      <select name="book_type" class="box" required>
      <option value="" disabled selected>選擇書籍版本</option>
      <option value="精裝版">精裝版</option>
      <option value="平裝版">平裝版</option>
      </select>
      <select name="type" class="box" required>
      <option value="" disabled selected>選擇閱讀類型</option>
      <option value="實體書">實體書</option>
      <option value="電子書">電子書</option>
      </select>
      <select name="category" class="box" required>
      <option value="" disabled selected>選擇書本種類</option>
      <option value="文學小說">文學小說</option>
      <option value="語言學習">語言學習</option>
      <option value="心靈成長">心靈成長</option>
      <option value="自然科學">自然科學</option>
      <option value="商業理財">商業理財</option>
      <option value="教育">教育</option>
      <option value="飲食與健康">飲食與健康</option>
      <option value="旅遊">旅遊</option>
      </select>
      <input type="number" name="inventory" class="box" placeholder="輸入庫存數(僅輸入數字)" min="1" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input type="submit" value="新增書籍!" name="add_product" class="btn">
   
   </form>

</section>

<!-- product CRUD section ends -->

<!-- show products  -->

<section class="show-products">

   <div class="box-container">

      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products))

            {
      ?>
      <div class="box">
         <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
         <div class="name">書名: <?php echo $fetch_products['name']; ?></div>
         <div class="price">$<?php echo $fetch_products['price']; ?></div>
         <div class="author">作者: <?php echo $fetch_products['author']; ?></div>
         <div class="edition">版本: <?php echo $fetch_products['edition']; ?></div>
         <div class="book_type">書籍版本: <?php echo $fetch_products['book_type']; ?></div>
         <div class="type">閱讀類型: <?php echo $fetch_products['type']; ?></div>
         <div class="category">書本種類: <?php echo $fetch_products['category']; ?></div>
         <div class="inventory">庫存量: <?php echo $fetch_products['inventory']; ?></div>
         
         <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">更新</a>
         <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">刪除</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">尚未新增任何商品</p>';
      }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
   <div>
   <form action="" method="post" enctype="multipart/form-data" style="height:700px;width:45%;margin:auto;overflow:auto;background:#EEEEEE;">
      <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
      <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
      <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="輸入書名">
      <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="輸入價格">
      <input type="text" name="update_author" value="<?php echo $fetch_update['author']; ?>" class="box" required placeholder="輸入作者">
      <input type="number" name="update_edition" value="<?php echo $fetch_update['edition']; ?>" class="box" required placeholder="輸入版本">

      <!-- 書籍版本 (book_type)-->
      <select name="update_book_type" class="box"> 
      <option value="" disabled selected><?php echo $fetch_update['book_type']; ?></option>
      <option value="精裝版"<?php echo ($fetch_update['book_type'] == "精裝版") ? "selected" : ""; ?>>精裝版</option>
      <option value="平裝版"<?php echo ($fetch_update['book_type'] == "平裝版") ? "selected" : ""; ?>>平裝版</option>
      </select>

      <!-- 閱讀類型 (type)-->
      <select name="update_type" class="box" >
      
      <option value="實體書" <?php echo ($fetch_update['type'] == "實體書") ? "selected" : ""; ?>>實體書</option>
      <option value="電子書" <?php echo ($fetch_update['type'] == "電子書") ? "selected" : ""; ?>>電子書</option>
      
      </select>
      
      <!-- 書本種類 (category)-->
      <select name="update_category" class="box">
      <option value="" disabled selected><?php echo $fetch_update['category']; ?></option>
      <option value="文學小說" <?php echo ($fetch_update['category'] == "文學小說") ? "selected" : ""; ?>>文學小說</option>
      <option value="語言學習" <?php echo ($fetch_update['category'] == "語言學習") ? "selected" : ""; ?>>語言學習</option>
      <option value="心靈成長" <?php echo ($fetch_update['category'] == "心靈成長") ? "selected" : ""; ?>>心靈成長</option>
      <option value="自然科學" <?php echo ($fetch_update['category'] == "自然科學") ? "selected" : ""; ?>>自然科學</option>
      <option value="商業理財" <?php echo ($fetch_update['category'] == "商業理財") ? "selected" : ""; ?>>商業理財</option>
      <option value="教育" <?php echo ($fetch_update['category'] == "教育") ? "selected" : ""; ?>>教育</option>
      <option value="飲食與健康" <?php echo ($fetch_update['category'] == "飲食與健康") ? "selected" : ""; ?>>飲食與健康</option>
      <option value="旅遊" <?php echo ($fetch_update['category'] == "旅遊") ? "selected" : ""; ?>>旅遊</option>
      </select>

      <input type="text" name="update_inventory" value="<?php echo $fetch_update['inventory']; ?>" class="box" required placeholder="輸入庫存">
      <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" value="更新" name="update_product" class="btn">
      <input type="reset" value="取消" id="close-update" class="option-btn">
   </form>
   </div>
   <?php
         }
      }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>







<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>
