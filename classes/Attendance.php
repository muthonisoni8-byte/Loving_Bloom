<?php
require_once('../db_connect.php');

class Attendance {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save_attendance() {
        extract($_POST);
        $attendance_date = isset($date) ? $date : date("Y-m-d");
        
        $check = $this->conn->query("SELECT * FROM attendance WHERE child_id = '$child_id' AND attendance_date = '$attendance_date'");
        
        if ($check->num_rows > 0) {
            $sql = "UPDATE attendance SET status = '$status' WHERE child_id = '$child_id' AND attendance_date = '$attendance_date'";
        } else {
            $sql = "INSERT INTO attendance (child_id, status, attendance_date) VALUES ('$child_id', '$status', '$attendance_date')";
        }
        
        if($this->conn->query($sql)){
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }

    public function get_attendance_history(){
        extract($_POST);
        $child_qry = $this->conn->query("SELECT created_at FROM child_info WHERE id = '$child_id'");
        $child_meta = $child_qry->fetch_assoc();
        $enrollment_date = isset($child_meta['created_at']) ? date("Y-m-d", strtotime($child_meta['created_at'])) : date("Y-m-d");

        $target_month = (isset($month) && !empty($month)) ? $month : date("Y-m");
        $start_date = $target_month . '-01'; 
        $end_date = date("Y-m-t", strtotime($start_date)); 
        $real_today = date("Y-m-d");
        $sql = "SELECT * FROM attendance WHERE child_id = '$child_id' AND DATE_FORMAT(attendance_date, '%Y-%m') = '$target_month'";
        $qry = $this->conn->query($sql);
        
        $db_records = [];
        while($row = $qry->fetch_assoc()){ 
            $db_records[$row['attendance_date']] = $row; 
        }

        $final_data = [];
        $current = $start_date;

        while(strtotime($current) <= strtotime($end_date)){
            
            if(strtotime($current) > strtotime($real_today)){
                break; 
            }

            if(strtotime($current) < strtotime($enrollment_date)){
                $current = date("Y-m-d", strtotime($current . " +1 day"));
                continue; 
            }

            if(isset($db_records[$current])){
                $final_data[] = $db_records[$current];
            } else {
                $status_code = ($current == $real_today) ? 'not_marked' : '0';

                $final_data[] = [
                    'attendance_date' => $current,
                    'status' => $status_code, 
                    'child_id' => $child_id
                ];
            }
            
            $current = date("Y-m-d", strtotime($current . " +1 day"));
        }

        usort($final_data, function($a, $b) {
            return strtotime($b['attendance_date']) - strtotime($a['attendance_date']);
        });

        return json_encode(['status'=>'success', 'data'=>$final_data]);
    }

    public function mark_attendance_by_reg_no() {
        extract($_POST);
        $date = date("Y-m-d");
        
        $stmt = $this->conn->prepare("SELECT * FROM child_info WHERE birth_reg_no = ?");
        $stmt->bind_param("s", $reg_no);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $child = $res->fetch_assoc();
            $this->log_presence($child['id'], $date);
            return json_encode([
                'status' => 'success', 
                'child_name' => $child['child_name'], 
                'photo' => $child['child_photo']
            ]);
        }
        return json_encode(['status' => 'failed', 'msg' => 'Registration Number not found.']);
    }
    public function get_webauthn_challenge(){
        try {
            $challenge = base64_encode(random_bytes(32));
            $_SESSION['webauthn_challenge'] = $challenge;
            return json_encode(['status' => 'success', 'challenge' => $challenge]);
        } catch (Exception $e) {
            return json_encode(['status' => 'failed', 'msg' => $e->getMessage()]);
        }
    }

    public function verify_webauthn_attendance(){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if(!isset($data['id'])){
             return json_encode(['status' => 'failed', 'msg' => 'Invalid Data Received']);
        }
        $cred_id = $data['id']; 
        
        $stmt = $this->conn->prepare("SELECT * FROM child_info WHERE webauthn_id = ?");
        $stmt->bind_param("s", $cred_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if($res->num_rows > 0){
            $child = $res->fetch_assoc();
            $this->log_presence($child['id'], date("Y-m-d"));
            return json_encode([
                'status' => 'success', 
                'child_name' => $child['child_name'], 
                'photo' => $child['child_photo']
            ]);
        } else {
            return json_encode(['status' => 'failed', 'msg' => 'Fingerprint not registered.']);
        }
    }

    private function log_presence($child_id, $date){
        $check = $this->conn->query("SELECT * FROM attendance WHERE child_id = '$child_id' AND attendance_date = '$date'");
        if($check->num_rows > 0){
            $this->conn->query("UPDATE attendance SET status = 1 WHERE child_id = '$child_id' AND attendance_date = '$date'");
        } else {
            $this->conn->query("INSERT INTO attendance (child_id, status, attendance_date) VALUES ('$child_id', 1, '$date')");
        }
    }
}
?>