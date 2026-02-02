<?php
require_once('../db_connect.php');

class SystemSettings {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function update_settings(){
        $resp = ['status' => 'success', 'msg' => 'Settings updated successfully'];
        foreach($_POST as $key => $value){
            if($key == 'content') continue;
            if(in_array($key, ['hero_image', 'about_image', 'logo'])) continue;
            
            $chk = $this->conn->query("SELECT * FROM system_info WHERE meta_field = '$key'");
            $value = $this->conn->real_escape_string($value);
            
            if($chk->num_rows > 0){
                $this->conn->query("UPDATE system_info SET meta_value = '$value' WHERE meta_field = '$key'");
            } else {
                $this->conn->query("INSERT INTO system_info (meta_field, meta_value) VALUES ('$key', '$value')");
            }
        }
        $uploads = ['hero_image', 'about_image', 'logo'];
        $upload_dir = '../uploads/site/'; 

        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }

        foreach($uploads as $field){
            if(isset($_FILES[$field]) && $_FILES[$field]['tmp_name'] != ''){
                $fname = time() . '_' . $_FILES[$field]['name'];
                $move = move_uploaded_file($_FILES[$field]['tmp_name'], $upload_dir . $fname);
                
                if($move){
                    $path = 'uploads/site/' . $fname; 
                    $chk = $this->conn->query("SELECT * FROM system_info WHERE meta_field = '$field'");
                    if($chk->num_rows > 0){
                        $this->conn->query("UPDATE system_info SET meta_value = '$path' WHERE meta_field = '$field'");
                    } else {
                        $this->conn->query("INSERT INTO system_info (meta_field, meta_value) VALUES ('$field', '$path')");
                    }
                }
            }
        }
        return json_encode($resp);
    }

    public function save_message(){
        extract($_POST);
        $fullname = $this->conn->real_escape_string($fullname);
        $email = $this->conn->real_escape_string($email);
        $subject = $this->conn->real_escape_string($subject);
        $message = $this->conn->real_escape_string($message);

        $sql = "INSERT INTO messages (fullname, email, subject, message, status) VALUES ('$fullname', '$email', '$subject', '$message', 0)";
        $save = $this->conn->query($sql);
        
        if($save){
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }

    public function delete_message(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM messages where id = '$id'");
        if($del){
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'failed']);
    }

    public function update_message_status(){
        extract($_POST);
        $update = $this->conn->query("UPDATE messages SET status = '$status' WHERE id = '$id'");
        if($update){
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }
}
?>