<?php

file_put_contents('php_debug.log', 'login0 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
var_dump("_POST=", $_POST, "END");
var_dump("_GET=", $_GET, "END");
$toss = ob_get_clean(); file_put_contents('php_debug.log', $toss .PHP_EOL, FILE_APPEND | LOCK_EX);

//load and connect to MySQL database stuff
require("config.inc.php");

if (!empty($_POST)) {

	file_put_contents('php_debug.log', 'login1 >'.PHP_EOL, FILE_APPEND | LOCK_EX);    ob_start();
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
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
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
        $response["success"] = 1;
        $response["message"] = "Login successful!";
        $response["id"] = $row['id'];
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
		<form action="login.php" method="post"> 
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
}

?> 

