<?php

//load and connect to MySQL database stuff
require("config.inc.php");

if (!empty($_POST)) {

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
        $response = getAssessmentsQuestions();
        die(json_encode($response));

    } else {
        $response["success"] = 0;
        $response["message"] = "Invalid Credentials!";
        die(json_encode($response));
    }
} else {
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
}
?>
<?php

function getAssessmentsQuestions(){

    global $db;

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

        die(json_encode($response));
    }
    $rows = $stmt->fetchAll();

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
