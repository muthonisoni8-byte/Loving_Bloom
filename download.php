<?php
require_once('db_connect.php'); 

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $qry = $conn->query("SELECT file_path FROM study_materials WHERE id = $id");
    if($qry->num_rows > 0){
        $row = $qry->fetch_assoc();
        $file = $row['file_path']; 
        if(file_exists($file)){
            $conn->query("UPDATE study_materials SET downloads = downloads + 1 WHERE id = $id");
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream'); 
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            if (ob_get_length()) ob_clean();
            flush();
            
            readfile($file);
            exit;
        } else {
            echo "Error: The file was not found on the server.";
        }
    } else {
        echo "Error: Invalid File ID.";
    }
}
?>