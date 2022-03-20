<?php ;
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
?>
<?php include 'header.php';?>
<div id="home-container">
  <div class="top left">
  <button class="home-button button3" onclick="document.location='viewSales.php'">Monthly sales report</button>
  </div>
  <div class="top right">
  <button class="home-button button3" onclick="document.location='websiteTraffic.php'">Holyhope.co.uk website Traffic</button>
  </div>
  <div class="bottom left">
  <button class="home-button button3" onclick="document.location='salesBreakdownReport.php'">Sales breakdown report</button>
  </div>
  <div class="bottom right">
  <button class="home-button button3" onclick="document.location='comparemonthly.php'">Compare monthly sales with last year</button>
  </div>
</div>
<?php include 'footer.php'; ?>
