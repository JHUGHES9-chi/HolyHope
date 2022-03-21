<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
$wix_end_point = "https://static.wixstatic.com/media/";

$pdo = pdo_connect_mysql();
$msg = '';
    // Get the events from the Events table
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit('products doesn\'t exist with that ID!');
    }

// Check if the events id exists, for example update.php?id=1 will get the events with the id of 1
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        // This part is similar to the create.php, but instead updates a record and not insert
        $name = $_POST['name'];
        $handleId = ltrim($_POST['handleid'], "product_");
        $productimageurl = $_POST['imageurl'];
        $collection = $_POST['collection'];
        $price = ltrim($_POST['price'], '£ ');
        $inventory = $_POST['inventory'];
        $description = $_POST['description'];
        // Update the record
        $sql = 'UPDATE products SET name = "' . $name . '", handleid = "' . $handleId . '", productimageurl = "' . $productimageurl . '", collection = "' . $collection . '", price = ' . $price . ', inventory = ' . $inventory . ', description = "' . $description . '" WHERE id = ' . $_GET['id'];
        //echo $sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        updateProduct($handleId, $name, $inventory, $description, $price, $productimageurl);
        $msg = 'Updated Successfully!';
        //header('Location: readproducts.php');
    }
} else {
    exit('No ID specified!');
}
?>

<?php include 'header.php'; ?>
        <div class="contact-section">
            <h2>Product #<?=$product['id']?></h2> 
            <form action="editproduct.php?id=<?=$product['id']?>" method="post">
                    <label for="productid">Product id</label>
                    <input type="number" name="id" id="id"class="contact-form-text" placeholder="Product id" value="<?=$product['id']?>" required>
                    <label for="name">Product Name</label>
                    <input type="text" name="name" id="name" class="contact-form-text" placeholder="Product Name" value="<?=$product['name']?>" required>
                    <label for="name">Product Handle Id</label>
                    <input type="text" name="handleid" id="handleid" class="contact-form-text" placeholder="Product Handle Id" value="<?=$product['handleid']?>" required>
                    <label for="name">Image Url</label>
                    <input type="text" name="imageurl" id="imageurl" class="contact-form-text" placeholder="Image url" value="<?=$wix_end_point . $product['productimageurl']?>" required>
                    <label for="name">Collection</label>
                    <input type="text" name="collection" id="collection" class="contact-form-text" placeholder="Collection" value="<?=$product['collection']?>" required>
                    <label for="name">Product Price</label>
                    <input type="decimals" name="price" id="price" class="contact-form-text" placeholder="Product price" value="&pound; <?=$product['price']?>" required>
                    <label for="productid">Inventory</label>
                    <input type="number" name="inventory" id="inventory"class="contact-form-text" placeholder="Inventory" value="<?=$product['inventory']?>" required>
                    <label for="description">Description</label>
                    <input type="text" name="description" placeholder="enter some description" value="<?=$product['description']?>" id="description">
                    <input type="submit" name="submit" class="contact-button update" value="update">
                    <input type="button" class="contact-button" value="Go back!" onclick="history.go(-1)">
            </form>
            <?php if ($msg): ?>
            <?php endif; ?>
        </div>

    <?php include 'footer.php'; ?>
