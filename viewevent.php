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
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) {
        exit('Product doesn\'t exist with that ID!');
    }
} else {
    exit ('Event id is not specified !');
}

?>
<?php include 'header.php'; ?>
        <div class="contact-section">
            <h2>View Event #<?=$event['id']?></h2> 
            <button type="button" class="btn btn-primary fa fa-edit" onclick="document.location='editevent.php?id=<?=$event['id']?>'">Update</button>
            <button type="button" class="btn btn-danger fa fa-remove" onclick="document.location='deleteevent.php?id=<?=$event['id']?>'">Delete</button>
            <form action="editevent.php?id=<?=$event['id']?>" method="post">
                    <label for="Eventid">Event id</label>
                    <input type="number" name="id" id="id"class="contact-form-text" placeholder="Event id" value="<?=$event['id']?>" required>
                    <label for="name">Event Name</label>
                    <input type="text" name="name" id="name" class="contact-form-text" placeholder="Event Name" value="<?=$event['name']?>" required>
                    <label for="image">Event Image</label>
                    <input type="text" name="image" id="image" class="contact-form-text" placeholder="Image" value="<?=$event['image']?>" required>
                    <label for="price">Event price</label>
                    <input type="decimals" name="price" id="price" class="contact-form-text" placeholder="Event price" value="&pound; <?=$event['price']?>" required>
                    <label for="stock">stock</label>
                    <input type="number" name="stock" id="stock"class="contact-form-text" placeholder="Stock" value="<?=$event['stock']?>" required>
                    <label for="description">Description : </label>
                    <textarea name="description" id="description" rows="5" cols="100"><?=$event['description']?></textarea>
                    <input type="button" class="contact-button" value="Go back!" onclick="history.go(-1)">
            </form>
            <?php if ($msg): ?>
            <?php endif; ?>
        </div>

    <?php include 'footer.php'; ?>

