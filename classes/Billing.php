<?php
require_once('../db_connect.php');

class Billing {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function save_fee(){
        extract($_POST);
        $name = $this->conn->real_escape_string($name);
        $description = $this->conn->real_escape_string($description);
        
        if(empty($id)){
            $sql = "INSERT INTO fee_structure (name, description, amount, type) VALUES ('$name', '$description', '$amount', '$type')";
        } else {
            $sql = "UPDATE fee_structure SET name='$name', description='$description', amount='$amount', type='$type' WHERE id='$id'";
        }
        return $this->conn->query($sql) ? json_encode(['status' => 'success']) : json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }

    public function delete_fee(){
        extract($_POST);
        return $this->conn->query("DELETE FROM fee_structure WHERE id = '$id'") ? json_encode(['status' => 'success']) : json_encode(['status' => 'failed']);
    }

    public function save_payment(){
        extract($_POST);
        $amount_paid = floatval($amount_paid);
        
        $qry = $this->conn->query("SELECT * FROM invoices WHERE id = '$invoice_id'");
        $inv = $qry->fetch_assoc();
        
        $new_balance = $inv['balance'] - $amount_paid;
        $status = ($new_balance <= 0) ? 1 : 2; 

        // Insert with status = 1 (Active)
        $stmt = $this->conn->prepare("INSERT INTO payments (invoice_id, amount_paid, status) VALUES (?, ?, 1)");
        $stmt->bind_param("id", $invoice_id, $amount_paid);
        
        if($stmt->execute()){
            $this->conn->query("UPDATE invoices SET balance = '$new_balance', status = '$status' WHERE id = '$invoice_id'");
            return json_encode(['status' => 'success', 'msg' => 'Payment recorded successfully!']);
        }
        return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
    }

    // --- VOID PAYMENT WITH REASON ---
    public function delete_payment(){
        extract($_POST);
        // $reason comes from POST
        $reason = isset($reason) ? $this->conn->real_escape_string($reason) : 'No reason provided';
        
        $pay_qry = $this->conn->query("SELECT * FROM payments WHERE id = '$payment_id' AND status = 1");
        
        if($pay_qry->num_rows > 0){
            $payment = $pay_qry->fetch_assoc();
            $amount_to_restore = $payment['amount_paid'];
            $invoice_id = $payment['invoice_id'];
            
            $inv_qry = $this->conn->query("SELECT * FROM invoices WHERE id = '$invoice_id'");
            $inv = $inv_qry->fetch_assoc();
            
            $new_balance = $inv['balance'] + $amount_to_restore;
            
            // Recalculate Invoice Status
            if($new_balance <= 0){
                $status = 1;
            } elseif($new_balance >= $inv['amount']) {
                $status = 0;
            } else {
                $status = 2;
            }

            // MARK AS VOID (0) AND SAVE REASON
            $void = $this->conn->query("UPDATE payments SET status = 0, void_reason = '$reason' WHERE id = '$payment_id'");
            
            if($void){
                $this->conn->query("UPDATE invoices SET balance = '$new_balance', status = '$status' WHERE id = '$invoice_id'");
                return json_encode(['status' => 'success', 'msg' => 'Transaction voided. Balance restored.']);
            }
        }
        return json_encode(['status' => 'failed', 'msg' => 'Payment invalid or already voided.']);
    }

    public function get_child_billing_history(){
        extract($_POST);
        $data = [];
        
        $inv_qry = $this->conn->query("SELECT * FROM invoices WHERE child_id = '$child_id' AND status != 3 ORDER BY date_created DESC");
        
        while($inv = $inv_qry->fetch_assoc()){
            $inv_id = $inv['id'];
            $payments = [];
            
            $pay_qry = $this->conn->query("SELECT * FROM payments WHERE invoice_id = '$inv_id' ORDER BY date_created DESC");
            while($p = $pay_qry->fetch_assoc()){
                $p['date_formatted'] = date("M d, Y h:i A", strtotime($p['date_created']));
                $p['amount_formatted'] = number_format($p['amount_paid'], 2);
                $p['status'] = $p['status']; 
                $p['void_reason'] = $p['void_reason']; // Send reason to frontend
                $payments[] = $p;
            }

            $inv['total_formatted'] = number_format($inv['amount'], 2);
            $inv['balance_formatted'] = number_format($inv['balance'], 2);
            $inv['date_formatted'] = date("M Y", strtotime($inv['date_created']));
            $inv['payments'] = $payments;
            
            $data[] = $inv;
        }
        
        return json_encode(['status' => 'success', 'history' => $data]);
    }

    public function generate_monthly_bills(){
        $current_month_str = date('Y-m'); 
        $month_name = date('F Y');    
        $start_date = date('Y-m-01 00:00:00');
        $end_date = date('Y-m-t 23:59:59');
        $count = 0;

        $students = $this->conn->query("SELECT * FROM child_info WHERE status = 1");
        
        while($child = $students->fetch_assoc()){
            $child_id = $child['id'];

            $chk = $this->conn->query("SELECT id FROM invoices WHERE child_id = '$child_id' AND date_created BETWEEN '$start_date' AND '$end_date'");
            if($chk->num_rows > 0) continue; 

            $dob = new DateTime($child['birth_date']);
            $now = new DateTime();
            $age_months = $now->diff($dob)->m + ($now->diff($dob)->y * 12);
            $age_years = $now->diff($dob)->y;

            $monthly_fee = 0;
            $items = [];

            $prog_sql = "";
            if($age_months <= 12) $prog_sql = "SELECT amount, name FROM fee_structure WHERE type='Program' AND name LIKE '%Infant%' LIMIT 1";
            elseif($age_years < 3) $prog_sql = "SELECT amount, name FROM fee_structure WHERE type='Program' AND name LIKE '%Toddler%' LIMIT 1";
            else $prog_sql = "SELECT amount, name FROM fee_structure WHERE type='Program' AND name LIKE '%Preschool%' LIMIT 1";
            
            $p_res = $this->conn->query($prog_sql);
            if($p_res->num_rows > 0){
                $prog = $p_res->fetch_assoc();
                $monthly_fee += $prog['amount'];
                $items[] = $prog['name'];
            }

            if(!empty($child['services_enrolled'])){
                $service_ids = $this->conn->real_escape_string($child['services_enrolled']);
                $s_res = $this->conn->query("SELECT amount, name FROM fee_structure WHERE id IN ($service_ids)");
                while($service = $s_res->fetch_assoc()){
                    $monthly_fee += $service['amount'];
                    $items[] = $service['name'];
                }
            }
            
            if($child['special_needs'] && $child['special_needs'] != 'None'){
                 $sn = $this->conn->query("SELECT amount, name FROM fee_structure WHERE name LIKE '%Special Needs%' LIMIT 1");
                 if($sn->num_rows > 0){
                     $sn_row = $sn->fetch_assoc();
                     $monthly_fee += $sn_row['amount'];
                     $items[] = "Special Needs";
                 }
            }

            $net_balance = 0;
            $prev_inv_qry = $this->conn->query("SELECT id, balance FROM invoices WHERE child_id = '$child_id' AND status != 3 AND balance != 0 AND date_created < '$start_date'");
            
            while($prev = $prev_inv_qry->fetch_assoc()){
                $net_balance += $prev['balance'];
                $this->conn->query("UPDATE invoices SET status = 3 WHERE id = '{$prev['id']}'");
            }

            $final_total = $monthly_fee + $net_balance;
            $title = "Fees for $month_name";
            
            $description_items = implode(", ", $items);
            if($net_balance > 0){
                $description_items .= " + Arrears (" . number_format($net_balance) . ")";
            } elseif ($net_balance < 0) {
                $description_items .= " - Credit Applied (" . number_format(abs($net_balance)) . ")";
            }

            if($final_total != 0 || $monthly_fee > 0){
                $stmt = $this->conn->prepare("INSERT INTO invoices (child_id, title, amount, balance, status) VALUES (?, ?, ?, ?, ?)");
                $init_status = ($final_total <= 0) ? 1 : 0;
                $stmt->bind_param("isddi", $child_id, $title, $final_total, $final_total, $init_status);
                if($stmt->execute()) $count++;
            }
        }

        return json_encode(['status' => 'success', 'msg' => "$count Invoices Generated for $month_name"]);
    }

    // --- NEW: WEBSITE CONTENT EDITOR (Linked to Fee Structure) ---
    // This allows staff to edit the "Pretty" description and image for the frontend
    // WITHOUT changing the official Fee Name or Amount.
    public function save_service(){
        extract($_POST);
        if(empty($id)){
            return json_encode([
                'status' => 'failed', 
                'msg' => 'Creation denied. Please add new services in the Fee Structure page first.'
            ]);
        }
        $public_description = $this->conn->real_escape_string($description);
        $image_db_path = ""; 
        if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
            $fname = 'service_' . time() . '_' . $_FILES['img']['name'];
            $dir_path = __DIR__ . '/../uploads/services/';
            
            if(!is_dir($dir_path)){ mkdir($dir_path, 0777, true); }
            
            $upload_path = $dir_path . $fname;
            if(move_uploaded_file($_FILES['img']['tmp_name'], $upload_path)){
                $image_db_path = "uploads/services/" . $fname;
            }
        }
        $sql = "UPDATE fee_structure SET public_description = '$public_description'";
        if(!empty($image_db_path)){
            $sql .= ", image_path = '$image_db_path'";
        }
        
        $sql .= " WHERE id = '$id'";

        $save = $this->conn->query($sql);

        if($save){
            return json_encode(['status' => 'success']);
        } else {
            return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
        }
    }
}
?>