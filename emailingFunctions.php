<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
function makeCheckbox($email, $identifier){
    echo '<div class="emailCheckbox"><input class="checkboxEmail" form="sendMailForm" type="checkbox" id="'.$identifier.'" value="'.$email.'"name="'.$identifier.'" checked> <label class=checkboxLabel" for="'.$identifier.'">'.$email.'</label></div>';

}
function createMailObject($email, $password){
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPAuth = TRUE;
    $mail->SMTPSecure = "ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Port = "465";
    $mail->Username = $email;
    $mail->Password = $password;
    $mail->Subject = "hello world";
    $mail->Body = "a test 123123123";
    return $mail;
}

// TODO once on actual server query need to be changed (id to event_id, name to event_name)
function getEvents($db){
    $query = "select id, name from events";
    $stmt = $db->query($query);
    $events = $stmt->fetchall(PDO::FETCH_ASSOC);
    return $events;
}

function getEventCustomersEmails($eventName, $db){
    $query = "select Customers.email as email
    from event_orders 
    inner join Customers 
    on event_orders.customer_email = Customers.email
    where event_orders.event_id = '".$eventName."'";
    // echo("--------------".$query."------------");
    $stmt = $db->query($query);
    $emails = $stmt->fetchall(PDO::FETCH_ASSOC);
    return $emails;
}

function makeEventButton($email, $identifier){
    echo '<button class="eventButton" id='.$identifier.' type="submit" name="eventButton" value="'.$email.'">'.$email.'</button>';
}
//TODO UPDATE QUERY HERE To
// function getEventId($eventName, $db){
//     $query="select id from events where name = '".$eventName."'";
//     // echo $query;
//     $stmt = $db->query($query);
//     $id = $stmt->fetch(PDO::FETCH_ASSOC);
//     return $id["id"];
// }
function uploadFile($fileNumber){
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["myFile"]["name"][$fileNumber]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if file already exists
    if (file_exists($target_file)) {
      echo "<p>Sorry, file already exists.</p>";
      $uploadOk = 0;
    }
    
    // // Check file size
    // if ($_FILES["fileToUpload"]["size"] > 500000) {
    //   echo "Sorry, your file is too large.";
    //   $uploadOk = 0;
    // }
    
    // // Allow certain file formats
    // if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    // && $imageFileType != "gif" ) {
    //   echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    //   $uploadOk = 0;
    // }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
      if (move_uploaded_file($_FILES["myFile"]["tmp_name"][$fileNumber], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["myFile"]["name"][$fileNumber])). " has been uploaded.";
      } else {
        echo "<p>there was an error uploading your file.</p>";
      }
    }
}
