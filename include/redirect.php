
<?php

function redirect() {
    if (!isset($_SERVER["HTTP_REFERER"])) {
        header("Location: index.php");
    }
}

redirect();

?>

