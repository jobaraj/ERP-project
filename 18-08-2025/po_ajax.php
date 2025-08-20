<?php
session_start();

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
@ini_set('error_reporting', E_ALL);
@ini_set('display_errors', 'Off');

$str = $_POST['data'];
$data=explode('##',$str);
$item=explode('##',$data[0]);
$item_all= find_all_field('item_info','','item_id="'.$data[2].'"');
//echo 'bb'.$data[2];
$warehouse_id = $data[3];

 
$stock = find_a_field('journal_item','sum(item_in - item_ex)','item_id="'.$data[2].'" and warehouse_id="'.$warehouse_id.'"');

?>



<table>
<tr>
<td width="27%">
<input name="stk" type="text" class="input3" id="stk" style="width:80px;" value="<?=$stock?>" readonly=""/>
</td>
<td width="23%">
<input name="unit_name" type="text" class="input3" id="unit_name" style="width:80px;" value="<?=$item_all->unit_name?>" readonly="" />

</td>
<td width="27%">

<input name="rate" type="text" class="input3" id="rate" style="width:80px;" onchange="count()" value="<?=$item_all->cost_price?>" />
</td>
</tr>
</table>