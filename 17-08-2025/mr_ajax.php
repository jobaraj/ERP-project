<?php
session_start();

require_once "../../../assets/template/layout.top.php";

@ini_set('error_reporting', E_ALL);

@ini_set('display_errors', 'Off');

$str = $_POST['data'];
$data=explode('##',$str);
$item=explode('#>',$data[0]);
$item_id = $item[2];
$var = $data[1];

$warehouse=explode('#-#',$var);

$warehouse_id=$warehouse[0];

$pl_id=$warehouse[1];





if($item_id>0){



$stock=find_a_field('journal_item','sum(item_in-item_ex)','warehouse_id='.$warehouse_id.' and item_id='.$item_id.' ');

$booking_stock=find_a_field('','','');

//echo $sql='SELECT sum(item_in-item_ex) FROM `journal_item` WHERE 1 and warehouse_id='.$warehouse_id.' and item_id='.$item_id.' and tr_from in ("Consumption","Production Return","Issue","Opening");';

//$query=mysql_query($sql);

//$stock = warehouse_product_stock($item_id ,$data[1]);



$center_stock= find_a_field('journal_item','sum(item_in-item_ex)','item_id="'.$item_id.'" and warehouse_id in (1,78,79)');



$item_all= find_all_field('item_info','','item_id="'.$item_id.'"');



$rate = find_a_field('journal_item','final_price',' final_price > 0 and item_id="'.$item_id.'" and tr_from in ("Purchase","Local Purchase","Production Receive","Opening") order by id desc');



}



?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

  	<td><input name="cwh_qoh" type="text" class="input3" id="cwh_qoh" style="width:60px;" value="<?=$stock?>" onfocus="focuson('qty')" readonly/> </td>

    <td><input name="qoh" type="text" class="input3" id="qoh" style="width:60px;" value="" onfocus="focuson('qty')" readonly/> </td>

    <td>

	<input name="rate" type="hidden" class="input3" id="rate" style="width:60px;" value="<?=$rate?>" onfocus="focuson('qty')" readonly/></td>

	<td>

	<input name="unit_name" type="text" class="input3" id="unit_name" style="width:60px;" value="<?=$item_all->unit_name?>" onfocus="focuson('qty')" readonly/></td>

  </tr>

</table>



 



