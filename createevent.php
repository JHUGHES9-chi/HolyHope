<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}


$name = $price = $image = $stock = $description = $product_page = "";
$msg = '';
// Check if POST data is not empty
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = pdo_connect_mysql();

      $pdo = pdo_connect_mysql();
        $stmt = $pdo->prepare("INSERT INTO events (name, price , image, stock, description, product_page) VALUES (:name, :price, :image, :stock, :description, :product_page)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':product_page', $product_page);


        $name = clean($_POST["name"]);
        $price = clean($_POST["price"]);
        $image = clean($_POST["image"]);
        $stock = clean($_POST["stock"]);
        $description = clean($_POST["description"]);
        $product_page = clean($_POST["product_page"]);
        $stmt->execute();

    echo "<div class='event-center'><h2>New Event created</h2>";
    echo "<b>Event Name :</b> " . $name;
    echo "<br>";
    echo "<b>Event Price :</b> ". $price;
    echo "<br>";
    echo "<b>Event Image :</b> " . $image;
    echo "<br>";
    echo "<b>Event Stock :</b>" . $stock;
    echo "<br>";
    echo "<b>Event Description :</b>" . $description;
    echo "<br>";
    echo "<b>Product Page :</b>" . $product_page;
    echo "<br>";
    echo "<h2>New event is added to the database</h2></div> ";

    $msg = 'Created Successfully!';


}
?>
    <?php include 'header.php'; ?>
    <div class="contact-section">

            <h1>Create New Event</h1>
            <p></p>
            <div class="border"></div>
            <form class="contact-form" action="createevent.php" method="post" enctype="multipart/form-data">
                <label for="name">Event Name</label>
                <input type="text" name="name" id="name" class="contact-form-text" placeholder="Event Name" required>
                <label for="name">Event price</label>
                <input type="decimals" name="price" id="price" class="contact-form-text" placeholder="Event Price" required>
                <label for="name">Image Url</label>
                <input type="text" name="image" id="image" class="contact-form-text" placeholder="Image url" required>
                <label for="name">Stock</label>
                <input type="number" name="stock" id="stock" class="contact-form-text" placeholder="Stock" required>
                <label for="name">Wix Page</label>
                <input type="text" name="product_page" id="product_page" class="contact-form-text" placeholder="product page url" required>
                <label for="description">Description : </label>
                <textarea class="contact-form-text" name="description" id="description" rows="5" cols="100">Description</textarea>
                <input type="submit" name="submit" class="contact-button" value="Create">
                <button onclick="goBack()">Go Back</button>

                <script>
                    function goBack() {
                    window.history.back();
                }
                </script>
            </form>
        <?php if ($msg): ?>
        <p><?=$msg?></p>
        <?php endif; ?>
    </div>


<?php include 'footer.php'; ?>
