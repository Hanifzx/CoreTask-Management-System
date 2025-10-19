<?php
require_once '../src/partials/sidebar.php';

switch ($role) {
    case 'super_admin':
        // Jika role adalah 'super_admin'
        include 'dashboard_views/admin_dashboard.php';
        break; 

    case 'project_manager':
        // Jika role adalah 'project_manager'
        include 'dashboard_views/manager_dashboard.php';
        break;

    case 'team_member':
        // Jika role adalah 'team_member'
        include 'dashboard_views/member_dashboard.php';
        break;

    default:
        // Pengaman jika $role tidak dikenali
        echo "<div class='p-8 text-center text-red-500'>Error: Peran pengguna tidak valid.</div>";
}

require_once '../src/partials/footer_tags.php';
?>