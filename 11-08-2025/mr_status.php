<?php



require_once "../../../assets/template/layout.top.php";



$title='Section For';

do_calander('#fdate');

do_calander('#tdate');



$table = 'master_requisition_master';

$unique = 'req_no';

$status = 'UNCHECKED';



$target_url = '../mr_fg/mr_print_view.php';



?>



<script language="javascript">



function custom(theUrl){

	window.open('<?=$target_url?>?<?=$unique?>='+theUrl);

}





</script>





<div class="form-container_large">
	<form action="" method="post" name="codz" id="codz">
<table width="80%" border="0" align="center">

  <tr>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right" bgcolor="#FF9966"><strong>Date:</strong></td>
    <td  bgcolor="#FF9966"><strong>
      <input type="text" name="fdate" id="fdate" style="width:100px;" value="<?=$_POST['fdate']?>" />
    </strong></td>
    <td align="center" bgcolor="#FF9966"><strong> -to- </strong></td>
    <td  bgcolor="#FF9966"><strong>
      <input type="text" name="tdate" id="tdate" style="width:100px;" value="<?=$_POST['tdate']?>" />
    </strong></td>

    <td rowspan="2" bgcolor="#FF9966"><strong>
      <input type="submit" name="submitit" id="submitit" value="VIEW DETAIL" style="width:120px; font-weight:bold; font-size:12px; height:30px; color:#090"/>
    </strong></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#FF9966"><strong><?=$title?>: </strong></td>
    <td colspan="3" bgcolor="#FF9966"><strong>
<select name="req_for" id="req_for">
	<option></option>
		<? foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['req_for'],' use_type="PL"');?>
	</select>
    </strong></td>
    </tr>
</table>
</form>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><div class="tabledesign2">
<? 
if($_POST['status']!=''&&$_POST['status']!='ALL')
$con .= 'and a.status="'.$_POST['status'].'"';
if(isset($_POST['submitit'])){
if($_POST['fdate']!=''&&$_POST['tdate']!='')
$con .= 'and a.req_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';
if($_POST['req_for']>0)
$con .= ' and a.req_for = "'.$_POST['req_for'].'"  ';
  $res='select  a.req_no,a.manual_req_no, a.req_date,(select do_no from sale_do_master where do_no=a.do_number) as Contruct_no, (select warehouse_name from warehouse where warehouse_id=a.req_for) as req_for, b.warehouse_name as warehouse, a.req_note as note, a.need_by, c.fname as entry_by, a.entry_at,a.status
 
  from master_requisition_master a,warehouse b,user_activity_management c 
  
  where  a.warehouse_id=b.warehouse_id and a.req_type="FG"  and a.entry_by=c.user_id '.$con.'  order by a.manual_req_no asc';
echo link_report($res,'mr_print_view.php');

}
?>
</div></td>
</tr>
</table>


<?
$main_content=ob_get_contents();
ob_end_clean();
include ("../../template/main_layout.php");
?>