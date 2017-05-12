
<?php

//include 'redirect.php';
include 'include/globals.php';

function download() {
    $filename=$_REQUEST["resume"];
    $destDir=$GLOBALS["resumesDir"];
    $fullPath=$destDir.$filename;

    if (is_file($fullPath)) {
        $fsize=filesize($fullPath);
        $path_parts=pathinfo($fullPath);
        $ext=strtolower($path_parts["extension"]);
        $basename=$path_parts["basename"];

        header('Connection: Keep-Alive');
        header('Content-Description: File Transfer');
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".$basename."\"");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');

        header("Content-Length: " . $fsize);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        ob_clean();
        readfile($fullPath);
    }
}

download();
?>
