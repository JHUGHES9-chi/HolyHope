<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
$pdo = pdo_connect_mysql();
// The amounts of products to show on each page
$num_products_on_each_page = 5;
// The current page, in the URL this will appear as readevent.php?page=products&p=1, readevent.php?page=products&p=2, etc...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
// Select products ordered by the date added
$stmt = $pdo->prepare('SELECT * FROM events LIMIT ?,?');
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
  <a href="createevent.php" class="create-action">Create Event</a>
    <div class="heading">
      <h2>Welcome to an Events page</h2>
      <p>All latest event added are displayed first</p>
    </div>
    <div class="row">
        <?php foreach ($events as $event): ?>
      <div class="card">
        <div class="card-header">
          <h1><?= $event['name']?></h1>
        </div>
        <div class="card-body">
          <a href="viewevent.php?id=<?=$event['id']?>" class="btn">View Event Information</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="center">
        <?php if ($current_page > 1): ?>
        <button class="main-button" onclick="document.location='readevent.php?page=events&p=<?=$current_page-1?>'">Prev</button>
        <?php endif; ?>
        <?php if ($total_products > ($current_page * $num_products_on_each_page) - $num_products_on_each_page + count($events)): ?>
        <button class="main-button" onclick="document.location='readevent.php?page=events&p=<?=$current_page+1?>'">Next</button>
        <?php endif; ?>
  </div>
  <div class="heading">
      <h2>Sort filtering</h2>
      <p>The table below is to allow you to sort by different elements</p>
  </div>
  <table>
    <tr>
      <th>Sort By</th>
      <th>Actions</th>
    </tr>
    <tr>
      <td class="text-weight">Price</td>
      <td><button class="sort-button" onclick="document.location='sortbyprice.php'" ><span>Sort</span></button></td>
    </tr>
  </table>

  <div>
  </div>

  <?php include 'footer.php'; ?>

