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
    <label for="months">Choose a month:</label>

    <select name="months" id="cars">
      <option value="march_2022">March 2022</option>
      <option value="febuary_2022">Febuary 2022</option>
      <option value="january_2022">January 2022</option>
      <option value="december_2021">December 2021</option>
      <option value="november_2021">November 2021</option>
      <option value="october_2021">October 2021</option>
      <option value="september_2021">September 2021</option>
      <option value="august_2021">August 2021</option>
      <option value="july_2021">July 2021</option>
      <option value="june_2021">June 2021</option>
      <option value="may_2021">May 2021</option>
      <option value="april_2021">April 2021</option>
    </select>
    <input type="submit" value="View sales stats"/>
  </form>
</div>

<?php
$selectOption = $_POST['months'];

if(isset($_POST['months'])){

echo "
<div>
   <img src='images/reports/compare_sales/" . $selectOption . "/report.png'>
</div>
";
}


?>
<?php include 'footer.php'; ?>
