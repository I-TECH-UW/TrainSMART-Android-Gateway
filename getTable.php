<?php

file_put_contents('php_debug.log', 'getTable start >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
//var_dump("_POST=", $_POST, "END");
var_dump("_Request=", $_SERVER['REQUEST_URI'], "END");
$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

//load and connect to MySQL database stuff
require("config.inc.php");

file_put_contents('php_debug.log', 'test0 getTable found config >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);


if (!empty($_POST)) {

	file_put_contents('php_debug.log', 'getTable0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
	//var_dump("_POST=", $_POST, "END");
	$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    //gets user's info based off of a username.
    $query = " 
            SELECT 
                id, 
                username, 
                password
            FROM user 
            WHERE 
                username = :username 
        ";
    
    $query_params = array(
        ':username' => $_POST['username']
    );
    
    try {
        file_put_contents('php_debug.log', 'getTable try select user >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
	var_dump("username=", $_POST['username'], "END");
	var_dump("password=", $_POST['password'], "END");
	var_dump("datatable=", $_POST['datatable'], "END");
	$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {

        file_put_contents('php_debug.log', 'getTable cannot access database >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
	//var_dump("_POST=", $_POST, "END");
	$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
		
	// For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error.";
        die(json_encode($response));
        
    }
    
    //This will be the variable to determine whether or not the user's information is correct.
    //we initialize it as false.
    $validated_info = false;
    
    //fetching all the rows from the query
    $row = $stmt->fetch();
    if ($row) {
        //if we encrypted the password, we would unencrypt it here, but in our case we just
        //compare the two passwords
        //if ($_POST['password'] === $row['password']) {
        if (md5($_POST['password']) === $row['password']) {
            $login_ok = true;
        }
    }
    
    // If the user logged in successfully, then we send them to the private members-only page 
    // Otherwise, we display a login failed message and show the login form again 
    if ($login_ok) {
        //$response["success"] = 1;
        //$response["message"] = "Login successful!";
        //$response["id"] = $row['id'];

	switch($_POST['datatable']) {

	  case 'Person':
	    $response = getPersons();
	  break;

	  case 'AssessmentsQuestions':
	    $response = getAssessmentsQuestions();
          break;

	  case 'Assessments':
	    $response = getAssessments();
          break;
          
      case 'QuestionDropdownOption':
        $response = getQuestionDropdownOption();
          break;

	  case 'PersonToAssessments':
	    $response = putPersonToAssessments();
          break;

	  case 'AssessmentsAnswers':
	    $response = putAssessmentsAnswers();
          break;
          
      case 'GeoLocations':
        $response = putGeoLocations();
          break;

          default:
            //$response = 'bad';

	} //switch
	
        $response["success"] = 0;
        $response["message"] = "Invalid Request.";
        die(json_encode($response));

    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid Credentials.";
        die(json_encode($response));
    }
} else {
	file_put_contents('php_debug.log', 'login2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
	//var_dump("_POST=", $_POST, "END");
	$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
?>
		<h1>Login</h1> 
		<form action="person.php" method="post"> 
		    Username:<br /> 
		    <input type="text" name="username" placeholder="username" /> 
		    <br /><br /> 
		    Password:<br /> 
		    <input type="password" name="password" placeholder="password" value="" /> 
		    <br /><br /> 
		    <input type="submit" value="Login" /> 
		</form> 
<?php
} // else


function getPersonToAssessments($person_id, $facility_id, $date_created, $assessment_id, $user_id){

   global $db;

   file_put_contents('php_debug.log', 'getPersonToAssessments1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      $query = "
select
person_id,
facility_id,
date_created,
assessment_id
from person_to_assessments
where person_id = :person_id
and facility_id = :facility_id 
and date_created = :date_created
and assessment_id = :assessment_id
and user_id = :user_id
	";

   file_put_contents('php_debug.log', 'getPersonToAssessments2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   // var_dump("query=", $query, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   try {

	   
      $stmt = $db->prepare($query);
      $stmt->bindParam(':person_id', $person_id, PDO::PARAM_INT);      
      $stmt->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);      
      $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);      
      $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);      
      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);      

      $result = $stmt->execute();
      $row = $stmt->fetch();

   } catch (PDOException $ex) {
	    //die
      file_put_contents('php_debug.log', 'getPersonToAssessments exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump("exception=", $ex, "END");
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      return null;
   }
   return $row;
}

function insertPersonToAssessments($person_id, $facility_id, $date_created, $assessment_id, $user_id){

   global $db;

   file_put_contents('php_debug.log', 'insertPersonToAssessments0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump($person_id, $facility_id, $date_created, $assessment_id, $user_id);
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $insert = "
insert into person_to_assessments
(person_id, facility_id, date_created, assessment_id, user_id)
values ( :person_id, :facility_id, :date_created, :assessment_id, :user_id )
	";

   file_put_contents('php_debug.log', 'insertPersonToAssessments1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    //var_dump("insert=", $insert, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   try {
      $stmt = $db->prepare($insert);
      $stmt->bindParam(':person_id', $person_id, PDO::PARAM_INT);      
      $stmt->bindParam(':facility_id', $facility_id, PDO::PARAM_INT);      
      $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);      
      $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);      
      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);      
      $result = $stmt->execute();

   file_put_contents('php_debug.log', 'insertPersonToAssessments2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("insert result=", $result, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    }
    catch (PDOException $ex) {
	    //die
    file_put_contents('php_debug.log', 'insertPersonToAssessments exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
	    // continue?
    }
}

function putPersonToAssessments(){

   global $db;

   $post = array();
   //$post = $_POST['recs'];

   file_put_contents('php_debug.log', 'putPersonToAssessments0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("post['datatable']: ", $_POST['datatable'], "END");
   //var_dump('$_POST: ', $_POST, "END");
   //var_dump('$_POST num ', $_POST['num_recs'], "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   
   for($i = 0; $i < $_POST['num_recs']; $i++){

      $recsKey = 'recs'.$i;
      $rec = explode(',', $_POST[$recsKey]);
      $person_id =     $rec[0];
      $facility_id =   $rec[1];
      $date_created =  $rec[2];
      $assessment_id = $rec[3];
      $user_id =       $rec[4];

      file_put_contents('php_debug.log', 'putPersonToAssessments() recs >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump($person_id, $facility_id, $date_created, $assessment_id, $user_id);
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      $row = getPersonToAssessments($person_id, $facility_id, $date_created, $assessment_id, $user_id);

      file_put_contents('php_debug.log', 'putPersonToAssessments() returned >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump('row: ', $row);
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      if(!$row) {
         insertPersonToAssessments($person_id, $facility_id, $date_created, $assessment_id, $user_id);
      }
   }

   file_put_contents('php_debug.log', 'putPersonToAssessments() DONE >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $response["success"] = 1;
   die(json_encode($response));
}

function putGeoLocations(){

    global $db;

    $post = array();
    //$post = $_POST['recs'];

    file_put_contents('php_debug.log', 'putGeoLocations0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("post['datatable']: ", $_POST['datatable'], "END");
    //var_dump('$_POST: ', $_POST, "END");
    //var_dump('$_POST num ', $_POST['num_recs'], "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

     
    for($i = 0; $i < $_POST['num_recs']; $i++){

        $recsKey = 'recs'.$i;
        $rec = explode(',', $_POST[$recsKey]);
        $longitude =     $rec[0];
        $latitude =      $rec[1];
        $device_id =     $rec[2];
        $created_at =    $rec[3];
        $username =      $rec[4];
        $password =      $rec[5];

        file_put_contents('php_debug.log', 'putGeoLocations() recs >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
        var_dump($longitude, $latitude, $device_id, $created_at, $username, $password);
        $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

        $row = getGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password);

        if(!$row) {
            insertGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password);
        }
    }

    file_put_contents('php_debug.log', 'putGeoLocations() DONE >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    $response["success"] = 1;
    die(json_encode($response));
}

function getGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password){
       global $db;

   file_put_contents('php_debug.log', 'getGeoLocations1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      $query = "
select
longitude,
latitude,
device_id,
created_at,
username,
password
from geolocations
where format(longitude,5) = format(:longitude,5)
and format(latitude,5) = format(:latitude,5) 
and device_id = :device_id
and created_at = :created_at
and username = :username
and password = :password
	";

   file_put_contents('php_debug.log', 'getGeoLocations2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   // var_dump("query=", $query, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   try {

	   
      $stmt = $db->prepare($query);
      $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
      $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
      $stmt->bindParam(':device_id', $device_id, PDO::PARAM_STR, strlen($device_id));      
      $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR, strlen($created_at));
      $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
      $stmt->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
         
      //file_put_contents('php_debug.log', 'getGeoLocations3a >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      //var_dump("queryString=", $stmt->queryString, "END");
      //$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
      
      $result = $stmt->execute();
      $row = $stmt->fetch();

   } catch (PDOException $ex) {
	    //die
      file_put_contents('php_debug.log', 'getGeoLocations exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump("exception=", $ex, "END");
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      return null;
   }
   
   file_put_contents('php_debug.log', 'getGeoLocations3b >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("row=", $row, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
   
   return $row;
}

function insertGeoLocations($longitude, $latitude, $device_id, $created_at, $username, $password) {
    global $db;
    
    file_put_contents('php_debug.log', 'insertGeoLocations0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump($longitude, $latitude, $device_id, $created_at, $username, $password);
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $insert = "
insert into geolocations
(longitude, latitude, device_id, created_at, username, password)
values ( :longitude, :latitude, :device_id, :created_at, :username, :password )
	";
    
    file_put_contents('php_debug.log', 'insertGeoLocations1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    //var_dump("insert=", $insert, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    try {
        $stmt = $db->prepare($insert);
        $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR, strlen($longitude));
        $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR, strlen($latitude));
        $stmt->bindParam(':device_id', $device_id, PDO::PARAM_STR, strlen($device_id));
        $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR, strlen($created_at));
        $stmt->bindParam(':username', $username, PDO::PARAM_STR, strlen($username));
        $stmt->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
        $result = $stmt->execute();
    
        file_put_contents('php_debug.log', 'insertGeoLocations2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
        var_dump("insert result=", $result, "END");
        $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    }
    catch (PDOException $ex) {
        //die
        file_put_contents('php_debug.log', 'insertGeoLocations exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
        var_dump("exception=", $ex, "END");
        $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
        // continue?
    }
    
}

function getAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question){

   global $db;

   file_put_contents('php_debug.log', 'getAssessmentsAnswers1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump($person, $facility, $date_created, $assessment_id, $question);
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      $query = "
select
person,
facility,
date_created,
assessment_id,
question,
`option`          
from assess
where person = :person
and facility = :facility 
and date_created = :date_created
and assessment_id = :assessment_id
and question = :question
	";

   try {
	   
      $stmt = $db->prepare($query);
      $stmt->bindParam(':person', $person, PDO::PARAM_INT);      
      $stmt->bindParam(':facility', $facility, PDO::PARAM_INT);      
      $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);      
      $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);      
      $stmt->bindParam(':question', $question, PDO::PARAM_INT);      

      $result = $stmt->execute();
      $row = $stmt->fetch();

   } catch (PDOException $ex) {
	    //die
      file_put_contents('php_debug.log', 'getAssessmentsAnswers exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump("exception=", $ex, "END");
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      return null;
   }
   return $row;
}

function insertAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer){

   global $db;

   file_put_contents('php_debug.log', 'insertAssessmentsAnswers0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump($person, $facility, $date_created, $assessment_id, $question, $answer);
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $insert = "
insert into assess
(person, facility, date_created, assessment_id, question, `option`, active)
values ( :person, :facility, :date_created, :assessment_id, :question, :answer, 'Y' )
	";

   file_put_contents('php_debug.log', 'insertAssessmentsAnswers1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    //var_dump("insert=", $insert, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   try {
      $stmt = $db->prepare($insert);
      $stmt->bindParam(':person', $person, PDO::PARAM_INT);      
      $stmt->bindParam(':facility', $facility, PDO::PARAM_INT);      
      $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);      
      $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT);      
      $stmt->bindParam(':question', $question, PDO::PARAM_INT);      
      $stmt->bindParam(':answer', $answer, PDO::PARAM_STR, strlen($answer));      
      $result = $stmt->execute();

   file_put_contents('php_debug.log', 'insertAssessmentsAnswers2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("insert result=", $result, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    }
    catch (PDOException $ex) {
	    //die
    file_put_contents('php_debug.log', 'insertAssessmentsAnswers exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
	    // continue?
    }
}

function updateAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer){

   global $db;

   file_put_contents('php_debug.log', 'updateAssessmentsAnswers0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump($answer);
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $update = "
update assess set 
`option` = :answer
where 1=1
and person = :person
and facility = :facility
and date_created = :date_created
and assessment_id = :assessment_id
and question = :question
	";

   file_put_contents('php_debug.log', 'updateAssessmentsAnswers1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    //var_dump("update=", $update, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   try {
      $stmt = $db->prepare($update);
      $stmt->bindParam(':answer', $answer, PDO::PARAM_STR, strlen($answer)); 
      $stmt->bindParam(':person', $person, PDO::PARAM_INT);      
      $stmt->bindParam(':facility', $facility, PDO::PARAM_INT);      
      $stmt->bindParam(':date_created', $date_created, PDO::PARAM_STR, 10);      
      $stmt->bindParam(':assessment_id', $assessment_id, PDO::PARAM_INT); 
      $stmt->bindParam(':question', $question, PDO::PARAM_INT);      
      $result = $stmt->execute();

   file_put_contents('php_debug.log', 'updateAssessmentsAnswers2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("update result=", $result, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    }
    catch (PDOException $ex) {
	    //die
    file_put_contents('php_debug.log', 'updateAssessmentsAnswers exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("exception=", $ex, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
	    // continue?
    }
}

function putAssessmentsAnswers() {
   global $db;

   $post = array();
   //$post = $_POST['recs'];

   file_put_contents('php_debug.log', 'putAssessmentsAnswers0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("post['datatable']: ", $_POST['datatable'], "END");
   //var_dump('$_POST: ', $_POST, "END");
   //var_dump('$_POST num ', $_POST['num_recs'], "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   
   for($i = 0; $i < $_POST['num_recs']; $i++){

      $recsKey = 'recs'.$i;
      $rec = explode('|', $_POST[$recsKey]);
      $assess_id =     $rec[0];
      $person =        $rec[1];
      $facility =      $rec[2];
      $date_created =  $rec[3];
      $assessment_id = $rec[4];
      $question =      $rec[5];
      $answer =        $rec[6];

      file_put_contents('php_debug.log', 'putAssessmentsAnswers recs >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump($person, $facility, $date_created, $assessment_id, $question, $answer);
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      $row = getAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question);

      file_put_contents('php_debug.log', 'putAssessmentsAnswers returned >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
      var_dump('row: ', $row);
      $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

      if(!$row) {
         insertAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer);
      } elseif (strcmp($row['option'], $answer)) { // exists, answer changed
         updateAssessmentsAnswers($person, $facility, $date_created, $assessment_id, $question, $answer);
      } else {
         file_put_contents('php_debug.log', 'putAssessmentsAnswers no change >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
         $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
      }
   }

   file_put_contents('php_debug.log', 'putAssessmentsAnswers DONE >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $response["success"] = 1;
   die(json_encode($response));
}

function getPersons(){

   global $db;

   file_put_contents('php_debug.log', 'getPersons()0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("_POST=", $_POST, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $query = " 
select
p.id,
p.first_name,
p.last_name,
ifnull(p.national_id, 'not available') as national_id,
p.facility_id,
f.facility_name facility_name
from person p
join facility f on p.facility_id = f.id 
where 1=1 
and p.is_deleted = 0
   ";

   file_put_contents('php_debug.log', 'getPersons()1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("query=", $query, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $query_params = array();

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error.";

   file_put_contents('php_debug.log', 'getPersons() exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("response=", $response, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

        die(json_encode($response));
    }

   file_put_contents('php_debug.log', 'getPersons()2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("result=", $result, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $rows = $stmt->fetchAll();

   file_put_contents('php_debug.log', 'getPersons()2a >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("rows=", $rows, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   if ($rows) {
      $response["success"] = 1;
      $response["number_records"] = count($rows);
      $response["posts"] = array();

      foreach($rows as $row) { 
        $post = array();
	$post["person_id"] = $row["id"];
	$post["first_name"] = $row["first_name"];
	$post["last_name"] = $row["last_name"];
	$post["national_id"] = $row["national_id"];
	$post["facility_id"] = $row["facility_id"];
	$post["facility_name"] = $row["facility_name"];
	array_push($response["posts"], $post);
      }

      die(json_encode($response));
   }
}

function getAssessmentsQuestions(){

   global $db;

   file_put_contents('php_debug.log', 'getAssessmentsQuestions()0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("_POST=", $_POST, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $query = " 
select
aq.id,
aq.assessment_id,
aq.question,
aq.itemorder,
aq.itemtype,
aq.status
from assessments_questions aq
   ";

   file_put_contents('php_debug.log', 'getAssessmentsQuestions()1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("query=", $query, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $query_params = array();

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error.";

   file_put_contents('php_debug.log', 'getAssessmentsQuestions() exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("response=", $response, "END");
   var_dump("ex=", $ex, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

        die(json_encode($response));
    }

   file_put_contents('php_debug.log', 'getAssessmentsQuestions()2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("result=", $result, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $rows = $stmt->fetchAll();

   file_put_contents('php_debug.log', 'getAssessmentsQuestions()2a >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("rows=", $rows, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   if ($rows) {
      $response["success"] = 1;
      $response["number_records"] = count($rows);
      $response["posts"] = array();

      foreach($rows as $row) { 
        $post = array();
	$post["assessments_questions_id"] = $row["id"];
	$post["assessment_id"] = $row["assessment_id"];
	$post["question"] = $row["question"];
	$post["itemorder"] = $row["itemorder"];
	$post["itemtype"] = $row["itemtype"];
	$post["status"] = $row["status"];
	array_push($response["posts"], $post);
      }

      die(json_encode($response));
   }
}

function getAssessments(){

   global $db;

   file_put_contents('php_debug.log', 'getAssessments()0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("_POST=", $_POST, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $query = " 
select 
  a.id, 
  lat.assessment_type, 
  status 
from assessments a
join lookup_assessment_types lat on a.assessment_type_id = lat.id
   ";

   file_put_contents('php_debug.log', 'getAssessments()1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("query=", $query, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
    
    $query_params = array();

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message. 
        //die("Failed to run query: " . $ex->getMessage());
        
        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error.";

   file_put_contents('php_debug.log', 'getAssessments() exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("response=", $response, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

        die(json_encode($response));
    }

   file_put_contents('php_debug.log', 'getAssessments()2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("result=", $result, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $rows = $stmt->fetchAll();

   file_put_contents('php_debug.log', 'getAssessments()2a >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("rows=", $rows, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   if ($rows) {
      $response["success"] = 1;
      $response["number_records"] = count($rows);
      $response["posts"] = array();

      foreach($rows as $row) { 
        $post = array();
	$post["assessment_id"] = $row["id"];
	$post["assessment_type"] = $row["assessment_type"];
	$post["status"] = $row["status"];
	array_push($response["posts"], $post);
      }

      die(json_encode($response));
   }
}

function getQuestionDropdownOption(){

    global $db;

    file_put_contents('php_debug.log', 'getQuestionDropdownOption()0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    //var_dump("_POST=", $_POST, "END");
    var_dump("Request=", $_SERVER['REQUEST_URI'], "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    $query = "
select
aq.id as assessment_question_id,
ado.dropdown_phrase as dropdown_option
from assessments_questions aq
join assessment_dropdowngroup_to_assessment_dropdown_option addo on aq.dropdowngroup_id = addo.assessment_dropdowngroup_option_id
join assessment_dropdown_option ado on ado.id = addo.assessment_dropdown_option_id
order by aq.id, addo.assessment_dropdown_option_id
   ";

    file_put_contents('php_debug.log', 'getQuestionDropdownOption()1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("query=", $query, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    $query_params = array();

    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        // For testing, you could use a die and message.
        //die("Failed to run query: " . $ex->getMessage());

        //or just use this use this one to product JSON data:
        $response["success"] = 0;
        $response["message"] = "Database Error.";

        file_put_contents('php_debug.log', 'getQuestionDropdownOption() exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
        var_dump("response=", $response, "END");
        $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

        die(json_encode($response));
    }

    file_put_contents('php_debug.log', 'getQuestionDropdownOption()2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("result=", $result, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    $rows = $stmt->fetchAll();

    file_put_contents('php_debug.log', 'getQuestionDropdownOption()2a >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("rows=", $rows, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    if ($rows) {
        $response["success"] = 1;
        $response["number_records"] = count($rows);
        $response["posts"] = array();

        foreach($rows as $row) {
            $post = array();
            $post["assessment_question_id"] = $row["assessment_question_id"];
            $post["dropdown_option"] = $row["dropdown_option"];
            array_push($response["posts"], $post);
        }

        die(json_encode($response));
    }
}

?> 



