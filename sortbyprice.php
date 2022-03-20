<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}

$pdo = pdo_connect_mysql();
// The amounts of products to show on each page
$num_products_on_each_page = 4;
// The current page, in the URL this will appear as readevent.php?page=products&p=1, readevent.php?page=products&p=2, etc...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
// Select products ordered by the date added
$stmt = $pdo->prepare('SELECT * FROM events  ORDER BY price ASC LIMIT ?,?');
// bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();
// Fetch the products from the database and return the result as an Array
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of products
$total_products = $pdo->query('SELECT * FROM events')->rowCount();
?>

<?php include 'header.php'; ?>
  <div class="container">
    <div class="heading">
      <h2>Welcome to an Events page</h2>
      <p>Events are sorted by Lowest Price</p>
    </div>
    <div class="row">
        <?php foreach ($events as $event): ?>
      <div class="card">
        <div class="card-header">
          <h1><?= $event['name']?></h1>
        </div>
        <div class="card-body">
          <h5>
          <?="£" .$event['price']?>
        </h5>
          <a href="viewevent.php?id=<?=$event['id']?>" class="btn">View Event Information</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="center">
        <?php if ($current_page > 1): ?>
        <button class="main-button" onclick="document.location='sortbyprice.php?page=events&p=<?=$current_page-1?>'">Prev</button>
        <?php endif; ?>
        <?php if ($total_products > ($current_page * $num_products_on_each_page) - $num_products_on_each_page + count($events)): ?>
        <button class="main-button" onclick="document.location='sortbyprice.php?page=events&p=<?=$current_page+1?>'">Next</button>
        <?php endif; ?>
  </div>

  <div class="center">
  <button class="main-button" onclick="history.go(-1)">Go back to read events </button>

  </div>

  <?php include 'footer.php'; ?>
