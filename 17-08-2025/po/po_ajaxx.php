<?php

session_start();


require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

@ini_set('error_reporting', E_ALL);

@ini_set('display_errors', 'Off');



$str = $_POST['data'];

$data=explode('##',$str);

$item=explode('#>',$data[0]);

$item_all= find_all_field('item_info','','item_id="'.$item[2].'"');
$item_in = find_a_field('journal_item','sum(item_in)','item_id="'.$item[2].'" and warehouse_id="'.$_SESSION['user']['depot'].'"');
$item_ex = find_a_field('journal_item','sum(item_ex)','item_id="'.$item[2].'" and warehouse_id="'.$_SESSION['user']['depot'].'"');
$stock = $item_in-$item_ex;

?>

<input name="stk" type="text" class="input3" id="stk" style="width:75px;" readonly="readonly"  value="<?=$stock?>"/>

<input name="unit_name" type="text" class="input3" id="unit_name" style="width:55px;" value="<?=$item_all->unit_name?>" readonly required/>

<input name="rate" type="text" class="input3" id="rate"  maxlength="100" style="width:70px;" onchange="count()" value="<?=$item_all->cost_price?>"  required/>