
<?php

$maximumResumeSize = 5 * 1024 * 1024; 
$defaultPageSize = 15;


$servername="localhost";
$username="root";
$password="admin";
$dbname="organization";
$documentsDir="/var/HR/documents/";
$employeesHomePage = "listEmployees.php";
$clientsHomePage = "listClients.php";

function getDBConnection() {
    $servername=$GLOBALS["servername"];
    $username=$GLOBALS["username"];
    $password=$GLOBALS["password"];
    $dbname=$GLOBALS["dbname"];

    $conn=mysqli_connect($servername,$username,$password,$dbname);

    if (!$conn) {
        die("Connection failed ". mysqli_connect_error() . " - Error No " . mysql_errno());
    } else {
//        echo "Connection established successfully <br> ";
    }

    return $conn;    
}

function test_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

function displayHeading($message) {
    echo "<br><br>";
    echo "<table align=center>";
    echo "<tr>";
    echo "<td>";
    echo "<h1 align = center>$message</h1>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
}

function addSearchConditionFields() {
    $type = $_REQUEST["type"];
    $cond = $_REQUEST["cond"];
    $val = $_REQUEST["value"];

    echo "<td>";
    echo "<input type = 'hidden' name = 'type' value = $type>";
    echo "</td>";

    echo "<td>";
    echo "<input type = 'hidden' name = 'cond' value = $cond>";
    echo "</td>";

    echo "<td>";
    echo "<input type = 'hidden' name = 'value' value = $val>";
    echo "</td>";
}

function displayCancelButton() {
    $search = 0;
    if (isset($_REQUEST["search"])) {
        $search = intval($_REQUEST["search"]);
    } 

    if ($search == 0) {
        echo "<td>";
        echo "<input type='submit' name='Cancel' value='Back'>";
        echo "</td>"; 
    } else {
        echo "<td>";
        echo "<input type='submit' name='Cancel' value='Back To Search Results'>";
        echo "</td>";
        addSearchConditionFields();
    }
}

function displayDocumentHyperlink($document,$add) {
    if (!$add) {
       echo "<td align = center>" ; 
    } else {
       echo "<td>";
    }
    if (!empty($document)) {
        echo "<a href='downloadDocument.php?document=$document'>";echo $document; echo "</a>";
    } else {
	echo "None";
    }
    echo "</td>";
}

function getCandidateProfile($id) {
    $conn = getDBConnection();
    $row = NULL;
    $sql = "select * from employee2 where id = $id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    }
    mysqli_close($conn);
    return $row;
}


?>

