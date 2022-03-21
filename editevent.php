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
        $name = $_POST['name'];
        $productimageurl = $_POST['imageurl'];
        $price = ltrim($_POST['price'], '£ ');
        $inventory = $_POST['stock'];
        $description = $_POST['description'];

        // Update the record
        $sql = 'UPDATE events SET name = "' . $name . '", image = "' . $productimageurl . '", price = ' . $price . ', stock = ' . $inventory . ', description = "' . $description . '" WHERE events.id = "' . $_GET['id'] . '"';
        //echo $sql;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $msg = 'Updated Successfully!';
        updateEvent($_GET['id'], $name, $description, $inventory, $price, $productimageurl);
        header('Location: readevent.php');
    }
    // Get the events from the Events table
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) {
        exit('events doesn\'t exist with that ID!');
    }
} else {
    exit('No ID specified!');
}
?>

<!-- The above code will check for the Event ID, the ID will be a parameter in the URL, for example, http://localhost/phpcrud/update.php?id=1 will get the Event with the ID of 1, and then request can be handelled with the GET method and execute a MySQL query that will get the Event by ID. -->
    <?php include 'header.php'; ?>
        <div class="contact-section">
            <h2>Update Event #<?=$event['id']?></h2>
                      <form action="editevent.php?id=<?=$event['id']?>" method="post">
                    <label for="name">Event Name</label>
                    <input type="text" name="name" id="name" class="contact-form-text" placeholder="Event Name" value="<?=$event['name']?>" required>
                    <label for="name">Event price</label>
                    <input type="decimals" name="price" id="price" class="contact-form-text" placeholder="Event price" value="&pound; <?=$event['price']?>" required>
                    <label for="image">Event Image</label>
                    <input type="image" name="image" id="image" class="contact-form-text" placeholder="Event image" value="<?get_image_url($event['image'])?>" required>
                    <label for="image">Stock</label>
                    <input type="number" name="stock" id="stock" class="contact-form-text" placeholder="Stock" value="&pound; <?=$event['stock']?>" required>
                    <label for="description">Description : </label>
                    <textarea name="description" id="description" rows="5" cols="100"><?=$event['description']?></textarea>

                    <input type="submit" name="submit" class="contact-button update" value="update">
                    <input type="button" class="contact-button" value="Go back!" onclick="history.go(-1)">
            </form>
            <?php if ($msg): ?>
            <?php endif; ?>
        </div>

<?php include 'footer.php'; ?>

