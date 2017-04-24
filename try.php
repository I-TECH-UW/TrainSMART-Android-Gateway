<?php

file_put_contents('php_debug.log', 'getTable start >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
//var_dump("_POST=", $_POST, "END");
//var_dump("_GET=", $_GET, "END");
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
	//var_dump("_POST=", $_POST, "END");
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
        $response["message"] = "Database Error. Please Try Again!";
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

	  case 'PersonToAssessments':
	    $response = putPersonToAssessments();
          break;

	  case 'AssessmentsAnswers':
	    $response = putAssessmentsAnswers();
          break;

          default:
            $response = 'bad';

	} //switch
	
        $response["success"] = 0;
        $response["message"] = "Invalid Datatable!";
        die(json_encode($response));

    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid Credentials!";
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
		<a href="register.php">Register</a>
<?php
} // else


function putPersonToAssessments(){

   global $db;

   $post = array();
   //$post = $_POST['rec'];

   file_put_contents('php_debug.log', 'putPersonToAssessments()0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   var_dump("post['datatable']: ", $_POST['datatable'], "END");
   //var_dump("post['rec']: ", $_POST['rec'], "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $response["success"] = 1;
   die(json_encode($response));
   
   // person_id, facility_id, date_created, assessment_id
   //foreach($post as $recs=>$rec ) 
      //$person_id =     $rec[0];
      //$facility_id =   $rec[1];
      //$date_created =  $rec[2];
      //$assessment_id = $rec[3];

   //file_put_contents('php_debug.log', 'putPersonToAssessments()1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
   //var_dump("rec: ", $person_id, $facility, $date_created, $assessment_id, "END");
   //$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

  // }
/*

   $response["success"] = 1;
   die(json_encode($response));
   //
   // assess_id, person, facility, date_created, assessment_id, question, answer, active

   foreach($post as $recs=>$rec ) {
      $assess_id =     $rec[0];
      $person =        $rec[1];
      $facility =      $rec[2];
      $date_created =  $rec[3];
      $assessment_id = $rec[4];
      $question =      $rec[5];
      $answer =        $rec[6];
      $active =        $rec[7];

   $query = "
select
first_name, 
last_name, 
facility_id, 
national_id
from person
where id = " . $person
;
   file_put_contents('php_debug.log', 'putPersonToAssessments()2 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("query=", $query, "END");
   $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   $query_params = array();

   try {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
	    //die
    file_put_contents('php_debug.log', 'putPersonToAssessments exception >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);
	    // continue?
    }

    file_put_contents('php_debug.log', 'putPersonToAssessments()3 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("result", $result, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

    $rows = $stmt->fetchAll();

    file_put_contents('php_debug.log', 'putPersonToAssessments()4 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
    var_dump("rows", $rows, "END");
    $toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

   } //foreach 

   $response["success"] = 1;
   die(json_encode($response));
 */
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
where p.last_name like 'r%'
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
        $response["message"] = "Database Error. Please Try Again!";

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
        $response["message"] = "Database Error. Please Try Again!";

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
        $response["message"] = "Database Error. Please Try Again!";

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

?> 
