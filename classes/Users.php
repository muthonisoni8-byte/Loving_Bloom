<?php
require_once('../db_connect.php');

class Users {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save_user() {
        extract($_POST);
        $password = md5($password);
        $check = $this->conn->query("SELECT * FROM users WHERE username = '$username' ".(!empty($id) ? " and id != {$id} " : ""))->num_rows;
        if($check > 0) return json_encode(['status' => 'failed', 'msg' => 'Username already exists.']);

        if(empty($id)){
            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
        }else{
            if(!empty($_POST['password'])){
                $sql = "UPDATE users SET username = '$username', password = '$password', email = '$email' WHERE id = '$id'";
            }else{
                $sql = "UPDATE users SET username = '$username', email = '$email' WHERE id = '$id'";
            }
        }
        return $this->conn->query($sql) ? json_encode(['status' => 'success', 'msg' => 'User saved successfully.']) : json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }

    public function delete_user() {
        extract($_POST);
        return $this->conn->query("DELETE FROM users WHERE id = '$id'") ? json_encode(['status' => 'success']) : json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }
}
?>