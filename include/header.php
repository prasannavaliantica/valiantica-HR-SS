<?php
session_start();
?>

<html>
<body>

<?php

function displayHeader() {
    $username = $_SESSION["username"];
    echo "<table align=right>";
    echo "<tr>";

    echo "<td>";
    echo "Logged in as $username";
    echo "</td>";

    echo "<td>";
    echo "<a href=changepassword.php> Change Password"; echo "</a>";
    echo "</td>";

    echo "<td>";
    echo "<a href = 'logout.php'> Logout </a>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
}

displayHeader();

?>

</body>
</html>
