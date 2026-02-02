<?php
require_once('../db_connect.php');

class Staff {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save_employee(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id','img')) && !is_array($_POST[$k])){
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$this->conn->real_escape_string($v)}' ";
            }
        }

        $is_new = empty($id);
        
        if($is_new){
            $code_prefix = "EMP-".date("Ym")."-";
            $i = 1;
            while(true){
                $code = $code_prefix . sprintf("%04d", $i);
                $check = $this->conn->query("SELECT id FROM employees WHERE code = '{$code}'")->num_rows;
                if($check > 0){
                    $i++;
                }else{
                    break;
                }
            }
            $data .= ", `code` = '{$code}' ";
            $sql = "INSERT INTO employees set {$data}";
        }else{
            $sql = "UPDATE employees set {$data} where id = '{$id}'";
        }

        $save = $this->conn->query($sql);
        
        if($save){
            $eid = empty($id) ? $this->conn->insert_id : $id;
            $user_avatar = '';
            if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
                $fname = 'uploads/employee_'.$eid.'.png';
                $dir_path =__DIR__ . '/../uploads/';
                
                if(!is_dir($dir_path)){ mkdir($dir_path, 0777, true); }
                
                $upload_path = $dir_path . 'employee_'.$eid.'.png';
                
                if(move_uploaded_file($_FILES['img']['tmp_name'], $upload_path)){
                    $this->conn->query("UPDATE employees SET avatar = '{$fname}' WHERE id = '{$eid}'");
                    $user_avatar = $fname;
                }
            }
            if($is_new){
                $user_email = $this->conn->real_escape_string($email);
                $user_pass = md5($code); 
                $chk = $this->conn->query("SELECT id FROM users WHERE username = '{$user_email}'")->num_rows;
                
                if($chk == 0){
                    $sql_user = "INSERT INTO users (username, email, password, type, avatar) 
                                 VALUES ('{$user_email}', '{$user_email}', '{$user_pass}', 2, '{$user_avatar}')";
                    $this->conn->query($sql_user);
                }
            }

            return json_encode(['status' => 'success']);
        }else{
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }

    public function delete_employee(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM employees where id = '{$id}'");
        if($del){
            return json_encode(['status' => 'success']);
        }else{
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }
    public function save_service(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k, array('id'))){
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$this->conn->real_escape_string($v)}' ";
            }
        }
        if(empty($id)){
            $sql = "INSERT INTO services set {$data}";
        }else{
            $sql = "UPDATE services set {$data} where id = '{$id}'";
        }
        $save = $this->conn->query($sql);
        if($save){
            return json_encode(['status' => 'success']);
        }else{
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }

    public function delete_service(){
        extract($_POST);
        $del = $this->conn->query("DELETE FROM services where id = '{$id}'");
        if($del){
            return json_encode(['status' => 'success']);
        }else{
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }
}
?>