<?php

function db_connect() {

	// DB info
	$dns = 'mysql:host=127.0.0.1;dbname=ascentus';
	$user = 'root';
	$pass = '';
	try{
		//Use PDO to create database connection
		$db = new PDO($dns, $user, $pass); 
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch(PDOException $e) {
		// If error, display error message
		echo 'Error: ' . $e->getMessage();
	}
	return $db;
}

function search ($eatery) {
	
	// Connect to Database
	$db = db_connect();
	
	//Query with
	$sql = "SELECT e.name, e.city, e.state, e.zip, et.type_name, eo.first_name, eo.last_name, eo.phone
			FROM eateries AS e 
			JOIN eatery_type AS et ON e.type=et.id  
			JOIN eatery_owners AS eo ON eo.id=e.owner 
			WHERE et.type_name = :eatery 
				 OR eo.first_name = :eatery 
				 OR eo.last_name = :eatery 
				 OR eo.phone = :eatery
				 OR e.name = :eatery 
				 OR e.city = :eatery
				 OR e.state = :eatery
				 OR e.zip = :eatery;";
	
	$query = $db->prepare($sql);	
	$values = array(':eatery' => $eatery);
	$result = $query->execute($values);
	
	if ($result === false) { 
		//Have some type of error handling here
		return false;
	}
	if ($query->rowCount() == 0) {
		echo "No results were found. Click <a href='index.php'>here</a> to go back and search again.";
		return false;
	}
	
	$eateries = $query->fetchAll();	
	
	foreach ($eateries as $eatery) {
		echo $eatery['name']. ":<br>";
		echo "&nbsp;&nbsp;&nbsp;Owned By: " . $eatery['first_name'] . " " . $eatery['last_name'] . " | Phone: " . $eatery['phone'] . "<br>";
		echo "&nbsp;&nbsp;&nbsp;Located in: " . $eatery['city'] . ", " . $eatery['state'] . " " . $eatery['zip'] . "<br><br>";
	}	
	echo "Click <a href='index.php'>here</a> to go back and search again.";
}

//Check is eatery post data is set
if (isset($_POST['eatery'])) {
	// If post data is set, go search the database
	search($_POST['eatery']);
} else {
	//Redirect user back to inedex.php to search again.
	echo "You did not enter valid search criteria and will be redirect back to index.php in 5 seconds";
	header( "refresh:5;url=index.php" );
}
?>