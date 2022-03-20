<?php ;
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
?>
<?php include 'header.php';?>
<div>
  <form method="post">
    <label for="time_selection">Choose a period of time to view web traffic</label>

    <select name="months" id="cars">
      <option value="14day">Last 2 weeks</option>
      <option value="monthly">Last month</option>
      <option value="yearly">Last 365 days</option>
	  <option value="YTD">Year to date</option>
    </select>
    <input type="submit" value="View web traffic"/>
  </form>
</div>

<?php
$selectOption = $_POST['time_selection'];

if(isset($_POST['time_selection'])){

echo "
<div>
   <img src='images/reports/website_traffic/" . $selectOption . "/report.png'>
</div>
";
}
?>



<?php include 'footer.php'; ?>

