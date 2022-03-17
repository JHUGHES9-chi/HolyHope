
<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}

$pdo = pdo_connect_mysql();
$msg = '';
// Check that the product ID exists
if (isset($_GET['id'])) {
    // Select the record that is going to be deleted
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit('Product doesn\'t exist with that ID!');
    }
} else {
    exit ('product id is not specified !');
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

                    <input type="button" class="contact-button" value="Go back!" onclick="history.go(-1)">
            </form>
            <?php if ($msg): ?>
            <?php endif; ?>
        </div>

    <?php include 'footer.php'; ?>
