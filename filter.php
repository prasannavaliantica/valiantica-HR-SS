

<?php
function javascript() {
    echo "<script type = 'text/javascript'>";
    echo "var filterFieldName = document.getElementById('filterFieldName');";
    echo "var filterdivtext = document.getElementById('filterdivtext');";
    echo "var filterdivdate = document.getElementById('filterdivdate');";

    echo "      var filtersearch = document.getElementById('search');";
    echo "      var filterclear = document.getElementById('clear');";

    echo "filterFieldName.onchange = function() {";
    echo "value = filterFieldName.value;";
    echo "if (value == 'firstname' || value == 'lastname' || value == '-1') {";
    echo "        filterdivtext.style.display = 'block';";
    echo "        filterdivdate.style.display = 'none';";
    echo "    } else {";
    echo "       filterdivtext.style.display = 'none';";
    echo "       filterdivdate.style.display = 'block';";
    echo "    } ";  
    echo "}; ";

    echo "filtersearch.onclick = function() {";
    echo "value = '';";
    echo "filtervalue = filterFieldName.value;";
    echo "if (filtervalue == 'firstname' || filtervalue == 'lastname' || filtervalue == '-1') {";
    echo "value = filterTextField.value;";
    echo "} else {";
    echo "value = filterDateField.value;";
    echo "}";

     echo "alert ('search clicked');"; echo "alert(value);"; 
    echo "if (value == '' || value == null) {";
    echo "alert('Filter value cant be empty');";  
    echo "event.preventDefault();";
    echo "} else {";
          
    echo "document.forms['filterForm'].submit();";
    echo "}";
    echo "}";

    echo "</script>";
}


function displayFilters() {
    echo "<form name = 'filterForm' id = 'filterForm' method='post' action=" ; echo htmlspecialchars($_SERVER["PHP_SELF"]); echo " enctype='multipart/form-data' >" ;

    echo "<table align=center>";  
    echo "<tr>";
 
    echo "<td>";   
        echo "<select id = 'filterFieldName' name = 'filterFieldName'>";
        echo "<option value = '-1'> Choose Filter </option>";
        echo "<option value = 'firstname'> First Name </option>";
        echo "<option value = 'lastname'> Last Name </option>";
        echo "<option value = 'Client'> Client Name </option>";
        echo "<option value = 'startdate'> Start Date </option>";
        echo "<option value = 'enddate'> End Date </option>";
        echo "</option>";
        echo "</select>"; 
    echo "</td>";

    echo "<td>";
        echo "<select id  = 'filterCondition' name = 'filterCondition'>";
        echo "<option value = '-1'> Choose condition </option>";
        echo "<option value = '='> Equals </option>";
        echo "<option value = '!='> Not Equals </option>";
        echo "<option value = '>='> Greater than or Equal to </option>";
        echo "<option value = '<='> Less than or Equal to </option>";   
        echo "</option>";
        echo "</select>";  
    echo "</td>";

    echo "<td>";
    echo "<div id = 'filterdivtext' style = 'display: none;'>";  
        echo "<input type = 'text' name = 'filterTextField' id = 'filterTextField' maxLength = 50>";
    echo "</div>";
    echo "</td>";

    echo "<td>";

    echo "<div id = 'filterdivdate' style = 'display: none;'>";  
        echo "<input type = 'date' name = 'filterDateField' id = 'filterDateField'>";
    echo "</div>";

    echo "</td>";

    echo "<td>";
       echo "<input type = 'submit' name = 'search' id = 'search' value = 'Search'>";
    echo "</td>";

    echo "<td>";
       echo "<input type = 'submit' name = 'clear' id = 'clear' value = 'Clear'> ";
    echo "</td>";

    echo "</tr>";

    echo "</form>";
}

function main() {
    if ($_SERVER["REQUEST_METHOD"] == 'GET') {
        displayFilters(); 
        javascript();  
       //displayFilters();
    } else {
        echo "Filter Name " . $_REQUEST["filterFieldName"] . "<br>";
        echo "Filter Cond " . $_REQUEST["filterCondition"] . "<br>";
        echo "Filter T Value " . $_REQUEST["filterTextField"] . "<br>";
        echo "Filter D Value " . $_REQUEST["filterDateField"] . "<br>";
    }   
}

main();

?>


