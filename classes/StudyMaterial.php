<?php
require_once('../db_connect.php');

class StudyMaterial {
    private $conn;
    public function __construct($db_conn){
        $this->conn = $db_conn;
    }

    public function save_material(){
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        
        $title = $this->conn->real_escape_string($_POST['title']);
        $subject = $this->conn->real_escape_string($_POST['subject']);
        $class_level = $this->conn->real_escape_string($_POST['class_level']);
        $description = $this->conn->real_escape_string($_POST['description']);

        $data = " title = '{$title}', subject = '{$subject}', class_level = '{$class_level}', description = '{$description}' ";
        if(isset($_FILES['cover_img']) && $_FILES['cover_img']['tmp_name'] != ''){
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover_img']['name'];
            $upload_path = '../uploads/covers/';
            if(!is_dir($upload_path)) mkdir($upload_path, 0777, true);
            
            $move = move_uploaded_file($_FILES['cover_img']['tmp_name'], $upload_path . $fname);
            if($move){
                $data .= ", cover_image = 'uploads/covers/{$fname}' ";
                if(!empty($id)){
                    $old = $this->conn->query("SELECT cover_image FROM study_materials where id = $id")->fetch_array();
                    if($old && !empty($old['cover_image']) && file_exists('../'.$old['cover_image'])) unlink('../'.$old['cover_image']);
                }
            }
        }

        if(isset($_FILES['material_file']) && $_FILES['material_file']['tmp_name'] != ''){
            $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['material_file']['name'];
            $upload_path = '../uploads/materials/';
            
            if(!is_dir($upload_path)) mkdir($upload_path, 0777, true);

            $move = move_uploaded_file($_FILES['material_file']['tmp_name'], $upload_path . $fname);
            if($move){
                $data .= ", file_path = 'uploads/materials/{$fname}' ";
                if(!empty($id)){
                    $old = $this->conn->query("SELECT file_path FROM study_materials where id = $id")->fetch_array();
                    if($old && !empty($old['file_path']) && file_exists('../'.$old['file_path'])) unlink('../'.$old['file_path']);
                }
            }
        }

        if(empty($id)){
            $sql = "INSERT INTO study_materials set {$data}";
            $action = "added";
        }else{
            $sql = "UPDATE study_materials set {$data} where id = {$id}";
            $action = "updated";
        }
        
        $save = $this->conn->query($sql);
        if($save){
            return json_encode(['status' => 'success', 'msg' => "Material successfully {$action}."]);
        }else{
            return json_encode(['status' => 'failed', 'msg' => "DB Error: " . $this->conn->error]);
        }
    }

    public function delete_material(){
        extract($_POST);
        $qry = $this->conn->query("SELECT * FROM study_materials where id = $id");
        if($qry->num_rows > 0){
            $res = $qry->fetch_array();
            if(!empty($res['cover_image']) && file_exists('../'.$res['cover_image'])) unlink('../'.$res['cover_image']);
            if(!empty($res['file_path']) && file_exists('../'.$res['file_path'])) unlink('../'.$res['file_path']);
            
            $del = $this->conn->query("DELETE FROM study_materials where id = $id");
            if($del){
                return json_encode(['status' => 'success']);
            }else{
                return json_encode(['status' => 'failed', 'msg' => $this->conn->error]);
            }
        }
        return json_encode(['status' => 'failed', 'msg' => 'Unknown ID']);
    }
}
?>