
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
                    <label for="name">Event price</label>
                    <input type="decimals" name="price" id="price" class="contact-form-text" placeholder="Event price" value="&pound; <?=$event['price']?>" required>
                    <label for="name">Event Date</label>
                    <input type="date" name="event_date" id="event_date" class="contact-form-text" value="2021-03-22" value="<?=$event['event_date']?>" required>
                    <label for="name">Event Location</label>
                    <input type="text" name="location" id="location" class="contact-form-text" placeholder="Event price" value="<?=$event['location']?>" required>
                    <label for="description">Description</label>
                    <input type="text" name="description" placeholder="enter some description" value="<?=$event['description']?>" id="description">

                    <input type="button" class="contact-button" value="Go back!" onclick="history.go(-1)">
            </form>
            <?php if ($msg): ?>
            <?php endif; ?>
        </div>

    <?php include 'footer.php'; ?>
