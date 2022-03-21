<!--The delete page will be used to delete records from the events table. Before a user can delete a record they will need to confirm it, this will prevent accidental deletion. -->
<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}

$pdo = pdo_connect_mysql();
$msg = '';
// Check that the event ID exists
if (isset($_GET['id'])) {
    // Select the record that is going to be deleted
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        exit('Product doesn\'t exist with that ID!');
    }
    else{
        $event_id = $product['handleid'];
}
    // Make sure the user confirms before deletion
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // User clicked the "Yes" button, delete record
            $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $msg = 'You have deleted the event!';
            delete_product($event_id);
            header('Location: readproducts.php');
        } else {
            // User clicked the "No" button, redirect them back to the read page
            header('Location: readproducts.php');
            exit;
        }
    }
} else {
    exit('No ID specified!');
}
?>

<!--To delete a record the code will check if the GET request variable "id" exists, if it does then check if the record exists in the events table and confirm the user if they would like to delete the event or not, a simple GET request will determine which button the user clicked (Yes or No). -->
    <?php include 'header.php'; ?>
        <div class="content delete">
            <h2>Delete event #<?=$product['id']?></h2>
            <?php if ($msg): ?>
            <p><?=$msg?></p>
            <?php else: ?>
            <p>Are you sure you want to delete event #<?=$product['id']?>?</p>
            <div class="yesno">
                <a href="deleteproduct.php?id=<?=$product['id']?>&confirm=yes">Yes</a>
                <a href="deleteproduct.php?id=<?=$product['id']?>&confirm=no">No</a>
            </div>
            <?php endif; ?>
        </div>
        
<?php include 'footer.php'; ?>
