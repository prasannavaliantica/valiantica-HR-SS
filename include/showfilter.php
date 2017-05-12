

<?php
function javascript() {
    echo "<script type = 'text/javascript'>";
    echo "var type = document.getElementById('type');";
    echo "var filterdivtext = document.getElementById('filterdivtext');";
    echo "var filterdivdate = document.getElementById('filterdivdate');";
    echo "var filterForm = document.forms['filterForm'];";

    echo "      var filtersearch = document.getElementById('filtersearch');";
    echo "      var filterclear = document.getElementById('filterclear');";

    echo "typevalue = type.value;";
    echo "if (typevalue != -1) {";
  // echo "    if (value == 'firstname' || value == 'lastname' || value == 'clientid') {";
    echo "if (!typevalue.endsWith('date')) {";
    echo "        filterdivtext.style.display = 'block';";
    echo "        filterdivdate.style.display = 'none';";
    echo "    } else {";
    echo "       filterdivtext.style.display = 'none';";
    echo "       filterdivdate.style.display = 'block';";
    echo "    } "; 
    echo " } ";  
  
    echo "type.onchange = function() {";
    echo "typevalue = type.value;";
   //echo "if (value == 'firstname' || value == 'lastname' || value == 'clientid') {";
    echo "textval.value = dateval.value = '';";                      
    echo "if (typevalue != '-1') { ";
    echo "    if (!typevalue.endsWith('date')) {";
    echo "        filterdivtext.style.display = 'block';";
    echo "        filterdivdate.style.display = 'none';";
    echo "    } else {";
    echo "       filterdivtext.style.display = 'none';";
    echo "       filterdivdate.style.display = 'block';";
    echo "    } ";
    echo "} else { ";
    echo "        filterdivtext.style.display = 'none';";
    echo "        filterdivdate.style.display = 'none';";   
    echo "}";  
    echo "}; ";

    echo "filterclear.onclick = function() {";
    //echo "alert('clear clicked');";
    echo "type.value = cond.value = '-1';";
    echo "textval.value = dateval.value = null;"; 
    echo "};";

    echo "filtersearch.onclick = function() {";
    echo "value = '';";
    echo "filtervalue = type.value;";
    echo "filtercondition = cond.value;";

    echo "if (filtervalue == '-1') {";
    echo "alert('Choose a Filter');";
    echo "event.preventDefault();";
    echo "return;";
    echo " }";

    echo "if (filtercondition == '-1') {";
    echo "alert('Choose a filter condition');";
    echo "event.preventDefault();";
    echo "return;";
    echo " }";

 //   echo "if (filtervalue == 'firstname' || filtervalue == 'lastname' || filtervalue == 'clientid' || filtervalue == 'visatype') {";
    echo "if (filtervalue.endsWith('date')) {";
    echo "value = dateval.value;";
    echo "} else {";
    echo "value = textval.value;";
    echo "}";

    //echo "alert('value ' + value);"; 

    echo "if (value == '' || value == null) {";
    echo "alert('Filter value cannot be empty');";  
    echo "event.preventDefault();";
    echo "} else {";
          
    echo "document.forms['filterForm'].submit();";
    echo "}";
    echo "}";

    
    echo "</script>";
}

function displaySelect($id, $array, $matchvalue) {
    echo "<select id = '$id' name = '$id'>";
     foreach ($array as $key => $value) {
	 echo "<option value = '$value'";
         if ($matchvalue == $value) {
             echo " selected";
         }
         
         echo ">";
         echo $key;
         echo "</option>";
     }
     echo "</select>";  	
}

function displayFilterTypes($name) {
    $filters = array(
        "Choose Filter" => "-1",
        "First Name" => "firstname",
        "Last Name" => "lastname",
        "Client Name"=> 'clientid',
        "Visa Type" => 'visatype',  
        "Job Start Date" => 'startdate',
        "Job End Date" => 'enddate'
     );
 
    displaySelect("type", $filters, $name);
}

function displayFilterConditions($cond) {

    $filters = array(
        "Choose Condition" => "-1",
        "Equals" => "=",
        "Not Equals" => "!=",
        "Greater than or Equal to"=> '>=',
        "Less than or Equal to" => '<='
     );

    displaySelect("cond", $filters, $cond);
}

function displayFilters() {
   // print_r($_REQUEST);

    $fname = $cond = "-1";
    $value = "";

    if (isset($_REQUEST["type"])) {
        $fname = $_REQUEST["type"]; 
    }

    if (isset($_REQUEST["cond"])) {
        $cond = $_REQUEST["cond"]; 
    }

    if (isset($_REQUEST["textval"])) {
        if (!empty($_REQUEST["textval"])) {
            $value = $_REQUEST["textval"]; 
        } 
    } 

    if (isset($_REQUEST["dateval"])) {
        if (!empty($_REQUEST["dateval"])) {
            $value = $_REQUEST["dateval"]; 
        } 
    } 

    /* 
     * This is to handle the case of getting redirected upon clicking 
     * the 'Back to Search Results' button.
     */
    if (isset($_REQUEST["value"])) {
        if (!empty($_REQUEST["value"])) {
            $value = $_REQUEST["value"]; 
        } 
    } 

 //   echo "val = $value";

    echo "<form name = 'filterForm' id = 'filterForm' method='post' action=listEmployees.php>";

    echo "<table align=center>";  
    echo "<tr>";
 
    echo "<td>";     
    displayFilterTypes($fname); 
    echo "</td>";

    echo "<td>";
    displayFilterConditions($cond);  
    echo "</td>";

    echo "<td>";
    echo "<div id = 'filterdivtext' style = 'display: none;'>";  
        echo "<input type = 'text' name = 'textval' id = 'textval' maxLength = 50 value = $value>";
    echo "</div>";
    echo "</td>";

    echo "<td>";

    echo "<div id = 'filterdivdate' style = 'display: none;'>";  
        echo "<input type = 'date' name = 'dateval' id = 'dateval' value = $value >";
    echo "</div>";

    echo "</td>";

    echo "<td>";
       echo "<input type = 'submit' name = 'filtersearch' id = 'filtersearch' value = 'Search'>";
    echo "</td>";

    echo "<td>";
       echo "<input type = 'submit' name = 'filterclear' id = 'filterclear' value = 'Clear'> ";
    echo "</td>";


    $status = $_REQUEST["status"];
    echo "<td>";
       echo "<input type = 'hidden' name = 'status' value = $status> ";
    echo "</td>";

    echo "</tr>";

    echo "</form>";
    echo "</table>";
    echo "<br><br>";
}

function filter() {
   displayFilters(); 
   javascript();    
}

filter();

?>


