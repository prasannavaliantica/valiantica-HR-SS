
<?php

$documents = array("offerletter","document2","document3");


function deleteDocument($documentFieldName, $currentdocumentName) {
    if (!empty($document)) {
        $destDir=$GLOBALS["documentsDir"];
        $document = $destDir.$currentdocumentName;

	if (file_exists($document)) { 
           $status =  unlink($document);
	}
    }
}

function uploadDocument($documentFieldName)   { 

   $destDir=$GLOBALS["documentsDir"];
   $newDir = $destDir."/".$_REQUEST["firstname"].$_REQUEST["lastname"];
 
   mkdir($newDir,0777);

    $uploadDoc = $_FILES[$documentFieldName]["name"];
    $tmpDoc = $_FILES[$documentFieldName]["tmp_name"];

    if (!empty($uploadDoc)) {
        $destDocFile = $destDir . basename($uploadDoc);
        $canUpload = 1;

        if (move_uploaded_file($tmpDoc,$destDocFile)) {
            echo "The file " . basename($uploadDoc) . " has been uploaded.<br> ";
        }
    } else {
        echo "No file chosen for upload";
    }
}

function deleteDocuments() {
    $offerletter = $_REQUEST["offerletter"];
    $document2 = $_REQUEST["document2"];
    $document3 = $_REQUEST["document3"];
}

function uploadDocuments() {
    $docs = $GLOBALS["documents"];
    $canupload = False;

    // No of current documents 
    foreach($docs as $doc) {
        if (empty($doc)) {
            canupload = True;
            break;
        }
    }

    if ($canupload) {
        foreach($array as $documentFieldName) {
            uploadDocument($documentFieldName);  
        }
    }
}

function displayCurrentDocument($documentName) {
    if (!empty($documentName)) {
        echo "<tr>";
        echo "<td>";
        // link to download document.
            
        echo "</td>";

        echo "<td>";
        // check box.
        echo "<input type  = 'checkbox' name = 'documents[]' value = $documentName>"; 
        echo "</td>";
        echo "</tr>";
    }
}

function  displayCurrentDocuments($offerletter, $document2, $document3) {
    echo "<table align=center>";
    $array = array($offerletter, $document2, $document3);
    foreach($array as $document) {
        displayCurrentDocument($document);
    }
    /*    
    displayCurrentDocument($offerletter);
    displayCurrentDocument($document2);
    displayCurrentDocument($document3);
    */
    echo "</table>";
}

function displayUploadDocumentField($documentFieldName, $documentLabel) {
    echo "<tr>";

    echo "<td>";
    echo "Upload $documentLabel: ";
    echo "</td>";

    echo "<td>";
    echo "<input type='file' name='$documentFieldName' id='$documentFieldName'>";
    echo "</td>";

    echo "</tr>";
}

function displayCurrentDocumentField($documentFieldName,$currentDocument) {
    echo "<tr>";

    echo "<td>";
        echo "Current $documentFieldName : ";
    echo "</td>";

    echo "<td>";
    if (!empty($currentDocument)) {
        echo "<b>$currentDocument</b>";
    } else {
        echo "<b> None </b>";
    }
    echo "</td>";
    echo "</tr>";
}

function displayFieldsForUpload() {
    displayUploadDocumentField("offerletter", "Offer Letter");
    displayUploadDocumentField("document2", "Document 2");
    displayUploadDocumentField("document3", "Document 3");
}

function addHiddenDocumentFields($firstname, $lastname, $offerletter, $document2, $document3) {
    echo "<tr>";
    echo "<td>"; 
    echo "<input type = 'hidden' name = 'offerletter' id = 'offerletter' value = $offerletter>";
    echo "</td>";

    echo "<td>"; 
    echo "<input type = 'hidden' name = 'document2' id = 'document2' value = $document2>";
    echo "</td>";

    echo "<td>"; 
    echo "<input type = 'hidden' name = 'document3' id = 'document3' value = $document3>";
    echo "</td>";

    echo "<td>"; 
    echo "<input type = 'hidden' name = 'firstname' id = 'firstname' value = $firstname>";
    echo "</td>";

    echo "<td>"; 
    echo "<input type = 'hidden' name = 'lastname' id = 'lastname' value = $lastname>";
    echo "</td>";

    echo "</tr>";
}

function displayFormSubmitButtons($showDelete) {
    echo "<tr>";
    if ($showDelete) {
        echo "<td>";
        echo "<input type = 'submit' name = 'delete' id = 'delete' value = 'Delete Selected Document(s)'>";
        echo "</td>";
    }
    echo "<td>";
        echo "<input type = 'submit' name = 'upload' id = 'upload' value = 'Upload Document(s)'>";
    echo "</td>";
    echo "</tr>";   
}

function displayForm$row) {
    $row = getClient($id);
    $offerletter  = $document2 = $document3 = $firstname = $lastname =  $name = "";
    $showDelete = True;

    echo "<form method = 'post' action = ". htmlspecialchars($_SERVER["PHP_SELF"]) . "enctype='multipart/form-data'>";
    if ($row != NULL) {
        $offerletter = $row["offerletter"];
        $document2 = $row["document2"];
        $document3 = $row["document3"];
        $firstname = $row["firstname"];
        $lastname = $row["lastname"];
        //$name = $firstname . $lastname;
        echo "<b> Manage Documents for $firstname $lastname <b>";
        displayCurrentDocuments($offer, $document2, $document3);  
    }

    displayFieldsForUpload();
    if (!empty($offerletter) || !empty($document2) || !empty($document3)) {
        $showDelete = False;
    }     
 
    addHiddenDocumentFields($firstname,$lastname,$offerletter,$document2,$document3);
    displayFormSubmitButtons($showDelete);

    echo "</form>";  
}


function main() {
    $method = $_SERVER["REQUEST_METHOD"];
    if ( $method == 'GET') {
        $id = intval($_REQUEST["id"]); 
    } else if ($method == 'POST')(
           // Handle Document deletion.
        if (isset($_REQUEST["delete"])) {
            deleteDocuments(); 
        } else if (isset($_REQUEST["upload"])) { 
          // Handle document upload.
            uploadDocuments();
        }
    }
}

main();

?>


