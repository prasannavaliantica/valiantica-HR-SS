<html>
<body>
<?php

include 'include/redirect.php';
include 'include/header.php';
include 'include/globals.php';

function displayRow($row) {
        echo "<tr>";
        echo "<td align = center>" ; 
	$id = $row["id"];	
	echo "<a href='manageClient.php?id=$id'>"; echo $row["clientname"]; echo "</a>"; 
	 echo "</td>";
        echo "<td align = center>" ; echo $row["email"]; echo "</td>";
        echo "<td align = center>" ; echo $row["mobileno"]; echo "</td>";
        echo "<td align = center>" ; echo $row["country"]; echo "</td>";
        echo "<td align = center>" ; echo $row["state"]; echo "</td>";
        echo "<td align = center>" ; echo $row["city"]; echo "</td>";
        echo "<td align = center>" ; echo $row["zip"]; echo "</td>";
        echo "</tr>";
}

function display($result) {

   echo "<table border = 1 align = center>";
   echo "<tr>";
   echo "<th>Client Name</th> ";
   echo "<th>Email </th>";
   echo "<th>Mobile</th>";
   echo "<th>Country</th>";
   echo "<th>State</th>";
   echo "<th>City</th>";
   echo "<th>Zip</th>";
   echo "</tr>";
    while($row = mysqli_fetch_assoc($result)) {
        displayRow($row);
    }
    echo "</table>";
}

function getClientInfo() {

    $type = $_REQUEST["clienttype"];
    $conn = getDBConnection();

    $sql = "select * from client where type = '$type'";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
	display($result);
    } else {
        echo "No $type profiles available as yet";
    }
    mysqli_close($conn);
}

function displayForm() {

    $type = $_REQUEST["clienttype"];

    echo "<br><br>";
    echo "<form action='manageClient.php' method='post'>";
    echo "<table align=center>";
    echo "<tr>";
    echo "<td>";
    echo "<input type='submit' name='addClient' value='Add $type'>";
    echo "</td>";
    echo "<td>";
    echo "<input type='submit' name='home' value='Back'>";
    echo "</td>";

    echo "<td>";
    echo "<input type='hidden' name='clienttype' value='$type'>";
    echo "</td>";

    echo "</tr>";
    echo "</table>";
    echo "</form>";
}

function showHeading() {
    $type = $_REQUEST["clienttype"];
    $typestr = $type. "s";
    displayHeading($typestr);
}


function main() {
    //echo print_r($_REQUEST);
    showHeading();   
    getClientInfo();
    displayForm();
}

main();
?>

</body>
</html>

