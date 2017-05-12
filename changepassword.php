
<?php
include 'include/redirect.php';
include 'include/globals.php';
session_start();
?>

<!DOCTYPE html>
<html>
<body>

<?php

function displayLoginScreen($oldpasswordErr, $newpasswordErr, $validationErr) {
    echo "<table align=center>";
    echo "<form method=post action=". htmlspecialchars($_SERVER["PHP_SELF"]). ">";
    echo "<tr>";
    echo "<td>";
    echo "Username";
    echo "</td>";

    echo "<td align=center>";
    echo "<b>"; echo $_SESSION["username"]; echo "</b>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
    echo "Old Password";
    echo "</td>";

    echo "<td>";
    echo "<input type='password' name='oldpassword' maxlength='50'><span class='error'> *$oldpasswordErr</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>";
    echo "New Password";
    echo "</td>";

    echo "<td>";
    echo "<input type='password' name='newpassword' maxlength='50'><span class='error'> *$newpasswordErr</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=right>";
    echo "<input type='submit' name='ChangePassword' value='Change'>";
    echo "</td>";

    echo "<td align=left>";
    echo "<input type='submit' name='Cancel' value='Cancel'>";
    echo "</td>";
    echo "</tr>";

    if (!empty($validationErr)) {
	echo "<tr>";
	echo "<td align=center>";
	echo "$validationErr"; 
	echo "</td>";
	echo "</tr>";
    }
    echo "</form>";
}

function displayStatusPage($message) {
    echo "<form method=post action=". htmlspecialchars($_SERVER["PHP_SELF"]). ">";
    echo "<table align=center>";
    echo "<tr>";
    echo "<td align=center>";
    echo "<b>$message</b>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=center>";
    echo "<input type='submit' name='Login' value='Login'>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
}


function isValid($loginname,$oldpasswd,$newpasswd) {
    $validationErr = "";

    $conn=getDBConnection();

    $sql = "select * from LoginProfile where username='$loginname'";
    //$sql = "select * from LoginProfile where username='$loginname' && password='$oldpasswd'";
    $result = mysqli_query($conn, $sql);

    /*
    if (mysqli_num_rows($result) > 0) {
        $sql = "update LoginProfile set password='$newpasswd' where username='$loginname' and password='$oldpasswd'";

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            $validationErr = "Couldn't change the password - ". mysqli_error($conn); 
        }
    } else {
	$validationErr = "Incorrect old password";
    }*/


    if (mysqli_num_rows($result) > 0) {
	while ($row=mysqli_fetch_assoc($result)) {
	    $oldcryptpasswd = $row["password"];
	   // $status = password_verify($oldpasswd,$oldhash);
	   
	    if (crypt($oldpasswd,$oldcryptpasswd)==$oldcryptpasswd) { 
	        //$newhash = password_hash($newpasswd, PASSWORD_DEFAULT);
		$newcryptpasswd = crypt($newpasswd);
                $sql = "update LoginProfile set password='$newcryptpasswd' where username='$loginname' and password='$oldcryptpasswd'";

                $result = mysqli_query($conn, $sql);
                if (!$result) {
	            $validationErr = "Couldn't change the password - ". mysqli_error($conn); 
	        }
	    } else {
		$validationErr = "Incorrect old password";
	    }
	}
    }


    mysqli_close($conn);
    return $validationErr; 
}


function main() {
    $oldpasswordErr = $newpasswordErr = $validationErr = "";
    $promptForCreds = False;

    $login = $_SESSION["username"];
    $oldpasswd = $newpasswd = ""; 
    $oldpasswordErr = $newpasswordErr = $validationErr = "";
    $promptForCreds = True;

    echo "<h1 align=center>";
    echo "Change Password";
    echo "</h1>";

    if ($_SERVER["REQUEST_METHOD"]=="POST") {
        $oldpasswd = $_REQUEST["oldpassword"];
        $newpasswd = $_REQUEST["newpassword"];

	
        if (isset($_REQUEST["Cancel"])) {
	    header("Location: home.php");
        } else if (isset($_REQUEST["Login"])) {
	    header("Location: logout.php");
        } else {

            if (empty($oldpasswd)){
                $oldpasswordErr = "Current password can't be empty.";
            } 

            if (empty($newpasswd)){
                $newpasswordErr = "New password can't be empty.";
            } else {
    	        $passwordRegExp = "/^[a-z_\-#0-9]*$/i";

                if ($oldpasswd == $newpasswd) {
                    $newpasswordErr = "Passwords are the same";
                } else  if (!preg_match($passwordRegExp,$newpasswd)) {
                    $newpasswordErr = "Only alpha-numeric characters, #, - and underscore are allowed";
   	        } 
	    }

            if (empty($oldpasswordErr) && empty($newpasswordErr)) {
	        $validationErr = isValid($login,$oldpasswd, $newpasswd);
	        if (empty($validationErr)) {
		    $promptForCreds = False;
	        }
	    }

            if ($promptForCreds) {
                displayLoginScreen($oldpasswordErr, $newpasswordErr, $validationErr);
            } else {
	        $message = "Password was changed successfully";
	        displayStatusPage($message);
            }
       }
    } else {
       displayLoginScreen($oldpasswordErr, $newpasswordErr, $validationErr);
    }
} 

main();

?>

</body>
</html>

