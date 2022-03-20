<?php ;
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
?>

<?php include 'header.php';?>


<label for="cars">Choose a month:</label>

<select name="cars" id="cars">
  <option value="volvo">Volvo</option>
  <option value="saab">Saab</option>
  <option value="mercedes">Mercedes</option>
  <option value="audi">Audi</option>
</select>


<?php include 'footer.php'; ?>
