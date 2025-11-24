<?php
// Facility Admin Logout

session_start();

unset($_SESSION['facility_admin_id']);
unset($_SESSION['facility_id']);
unset($_SESSION['facility_admin_name']);
unset($_SESSION['facility_name']);

session_destroy();
header('Location: facility-admin-login.php');
exit();
?>

