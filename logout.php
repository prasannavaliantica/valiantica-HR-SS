
<?php
session_start();

function logout() {
    if (isset($_SESSION["username"])) {
        $_SESSION["username"] = "";
    }
 
    if (isset($_SESSION["status"])) {
        $_SESSION["status"] = -1;
    }
	
    session_destroy();
    header("Location: index.php");
}

logout();

?>

