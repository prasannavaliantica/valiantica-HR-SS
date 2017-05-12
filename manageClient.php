
<body>

<?php

include 'include/redirect.php';
include 'include/header.php';
include 'include/globals.php';


$clientname = $email = $address = $mobile = $country = $state = $city = $zip = $clienttype = $primarycontactperson = "";
$clientnameErr = $emailErr = $addressErr = $mobileErr = $countryErr = $stateErr = $cityErr = $zipErr = $clienttypeErr = $primarycontactpersonErr = "";
$currentclientname = "";

$nameRegExp = "/^[a-zA-Z\d ]*$/";
$phoneRegExp = "/^\d{3}-?\d{3}-?\d{4}$/";
$zipRegExp = "/^\d{5}$/";
$countryRegExp = $stateRegExp = $cityRegExp = $nameRegExp;

$validData = 1;
$editAction = 0;
$deleteAction = 0;
$cancelAction = 0;
$createAction = 0;
$addClientActionPrevPage = 0;
$editClientActionPrevPage = 0;

$id = "";

function getClient($id) {
    $result = "";
    if (isset($id) && (intval($id) > 0)) { 
        $exists = False;
        $conn=getDBConnection();

        $sql = "select * from client where id = $id";
        $result = mysqli_query($conn, $sql);
    }

    return $result;
}

function getClientId($clientname,$city) {
    $clientId = "";

    $conn=getDBConnection();

    $sql = "select id from client where clientname = '$clientname' and city = '$city'"; 
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $clientId = $row["id"];
        }
    }
    return $clientId;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
   if ($_REQUEST["id"] > 0) {
    // Get the values from DB and fill-out the fields above.
    $id = intval($_REQUEST["id"]);
    $editClientActionPrevPage = 1;

    $result = getClient($id);
    if (isset($result) && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $clientname = $row["clientname"];
	    $currentclientname = $clientname;
            $email = $row["email"];
            $address = $row["address"];
            $primarycontactperson = $row["primarycontactperson"];   
            $mobile = $row["mobileno"];
            $country = $row["country"];
            $state = $row["state"];
            $city = $row["city"];
            $zip = $row["zip"];
            $clienttype = $row["type"];  
	    $validData = 0;
        }
    }
   }

} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $id = intval($_REQUEST["id"]);
  $clienttype = $_REQUEST["clienttype"];   
  
  if (isset($_REQUEST["clientProfilesHome"])) {
      header("Location: listClients.php?clienttype=$clienttype");

  } else  if (isset($_REQUEST["home"])) {
      header("Location: home.php");

  } else if (isset($_REQUEST["addClient"])) {
    $addClientActionPrevPage = 1;
 
  } else if (isset($_REQUEST["Cancel"])) {
     
  //    echo "cancell = " . print_r($_REQUEST);
      if (isset($_REQUEST["edit"])) { 
          // from the employees page.  So go back to the employee page.
         // header("Location: ". $GLOBALS["employeesHomePage"]);
          $status = $_REQUEST["status"];  
          $redirecturl = $GLOBALS["employeesHomePage"]."?status=$status";
          // code to redirect to employees home page.
         if (isset($_REQUEST["type"])) {
             $filtertype =  $_REQUEST["type"];
             $cond =  $_REQUEST["cond"];
             $value =  $_REQUEST["value"];

             $searchFields = "search=1&type=$filtertype&cond=$cond&value=$value";

             $redirecturl = $redirecturl."&".$searchFields;
         } 
         header("Location: ". $redirecturl);

      } else {
       // code to redirect to clients list page. 
          $clienttype = $_REQUEST["clienttype"]; 
          $redirecturl = $GLOBALS["clientsHomePage"]. "?&clienttype=$clienttype";
          header("Location: ". $redirecturl);
      }
  
  } else if (isset($_REQUEST["Delete"])) {
    
    $deleteAction = 1;
    
  } else if (isset($_REQUEST["Create"]))  {
      $createAction = 1;

  } else if (isset($_REQUEST["Update"])) {
      $editAction = 1;
  }

  if ($deleteAction) {
    // case when user chooses to delete the record for this candidate. All data is valid.  Just delete it.
    $validData = 1;
  } else if ($createAction || $editAction) {
    // case when user chooses to create a new record or edit an existing one.  Validate and execute the action.
    $clientname = $_REQUEST["clientname"];
    $currentclientname = $_REQUEST["currentclientname"];
    $email = $_REQUEST["mail"];
    $address = $_REQUEST["address"];
    $primarycontactperson = $_REQUEST["primarycontactperson"];
    $mobile = $_REQUEST["mobileno"];
    $country = $_REQUEST["country"];
    $state = $_REQUEST["state"];
    $city = $_REQUEST["city"];
    $zip = $_REQUEST["zip"];
 
    if (empty($clientname)) {
        $clientnameErr = "First Name is required";
	$validData = 0;
    }  else {
        $clientname = test_input($clientname);
	if (!preg_match($nameRegExp,$clientname)) {
	    $clientnameErr = "Only letters,numbers and white-space are allowed.";
	    $validData = 0;
        } else {

            // check if the client exists
            $currentClientId = getClientId($clientname,$city);

            if (($createAction && !empty($currentClientId)) ||
                ($editAction && !empty($currentClientId) &&
                (intval($id) !== intval($currentClientId)))) {
                 $clientnameErr = "Profile with this Name and Location already exists";
                 $validData = 0;
            }
        }
    }

    if (!empty($email)) {
        $email = test_input($email);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	    $emailErr = "Invalid email format";
	    $validData = 0;
        }
    }

    if (!empty($address)) {
        $address = test_input($address);
    }

    if (!empty($primarycontactperson)) {
        $primarycontactperson = test_input($primarycontactperson);
	if (!preg_match($nameRegExp,$primarycontactperson)) {
	    $primarycontactpersonErr = "Only letters and white-space are allowed.";
	    $validData = 0;
        }
    }

    if (!empty($mobile)) {
        $mobile = test_input($mobile);
	if (!preg_match($phoneRegExp,$mobile)) {
	    $mobileErr = "Invalid phone number - Expected format - 123-456-7890 or 1234567890";
	    $validData = 0;
        }
    }


    if (!empty($country)) {
        $country = test_input($country);
        if (!preg_match($countryRegExp,$country)) {
            $countryErr = "Only letters and white-space are allowed";
            $validData = 0;
        }
    }


    if (!empty($state)) {
        $state = test_input($state);
        if (!preg_match($stateRegExp,$state)) {
            $stateErr = "Only letters and white-space are allowed";
            $validData = 0;
        }
    }

    if (empty($city)) {
        $cityErr = "City is required";
        $validData = 0;
    }  else {
        $city = test_input($city);
        if (!preg_match($cityRegExp,$city)) {
            $cityErr = "Only letters and white-space are allowed";
            $validData = 0;
        }
    }

    if (!empty($zip)) {
        $zip = test_input($zip);
	if (!preg_match($zipRegExp,$zip)) {
	    $zipErr = "Invalid zip - Expected the 5-digit zip code"; 
	    $validData = 0;
        }
    }
 }  else {
    // case when this page is loaded by a GET from the previous page.
    $validData = 0;
 }
}

// Show the values in editable fields only if launched from the 'Manage Clients' Page.
if ($validData == 0) {
    $edit = True;
    if ($_REQUEST["edit"]) {
        $edit = False;
    }

    echo "<br><br>";
    if ($edit) {
        echo "<p> <span class=error> * required field </span> </p>";
    }
    echo "<form method='post' action=" ; echo htmlspecialchars($_SERVER["PHP_SELF"]); echo " enctype='multipart/form-data' >" ;

    echo "<table align=center>";
    echo "<tr>";
    echo "<td>";

    echo "<h1 align=center>";
    if (!empty($currentclientname)) {
        echo "Profile of $currentclientname";
    } else {
        echo "New $clienttype Profile";             
    }

    echo "</td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo "$clienttype Name :";   
    echo "</td>";

    echo "<td>";
        if ($edit) {
        echo "<input type='text' name='clientname' value = '$clientname' maxlength=30> <span class='error'>* $clientnameErr </span>";
	} else {
	echo "$clientname";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "Email :";
    echo "</td>";

    echo "<td>";
        if ($edit) {
        echo "<input type='text' name='mail' value = '$email' maxlength=50> <span class='error'> $emailErr </span>";
	} else {
	    echo "$email";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "Address :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='address' value = '$address' maxlength=100> <span class='error'> $addressErr </span>";
	} else {
	    echo "$address";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "Primary Contact :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='primarycontactperson' value = '$primarycontactperson' maxlength=50> <span class='error'> $primarycontactpersonErr </span>";
	} else {
	    echo "$primarycontactperson";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "Mobile No :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='mobileno' value = '$mobile' maxlength='14'> <span class='error'> $mobileErr </span>";
	} else {
	    echo "$mobile";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "Country :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='country' value = '$country' maxlength=50> <span class='error'> $countryErr </span>";
	} else {
	    echo "$country";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "State :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='state' value = '$state' maxlength=25> <span class='error'> $stateErr </span>";
	} else {
	    echo "$state";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "City :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='city' value = '$city' maxlength=25> <span class='error'>* $cityErr </span>";
	} else {
	    echo "$city";
	}
    echo "<td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
        echo  "Zip :";
    echo "</td>";

    echo "<td>";
	if ($edit) {
        echo "<input type='text' name='zip' value = '$zip' maxlength=10> <span class='error'> $zipErr </span> <br>";
	} else {
	    echo "$zip";
	}
    echo "<td>";
    echo "</tr>";

    echo "</table>";

    echo "<table align=center>";
    echo "<tr>"; 

    if ($edit) {
        echo "<br><br>"; 
        echo "<td>"; 
        echo "<input type='hidden' name='id' value=$id>";
        echo "</td>";
 
        echo "<td>";
        echo "<input type='hidden' name='currentclientname' value=$currentclientname>";
        echo "</td>";
        //echo "<input type='hidden' name='clienttype' value=$type>";

        if ($createAction || $addClientActionPrevPage) {
            echo "<td>"; 
            echo "<input type='submit' name='Create' value='Create'>";
            echo "</td>"; 
        } else if ($editAction || $editClientActionPrevPage) {
            echo "<td>"; 
            echo "<input type='submit' name='Update' value='Update'>";
            echo "</td>"; 
            echo "<td>"; 
            echo "<input type='submit' name='Delete' value='Delete'>";
            echo "</td>"; 
        }
    } else {
        $status = $_REQUEST["status"];
        echo "<td>"; 
        echo "<input type='hidden' name='edit'>";
        echo "</td>";

        echo "<td>"; 
        echo "<input type='hidden' name='status' value=$status>";
        echo "</td>";

	echo "<br><br>";
    }
    displayCancelButton();
    echo "<input type='hidden' name='clienttype' value=$clienttype>";
    echo "</tr>";
     
    echo "</form>";

} else {

// Establish the connection to the Mysql db.
    $conn = getDBConnection();

    $sql = "";
    $action = "";
    if ($createAction) {
        $sql = "INSERT INTO client(clientname,email,address,primarycontactperson,mobileno,country,state,city,zip,type) values('$clientname', '$email', '$address', '$primarycontactperson','$mobile', '$country', '$state', '$city','$zip', '$clienttype')";
        $action = "added";
    } else if ($editAction) {
      $sql = "UPDATE client set clientname='$clientname',email='$email',address='$address',primarycontactperson='$primarycontactperson',mobileno='$mobile',country='$country',state='$state',city='$city',zip='$zip' , type = '$clienttype' where id=$id";
      $action = "updated";
  } else if ($deleteAction) {
      $sql="delete from client where id=$id";
      $action = "deleted";
  }

    if (mysqli_query($conn, $sql)) {
        $message = "$clienttype Profile was $action successfully <br>";
        echo "<br><br>";

	echo "<form method='post' action=" ; echo htmlspecialchars($_SERVER["PHP_SELF"]); echo ">" ;
        echo "<table align=center>";
        echo "<tr>";
        echo "<td>";
        echo "<b>$message</b>";
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>";
        echo "<input type='submit' name='clientProfilesHome' value='Back To $clienttype Profiles Page'>";
        echo "</td>";
        echo "</tr>";


        echo "<tr>";
        echo "<td>";
        echo "<input type='hidden' name='clienttype' value='$clienttype'>";
        echo "</td>";
        echo "</tr>";

        echo "</form>";

    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}

?>

</body>
</html>


