
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'functions.php';
include 'emailingFunctions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}

if (isset($_FILES['myFile'])) {
    $i= 0;
    if (!empty($_FILES['myFile']["name"][0])){
        file_uploader($i);
        $i++;
    }


  }

$name = $price = $image = $stock = $description = $product_page = "";
$msg = '';
// Check if POST data is not empty
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    
    $pdo = pdo_connect_mysql();
$name = clean($_POST["name"]);
        $price = clean($_POST["price"]);
        $image = $_FILES['myFile']['name'][0];
        echo $image;
        $stock = clean($_POST["stock"]);
        $description = clean($_POST["description"]);
        $product_page = clean($_POST["product_page"]);
      $pdo = pdo_connect_mysql();
      
        $sql = 'UPDATE events SET name = "' . $name . '", image = "' . $image . '", price = ' . $price . ', stock = ' . $stock . ', description = "' . $description . '", temp_event = 0 WHERE events.id = "' . get_temp_event_id() . '"';
        $stmt = $pdo->prepare($sql);
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

                <input type="file" id="myFile" name="myFile[]" multiple>
                    
                <label for="name">Stock</label>
                <input type="number" name="stock" id="stock" class="contact-form-text" placeholder="Stock" required>
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
