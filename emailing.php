<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#selectAllButton").click(function(){
    $(".checkboxEmail").each(function(){this.checked = true;});
  });
});
</script>
<script>
$(document).ready(function(){
  $("#deselectAllButton").click(function(){
    $(".checkboxEmail").each(function(){this.checked = false;});
  });
});
</script>


<?php 
session_start();
if(!isset($_SESSION['username']))
{
  header('location: index.php');
}
?>
<?php include 'functions.php';?>
<?php include 'header.php';?>
<?php include 'emailingFunctions.php';?>
<h1>Emailing system</h1>

<?php
$db = pdo_connect_mysql()
?>
<style>
  textarea {
    resize:none;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
  }
  .sendMailButton {
  background-color: white; 
  color: black; 
  border: 2px solid #f44336;
  width: 600px;
}

.sendMailButton:hover {
  background-color: #f44336;
  color: white;
}
     
.sendMailForm > * {             
  padding: 1px;      
  margin: 2px;
}
.sendMailForm {
  display: grid;
  grid-template-columns: auto;
  grid-template-rows: auto auto auto;
  align-content:start ;
  /* align-content: center; */
  margin: 1px;
  padding: 1px;
  border: 1px solid red;
}
.eventEmailForm{
  border: 0px solid white;
  margin: 1px;
  padding: 1px;
}
.emailCheckbox{
  background-color: white; 
  color: black; 
  border: 1px solid #f44336;
  padding: 1px;
  margin: 1px;
}
.emailButton:hover {
  background-color: #f44336;
  color: white;
}
.eventButton:hover{
  background-color: #f44336;
  color: white;
}
.emailListBody{
  float:left;
  width: 450px;
  height: 600px;
  border:1px solid #f44336;
  display: grid;
  grid-template-columns:auto;
  align-content:start ;
  
  /* grid-template-rows: repeat(auto-fill, 3)); */
}
.emailingBody{
  display: flex;
  align-items: stretch;
  /* grid-template-columns: auto auto auto; */
  /* grid-template-rows: auto auto auto */
}
.emailList{
  overflow-x: hidden;
  overflow-y: auto;
}
.checkboxLabel{
}
.selectAllButtonEmail{
  background-color: white; 
  color: black; 
  border: 1px solid green;
  padding: 1px;
  margin: 1px;
}
.selectAllButtonEmail:hover {
  background-color: #f44336;
  color: white;
}
.eventButton{
  background-color: white; 
  color: black; 
  border: 1px solid green;
  padding: 1px;
  margin: 1px;
}
</style>
<div class="emailingBody">
  <form class="sendMailForm" name="sendemail" method="post" id="sendMailForm" enctype="multipart/form-data">
    <input type="text" placeholder="Subject" name="username">
    <textarea id="body" name="body" placeholder="Body" rows=15 cols="60"></textarea>
    <input type="file" id="myFile" name="myFile[]" multiple>
  <button class="sendMailButton" type="submit" name="submitEmail">Send email</button>
  </form> 
    <div class="emailListBody">
      <h1>Emails</h1>
      <button class="selectAllButtonEmail" id="selectAllButton" value="selectAll">select all</button>
      <button class="selectAllButtonEmail" id="deselectAllButton" value="deselectAll">deselect all</button>
      <div class="emailList">
      <?php
      if(isset($_POST["eventButton"])){
        $emails = getEventCustomersEmails($_POST["eventButton"], $db);
        $i = 0;
        foreach ($emails as $email) {
          makeCheckbox($email["email"], $i);
          $i ++;
        }
      }
      ?>
      </div>
    </div>
    <div class = "emailListBody">
      <h1>Events</h1>
      <div class="emailList">
        <form class ="eventEmailForm" method="post">
        <?php
        $events = getEvents($db);
        $i = 0;
        //TODO change key when on main server
        foreach($events as $email){
          makeEventButton($email["name"], $i);
          $i ++;
        }
        ?>
        </form>
      </div>
    </div>
</div>




<?php
if(isset($_POST['submitEmail'])){  
  $password = "HolyhopeTestEmailPassword123";
  $email  = "holyhopetestemail@gmail.com";
  $mailObj = createMailObject($email, $password);
  if (isset($_FILES['myFile'])) {
    $i= 0;
    foreach ($_FILES['myFile']["name"] as $value) {
      uploadFile($i);
      $mailObj->addAttachment("uploads/".$_FILES['myFile']["name"][$i]);
      $i++;
    }
  }

  // foreach($_POST as $var => $email){
  //   if (is_int($var)){
  //       $mailObj->AddAddress($email);
  //   }
  }
  $mailObj->AddAddress($email);
  $mailObj->Body = $_POST["body"];
  if(!$mailObj->Send()) {
    
    echo "<p>There was an error sending the email</p>";
    }
    else{
      echo "<p>Message was sent successfully</p>";
    }

?>
<?php include 'footer.php';?>

