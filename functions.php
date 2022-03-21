<?php
function pdo_connect_mysql() {
    // Update the details below with your MySQL details
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'admin';
    $DATABASE_PASS = 'j159540!';
    $DATABASE_NAME = 'holyhope';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	exit('Failed to connect to database!');
    }
}

function get_password_hint($username){
    $db = pdo_connect_mysql();
    $qry = "SELECT * from authenticated_users where username = '" . $username . "'";
    $stmt = $db->prepare($qry);
    $stmt->execute();
    $result = $stmt->fetch();
    return ($result['password_hint']);
}


function file_uploader($fileNumber){
    $target_dir = "images/";
    $target_file = $target_dir . basename($_FILES["myFile"]["name"][$fileNumber]);
    
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if file already exists
    if (file_exists($target_file)) {
      echo "<p>Sorry, file already exists.</p>";
      $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
      if (move_uploaded_file($_FILES["myFile"]["tmp_name"][$fileNumber], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["myFile"]["name"][$fileNumber])). " has been uploaded.";
      } else {
           echo "Not uploaded because of error #".$_FILES["datei"]["error"];
      }
    }
}


function get_temp_event_id(){
    $db = pdo_connect_mysql();
    $stmt = $db->prepare("SELECT * from events where temp_event = 1");
    $stmt->execute();
    $result = $stmt->fetch();
    return($result['id']);  
}


function delete_product($productId){
        $function = "deleteProduct";
        
        curl_ping($function, $productId);
}


function get_image_url($event){
    
    $wix_end_point = "https://static.wixstatic.com/media/";
    $sql = "SELECT * FROM events where id like '" . $event . "'";
    $db = pdo_connect_mysql();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    $wix_image_ref = $result['image'];
    $image_array = explode('/', $wix_image_ref);
    $image_url = $wix_end_point . $image_array[3];
    echo $image_url;
    return $image_url;
       

}

function calculate_month_event_sales(){
    $total = 0;
    $qry_get_month = "SELECT * FROM event_orders WHERE YEAR(event_orders.event_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(event_orders.event_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
    $db = pdo_connect_mysql();
    $stmt = $db->query($qry_get_month);
    

    while ($row = $stmt->fetch()) {
        $total += (float)$row['order_total'];
    }
    
    return $total;
    
}

function calculate_month_product_sales(){
    $total = 0;
    $qry_get_month = "SELECT * FROM product_orders WHERE YEAR(product_orders.date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(product_orders.date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
    $db = pdo_connect_mysql();
    $stmt = $db->query($qry_get_month);
    

    while ($row = $stmt->fetch()) {
        $total += (float)$row['total_spent'];
    }
    
    return $total;
    
}

    
    
    

//Function to ping http api on wix. This function handles functions that require 1 argument. Authentication included.
function curl_ping($function, $arg1){
    $url_base = "https://holyhope.co.uk/_functions-dev/";
    
    $url = $url_base . $function . "/secretphrase/" . $arg1;
    shell_exec("wget " . $url);

    echo $url;
}
function parse_string($string){
    return str_replace(" ", "_", $string);
}

//Function to ping http API on wix. This function handles updateevent & addevent. Authentication included.
function curl_event_ping($function, $productId, $name, $max_attendee, $description, $price, $image_url){
    $name = parse_string($name);
    $description = parse_string($description);
    
    if ($image_url == ""){
        $ending = $productId . "/" . $name . "/" . $max_attendee . "/" . $description . "/" . $price;
    } else{
        $ending = $productId . "/" . $name . "/" . $max_attendee . "/" . $description . "/" . $price . "/" . $image_url;
    }
    $url = "https://holyhope.co.uk/_functions-dev/" . $function . "/secretphrase/" . $ending;
    echo $url;
    shell_exec("wget " . $url);
}

function add_event($name, $description, $max_attendee, $price, $image_url = ""){
    $function = "add_event";
    $db = pdo_connect_mysql();
    $productId = get_temp_event_id();
    $qry = 'UPDATE `events` SET `temp_event` = 0 WHERE id = "' . $productId . '";';
    echo $qry;
    $db->query($qry);    
    curl_event_ping($function, $productId, $name, $description, $max_attendee, $price, $image_url);


}

function updateEvent($productId, $name, $description, $max_attendee, $price, $image_url = ""){
    $function = "updateEvent";

    curl_event_ping($function, $productId, $name, $description, $max_attendee, $price, $image_url);
}

function updateProduct($productId, $name, $description, $stock, $price, $image_url = ""){
    $function = "updateEvent";

    curl_event_ping($function, $productId, $name, $description, $stock, $price, $image_url);
}






function clean($userInput) {
    $userInput = trim($userInput);
    $userInput = stripslashes($userInput);
    $userInput = htmlspecialchars($userInput);
    return $userInput;
}

//returns the total amount of columns in the table
function getRowAmount($tableName){
    $db = pdo_connect_mysql();
    $query = "select COUNT(*) from " .$tableName;
    $stmt = $db->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
}


// returns the amount of attendees at a given event id
// NOTE - $tablename should always be the name of the linking table between events and custumers.
// TO CHANGE - once table names are known $tablename can be removed
function getAttendeesAmount($tableName, $eventId){
    $db = pdo_connect_mysql();
    $query = "select COUNT(id) from". $tableName ."where id =". $eventid;
    $stmt = $db->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC)["COUNT(id)"];
}

function getHashValue($toHash){
    return hash('sha256', $toHash);
}

function loginAutentication($givenUsername, $givenPassword){
    $db = pdo_connect_mysql();
    $query = 'select exists(SELECT * from authenticated_users where username = ?) as "exists"';
    $stmt = $db->prepare($query);
    $stmt->execute([$givenUsername]);
    $returnedValue = $stmt->fetch(PDO::FETCH_ASSOC)["exists"];
    if ($returnedValue == "1"){
        $query = "select password from authenticated_users where username = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$givenUsername]);
        $returnedValue = $stmt->fetch(PDO::FETCH_ASSOC)["password"];
        if ($returnedValue == getHashValue($givenPassword)){
            return TRUE;
        }
        else{
            return False;
        }

    }
    else{
        return FALSE;
        
    }

}
?>

