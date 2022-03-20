<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}

$pdo = pdo_connect_mysql();
$msg = '';
// Check if the events id exists, for example update.php?id=1 will get the events with the id of 1
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        // This part is similar to the create.php, but instead updates a record and not insert
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $handleId = isset($_POST['handleid']) ? $_POST['handleid'] : '';
        $productimageurl = isset($_POST['productimageurl']) ? $_POST['productimageurl'] : '';
        $collection = isset($_POST['collection']) ? $_POST['collection'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $inventory = isset($_POST['inventory']) ? $_POST['inventory'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        // Update the record
        $stmt = $pdo->prepare('UPDATE products SET name = ?, name = ?, handleid = ?, productimageurl = ?, collection = ?, price = ?, inventory = ?, description = ? WHERE id = ?');
        $stmt->execute([$id, $name, $handleId, $productimageurl, $collection , $price, $inventory, $description, $_GET['id']]);
        $msg = 'Updated Successfully!';
        header('Location: readproducts.php');
    }
    // Get the events from the Events table
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit('products doesn\'t exist with that ID!');
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
                    <input type="text" name="name" id="name" class="contact-form-text" placeholder="Product Handle Id" value="<?=$product['handleid']?>" required>
                    <label for="name">Image Url</label>
                    <input type="text" name="name" id="name" class="contact-form-text" placeholder="Image url" value="<?=$product['productimageurl']?>" required>
                    <label for="name">Collection</label>
                    <input type="text" name="name" id="name" class="contact-form-text" placeholder="Collection" value="<?=$product['collection']?>" required>
                    <label for="name">Product Price</label>
                    <input type="decimals" name="price" id="price" class="contact-form-text" placeholder="Product price" value="&pound; <?=$product['price']?>" required>
                    <label for="productid">Inventory</label>
                    <input type="number" name="id" id="id"class="contact-form-text" placeholder="Inventory" value="<?=$product['inventory']?>" required>
                    <label for="description">Description</label>
                    <input type="text" name="description" placeholder="enter some description" value="<?=$product['description']?>" id="description">
                    <input type="submit" name="submit" class="contact-button update" value="update">
                    <input type="button" class="contact-button" value="Go back!" onclick="history.go(-1)">
            </form>
            <?php if ($msg): ?>
            <?php endif; ?>
        </div>

    <?php include 'footer.php'; ?>
