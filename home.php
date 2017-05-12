<html>
<body>
<?php

include 'include/header.php';
include 'include/globals.php';
//include 'redirect.php';

function displayHomePage() {

	echo "<br><br>";
	echo "<table align=center>";
	echo "<tr>";
	echo "<td align=center>";

	echo "<h1 align=center>";
	echo "Valiantica Inc HR Page"; 
	echo "</h1>";

	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=center>";
	echo "<a href=". $GLOBALS["employeesHomePage"]."?status=1" . ">Manage Current Employee Profiles</a><br>"; 
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=center>";
	echo "<a href=". $GLOBALS["clientsHomePage"] . "?clienttype=Client>Manage Clients</a><br>"; 
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=center>";
	echo "<a href=listClients.php?clienttype=Vendor>Manage Vendors</a><br>"; 
	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=center>";
	echo "<a href=". $GLOBALS["employeesHomePage"]."?status=0" . ">Manage Past Employment</a><br>"; 
	echo "</td>";
	echo "</tr>";

	echo "</table>";

}

displayHomePage();
?>

</body>
</html>

