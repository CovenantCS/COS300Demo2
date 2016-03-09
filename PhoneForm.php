
<?php
session_start();
print_r($_SESSION);
echo "<br>";
print_r($_POST);
if(!isset($_POST["Btn"])) {
	//echo "<br>print SESSION<br>";
	//print_r($_SESSION);
	if (array_key_exists('name', $_SESSION)) {
		$cur_name = $_SESSION['name'];
	} else {
		$cur_name = '';
	}
?>

<html>
<head>
<title>Phone Book</title>

</head>
<body>
<H1>Phone Book</H1>

<form  action="PhoneForm.php" method="post">
<?php
echo 'Name: <input type="text", name="ServiceName", value="'.$cur_name.'", size="15">';
?>

<input type="submit" name="Btn" value="Add Name">
<input type="submit" name="Btn" value="Find Name">
<input type="submit" name="Btn" value="Update Name">
<input type="submit" name="Btn" value="Delete Name">
<BR>
Phone: <input type="text", name="phone" size="10">

<input type="radio" name="phonetype" value="other" CHECKED>Other
<input type="radio" name="phonetype" value="home">Home
<input type="radio" name="phonetype" value="work">Work
<input type="radio" name="phonetype" value="cell">Cell
<input type="radio" name="phonetype" value="fax">Fax
<BR>
<input type="submit" name="Btn" value="Add Phone">
<input type="submit" name="Btn" value="Find Phone">
<input type="submit" name="Btn" value="Delete Phone">
</form>
<?php
// set server access variables
$host = "localhost";
$user = "Hunt";
$pass = "PW300";
$db = "phone";

// create mysqli object
// open connection
$mysqli = new mysqli($host, $user, $pass, $db);

// check for connection errors
if (mysqli_connect_errno()) {
    die("Unable to connect!");
}
// create query
$query = "SELECT * FROM PERSON";

// execute query
if ($result = $mysqli->query($query)) {
    // see if any rows were returned
    if ($result->num_rows > 0) {
        // yes
        // print them one after another

		echo '<textarea cols="40" rows="20" name="myname" readonly>';
        while($row = $result->fetch_array()) {
			$phone_query = "SELECT * FROM PHONE where personId='".$row[0]."'";
			if ($phone_result = $mysqli->query($phone_query)) {
				if ($phone_result->num_rows > 0) {
					echo "\n\n".$row[1];
					while($phone_row = $phone_result->fetch_array()) {
						$ps = $phone_row[3];
						$fps = substr($ps,0,3)."-".substr($ps,3,3)."-".substr($ps,6);
						$phone_type = $phone_row[2];
						$code = "";
						switch($phone_type) {
							case "other":
								$code = "O: ";
								break;
							case "work":
								$code = "W: ";
								break;							
							case "home":
								$code = "H: ";
								break;
							case "cell":
								$code = "C: ";
								break;
							case "fax":
								$code = "F: ";
								break;
						}
						echo "\n\t".$code.$fps;
					}
				} else {
					echo "\n\n".$row[1];
					echo "\n\tNo Phone Numbers Available";
				}
			}
        }
		echo "</textarea>";
    }
    else {
        // no
        // print status message
        echo "No rows found!";
    }

    // free result set memory
    $result->close();
}
else {
    // print error message
    echo "Error in query: $query. ".$mysqli->error;
}
// close connection
$mysqli->close();
?>
<?php
} else {	
	//echo "<br>print POST<br>";
	//print_r($_POST);
	//echo "<br>print SESSION<br>";
	//print_r($_SESSION);
	// set server access variables
	$host = "localhost";
	$user = "Hunt";
	$pass = "PW300";
	$db = "phone";

	// create mysqli object
	// open connection
	$mysqli = new mysqli($host, $user, $pass, $db);

	// check for connection errors
	if (mysqli_connect_errno()) {
		die("Unable to connect!");
	}
	$action = $_POST["Btn"];
	echo "<Br>action is ".$action."<BR>";
	if ($action == "Add Name") {
		echo "Add";
		$cur_name = $_POST['ServiceName'];
		$_SESSION['name'] = $cur_name;	
		
		if (!($stmt = $mysqli->prepare("INSERT INTO PERSON(name) VALUES (?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("s", $cur_name)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		$query = "SELECT personId, name FROM PERSON where name='".$cur_name."'";
		if ($result = $mysqli->query($query)) {
			// see if any rows were returned we want exactly one
			if ($result->num_rows==1) {
				$row = $result->fetch_array();
				$_SESSION['personId'] = $row[0];
			} else {
				echo "select found other than one row";
			}
		}
	} elseif ($action == "Find Name") {
		echo "Find";
		$cur_name = $_POST['ServiceName'];
		$_SESSION['name'] = $cur_name;
		
		$query = "SELECT personId, name FROM PERSON where name='".$cur_name."'";
		if ($result = $mysqli->query($query)) {
			// see if any rows were returned we want exactly one
			if ($result->num_rows==1) {
				$row = $result->fetch_array();
				$_SESSION['personId'] = $row[0];
			} else {
				echo "select found other than one row";
			}
		}

	} elseif ($action == "Update Name") {
		echo "<BR>Update Name<BR>".$action."XXX<BR>";
		if (array_key_exists('personId', $_SESSION)) {
			$personId = $_SESSION['personId'];
			$cur_name = $_POST['ServiceName'];
			echo "<BR>Made it into update ".$personId."<BR>";
			if (!($stmt = $mysqli->prepare("UPDATE PERSON set name=? where personId=?"))) {
				echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			echo "<BR>about to bind ".$cur_name." ".$personId."<BR>";
			if (!$stmt->bind_param("si", $cur_name, $personId)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			if (!$stmt->execute()) {
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$_SESSION['name'] = $cur_name;	
		}
	
	} elseif ($action == "Delete Name") {
		echo "Delete";
		$cur_name = $_POST['ServiceName'];
		$_SESSION['name'] = $cur_name;
		if (!($stmt = $mysqli->prepare("SELECT personId, name FROM PERSON where name=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("s", $cur_name)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!($stmt = $mysqli->prepare("DELETE FROM PHONE where personId=?"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("i", $personId)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
	} elseif ($action == "Add Phone") {
		echo "Add Phone";
		
		$personId = $_SESSION['personId'];
		$phoneType = $_POST['phonetype'];
		$phone = $_POST['phone'];
		
		if (!($stmt = $mysqli->prepare("INSERT INTO PHONE(personId, phoneType, phoneNumber) VALUES (?,?,?)"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		if (!$stmt->bind_param("iss", $personId,$phoneType,$phone)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		
	} elseif ($action == "Find Phone") {
		echo "Find";
	} elseif ($action == "Delete Phone") {
		echo "Delete";
	} else {
		echo "Error";
	}
	header('Location: PhoneForm.php'); 
	//echo "<br>print SESSION<br>";
	//print_r($_SESSION);
}
?>

</body>
</html>