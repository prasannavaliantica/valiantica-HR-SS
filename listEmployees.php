<html>
<body>
<?php

include 'include/redirect.php';
include 'include/header.php';
include 'include/globals.php';

$status = $_REQUEST["status"];

$heading = "Profiles of Current Employees";
if (!$status) {
    $heading = "Profiles of Past Employees";    
}

displayHeading($heading);

include 'include/showfilter.php';

function getSqlStatementFilters() {
    $filterFieldName = "";
    $filterTextField = "";
    $filterDateField = "";
    $filterCondition = "";
    $filterText = "";
    $filterValue = "";
    $sql = "";
    $status = $_REQUEST["status"];
    $statusfilter = " e.status = $status";
    $join = " and ";

    if (isset($_REQUEST["type"]) and $_REQUEST["type"] != '-1') {
        $filterFieldName = $_REQUEST["type"];
        $filterCondition  = $_REQUEST["cond"];
 
            if ($filterFieldName == 'firstname' || $filterFieldName == 'lastname' || $filterFieldName == 'clientid' || 
                $filterFieldName == 'visatype') {
                if (isset($_REQUEST["textval"])) {  
                    $filterValue = $_REQUEST["textval"]; 
                }                 
            } else {
                if (isset($_REQUEST["dateval"])) {
                    $filterValue = $_REQUEST["dateval"];
                }
            }

            if (isset($_REQUEST["value"])) {
                $filterValue = $_REQUEST["value"]; 
            }
        
                
            if ($filterFieldName == 'clientid') {
                $filterText = "c.clientname " . $filterCondition . "'$filterValue' and e.clientid = c.id";
                $sql =  " , client c where ". $filterText; 
            } else {
                $filterText = $filterFieldName . " " . $filterCondition . " " . "'$filterValue'";
                $sql =  " where ". $filterText;
                if ($filterFieldName == 'startdate' || ($filterFieldName == 'enddate')) {
                    $sql = $sql . " or $filterFieldName is NULL ";
                }
            }    
    }
    if (empty($sql)) {
        
        /* case when the page is loaded without filters. Just add the status
         * with the where clause. Else add the status field with the and clause.
         */
        $join = " where ";
    }
  
    $sql = $sql . $join . $statusfilter;

    return $sql; 
}

function getClientName($conn,$clientid) {

    $clientname="";
    $clientsql = "select clientname from client where id=$clientid";
    $result = mysqli_query($conn, $clientsql);

    if (mysqli_num_rows($result) > 0) {
	while ($row=mysqli_fetch_assoc($result)) {
	    $clientname=$row["clientname"];
        }
    }
    return $clientname;
}

function getProfilesCount($sqlFilters) {
    $conn = getDBConnection();
    $count = 0;
    $sql = "select count(*) as count from employee2 e " . $sqlFilters;
   //echo "profile count $sql";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
	while ($row=mysqli_fetch_assoc($result)) {
       	    $count = $row["count"];
	}
    }
    mysqli_close($conn);
    return $count;
}

function getProfilesPaged($sqlFilters, $offset, $pageSize,$totalCount) {
    $conn = getDBConnection();
    $sql = " select * from employee2 e " . $sqlFilters . " order by e.id limit $offset, $pageSize";
    
    $result = mysqli_query($conn, $sql);
    if (($count = mysqli_num_rows($result)) > 0) {
        display($conn, $result, $offset, $pageSize, $totalCount);
    } else {
        echo "No profiles available as yet";
    }
    mysqli_close($conn);
}

function displayDocument($document) {
    echo "<td align = center>" ; 
    if (!empty($document)) {
        echo "<a href='downloadDocument.php?document=$document'>";echo $document; echo "</a>";
    } else {
	echo "Not available";
    }
    echo "</td>";
}


function displayRow($conn,$row) {
        echo "<tr>";
        echo "<td align = center>" ; 
	$id = $row["id"];
        $search = 0; 
        $type = $cond = $value = $searchFields = "";
        $status = $_REQUEST["status"];

        if (isset($_REQUEST["filtersearch"])) {
             $search = 1; 
             $type = $_REQUEST["type"];
             $cond = $_REQUEST["cond"];
             $valuetext = $_REQUEST["textval"];
             $valuedate = $_REQUEST["dateval"];
             
	     //echo "type $type $cond $valuetext $valuedate";
             if ($type == 'startdate' || $type == 'enddate') {
                 $value = $valuedate;
             } else {
                 $value = $valuetext;
             }   
	     
        }

        if ($search==1) { 
            $searchFields = "search=1&type=$type&cond=$cond&value=$value";
	    echo "<a href='addemployee.php?status=$status&id=$id&" . $searchFields . "'>"; echo $row["firstname"]; echo "</a>"; 
        } else {
             echo "<a href='addemployee.php?status=$status&id=$id'>"; echo $row["firstname"]; echo "</a>";
        }
	 echo "</td>";
        echo "<td align = center>" ; echo $row["lastname"]; echo "</td>";
        echo "<td align = center>" ; echo $row["contactno"]; echo "</td>";
        echo "<td align  = center>"; echo $row["personalemail"]; echo "</td>";

	echo "<td align=center>";

	if (intval($row["clientid"]) > 0) {
	    $clientid=intval($row["clientid"]);
	    $clientname=getClientName($conn,$clientid);
            if ($search == 1) {
	        echo "<a href='manageClient.php?status=$status&id=$clientid&edit=False&".$searchFields. "'>" . $clientname;
            } else {
                echo "<a href='manageClient.php?status=$status&id=$clientid&edit=False'> $clientname";  
            } 
	    echo "</a>";	    
	} else {
	    echo "None";
	}
	echo "</td>";

        echo "<td align  = center>"; echo $row["jobtitle"]; echo "</td>";
 
        echo "<td align=center>";
        if (!empty($row["startdate"])) {
            echo $row["startdate"]; 
        } else {
            echo "None";  
        }  
        echo "</td>";
       
        echo "<td align  = center>"; echo $row["visatype"]; echo "</td>";

        $add = False;
        displayDocumentHyperlink($row["document1"],$add);
        //displayDocument($row["document2"]);
        //displayDocument($row["document3"]);
  
        echo "</tr>";
}

function displayPaginationFields($offset, $pageSize, $totalcount) {
 
   // display the 'Previous' link for all but the first page. 

   $type = $cond = $textval = $dateval = $value = $searchFields = "";
   $status = $_REQUEST["status"];
 
   if (isset($_REQUEST["type"])) {
       $type =  $_REQUEST["type"];
       if ($type != "-1") {
           $cond =  $_REQUEST["cond"];
           $textval =  $_REQUEST["textval"];
           $dateval =  $_REQUEST["dateval"];
           $value = $_REQUEST["value"];

           if (empty($value)) { 
               if (!empty($textval)) {
                   $value = $textval;
               } else if (!empty($dateval)) {
                   $value = $dateval;
               }
           }
        
           $searchFields = "&search=1&type=$type&cond=$cond&value=$value";
       }
   }
   

   echo "<table  align = center>";
    echo "<tr>";
    echo "<td colspan=2>";
    $startrange = $offset + 1;
    $endrange = $startrange + $pageSize - 1;
    $endrange = min($endrange,$totalcount);
 
    echo "<b>Displaying $startrange - $endrange of $totalcount Profiles</b>";
    //echo "<input type = 'label' value = 'Displaying $startrange - $endrange of $totalcount Profiles>";
    echo "</td>";
    echo "</tr>";
    
   echo "<tr>";

     if ($offset > 0) {
         $prevOffset = $offset - $pageSize;
         $url = htmlspecialchars($_SERVER["PHP_SELF"]) . "?status=$status&offset=$prevOffset&pageSize=$pageSize&totalcount=$totalcount";
         if (!empty($searchFields)) {
             $url = $url . "$searchFields";
         }
	 echo "<td align=center>";
         echo "<a href = " . $url . ">Previous</a>";
	 echo "</td>";
     }

   // display the 'Next' link for all but the last page. 

    if ($offset + $pageSize <= $totalcount  && (($totalcount - ($offset+$pageSize)) != 0)) {
        $currOffset = $offset + $pageSize;
        $url = htmlspecialchars($_SERVER["PHP_SELF"]) . "?status=$status&offset=$currOffset&pageSize=$pageSize&totalcount=$totalcount";
        if (!empty($searchFields)) {
            $url = $url . "$searchFields";
        }
	echo "<td align=center>";
        echo "<a href = " . $url . ">Next</a>";
	echo "</td>";
    }

    echo "</tr>";
    echo "</table>";
}

function display($conn, $result, $offset, $pageSize, $totalCount) {

   echo "<table border = 1 align = center>";
   echo "<tr>";
   echo "<th>First Name</th> ";
   echo "<th>Last Name </th>";
   echo "<th>Contact</th>";
   echo "<th>Email</th>";

   echo "<th>Client</th>";
   echo "<th>Job Title</th>";

   echo "<th>Start Date</th>";
   echo "<th>Visa Type </th>";

   echo "<th>Offer Letter</th>";

   echo "</tr>";
    while($row = mysqli_fetch_assoc($result)) {
        displayRow($conn,$row);
    }
    echo "</table>";
    echo "<br><br>";

   // Now display the fields for pagination.
   displayPaginationFields($offset, $pageSize, $totalCount);

}

function displayAddProfileForm() {

    $status = $_REQUEST["status"];

    echo "<br><br>";
    echo "<form name = 'addProfile' action='addemployee.php' method='post'>";
    echo "<table align=center>";
    echo "<tr>";

    if ($status) {
        echo "<td>";
        echo "<input type='submit' name='addEmployee' value='Add Profile'>";
        echo "</td>";
    }

    echo "<td>";
    echo "<input type='submit' name='home' value='Back'>";
    echo "</td>";

    echo "<td>";
    echo "<input type='hidden' name='status' value=$status>";
    echo "</td>";

    echo "</tr>";
    echo "</form>";
}


function main() {

    $totalcount = $offset = 0;
    $pageSize = $GLOBALS["defaultPageSize"];
    $sqlFilters = getSqlStatementFilters();
  //  echo print_r($_REQUEST);
   // echo "sq = $sqlFilters";
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        // true when navigated with the Next/Previous links.
	//if (count($_REQUEST) > 0 & $search == 0) {
        if (isset($_REQUEST["offset"])) {  
	    $offset = $_REQUEST["offset"];
	    $pageSize = $_REQUEST["pageSize"];
	    $totalcount = $_REQUEST["totalcount"];    
        } else {
           // when page is loaded. 
    	   $totalcount = getProfilesCount($sqlFilters);
	}

    } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_REQUEST["filterclear"])) {
            // filters reset, page reloaded with all the entries.
	   // $sqlFilters = "";
        }
        $totalcount = getProfilesCount($sqlFilters);
        //echo "total count $totalcount";    
    } 

    getProfilesPaged($sqlFilters, $offset, $pageSize,$totalcount);

        displayAddProfileForm();

}

main();

?>

</body>
</html>

