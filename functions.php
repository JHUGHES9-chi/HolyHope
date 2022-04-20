<?php

/**
*    Connection function for our localhost database. This function is self contained and has all the required credentials to authenticate a session with the MySQL database.
*    If succesful the PDO object is returned for the function to use to query the database with.
*    If unsuccesful the script will stop and the page will display a non descriptive error message for security reasons.
*/
function pdo_connect_mysql() {
    // MYSQL login details
    $DATABASE_HOST = 'localhost'; /** Host location of database, our database is hosted on the same machine running the php server so our host is 'localhost' */
    $DATABASE_USER = 'admin'; /** Username for database */
    $DATABASE_PASS = 'j159540!'; /** Password for database to access the 'admin' account the user must be connected from localhost, a user cannot connect from the internet and use the 'admin' account so there is a minimized risk of the password being leaked by being stored as plaintext on this php file. */
    $DATABASE_NAME = 'holyhope'; /** Database name to be accessed on the DATABASE_HOST */
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	/** If there is an error with the connection, the script stop and display the following error message. */
    	exit('Failed to connect to database!');
    }
}

/**
*   Given a username as a parameter the function will return the account password hint. 
*   There is no error checking if the username has no related account and will return a empty object if this event occurs.
*/
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

/** 
*   Gets a temporary event ID from local DB to be used to create a real event and unhide it on Wix.
*   Takes no paramters
*   Returns empty if no temporary ID's are left on the local DB
*/
function get_temp_event_id(){
    $db = pdo_connect_mysql();
    $stmt = $db->prepare("SELECT * from events where temp_event = 1");
    $stmt->execute();
    $result = $stmt->fetch();
    return($result['id']);  
}


/**
*   Calls the wix http end point using curl_ping() to delete a product. 
*   This function required a productId paramter
*/
function delete_product($productId){
        $function = "deleteProduct";
        curl_ping($function, $productId);
}

/**
8   Using string manipulation and the saved wix image reference value (locally stored variable on our MySQL database pointing to the local location of image on wix server)
*   The URL for the image is created and returned as a string.
*/
function get_image_url($event){
    
    $wix_end_point = "https://static.wixstatic.com/media/"; /* wix URL excluding the image location which will be cocatinated on the end */
    $sql = "SELECT * FROM events where id like '" . $event . "'";
    $db = pdo_connect_mysql();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    $wix_image_ref = $result['image'];
    $image_array = explode('/', $wix_image_ref);
    $image_url = $wix_end_point . $image_array[3];
    //echo $image_url; //Debugging line
    return $image_url;
       

}

/**
*   This function returns the total revenue from events for the last month.
*   A dynamic SQL query will be run to calculate the 1 month prior date range and returns all the event transactions in that time date.
*   The function will calculate the sum of all event transactions and return the total
*/
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


/**
*   This function returns the total revenue from product for the last month.
*   A dynamic SQL query will be run to calculate the 1 month prior date range and returns all the product transactions in that time date.
*   The function will calculate the sum of all product transactions and return the total
*/
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


/**
*   Function to handle single argument API calls to wix, this function includes authentication for the API's
*/
function curl_ping($function, $arg1){
    $url_base = "https://holyhope.co.uk/_functions-dev/"; /** base http endpoint for wix API */
    
    $url = $url_base . $function . "/secretphrase/" . $arg1; /** Generation of URL to call including authentication */
    shell_exec("wget " . $url);

    echo $url;
}

/**
*   parse_string takes 1 parameter of a string type.
*   It removes all spaces and replaces them with '_' so they dont break the URL. API's on the wix side have the opposite function to replace the newly added '_' with ' '
*/
function parse_string($string){
    return str_replace(" ", "_", $string);
}


/**
*   Funciton to ping HTTP endpoint on wix. this function handles updateevent & addevent functionality. Authentication is handled by this function without additional parameters.
*
*    Parameters needed:
*        $productId = ID of event to update or add
*        $name = new name of product
*        $max_attendee = maximum amount of attenddee's allowed
*        $description = new description of event
*        $price = price per ticket
*        $image_url = URL of image, this image must be hosted. Any image stored in the HolyHope directory on the pi is accessible from: (device public ip)/HolyHope/Images/       
*/
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

/**
*   Add event handle function
*/

function add_event($name, $description, $max_attendee, $price, $image_url = ""){
    $function = "add_event";
    $db = pdo_connect_mysql();
    $productId = get_temp_event_id();
    $qry = 'UPDATE `events` SET `temp_event` = 0 WHERE id = "' . $productId . '";';
    //echo $qry; //Debugging echo
    $db->query($qry);    
    curl_event_ping($function, $productId, $name, $description, $max_attendee, $price, $image_url);


}

/**
*   Update event handle function
*/
function updateEvent($productId, $name, $description, $max_attendee, $price, $image_url = ""){
    $function = "updateEvent";

    curl_event_ping($function, $productId, $name, $description, $max_attendee, $price, $image_url);
}


/** Update product event handle */
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

/**
*   Returns the total amount of rows for a specified table.
*
*   Returns integer
*/
function getRowAmount($tableName){
    $db = pdo_connect_mysql();
    $query = "select COUNT(*) from " .$tableName;
    $stmt = $db->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC)["COUNT(*)"];
}

/**
*   returns the amount of attendees at a given event id
*   NOTE - $tablename should always be the name of the linking table between events and custumers.
*/
function getAttendeesAmount($tableName, $eventId){
    $db = pdo_connect_mysql();
    $query = "select COUNT(id) from". $tableName ."where id =". $eventid;
    $stmt = $db->query($query);
    return $stmt->fetch(PDO::FETCH_ASSOC)["COUNT(id)"];
}

/**
*    Hashes string value provided and returns the SHA256 Encrypted text
*   This is for login authentication as passwords are not stored in plain text.
*/
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

