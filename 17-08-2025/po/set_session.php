<?php
session_start();

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

$_SESSION['po_no']= $_POST['po_no'];
$all=1;
echo json_encode($all);


?>

