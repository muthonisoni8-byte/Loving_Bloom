<?php
require_once('../db_connect.php');

class Enrollment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save_enrollment() {
        extract($_POST);

        $services_enrolled = "";
        if(isset($_POST['service_ids']) && is_array($_POST['service_ids'])){
            $services_enrolled = implode(",", $_POST['service_ids']);
        }

        $webauthn_id = NULL;
        if(isset($_POST['biometric_registered']) && $_POST['biometric_registered'] == '1'){
            $webauthn_id = 'bio_' . uniqid() . '_' . md5($child_firstname . time());
        }

        $fullname_child = $child_firstname . ' ' . $child_middlename . ' ' . $child_lastname;
        $fullname_parent = $parent_firstname . ' ' . $parent_middlename . ' ' . $parent_lastname;
        $full_address = $address . ' (Contact: ' . $parent_contact . ')';
        
        $birth_reg_no = 'REG-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)); 

        $password = md5($parent_contact); 
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $parent_email, $password, $parent_email);
        
        if ($stmt->execute()) {
            $userid = $this->conn->insert_id;
            $stmt->close();
            $stmt = $this->conn->prepare("INSERT INTO parent_info (parent_name, contact_address, userid) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $fullname_parent, $full_address, $userid);
            
            if ($stmt->execute()) {
                $parentid = $this->conn->insert_id;
                $stmt->close();

                $child_photo = "";
                if(isset($_FILES['child_photo']) && $_FILES['child_photo']['tmp_name'] != ''){
                    $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['child_photo']['name'];
                    $upload_path = '../uploads/children/'; 
                    
                    if(!is_dir($upload_path)) mkdir($upload_path, 0777, true);
                    
                    if(move_uploaded_file($_FILES['child_photo']['tmp_name'], $upload_path.$fname)){
                        $child_photo = "uploads/children/".$fname; 
                    }
                }

                $status = 0; 
                $stmt = $this->conn->prepare("INSERT INTO child_info (child_name, parentid, birth_date, birth_reg_no, gender, blood_type, allergies, med_conditions, special_needs, status, child_photo, services_enrolled, webauthn_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->bind_param("sisssssssisss", $fullname_child, $parentid, $child_dob, $birth_reg_no, $gender, $blood_type, $allergies, $med_conditions, $special_needs, $status, $child_photo, $services_enrolled, $webauthn_id);

                if ($stmt->execute()) {
                    return json_encode(['status' => 'success', 'msg' => 'Enrollment submitted successfully!']);
                } else {
                    return json_encode(['status' => 'failed', 'msg' => 'Failed to save child info: ' . $this->conn->error]);
                }
            } else {
                return json_encode(['status' => 'failed', 'msg' => 'Failed to save parent info.']);
            }
        } else {
            return json_encode(['status' => 'failed', 'msg' => 'Failed to create user account.']);
        }
    }

    public function update_enrollment_status() {
        extract($_POST);
        $sql = "UPDATE child_info SET status = '$status' WHERE id = '$id'";
        if ($this->conn->query($sql)) {
            if($status == 1){
                require_once('Billing.php');
                $billing = new Billing($this->conn);
                $billing->generate_initial_invoice($id);
            }
            return json_encode(['status' => 'success']);
        } else {
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }
    
    public function update_child_details(){
        extract($_POST);
        $child_name = $this->conn->real_escape_string($child_name);
        $parent_name = $this->conn->real_escape_string($parent_name);
        $address = $this->conn->real_escape_string($address);
        $phone = $this->conn->real_escape_string($phone);
        $med_conditions = $this->conn->real_escape_string($med_conditions);
        $services_enrolled = "";
        if(isset($_POST['service_ids']) && is_array($_POST['service_ids'])){
            $services_enrolled = implode(",", $_POST['service_ids']);
        }
        $combined_contact = $address . " (Contact: " . $phone . ")";
        $photo_update_str = "";
        if(isset($_FILES['child_photo']) && $_FILES['child_photo']['tmp_name'] != ''){
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['child_photo']['name'];
            $upload_path = '../uploads/children/'; 
            if(!is_dir($upload_path)) mkdir($upload_path, 0777, true);
            if(move_uploaded_file($_FILES['child_photo']['tmp_name'], $upload_path.$fname)){
                $new_photo_path = "uploads/children/".$fname;
                $photo_update_str = ", child_photo = '{$new_photo_path}'";
                $old_photo_qry = $this->conn->query("SELECT child_photo FROM child_info WHERE id = '{$id}'");
                if($old_photo_qry->num_rows > 0){
                    $old_photo = $old_photo_qry->fetch_array()['child_photo'];
                    if($old_photo && file_exists('../'.$old_photo)) unlink('../'.$old_photo);
                }
            }
        }
        $sql_child = "UPDATE child_info set child_name = '{$child_name}', birth_date = '{$birth_date}', gender = '{$gender}', med_conditions = '{$med_conditions}', services_enrolled = '{$services_enrolled}' {$photo_update_str} WHERE id = '{$id}'";
        $update_child = $this->conn->query($sql_child);
        $get_parent = $this->conn->query("SELECT parentid FROM child_info WHERE id = '{$id}'");
        if($get_parent->num_rows > 0){
            $res = $get_parent->fetch_array();
            $pid = $res['parentid'];
            $sql_parent = "UPDATE parent_info set parent_name = '{$parent_name}', contact_address = '{$combined_contact}' WHERE id = '{$pid}'";
            $update_parent = $this->conn->query($sql_parent);
        }
        if($update_child){
            return json_encode(['status' => 'success']);
        }else{
            return json_encode(['status' => 'failed', 'error' => $this->conn->error]);
        }
    }

    public function delete_child(){
        extract($_POST);
        $qry = $this->conn->query("SELECT child_photo FROM child_info WHERE id = '{$id}'");
        if($qry->num_rows > 0){
            $row = $qry->fetch_assoc();
            if(!empty($row['child_photo']) && file_exists('../'.$row['child_photo'])) unlink('../'.$row['child_photo']);
        }
        $sql = "DELETE FROM child_info WHERE id = '$id'";
        if ($this->conn->query($sql)) {
            return json_encode(['status' => 'success']);
        } else {
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }
}
?>