<html>
<body>

<?php
include 'include/redirect.php';
include 'include/header.php';
include 'include/globals.php';


$firstnameErr = $lastnameErr = $personalemailErr = $ssnErr = $dobErr = $genderErr = $addressErr = $contactnoErr = $countryErr = $stateErr = $cityErr = $zipErr  = $emergencycontactnoErr =  "";

$startdateErr = $enddateErr = $worktypeErr = $jobtitleErr = $worklocationErr = $payrateErr = $visatypeErr = $visaexpirydateErr = $workemailErr = $emergencycontactpersonErr = $emergencycontactnoErr = $notesErr = "";

$firstname = $lastname = $personalemail  = $dob = $gender = $address = $contactno = $country = $state = $city = $zip = $clientid = "";

$currentfirstname = "";
$currentlastname = "";

$startdate = $enddate = $worktype = $jobtitle = $worklocation = $payrate = $visatype = $visaexpirydate = $workemail = $emergencycontactperson = $emergencycontactno = $notes = "";

$currentdocument1 = $currentdocument2 = $currentdocument3 = "";
$newdocument1 = $newdocument2 = $newdocument3 = "";

$nameRegExp = "/^[a-zA-Z ]*$/";
//$ssnRegExp = "/^[^-]{3}-?[^-]{2}-?[^-]{4}$/";
$ssnRegExp = "/^[^-]{3}-[^-]{2}-[^-]{4}$/";
$phoneRegExp = "/^\d{3}-?\d{3}-?\d{4}$/";
$zipRegExp = "/^\d{5}$/";
$countryRegExp = "/^[a-zA-Z ]*$/";
$stateRegExp = $cityRegExp = $countryRegExp;

$validData = 1;
$editAction = 0;
$deleteAction = 0;
$cancelAction = 0;
$createAction = 0;
$addEmployeeActionPrevPage = 0;
$editEmployeeActionPrevPage = 0;

$clientid = "";
$primaryvendorid = "";
$midvendorid = "";

$id = intval($_REQUEST["id"]);
$status = $_REQUEST["status"];
$employeesHomePage = $GLOBALS["employeesHomePage"]."?status=$status";

function getClients($clientType) {
   $conn = getDBConnection();

    $sql = "select * from client where type = '$clientType'"; 
   // echo "sql  = $sql";
    $result = mysqli_query($conn, $sql);

    mysqli_close($conn);

    return $result;
}

function getUserId($firstname,$lastname) {
    $currentUserId = "";

    $conn=getDBConnection();

    $sql = "select id from employee2 where firstname = '$firstname' && lastname = '$lastname'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
	    $currentUserId = $row["id"];
	}
    }
    return $currentUserId;
}

function deleteDocument($currentdocumentName) {
    if (!empty($currentdocumentName)) {
        $destDir=$GLOBALS["documentsDir"];
        $document = $destDir.$currentdocumentName;

      ///  echo "to delete $currentdocumentName";
	if (file_exists($document)) { 
        //   echo "file exists $currentdocumentName";
           $deletestatus =  unlink($document);
           if ($deletestatus) {
               echo "The document $currentdocumentName has been deleted <br>";   
           }
	}
    } 
}

function deleteDocuments($currentdoc1, $currentdoc2, $currentdoc3) {
    deleteDocument($currentdoc1);
    deleteDocument($currentdoc2);
    deleteDocument($currentdoc3);    
}

function deleteAllDocumentsInDir($dirName) {
    foreach(glob($dirName."/*") as $filename) {
        if (is_file($filename)) {
           $deletestatus =  unlink($document);
           if ($deletestatus) {
               echo "The document $filename has been deleted <br>";   
           }              
        }
    }
    rmdir($dirName);
}

function uploadDocument($documentFieldName,$currentDocumentName)   { 

   $destDir=$GLOBALS["documentsDir"];

    $uploadDoc = $_FILES[$documentFieldName]["name"];
    $tmpDoc = $_FILES[$documentFieldName]["tmp_name"];

    if (!empty($uploadDoc)) {
        $destDocFile = $destDir . basename($uploadDoc);
        $canUpload = 1;

        // delete the current document.
        deleteDocument($currentDocumentName);

        if (move_uploaded_file($tmpDoc,$destDocFile)) {
            echo "The file " . basename($uploadDoc) . " has been uploaded.<br> ";
        }
    } 
}

function displayClients($clientid, $fieldName, $clientTypeStr, $clientType) {
    echo "<tr>";
    echo "<td>";
    echo "$clientTypeStr :";
 
    echo "</td>";

    echo "<td>";

    $clients = getClients($clientType);
    echo "<select name='$fieldName'>";
    echo "<option value= -1";
        if (!isset($clientid) || is_null($clientid)) {
            echo " selected ";
        }
        echo ">";
        echo "Assign $clientTypeStr";
    echo "</option>";

    if (mysqli_num_rows($clients) > 0) {
        while($row = mysqli_fetch_assoc($clients)) {
                echo "<option value=";echo $row["id"];
                if ($clientid == $row["id"]) {
                    echo " selected ";
                }
                echo ">"; 	
                    if ($clientType == 'Client') {
                        echo $row["clientname"] . " @ " .  $row["city"]; 
                    } else {
                        echo $row["clientname"];
                    }  
                echo "</option>";
        }
    }
    echo "</select>";

    echo "</td>";
    echo "</tr>";
}

function displayUploadDocumentField($documentLabel, $documentFieldName) {
    echo "<tr>";

    echo "<td>";
    echo "Upload $documentLabel: ";
    echo "</td>";

    echo "<td>";
    echo "<input type='file' name='$documentFieldName' id='$documentFieldName'>";
    echo "</td>";

    echo "</tr>";
}

function displayCurrentDocumentField($documentLabel,$currentDocument) {
    echo "<tr>";

    echo "<td>";
        echo "Current $documentLabel : ";
    echo "</td>";

/*
    echo "<td>";
    if (!empty($currentDocument)) {
        echo "<b>$currentDocument</b>";
    } else {
        echo "<b> None </b>";
    }
*/
    $add = True;
    displayDocumentHyperlink($currentDocument,$add);

    echo "</td>";
    echo "</tr>";
}

function addHiddenStatusField($status) {

    echo "<tr>";
    echo "<td>";

    echo "<input type = 'hidden' name = 'status' value = $status >";
 
    echo "</td>";

    echo "</tr>";
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
 
   if ($id > 0) {
    // Get the values from DB and fill-out the fields above
    //$id = intval($_REQUEST["id"]);
    $editEmployeeActionPrevPage = 1;

   /* $conn = getDBConnection();

    $sql = "select * from employee2 where id = $id";
    $result = mysqli_query($conn, $sql);*/  

    $row = getCandidateProfile($id);

  //  if (mysqli_num_rows($result) > 0) {
    //    while($row = mysqli_fetch_assoc($result)) {
      if ($row != NULL) {
            $firstname = $row["firstname"];
            $lastname = $row["lastname"];
            $personalemail = $row["personalemail"];
            $dob = $row["dob"];
            $gender = $row["gender"];
            $address = $row["address"];
            $contactno = $row["contactno"];
            $country = $row["country"];
            $state = $row["state"];
            $city = $row["city"];
            $zip = $row["zip"];

            $clientid = $row["clientid"];
            $primaryvendorid = $row["primaryvendorid"];
            $midvendorid = $row["midvendorid"];
   
	    $currentfirstname = $firstname;
	    $currentlastname = $lastname;

            $startdate = $row["startdate"]; 
            $enddate = $row["enddate"];
            $worktype = $row["worktype"];
            $jobtitle = $row["jobtitle"];
 
            $payrate = $row["payrate"];
            $visatype = $row["visatype"];
            $visaexpirydate = $row["visaexpirydate"];
            $workemail = $row["workemail"];
            $emergencycontactperson = $row["emergencycontactperson"];
            $emergencycontactno = $row["emergencycontactno"];
            $notes = $row["notes"];

            $currentdocument1 = $row["document1"];
	    $currentdocument2 = $row["document2"];
            $currentdocument3 = $row["document3"];               
	    
            $validData = 0;
     }
   }

} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

//  echo "post request "  . print_r($_REQUEST);
 // $id = intval($_REQUEST["id"]);
  
  if (isset($_REQUEST["candidateProfilesHome"])) {
    header("Location: " . $employeesHomePage);

  } else  if (isset($_REQUEST["home"])) {
    header("Location: home.php");

  } else if (isset($_REQUEST["addEmployee"])) {
    $addEmployeeActionPrevPage = 1;
 
  } else if (isset($_REQUEST["Cancel"])) {
      
     $redirecturl = $employeesHomePage;
       // code to redirect to home page.
     if (isset($_REQUEST["type"])) {
         $type =  $_REQUEST["type"];
         $cond =  $_REQUEST["cond"];
         $value =  $_REQUEST["value"];

         $searchFields = "search=1&type=$type&cond=$cond&value=$value";

         $redirecturl = $redirecturl."&".$searchFields;
         echo "redireect " . $redirecturl;
     } 
     header("Location: ". $redirecturl);
  
  } else if (isset($_REQUEST["Delete"])) {
    
    $deleteAction = 1;
    
  } else if (isset($_REQUEST["Create"]))  {
      $createAction = 1;

  } else if (isset($_REQUEST["Update"])) {
      $editAction = 1;
  }

   if (isset($_REQUEST["currentdocument1"])) {
       $currentdocument1 =  $_REQUEST["currentdocument1"];
   } 

   if (isset($_REQUEST["currentdocument2"])) {
       $currentdocument2 =  $_REQUEST["currentdocument2"];
   } 

   if (isset($_REQUEST["currentdocument3"])) {
       $currentdocument3 =  $_REQUEST["currentdocument3"];
   } 

    $currentfirstname = $_REQUEST["currentfirstname"];
    $currentlastname = $_REQUEST["currentlastname"];

  if ($deleteAction) {
    // case when user chooses to delete the record for this candidate. All data is valid.  Just delete it.
    $validData = 1;
  
  } else if ($createAction || $editAction) {
    // case when user chooses to create a new record or edit an existing one.  Validate and execute the action.
    $firstname = $_REQUEST["firstname"];
    $lastname = $_REQUEST["lastname"];
    $personalemail = $_REQUEST["personalemail"];
    $dob = $_REQUEST["dob"];
    $gender = $_REQUEST["gender"];
    $address = $_REQUEST["address"];
    $contactno = $_REQUEST["contactno"];
    $country = $_REQUEST["country"];
    $state = $_REQUEST["state"];
    $city = $_REQUEST["city"];
    $zip = $_REQUEST["zip"];

    $clientid = $_REQUEST["clientid"];
    $primaryvendorid = $_REQUEST["primaryvendorid"];
    $midvendorid = $_REQUEST["midvendorid"];

    $startdate = $_REQUEST["startdate"]; 
    $enddate = $_REQUEST["enddate"];
    $worktype = $_REQUEST["worktype"];
    $jobtitle = $_REQUEST["jobtitle"];
    $payrate = $_REQUEST["payrate"];
    $visatype = $_REQUEST["visatype"];
    $visaexpirydate = $_REQUEST["visaexpirydate"];
    $workemail = $_REQUEST["workemail"];
    $emergencycontactperson = $_REQUEST["emergencycontactperson"];
    $emergencycontactno = $_REQUEST["emergencycontactno"];
    $notes = $_REQUEST["notes"];

    //$currentdocument1 = $_REQUEST["currentdocument1"];
    //$currentdocument2 = $_REQUEST["currentdocument2"];
    //$currentdocument3 = $_REQUEST["currentdocument3"];               
  
    $newdocument1 = $_FILES["document1"]["name"];
    $newdocument2 = $_FILES["document2"]["name"];
    $newdocument3 = $_FILES["document3"]["name"];
 
    if ($clientid == -1) {
        $clientid = 'null'; 
    }

    if ($primaryvendorid == -1) {
        $primaryvendorid = 'null'; 
    }

    if ($midvendorid == -1) {
        $midvendorid = 'null'; 
    }

    if (empty($firstname)) {
        $firstnameErr = "First Name is required";
	$validData = 0;
    }  else {
        $firstname = test_input($firstname);
	if (!preg_match($nameRegExp,$firstname)) {
	    $firstnameErr = "Only letters and white-space are allowed.";
	    $validData = 0;
        }
    }

    if (empty($lastname)) {
        $lastnameErr = "Last Name is required";
	$validData = 0;
    }  else {
        $lastname = test_input($lastname);
	if (!preg_match($nameRegExp,$lastname)) {
	    $lastnameErr = "Only letters and white-space are allowed.";
	    $validData = 0;
        }
    }

    if (!empty($personalemail)) {
        $personalemail = test_input($personalemail);
	if (!filter_var($personalemail, FILTER_VALIDATE_EMAIL)) {
	    $personalemailErr = "Invalid email format";
	    $validData = 0;
        }
    }

    if (!empty($dob)) {
        $dob = test_input($dob);
    }


    if (!empty($gender)) {
        $gender = test_input($gender);
    }


    if (!empty($address)) {
        $address = test_input($address);
    }


    if (!empty($contactno)) {
        $contactno = test_input($contactno);
	if (!preg_match($phoneRegExp,$contactno)) {
	    $contactnoErr = "Invalid phone number - Expected format - 123-456-7890 or 1234567890";
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
   
    if (!empty($city)) {
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

    // Add more checks.
    
    if (!empty($jobtitle)) {
        $zip = test_input($zip);
    }

    if (!empty($startdate)) {
        $startdate = test_input($startdate);
    }

    if (!empty($enddate)) {
        $enddate = test_input($enddate);
    }

    if (!empty($worktype)) {
        $worktype = test_input($worktype);
    }

    if (!empty($payrate)) {
        $payrate = test_input($payrate);
    }

    if (!empty($visatype)) {
        $visatype = test_input($visatype);
    }

    if (!empty($visaexpirydate)) {
        $visaexpirydate = test_input($visaexpirydate);
    }

    if (!empty($workemail)) {
        $workemail = test_input($workemail);
	if (!filter_var($workemail, FILTER_VALIDATE_EMAIL)) {
	    $workemailErr = "Invalid email format";
	    $validData = 0;
        }
    }

    if (!empty($emergencycontactperson)) {
        $emergencycontactperson = test_input($emergencycontactperson);
    }

    if (!empty($emergencycontactno)) {
        $emergencycontactno = test_input($emergencycontactno);
  	if (!preg_match($phoneRegExp,$emergencycontactno)) {
	    $emergencycontactnoErr = "Invalid phone number - Expected format - 123-456-7890 or 1234567890";
	    $validData = 0;
        }
    }

    if (!empty($notes)) {
        $notes = test_input($notes);
    }

    // check if the user exists
    $currentUserId = getUserId($firstname,$lastname);

    if (($createAction && !empty($currentUserId)) || 
	($editAction && !empty($currentUserId) && 
		(intval($id) !== intval($currentUserId)))) {
         $firstnameErr = "Profile already exists";
         $validData = 0;
    }
 }  else {
    // case when this page is loaded by a GET from the previous page.
    $validData = 0;
 }
}

if ($validData == 0) {
    echo "<p> <span class=error> * required field </span> </p>";

    echo "<form method='post' action=" ; echo htmlspecialchars($_SERVER["PHP_SELF"]); echo " enctype='multipart/form-data' >" ;

    echo "<table align=center>";

    echo "<tr>";
    echo "<td align=center>";

    echo "<h1 align=center>";
    if (!empty($currentfirstname)) {
        echo "Profile of $currentfirstname";
        if (!empty($currentlastname)) {
            echo " $currentlastname";
	}
    } else { 
        echo "New Profile";
    }

    echo "</h1>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
		echo  "First Name :";
    echo "</td>";

    echo "<td>";
         	echo "<input type='text' name='firstname' value = '$firstname' maxlength=50> <span class='error'>* $firstnameErr </span>";
	
    echo "</td>";
    echo "</tr>";


    echo "<tr>";
    echo "<td>";
		echo  "Last  Name :";
    echo "</td>";

    echo "<td>";
	 	echo "<input type='text' name='lastname' value = '$lastname' maxlength=50> <span class='error'>* $lastnameErr </span> ";
        
    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";

    echo "<td>";
		echo  "Personal Email :";
    echo "</td>";

    echo "<td>";
	 	echo "<input type='text' name='personalemail' value = '$personalemail' maxlength=50> <span class='error'> $personalemailErr </span>";
	  
    echo "</td>";
    echo "</tr>";
   
    echo "<tr>";

    echo "<td>";
		echo  "Date of Birth :";
    echo "</td>";

    echo "<td>";
	  	echo "<input type='date' name='dob' value = '$dob'> <span class='error'> $dobErr </span>";
	  
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
		echo  "Gender :";
    echo "</td>";

    echo "<td>";
	echo "<input type='radio' name='gender' "; if (isset($gender) && ($gender == 'Female')) { echo " checked "; }; echo " value = 'Female'> Female";
        echo "<input type='radio' name='gender' "; if (isset($gender) && ($gender == 'Male')) { echo " checked "; }; echo " value = 'Male'> Male";
        echo "<span class='error'>$genderErr</span> ";   
	    	 	
    echo "</td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td>";
        echo "Job Title :";
    echo "</td>";
     
    echo "<td>";
        echo "<input type='text' name = 'jobtitle' value = '$jobtitle' maxlength = 75> <span class='error'> $jobtitleErr </span>";
    echo "</td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td>";
		echo  "Address :";
    echo "</td>";

    echo "<td>";
    	 	echo "<input type='text' name='address' value = '$address' maxlength=150> <span class='error'> $addressErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
		echo  "Mobile No :";
    echo "</td>";

    echo "<td>";
    	 	echo "<input type='text' name='contactno' value = '$contactno' maxlength=20> <span class='error'> $contactnoErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
		echo  "Country :";
    echo "</td>";

    echo "<td>";
    	 	echo "<input type='text' name='country' value = '$country' maxlength=50> <span class='error'> $countryErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";

    echo "<td>";
		echo  "State :";
    echo "</td>";

    echo "<td>";
    	 	echo "<input type='text' name='state' value = '$state' maxlength=50> <span class='error'> $stateErr </span> ";
    echo "</td>";

    echo "</tr>";

    echo "<tr>";

    echo "<td>";
		echo  "City :";
    echo "</td>";

    echo "<td>";
    	 	echo "<input type='text' name='city' value = '$city' maxlength=50> <span class='error'> $cityErr </span> ";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
    	       echo "Zip :";
    echo "</td>";

    echo "<td>";
	       echo "<input type='text' name='zip' value = '$zip' maxlength=15> <span class='error'> $zipErr </span>";
    echo "</td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td>";
    	       echo "Emergency Contact Person :";
    echo "</td>";

    echo "<td>";
	       echo "<input type='text' name='emergencycontactperson' value = '$emergencycontactperson' maxlength=50> <span class='error'> $emergencycontactpersonErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
    	       echo "Emergency Contact No :";
    echo "</td>";

    echo "<td>";
	       echo "<input type='text' name='emergencycontactno' value = '$emergencycontactno' maxlength=20> <span class='error'> $emergencycontactnoErr </span>";
    echo "</td>";
    echo "</tr>";

	echo "<p>";

    echo "<tr>";
    echo "<td>";
                echo  "Job Start Date:";
    echo "</td>";
    echo "<td>";
                echo "<input type='date' name='startdate' value = '$startdate'> <span class='error'> $startdateErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
                echo  "Job End Date:";
    echo "</td>";
    echo "<td>";
                echo "<input type='date' name='enddate' value = '$enddate'> <span class='error'> $enddateErr </span>";
    echo "</td>";
    echo "</tr>";


    echo "<tr>";
    echo "<td>";
		echo  "Work Type :";
    echo "</td>";

    echo "<td>";
        echo "<input type='radio' name='worktype' "; if (isset($worktype) && ($worktype == 'Full Time')) { echo " checked "; }; echo " value = 'Full Time'> Full Time";
        echo "<input type='radio' name='worktype' "; if (isset($worktype) && ($worktype == 'Part Time')) { echo " checked "; }; echo " value = 'Part Time'> Part Time";
        echo "<span class='error'>$worktypeErr</span> ";   	 	
    echo "</td>";
    echo "</tr>";  

    echo "<tr>";
    echo "<td>";
                echo  "Pay Rate :";
    echo "</td>";
    echo "<td>";
                echo "<input type='text' name='payrate' value = '$payrate' maxlength=20> <span class='error'> $worktypeErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
                echo  "Visa Type:";
    echo "</td>";
    echo "<td>";
                echo "<input type='text' name='visatype' value = '$visatype' maxlength=20> <span class='error'> $visatypeErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
                echo  "Visa Expiry Date:";
    echo "</td>";
    echo "<td>";
                echo "<input type='date' name='visaexpirydate' value = '$visaexpirydate'> <span class='error'> $visaexpirydateErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
                echo  "Work Email :";
    echo "</td>";
    echo "<td>";
                echo "<input type='text' name='workemail' value = '$workemail' maxlength=50> <span class='error'> $workemailErr </span>";
    echo "</td>";
    echo "</tr>";
   
    displayClients($clientid, "clientid", "Client","Client");
 
    displayClients($primaryvendorid,"primaryvendorid","Primary Vendor", "Vendor");

    displayClients($midvendorid,"midvendorid", "Mid Vendor", "Vendor");

    displayCurrentDocumentField("Offer Letter", $currentdocument1);
    displayCurrentDocumentField("Document 2", $currentdocument2);
    displayCurrentDocumentField("Document 3", $currentdocument3);

    if ($GLOBALS["status"] == 1) {
        displayUploadDocumentField("Offer Letter", "document1");
        displayUploadDocumentField("Document 2", "document2");
        displayUploadDocumentField("Document 3", "document3");
    }

    echo "<tr>";
    echo "<td>";
                echo  "Notes :";
    echo "</td>";
    echo "<td>";
                echo "<textarea name='notes' rows='10' cols='40' maxlength=300> " . $notes . "</textarea>  <span class='error'> $notesErr </span>";
    echo "</td>";
    echo "</tr>";

    echo "</table>";

    echo "<input type='hidden' name='id' value=$id>";
    echo "<input type='hidden' name='currentfirstname' value=$currentfirstname> <br>";
    echo "<input type='hidden' name='currentlastname' value=$currentlastname> <br>";

    echo "<input type='hidden' name='currentdocument1' value=$currentdocument1> <br>";
    echo "<input type='hidden' name='currentdocument2' value=$currentdocument2> <br>";
    echo "<input type='hidden' name='currentdocument3' value=$currentdocument3> <br>";

    echo "<table align=center>";
    echo "<tr>";

    if ($createAction || $addEmployeeActionPrevPage) {
        echo "<td>";  
        echo "<input type='submit' name='Create' value='Create'>";
        echo "</td>";
    } else if ($editAction || $editEmployeeActionPrevPage) {
        echo "<td>"; 
        echo "<input type='submit' name='Update' value='Update'>";
	echo "</td>";
	echo "<td>";
        $deleteLabel = "";
        if ($status == 1) {
           $deleteLabel = "Terminate";
        } else {
           $deleteLabel = "Purge";
        }
        echo "<input type='submit' name='Delete' value='$deleteLabel'>";
	echo "</td>";
    }
   
    displayCancelButton();   
    echo "</tr>";

    addHiddenStatusField($status);
    echo "</table>";
    echo "</form>";
} else {

// Establish the connection to the Mysql db.
    $conn = getDBConnection();

    $sql = "";
    $action = "";

       if (empty($dob)) {
            $dob = "NULL";
       } 

       if (empty($startdate)) {
            $startdate = "NULL"; 
       }

       if (empty($enddate)) {
            $enddate = "NULL"; 
       }

       if (empty($visaexpirydate)) {
            $visaexpirydate = "NULL"; 
       }

 
    if ($createAction) {         

      $sql = "INSERT INTO employee2(firstname,lastname,personalemail,dob,gender,address,contactno,country,state,city,zip,startdate ,enddate, worktype, jobtitle, payrate , visatype, visaexpirydate, workemail, emergencycontactperson , emergencycontactno , notes, clientid, primaryvendorid, midvendorid,document1, document2, document3) values('$firstname', '$lastname', '$personalemail', STR_TO_DATE('$dob','%Y-%m-%d'), '$gender','$address', '$contactno', '$country', '$state', '$city','$zip', STR_TO_DATE('$startdate','%Y-%m-%d'), STR_TO_DATE('$enddate','%Y-%m-%d'), '$worktype' , '$jobtitle', '$payrate', '$visatype', STR_TO_DATE('$visaexpirydate','%Y-%m-%d'), '$workemail', '$emergencycontactperson', '$emergencycontactno','$notes', $clientid, $primaryvendorid, $midvendorid, '$newdocument1', '$newdocument2', '$newdocument3')";

	
        $action = "added";
    } else if ($editAction) {
 
      if (empty($newdocument1)) {
          $newdocument1 = $currentdocument1;
      }

      if (empty($newdocument2)) {
          $newdocument2 = $currentdocument2;
      }

      if (empty($newdocument3)) {
          $newdocument3 = $currentdocument3;
      }


     
     $sql = "UPDATE employee2 set firstname='$firstname',lastname='$lastname',personalemail='$personalemail',dob= STR_TO_DATE('$dob','%Y-%m-%d'), gender='$gender',address='$address',contactno='$contactno',country='$country',state='$state',city='$city',zip='$zip', startdate = STR_TO_DATE('$startdate','%Y-%m-%d'), enddate = STR_TO_DATE('$enddate','%Y-%m-%d'), worktype = '$worktype', jobtitle = '$jobtitle', payrate = '$payrate', visatype = '$visatype', visaexpirydate = STR_TO_DATE('$visaexpirydate','%Y-%m-%d'), workemail = '$workemail', emergencycontactperson = '$emergencycontactperson', emergencycontactno = '$emergencycontactno', notes = '$notes', clientid = $clientid, primaryvendorid = $primaryvendorid, midvendorid = $midvendorid, document1 = '$newdocument1', document2 = '$newdocument2', document3 = '$newdocument3' where id=$id";

      $action = "updated";
  } else if ($deleteAction) {
      if ($status == 0) {
          $sql="delete from employee2 where id=$id";
          $action = "purged";
      } else {
          $sql="update employee2 set status = 0 where id = $id";
          $action="marked as inactive";
      }
    //  echo "delete sql $sql";
  }

    if (mysqli_query($conn, $sql)) {
        $message = "Profile was $action successfully <br>";
        if ($createAction || $editAction) {
            
            uploadDocument("document1", $currentdocument1);
            uploadDocument("document2", $currentdocument2);
            uploadDocument("document3", $currentdocument3);
        } else if ($deleteAction) {
 		// delete the resume. 
	    //deleteResume();
            if ($status == 0) {
                deleteDocuments($currentdocument1, $currentdocument2, $currentdocument3); 
                //deleteAllDocumentsInDir($dirName);
            } 
        }

        echo "<form method='post' action=" ; echo htmlspecialchars($_SERVER["PHP_SELF"]); echo ">" ;
	echo "<table align=center>";
	echo "<tr>";
	echo "<td>";
	echo "<b>$message</b>";
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td>";
        $backbuttonLabel = "Back to Current Employees Page";
        if (!$status) {
            $backbuttonLabel = "Back to Past Employees Page";  
        }
	echo "<input type='submit' name='candidateProfilesHome' value='$backbuttonLabel'>";
	echo "</td>";
	echo "</tr>";
  
        addHiddenStatusField($status);
	echo "</form>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}

?>

</body>
</html>


