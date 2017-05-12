
<?php
session_start();
include "include/globals.php";
?>

<!DOCTYPE html>
<html>
<body>

<?php

function displayLoginScreen($usernameErr,$passwordErr, $validationErr) {
    echo "<table align=center>";
    echo "<form method=post action=". htmlspecialchars($_SERVER["PHP_SELF"]). ">";
    echo "<tr>";
    echo "<td>";
    echo "Username";
    echo "</td>";

    echo "<td>";
    echo "<input type='text' name='username'> <span class='error'>*$usernameErr</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
    echo "Password";
    echo "</td>";

    echo "<td>";
    echo "<input type='password' name='password'> <span class='error'>*$passwordErr</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=center>";
    echo "<input type='submit' name='Login' value='Login'>";
    echo "</td>";
    echo "</tr>";

    if (!empty($validationErr)) {
	echo "<tr>";
	echo "<td align=center>";
	echo "$validationErr"; 
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>";
    echo "</form>";
}

function isValid($loginname,$loginpasswd) {
  $exists = False;
   $conn = getDBConnection();

    $sql = "select * from LoginProfile where username='$loginname'";
    //$sql = "select * from LoginProfile where username='$loginname' && password='$loginpasswd'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
 	
	while ($row=mysqli_fetch_assoc($result)) {
            $cryptpasswd = $row["password"];
	    if ($cryptpasswd == crypt($loginpasswd,$cryptpasswd)) { 	
   	        $exists = True;
	    }
	}
    }
    mysqli_close($conn);
    return $exists;
}


function main() {
    $usernameErr = $passwordErr = $validationErr = "";
    $promptForCreds = True;

    $login = "";
    $passwd = "";

    echo "<h1 align=center>";
    echo "Welcome to Valiantica HR page";
    echo "</h1>";

    if ($_SERVER["REQUEST_METHOD"]=="POST") {
        $login = $_REQUEST["username"];
        $passwd = $_REQUEST["password"];

        if (empty($login)){
            $usernameErr = "Username is required.";
        }
        if (empty($passwd)){
            $passwordErr = "password is required.";
        }

        if (empty($usernameErr) && empty($passwordErr)) {
	    if (isValid($login,$passwd)) {
	        $promptForCreds = False;
            } else {
	        $validationErr = "Incorrect Credentials";
	    }
	}
    } else if ($_SERVER["REQUEST_METHOD"] == "GET") {
	if (isset($_SESSION["username"])) {
	   header("Location: home.php");
	}
    }

    if ($promptForCreds) {
        displayLoginScreen($usernameErr, $passwordErr, $validationErr);
    } else {
	$_SESSION["username"] = $login;
        header("Location: home.php");
    }
} 

main();

?>

</body>
</html>

