<?php
$current_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($current_dir, '/');

if(isset($_SESSION['userdata']['id'])){
    $user_id = $_SESSION['userdata']['id'];
    $usertype = $_SESSION['userdata']['type'];

    if(isset($conn)){
        $stmt = $conn->prepare("SELECT avatar, username FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $db_avatar = $row['avatar'];
            $db_username = $row['username'];
        } else {
            $db_avatar = '';
            $db_username = 'Administrator';
        }
    }
} else {
    $db_avatar = '';
    $db_username = 'Administrator';
    $usertype = 2; 
}

if(!empty($db_avatar)) {
    $final_avatar = "../" . $db_avatar . "?v=" . time();
} else {
    $final_avatar = "https://via.placeholder.com/150?text=Admin";
}

function getLink($type, $url){
    return ($type == 1) ? $url : 'javascript:void(0)';
}

function getClick($type){
    return ($type == 1) ? '' : 'onclick="showAdminOnlyAlert(event)"';
}

function getLockIcon($type){
    return ($type != 1) ? '<i class="fas fa-lock float-right text-muted" style="font-size:0.8em"></i>' : '';
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo $base_url; ?>/index.php" class="brand-link">
        <img src="../favicon.ico" alt="Loving Bloom Logo" class="brand-image img-circle elevation-3" style="opacity: .8; background: white;">
        <span class="brand-text font-weight-light"><b>Loving</b>Bloom</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo $final_avatar; ?>" 
                     class="img-circle elevation-2" 
                     alt="User Image" 
                     style="width: 35px; height: 35px; object-fit: cover; background: white;"
                     onerror="this.onerror=null; this.src='../dist/img/avatar5.png';">
            </div>
            <div class="info">
                <a href="<?php echo $base_url; ?>/profile.php" class="d-block">
                    <?php echo ucwords($db_username); ?>
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>/index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/enrollment.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'enrollment.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-child"></i>
                        <p>
                            Enrollments <?php echo getLockIcon($usertype); ?>
                            <?php 
                            if(isset($conn)){
                                $pending_qry = $conn->query("SELECT * FROM child_info WHERE status = 0");
                                $pending_count = $pending_qry ? $pending_qry->num_rows : 0;
                                if($pending_count > 0): 
                            ?>
                            <span class="badge badge-warning right"><?php echo $pending_count ?></span>
                            <?php endif; } ?>
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>/attendance.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-check"></i><p>Attendance</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/billing.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>Billing <?php echo getLockIcon($usertype); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/fee_structure.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'fee_structure.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Fee Structure <?php echo getLockIcon($usertype); ?></p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>/study_materials.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'study_materials.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-book-open"></i><p>Upload Resources</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>/inquiries.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inquiries.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Inquiries
                            <?php 
                            if(isset($conn)){
                                $msg_qry = $conn->query("SELECT * FROM messages WHERE status = 0");
                                $msg_count = $msg_qry ? $msg_qry->num_rows : 0;
                                if($msg_count > 0): 
                            ?>
                            <span class="badge badge-danger right"><?php echo $msg_count ?></span>
                            <?php endif; } ?>
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/employees.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employees <?php echo getLockIcon($usertype); ?></p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo $base_url; ?>/reports.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-chart-pie"></i><p>Reports & Analytics</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/services_list.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services_list.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-list"></i>
                        <p>Services List <?php echo getLockIcon($usertype); ?></p>
                    </a>
                </li>

                <li class="nav-header">SYSTEM</li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/user_list.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'user_list.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>User Management <?php echo getLockIcon($usertype); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo getLink($usertype, $base_url.'/settings.php'); ?>" 
                       class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>"
                       <?php echo getClick($usertype); ?>>
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Website Settings <?php echo getLockIcon($usertype); ?></p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showAdminOnlyAlert(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Access Denied',
            text: 'You do not have administrative privileges to access this page.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }
</script>