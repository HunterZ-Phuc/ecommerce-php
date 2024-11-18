<?php
require_once 'employeeManagement.php';
$employeeManagement = new \App\Views\AdminManagement\EmployeeManagement();
echo $employeeManagement->index();
?> 