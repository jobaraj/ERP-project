<?php

session_start();


require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

@ini_set('error_reporting', E_ALL);

@ini_set('display_errors', 'Off');

$str = $_POST['data'];

$data=explode('##',$str);



$field='vendor_id'; $table='vendor';$get_field='vendor_id';$show_field='vendor_name';

$group_for = find_a_field('warehouse','group_for','warehouse_id='.$data[0].' ');

?>



<select id="vendor_id" name="vendor_id" required>
<option></option>

<? foreign_relation($table,$get_field,$show_field,$$field,"group_for='".$group_for."' order by vendor_name");?>

</select>