<?php
require_once '../db_connect.php';

class Login {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function login(){
        extract($_POST);
        
        $password = md5($password);
        
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? and password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $data = $result->fetch_assoc();
            session_start();
            foreach($data as $k => $v){
                if(!is_numeric($k) && $k != 'password'){
                    $_SESSION['userdata'][$k] = $v;
                }
            }
            return json_encode(array('status'=>'success'));
        }else{
            return json_encode(array('status'=>'failed','msg'=>'Incorrect Username or Password'));
        }
    }
}

$auth = new Login($conn);
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
    case 'login':
        echo $auth->login();
        break;
    default:
        break;
}
?>