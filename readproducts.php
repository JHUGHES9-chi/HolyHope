<?php
include 'functions.php';
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
$pdo = pdo_connect_mysql();
// The amounts of products to show on each page
$num_products_on_each_page = 40;
// The current page, in the URL this will appear as readevent.php?page=products&p=1, readevent.php?page=products&p=2, etc...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
// Select products ordered by the date added
$stmt = $pdo->prepare('SELECT * FROM products WHERE name <> ""');
// bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();
// Fetch the products from the database and return the result as an Array
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of products
$total_products = $pdo->query('SELECT * FROM products')->rowCount();

?>

<?php include 'header.php'; ?>
<?php

echo "<table border=1>
    <tr>
        <th>Product Id: </th>
        <th>Product Name: </th>
        <th>Price: </th> 
        <th>Inventory: </th>
        <th>Actions</th>
    </tr>";   //table heading for to display the name of what is going to be in each column.


    foreach ($products as $product){
        echo "<tr>";
        echo "<td>" .$product['id']. "</td>";
        echo "<td>" .$product['name']. "</td>";
        echo "<td>" .$product['price']. "</td>";  
        echo "<td>" .$product['inventory']. "</td>";
        echo '<td><a href="viewproduct.php?id='.$product["id"].'" class="btn btn-outline-info">View</a>  
                  <a href="editproduct.php?id='.$product["id"].'" class="btn btn-outline-secondary">Update</a>
                  <a href="deleteproduct.php?id='.$product["id"].'" class="btn btn-outline-secondary">Delete</a>
        </td>'; // linked pages with button to allow users to navigate to viw payslips and update product data if there are latest changes to the product records.

        echo "</tr>";
    }
        echo "</table>";
?>
  <?php include 'footer.php'; ?>

