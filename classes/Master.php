<?php
session_start();
require_once('../db_connect.php');
require_once('Enrollment.php');
require_once('Attendance.php');
require_once('Billing.php');
require_once('Users.php');
require_once('Staff.php');
require_once('SystemSettings.php');
// THIS LINE IS CRITICAL:
require_once('StudyMaterial.php'); 

$action = isset($_GET['f']) ? $_GET['f'] : '';

$enrollment = new Enrollment($conn);
$attendance = new Attendance($conn);
$billing = new Billing($conn);
$users = new Users($conn);
$staff = new Staff($conn);
$sys_set = new SystemSettings($conn);
$study_material = new StudyMaterial($conn);

switch ($action) {
    case 'save_enrollment': echo $enrollment->save_enrollment(); break;
    case 'update_enrollment_status': echo $enrollment->update_enrollment_status(); break;
    case 'update_child_details': echo $enrollment->update_child_details(); break;
    case 'delete_child': echo $enrollment->delete_child(); break;
    case 'register_biometric': echo $enrollment->register_biometric(); break;
    case 'save_attendance': echo $attendance->save_attendance(); break;
    case 'get_attendance_history': echo $attendance->get_attendance_history(); break;
    case 'mark_attendance_by_reg_no': echo $attendance->mark_attendance_by_reg_no(); break;
    case 'get_webauthn_challenge': echo $attendance->get_webauthn_challenge(); break;
    case 'verify_webauthn_attendance': echo $attendance->verify_webauthn_attendance(); break;
    case 'save_payment': echo $billing->save_payment(); break;
    case 'delete_payment': echo $billing->delete_payment(); break;
    case 'get_child_billing_history': echo $billing->get_child_billing_history(); break;
    case 'save_fee': echo $billing->save_fee(); break;
    case 'delete_fee': echo $billing->delete_fee(); break;
    case 'generate_monthly_bills': echo $billing->generate_monthly_bills(); break;
    case 'save_service': echo $billing->save_service(); break;
    case 'save_user': echo $users->save_user(); break;
    case 'delete_user': echo $users->delete_user(); break;
    case 'save_employee': echo $staff->save_employee(); break;
    case 'delete_employee': echo $staff->delete_employee(); break;
    case 'update_settings': echo $sys_set->update_settings(); break;
    case 'save_message': echo $sys_set->save_message(); break;
    case 'delete_message': echo $sys_set->delete_message(); break;
    case 'update_message_status': echo $sys_set->update_message_status(); break; 
    case 'save_material': echo $study_material->save_material(); break;
    case 'delete_material': echo $study_material->delete_material(); break;

    default: echo json_encode(['status' => 'failed', 'msg' => 'Invalid Action']); break;
}
?>