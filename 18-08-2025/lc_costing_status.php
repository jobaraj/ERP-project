<?php
session_start();
ob_start();
require_once "../../../assets/template/layout.top.php";
$title='L/C Entry Status';
do_calander('#fdate');
do_calander('#tdate');
$table_master='sale_do_master';
$unique='do_no';

create_combobox('lc_no');
//create_combobox('dealer_code');

$table_details='sale_do_details';
//$unique_chalan='id';

$$unique=$_POST[$unique];

//if(isset($_POST['delete']))
//{
//		$crud   = new crud($table_master);
//		$condition=$unique_master."=".$$unique_master;		
//		$crud->delete($condition);
//		$crud   = new crud($table_detail);
//		$crud->delete_all($condition);
//		$crud   = new crud($table_chalan);
//		$crud->delete_all($condition);
//		unset($$unique_master);
//		unset($_SESSION[$unique_master]);
//		$type=1;
//		$msg='Successfully Deleted.';
//}
if(isset($_POST['confirm']))
{
		unset($_POST);
		$_POST[$unique_master]=$$unique_master;
		$_POST['entry_at']=date('Y-m-d h:s:i');
		//$_POST['do_date']=date('Y-m-d');
		$_POST['status']='COMPLETED';
		$crud   = new crud($table_master);
		$crud->update($unique_master);
		$crud   = new crud($table_detail);
		$crud->update($unique_master);
		$crud   = new crud($table_chalan);
		$crud->update($unique_master);
		
		
		
		
		
		
		
		unset($$unique_master);
		unset($_SESSION[$unique_master]);
		$type=1;
		$msg='Successfully Instructed to Depot.';
}


$table='lc_number_setup';
$lc_no='id';
$text_field_id='id';

$target_url = '../lc_grn/lc_grn_costing_print_view.php';


?>
<script language="javascript">
window.onload = function() {
  document.getElementById("dealer").focus();
}
</script>
<script language="javascript">
function custom(theUrl)
{
	window.open('<?=$target_url?>?lc_no='+theUrl);
}
</script><div class="form-container_large">




<style>
/*
.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited, a.ui-button, a:link.ui-button, a:visited.ui-button, .ui-button {
    color: #454545;
    text-decoration: none;
    display: none;
}*/


div.form-container_large input {
    width: 250px;
    height: 38px;
    border-radius: 0px !important;
}



</style>



  <form action="" method="post" name="codz" id="codz">
    <table width="80%" border="0" align="center">
      <tr>
        <td width="20%" align="right" bgcolor="#FF9966"><strong>L/C NO:</strong></td>
        <td width="60%" colspan="3" bgcolor="#FF9966">
		<select name="lc_no" id="lc_no" style="width:250px;">
		
		<option></option>
<? foreign_relation('lc_number_setup','id','lc_number',$_POST['lc_no'],'1');?>
    </select>		</td>
        <td width="20%" rowspan="4" bgcolor="#FF9966"><strong>
          <input type="submit" name="submitit" id="submitit" value="VIEW DETAIL" style="width:120px; font-weight:bold; font-size:12px; height:30px; color:#090"/>
        </strong></td>
      </tr>
      <tr>
        <td align="right" bgcolor="#FF9966"><strong>DATE:</strong></td>
        <td bgcolor="#FF9966">
		<strong>

          <input type="text" name="fdate" id="fdate" style="width:120px; height:30px;" value="<?=isset($_POST['fdate'])?$_POST['fdate']:date('Y-01-01');?>" />

        </strong></td>
        <td bgcolor="#FF9966" align="center"><strong>TO</strong></td>
        <td bgcolor="#FF9966">
		<strong>

          <input type="text" name="tdate" id="tdate" style="width:120px; height:30px;" value="<?=isset($_POST['tdate'])?$_POST['tdate']:date('Y-m-d');?>" />

        </strong>
		</td>
      </tr>
    </table>
  </form>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><div class="tabledesign2">
<table width="100%" cellspacing="0" cellpadding="0" id="grp"><tbody>
<tr>
  <th width="12%">PI No </th>
  <th width="18%">Date</th>
  <th width="25%"><strong>  L/C Number </strong></th>
  <th width="25%">Company</th>
  <th width="20%">Status</th>
</tr>


<? 

if(isset($_POST['submitit'])){



if($_POST['fdate']!=''&&$_POST['tdate']!='') $con .= ' and p.po_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';

if($_POST['dealer_code']!='') 
$con .= ' and dealer_code in ('.$_POST['dealer_code'].') ';

if($_POST['lc_no']!='') 
$con .= ' and a.id in ('.$_POST['lc_no'].') ';



 		$sql = "select lc_no, lc_no as lc_number  from lc_bank_entry where 1 group by lc_no ";
		 $query = mysql_query($sql);
		 while($info=mysql_fetch_object($query)){
  		 $lc_number[$info->lc_no]=$info->lc_number;
		}



    $res="select a.id, p.pi_no, p.po_date, a.lc_number, a.group_for, a.status from lc_number_setup a, lc_purchase_master p  where a.id=p.lc_no ".$con."  group by p.lc_no order by p.po_date";


$query = mysql_query($res);
while($data = mysql_fetch_object($query))
{
?>

<? // if($lc_number[$data->id]==0) {  ?>

<tr <?=($data->RCV_AMT>0)?'style="background-color:#FFCCFF"':'';?>>
<td onClick="custom(<?=$data->id;?>);" <?=(++$z%2)?'':'class="alt"';?>><?=$data->pi_no;?></td>
<td onClick="custom(<?=$data->id;?>);" <?=(++$z%2)?'':'class="alt"';?>><?=$data->po_date;?></td>
<td onClick="custom(<?=$data->id;?>);" <?=(++$z%2)?'':'class="alt"';?>><?=$data->lc_number;?></td>
<td onClick="custom(<?=$data->id;?>);" <?=(++$z%2)?'':'class="alt"';?>><?= find_a_field('user_group','group_name','id="'.$data->group_for.'"');?></td>
<td onClick="custom(<?=$data->id;?>);" <?=(++$z%2)?'':'class="alt"';?>><?=$data->status;?></td>
</tr>


<?
$total_send_amt = $total_send_amt + $data->SEND_AMT;
$total_rcv_amt = $total_rcv_amt + $data->RCV_AMT;

} } //}  ?>


</tbody></table>
</div></td>
</tr>
</table>
</div>

<?
$main_content=ob_get_contents();
ob_end_clean();
include ("../../template/main_layout.php");
?>