<?php
require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";



$title='Batch Wise Production Receive';



//do_calander('#tdate');



//do_calander('#odate');



$batch_no=$_REQUEST['batch_no'];



$pr_no=$_REQUEST['request_no'];

 



$data_found = $pr_no;



if ($data_found==0) {

do_calander('#pr_date');

//create_combobox('fg_item_id');

  }





if(prevent_multi_submit()){





if(isset($_POST['master_data'])){

	

	$status = 'MANUAL';

	$entry_by = $_SESSION['user']['id'];

	$entry_at=date('Y-m-d H:i:s');

	

	$batch_no = $_POST['batch_no'];

	$batch_qty = $_POST['batch_qty'];

	$unit_name = $_POST['unit_name'];

	$group_for = $_POST['group_for'];

	$fg_item_id = $_POST['fg_item_id'];

	

	

	$pr_date = $_POST['pr_date'];

	$warehouse_id = $_POST['warehouse_id'];
$section_id = $_POST['section_id'];
	$pr_qty = $_POST['pr_qty'];

	

	$inv_type='FG-';

	$inv_type_b='BATCH-';

		

	//$pr_date = $_POST['so_date'];

	//$fg_item_id = $_POST['fg_item_id'];

	//$item_watt = find_a_field('item_info','item_watt','item_id="'.$_POST['fg_item_id'].'"');

	//$group_for=$_SESSION['user']['group'];

	//$warehouse_id=$_SESSION['user']['depot'];

	

	

		$YR = date('Y',strtotime($pr_date));

 		$year = date('y',strtotime($pr_date));

  		$month = date('m',strtotime($pr_date));

 		//$inv_cy_id = find_a_field('production_receive_master','max(inv_id)','year="'.$YR.'"')+1;

		$inv_cy_id_batch = find_a_field('production_receive_master','max(inv_id)','year="'.$YR.'"')+1;

  		//$cy_id = sprintf("%04d", $inv_cy_id);

		$cy_id_batch = sprintf("%04d", $inv_cy_id_batch);

   		$pr_no=''.$year.''.$month.'01'.$cy_id_batch;

		//$batch_no=''.$year.''.$month.'01'.$cy_id_batch;

		

		

		$bom_data = find_all_field('bom_master','','bom_no='.$bom_no);

	



	//$tr_no = date('ymd',strtotime($tr_date));

//	

//	$lot_cy_id = find_a_field('import_purchase_details','max(lot_id)','tr_no="'.$tr_no.'"')+1;

//		

//   	$cy_id = sprintf("%04d", $lot_cy_id);

//	

//   	 $lot_no_generate='LOT'.$tr_no.''.$cy_id;

	 

	 if($fg_item_id>0) {

	

	 

	 

	 

	  $ins_sql1 = 'INSERT INTO production_receive_master (pr_no, year, inv_id, inv_type, pr_date, group_for, warehouse_id, batch_no, fg_item_id, unit_name, batch_qty, pr_qty, status, entry_at, entry_by,section_id) VALUES



("'.$pr_no.'", "'.$YR.'", "'.$cy_id_batch.'", "'.$inv_type.'", "'.$pr_date.'", "'.$group_for.'", "'.$warehouse_id.'", "'.$batch_no.'", "'.$fg_item_id.'", "'.$unit_name.'", "'.$pr_qty.'", "'.$pr_qty.'", "'.$status.'", "'.$entry_at.'", "'.$entry_by.'", "'.$section_id.'")';

	

	db_query($ins_sql1);

	

	

	 $ins_sql2 = 'INSERT INTO production_receive_detail (pr_no, pr_date, batch_no, group_for, warehouse_id, item_id, total_unit, unit_price, total_amt, status, entry_at, entry_by) VALUES



("'.$pr_no.'", "'.$pr_date.'", "'.$batch_no.'", "'.$group_for.'", "'.$warehouse_id.'", "'.$fg_item_id.'", "'.$pr_qty.'", "'.$unit_price.'", "'.$total_amt.'", "'.$status.'", "'.$entry_at.'", "'.$entry_by.'")';

	

	db_query($ins_sql2);

	}

	

	

	 $sql1 = 'select * from batch_factory_overhead where batch_no="'.$batch_no.'"  order by id';

	 

	 $batchQty=find_a_field('batch_master','batch_qty','batch_no="'.$batch_no.'"');



		$query1 = db_query($sql1);



		while($data1=mysqli_fetch_object($query1))



		{

		

		$primeAmt=$data1->batch_amt/$batchQty;

		

		$final_foe_amt=($primeAmt*$pr_qty)*1;





 $foe_pro='INSERT INTO production_factory_overhead (pr_no, pr_date, batch_no, group_for, warehouse_id, fg_item_id, pr_qty, ledger_id, batch_qty, batch_rate, batch_amt, final_foe_rate, final_foe_amt, status, entry_at, entry_by)

  

  VALUES("'.$pr_no.'", "'.$pr_date.'", "'.$batch_no.'", "'.$group_for.'", "'.$warehouse_id.'", "'.$fg_item_id.'", "'.$pr_qty.'", "'.$data1->ledger_id.'", "'.$pr_qty.'", "'.$data1->foe_rate.'", "'.$data1->final_amt.'", "'.$data1->foe_rate.'", "'.$final_foe_amt.'", "'.$status.'", "'.$entry_at.'", "'.$entry_by.'")';



db_query($foe_pro);









}



	









?>

<script language="javascript">

window.location.href = "invoice_entry.php?request_no=<?=$pr_no?>";

</script>



<? 

		

}







if(isset($_POST['confirm'])){

	
	
	echo "<pre>";
	
	var_dump($_POST);
	echo "</pre>";

	$status = 'UNCHECKED';

	$entry_by = $_SESSION['user']['id'];

	$entry_at=date('Y-m-d H:i:s');

	$pr_no = $_POST['pr_no'];

	$pr_date = $_POST['pr_date'];

	$batch_no = $_POST['batch_no'];

	

	$group_for = $_POST['group_for'];

	$warehouse_id = $_POST['warehouse_id'];
$section_id = $_POST['section_id'];
	$fg_item_id = $_POST['fg_item_id'];	

	

	$tot_foe_cost = $_POST['tot_foe_cost'];	

	$tot_rm_cost = $_POST['tot_rm_cost'];

	

	

	$tot_production_cost = $tot_foe_cost+$tot_rm_cost;

	

	

	$pr_qty = $_POST['pr_qty'];	

	

	

	$sql_by = "select * from batch_by_product  where batch_no=".$batch_no."";

	$queryby = db_query($sql_by);

	

	while($databy =mysqli_fetch_object($queryby)){

	

		if($databy->final_unit>0){

			

			$rate = ((($databy->rate_ratio / 100) * $tot_production_cost)/$databy->final_qty);

			

			 $updateby = "update  batch_by_product  set unit_price = ".$rate." where id=".$databy->id."";

			db_query($updateby);

			

			$amount_by += $databy->final_qty* $rate;

			

			//journal_item_control($databy->item_id ,$section_id,$pr_date,$databy->final_qty,0,'Production Receive',$pr_no,$rate,0,$pr_no,'','',$_SESSION['user']['group'],'');

		

		}

		

	

	}

	

	$tot_production_cost = $tot_production_cost-$amount_by;

	

	$pr_unit_price = $tot_production_cost/$pr_qty;

	

	

	

	

	

	$sql = 'select a.*, s.sub_group_name, i.finish_goods_code, i.item_name, i.unit_name from batch_raw_material a, item_info i, item_sub_group s where i.item_id=a.item_id  and i.sub_group_id=s.sub_group_id and a.batch_no="'.$batch_no.'" and a.final_qty>0 order by i.sub_group_id, i.item_name';



		$query = db_query($sql);



		while($data=mysqli_fetch_object($query))



		{



			if($_POST['actual_unit_'.$data->id]>0)



			{

			

				$item_id=$_POST['item_id_'.$data->id];

				$unit_price=$_POST['unit_price_'.$data->id];

				$total_unit=$_POST['actual_unit_'.$data->id];

				$total_amt=$_POST['total_amt_'.$data->id];

			

				



  $rm_invoice = 'INSERT INTO production_rm_consumption (pr_no, pr_date, batch_no, group_for, warehouse_id, fg_item_id, item_id, batch_qty, batch_unit, batch_cal_qty, rm_per_unit, total_unit, unit_price, total_amt, status, entry_at, entry_by, pr_qty)

  

  VALUES("'.$pr_no.'", "'.$pr_date.'", "'.$batch_no.'", "'.$group_for.'", "'.$warehouse_id.'", "'.$fg_item_id.'", "'.$item_id.'", "'.$data->batch_qty.'", "'.$data->final_unit.'", "'.$data->final_qty.'", "'.$data->final_unit.'", "'.$total_unit.'", "'.$unit_price.'", "'.$total_amt.'", "'.$status.'", "'.$entry_at.'", "'.$entry_by.'", "'.$pr_qty.'")';



db_query($rm_invoice);





//journal_item_control($item_id,$warehouse_id,$pr_date,0,$total_unit,'Consumption',$data->id,$unit_price,0,$pr_no,'','',$_SESSION['user']['group'],'');







}



}



	//if($find_foe_data>0 && $find_rm_data>0) {}

	

	$con_sql1="UPDATE production_receive_master SET cost_price='".$pr_unit_price."', cost_amt='".$tot_production_cost."', status='".$status."' WHERE pr_no = '".$pr_no."' ";

	 db_query($con_sql1);

	 

	 $con_sql2="UPDATE production_receive_detail SET unit_price='".$pr_unit_price."', total_amt='".$tot_production_cost."', status='".$status."' WHERE batch_no = '".$batch_no."' and  pr_no = '".$pr_no."'";

	 db_query($con_sql2);

	 

	 //journal_item_control($fg_item_id ,$section_id,$pr_date,$pr_qty,0,'Production Receive',$pr_no,$pr_unit_price,0,$pr_no,'','',$_SESSION['user']['group'],'');



	 

	 $fg_ledger = find_a_field('item_info i,item_sub_group s','s.item_ledger',' i.sub_group_id=s.sub_group_id and i.item_id='.$fg_item_id);

	 

	 ////factory overhead Cr  foe_amt_

	 $jv=next_journal_voucher_id('','Production Receive');

	 $tr_from ='Production Receive';

	 $oh_sum = 0;

   $oh_sql = "select l.group_name, a.ledger_id, a.ledger_name

	from ledger_group l, accounts_ledger a where l.group_id=a.ledger_group_id and a.ledger_group_id in (424002)

	group by a.ledger_id";


	// $oh_sql="select * from factory_overhead where 1 group by ledger_id";

	 $oh_query = db_query($oh_sql);


	 while($oh_data=mysqli_fetch_object($oh_query)){
	 
	 
	 
	  if($_POST['foe_amt_'.$oh_data->ledger_id]>0){
	  
	  $overhead_data=$_POST['foe_amt_'.$oh_data->ledger_id];
	 
	 $oh_sum +=$overhead_data;
	 
	 
	 
	 	// $oh_insert="INSERT INTO `secondary_journal` (`proj_id`, `jv_no`, `jv_date`, `ledger_id`, `narration`, `dr_amt`, `cr_amt`, `tr_from`, `tr_no`, `sub_ledger`, `cc_code`, `entry_by`, `tr_id`, `group_for`, `entry_at`) VALUES ('".$_SESSION['proj_id']."', '".$jv."', '".$pr_date."', '".$oh_data->ledger_id."','', '0.00',".$overhead_data.", '".$tr_from."', '".$pr_no."', '', '0', '".$_SESSION['user']['id']."', '', '".$_SESSION['user']['group']."', '".date('Y-m-d H:i:s')."')";

	 //db_query($oh_insert);
	 
	 
		 }

	 }

	 

	//raw material  Cr

	   $all_pr=find_all_field('production_receive_master','*','pr_no="'.$pr_no.'"');

	 $wip_ledger = find_a_field('warehouse','ledger_id','warehouse_id="'.$all_pr->warehouse_id.'"');

	 $rm_sql="select sum(total_amt) as total_amt,id,pr_no,pr_date from production_rm_consumption where pr_no='".$pr_no."'";

	 $rm_query = db_query($rm_sql);

	 

	 

	 while($rm_data=mysqli_fetch_object($rm_query)){

	 $rm_sum +=$rm_data->total_amt;

	 

	 //$rm_insert="INSERT INTO `secondary_journal` (`proj_id`, `jv_no`, `jv_date`, `ledger_id`, `narration`, `dr_amt`, `cr_amt`, `tr_from`, `tr_no`, `sub_ledger`, `cc_code`, `entry_by`, `tr_id`, `group_for`, `entry_at`) VALUES ('".$_SESSION['proj_id']."', '".$jv."', '".$pr_date."', '".$wip_ledger."','', '0.00',".$rm_data->total_amt.", '".$tr_from."', '".$pr_no."', '', '0', '".$_SESSION['user']['id']."', '', '".$_SESSION['user']['group']."', '".date('Y-m-d H:i:s')."')";

	// db_query($rm_insert);

	 

	 }

	 //Item Sub Group Dr

	 //$fg_insert="INSERT INTO `secondary_journal` (`proj_id`, `jv_no`, `jv_date`, `ledger_id`, `narration`, `dr_amt`, `cr_amt`, `tr_from`, `tr_no`, `sub_ledger`, `cc_code`, `entry_by`, `tr_id`, `group_for`, `entry_at`) VALUES ('".$_SESSION['proj_id']."', '".$jv."', '".$pr_date."', '".$fg_ledger."','',".($rm_sum+$oh_sum).",'0.00', '".$tr_from."', '".$pr_no."', '', '0', '".$_SESSION['user']['id']."', '', '".$_SESSION['user']['group']."', '".date('Y-m-d H:i:s')."')";

	 //db_query($fg_insert);

	 

	 



	//sec_journal_journal($jv,$jv,$tr_from);

	 

	 

	 $batchTotQty=find_a_field('batch_master','batch_qty','batch_no="'.$batch_no.'"');

	 

	 $productionQty=find_a_field('production_receive_detail','sum(total_unit)','batch_no="'.$batch_no.'"');

	  

	  if($productionQty==$batchTotQty){

	  db_query('update batch_master set status="COMPLETED" where batch_no="'.$batch_no.'"');

	  }

	 

	 

	 

	 

	 

	 

	 ?>



<script language="javascript">

window.location.href = "batch_fg_receive.php";

</script>



<? 

		

}













if(isset($_POST['delete'])){

	

	$status = 'CHECKED';

	$entry_by = $_SESSION['user']['id'];

	$entry_at=date('Y-m-d H:i:s');

		$pr_no = $_POST['pr_no'];

	$batch_no = $_POST['batch_no'];

	$bom_no = $_POST['bom_no'];

	

	$sql_del_ms = 'delete from production_receive_master where  pr_no="'.$pr_no.'"';

	db_query($sql_del_ms);

	

	$sql_del_foe = 'delete from production_receive_detail where pr_no="'.$pr_no.'"';

	db_query($sql_del_foe);
 
	

	$sql_del_rm = 'delete from production_rm_consumption where pr_no="'.$pr_no.'"';

	db_query($sql_del_rm);
	
	
		$sql_del_fo = 'delete from production_factory_overhead where pr_no="'.$pr_no.'"';

	db_query($sql_del_fo);
	
	
		$con_sql1="UPDATE batch_master SET status='".$status."', checked_by='".$entry_by."', checked_at='".$entry_at."' WHERE batch_no = '".$batch_no."' ";

			db_query($con_sql1);


	

	 ?>



<script language="javascript">

window.location.href = "invoice_entry.php";

</script>



<? 

		

}















}

















?>



<script>








function getXMLHTTP() { //fuction to return the xml http object







		var xmlhttp=false;	







		try{







			xmlhttp=new XMLHttpRequest();







		}







		catch(e)	{		







			try{			







				xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");



			}



			catch(e){







				try{







				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");







				}







				catch(e1){







					xmlhttp=false;







				}







			}







		}







		 	







		return xmlhttp;







    }

	

	

	function count()

{





//if(document.getElementById('unit_price').value!=''){}





var rate = ((document.getElementById('rate').value)*1);



//var total_unit = (document.getElementById('total_unit').value) = pkt_unit*pkt_size;



var qty = ((document.getElementById('qty').value)*1);



var amount = (document.getElementById('amount').value) = rate*qty;



}





	

	



function TRcalculation(order_no){



var po_qty = document.getElementById('po_qty_'+order_no).value*1;



var unit_price = document.getElementById('unit_price_'+order_no).value*1;



var adj_qty = document.getElementById('adj_qty_'+order_no).value*1;



var adj_amt = document.getElementById('adj_amt_'+order_no).value= (unit_price*adj_qty);



 if(po_qty<adj_qty)

  {

alert('You can`t reduce the qty');

document.getElementById('adj_qty_'+order_no).value='';

document.getElementById('adj_amt_'+order_no).value='';

  } 



}









function TRcalculationpo(order_no){



var rate = document.getElementById('rate_'+order_no).value*1;



var qty = document.getElementById('qty_'+order_no).value*1;





var amount = document.getElementById('amount_'+order_no).value= (rate*qty);



// if(po_qty<adj_qty)

//  {

//alert('You can`t reduce the qty');

//document.getElementById('adj_qty_'+order_no).value='';

//document.getElementById('adj_amt_'+order_no).value='';

//  } 



}







	function update_value(order_no)



	{



var order_no=order_no; // Rent



var item_id=(document.getElementById('item_id_'+order_no).value);



var batch_qty=(document.getElementById('batch_qty_'+order_no).value);

var actual_qty=(document.getElementById('actual_unit_'+order_no).value);

//var amount=(document.getElementById('amount_'+order_no).value);

var flag=(document.getElementById('flag_'+order_no).value); 



var strURL="item_revise_ajax.php?order_no="+order_no+"&item_id="+item_id+"&batch_qty="+batch_qty+"&actual_qty="+actual_qty+"&flag="+flag;



		var req = getXMLHTTP();







		if (req){







			req.onreadystatechange = function() {



				if (req.readyState == 4) {



					// only if "OK"



					if (req.status == 200) {						



						document.getElementById('divi_'+order_no).style.display='inline';



						document.getElementById('divi_'+order_no).innerHTML=req.responseText;

						

						window.location.reload();

						

				



					} else {



						alert("There was a problem while using XMLHTTP:\n" + req.statusText);



					}



				}				



			}



			req.open("GET", strURL, true);



			req.send(null);



		}	





}





	function update_value2(order_no)



	{



var order_no=order_no; // Rent



var item_id=(document.getElementById('item_id_by_'+order_no).value);



var batch_qty=(document.getElementById('batch_qty_by_'+order_no).value);

var actual_qty=(document.getElementById('actual_unit_by_'+order_no).value);

//var amount=(document.getElementById('amount_'+order_no).value);

var flag=(document.getElementById('flag_by_'+order_no).value); 



var strURL="item_revise_by_ajax.php?order_no="+order_no+"&item_id="+item_id+"&batch_qty="+batch_qty+"&actual_qty="+actual_qty+"&flag="+flag;



		var req = getXMLHTTP();







		if (req) {







			req.onreadystatechange = function() {



				if (req.readyState == 4) {



					// only if "OK"



					if (req.status == 200) {						



						document.getElementById('divi_'+order_no).style.display='inline';



						document.getElementById('divi_'+order_no).innerHTML=req.responseText;

						

						window.location.reload();					



					} else {



						alert("There was a problem while using XMLHTTP:\n" + req.statusText);



					}



				}				



			}



			req.open("GET", strURL, true);



			req.send(null);



		}

		

		

		

		





}













function SUMcalculation(ledger_id){

//var unit_price = (document.getElementById('unit_price_'+item_id).value)*1;

var foe_amt = (document.getElementById('foe_amt_'+ledger_id).value)*1;

//document.getElementById('foe_amt_'+item_id).value = unit_price*qty;



var foe_amt = 0;



<?



	$sql = "select l.group_name, a.ledger_id, a.ledger_name

	from ledger_group l, accounts_ledger a where l.group_id=a.ledger_group_id and a.ledger_group_id=424002

	order by l.group_id, a.ledger_id";

$query = db_query($sql);

while($data=mysqli_fetch_object($query)){

?>

foe_amt = foe_amt + document.getElementById('foe_amt_<?=$data->ledger_id?>').value*1;;

<?

}



?>



document.getElementById('foe_amt').value = foe_amt;



}

















</script>













<style>

/*

.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited, a.ui-button, a:link.ui-button, a:visited.ui-button, .ui-button {

    color: #454545;

    text-decoration: none;

    display: none;

}*/





div.form-container_large input {

    width: 220px;

    height: 38px;

    border-radius: 0px !important;

}







</style>



<div class="form-container_large">





<form action="" method="post" name="codz" id="codz">



<? if ($data_found==0) { ?>

<? $batch_data = find_all_field('batch_master','','batch_no="'.$batch_no.'"'); ?>

<div class="box" style="width:100%;">



								<table width="100%" border="0" cellspacing="0" cellpadding="0">

 <tr>
     <td style="background-color: yellow;" width="15%" align="right" ><strong>Total Batch Qty: </strong></td>

    <td style="background-color: yellow;" width="16%" ><?=$batch_data->batch_qty?></td>
	
	     <td style="background-color: yellow;" width="15%" align="right" ><strong>Batch receive Qty: </strong></td>

    <td style="background-color: yellow;" width="16%" ><?=find_a_field('production_receive_detail','sum(total_unit)','batch_no="'.$batch_no.'" and status not in ("MANUAL")');?></td>
 </tr>

								  <tr>







    <td width="9%" align="right" ><strong> Batch No: </strong></td>

    <td width="16%" >

	

	

	<input name="batch_no" type="hidden" id="batch_no" style="width:90%; height:32px;" value="<?=$batch_data->batch_no;?>" readonly="" required tabindex="1" />

	<input name="batch_qty" type="hidden" id="batch_qty" style="width:90%; height:32px;" value="<?=$batch_data->batch_qty;?>" readonly="" required tabindex="1" />

	<input name="unit_name" type="hidden" id="unit_name" style="width:90%; height:32px;" value="<?=$batch_data->unit_name;?>" readonly="" required tabindex="1" />

	<input name="group_for" type="hidden" id="group_for" style="width:90%; height:32px;" value="<?=$batch_data->group_for;?>" readonly="" required tabindex="1" />

	<a href="../fg_from_pl/invoice_print_view.php?bom_no=<?=$batch_no;?>" target="_blank"><input name="batch" type="text" id="batch" style="width:90%; height:32px;" value="<?=$batch_data->inv_type;?><?=$batch_data->batch_no;?>" readonly="" required tabindex="1" /></a>	</td>

	

	 <td width="13%" ><strong>  Product: </strong></td>

	 <td width="23%" >

	 <select name="fg_item_id" id="fg_item_id" style="width:200px;" >

		<? foreign_relation('item_info','item_id','item_name',$_POST['fg_item_id'],'item_id="'.$batch_data->fg_item_id.'"');?>

      </select></td>

	 <td width="9%" ><strong>BATCH Qty: </strong></td>

	 <td width="13%" ><input name="batch_tot_qty" type="text" id="batch_tot_qty" style="width:90%; height:32px;" value="<?=$batch_data->batch_qty-find_a_field('production_receive_detail','sum(total_unit)','batch_no="'.$batch_no.'" and status not in ("MANUAL")');?> <?=$batch_data->unit_name;?> " required readonly="" tabindex="1" /></td>

	 <td width="17%" rowspan="8" align="center" ><strong>



      <input type="submit" name="master_data" id="master_data" value="Initiate Entry" style="width:180px; text-align:center; font-weight:bold; font-size:12px; height:30px; color:#090"/>



    </strong></td>

    </tr>

								  <tr>

								    <td align="right" ><strong> PR Date: </strong></td>

								    <td ><input name="pr_date" type="text" id="pr_date" style="width:90%; height:32px;" value="<?=$pr_date;?>"  required tabindex="1" autocomplete="off" /></td>

								    <td ><strong> Factory Unit: </strong></td>

								    <td >

									<select name="warehouse_id" id="warehouse_id" style="width:200px;" required >

									

										<? foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['warehouse_id'],'warehouse_id="'.$batch_data->warehouse_id.'"');?>

     								 </select>

									</td>

								    <td ><strong>PR Qty:</strong>  </td>

								    <td ><input name="pr_qty" type="number" id="pr_qty" style="width:90%; height:32px;" onkeyup="qtyLimit(this.value)" value="<?=$pr_qty;?> " required tabindex="1" /></td>

							      </tr>
								  <tr>

			<td>
									Section 
									</td>
									<td>
											<select name="section_id" id="section_id" style="width:200px;" required >



									<option></option>



										<? foreign_relation('warehouse','warehouse_id','warehouse_name',$batch_data->section_id,'use_type="SC"');?>



     								 </select>
									</td>
</tr>

								</table>



</div>





<? }?>







<? if ($data_found>0) { ?>



<div class="box" style="width:100%;">



								<table width="100%" border="0" cellspacing="0" cellpadding="0">



								  <tr>







    <td width="15%" align="right" ><strong> PR No: </strong></td>







    <td width="21%" >

	<? 

	$ms_data = find_all_field('production_receive_master','','pr_no="'.$_REQUEST['request_no'].'"');

	$bch_data = find_all_field('batch_master','','batch_no="'.$ms_data->batch_no.'"');

	 ?>

	 

	 <input name="pr_no" type="hidden" id="pr_no" style="width:90%; height:32px;" value="<?=$ms_data->pr_no;?>" readonly="" required tabindex="1" />

	 

	 <input name="pr_qty" type="hidden" id="pr_qty" style="width:90%; height:32px;" value="<?=$ms_data->pr_qty;?>" readonly="" required tabindex="1" />

	 

	<input name="batch_no" type="hidden" id="batch_no" style="width:90%; height:32px;" value="<?=$ms_data->batch_no;?>" readonly="" required tabindex="1" />

	<input name="batch_qty" type="hidden" id="batch_qty" style="width:90%; height:32px;" value="<?=$ms_data->batch_qty;?>" readonly="" required tabindex="1" />

	

	

	<input name="group_for" type="hidden" id="group_for" style="width:90%; height:32px;" value="<?=$ms_data->group_for;?>" readonly="" required tabindex="1" />

	<input name="fg_item_id" type="hidden" id="fg_item_id" style="width:90%; height:32px;" value="<?=$ms_data->fg_item_id;?>" readonly="" required tabindex="1" />

	

	<input name="pr" type="text" id="pr" style="width:90%; height:32px;" value="<?=$ms_data->inv_type;?><?=$ms_data->pr_no;?>" readonly="" required tabindex="1" /></td>

	

	 <td width="12%" align="right"><strong>  PR Date: </strong></td>

	 <td width="25%" align="right"><input name="pr_date" type="text" id="pr_date" style="width:90%; height:32px;" value="<?=$ms_data->pr_date?>" required readonly="" tabindex="1" /></td>

	 <td width="10%" align="right"><strong>BATCH No: </strong></td>

	 <td width="17%" >

	<a href="../BATCH/invoice_print_view.php?batch_no=<?=$ms_data->batch_no;?>" target="_blank">

	<input name="batch" type="text" id="batch" style="width:90%; height:32px;" 

	value="<?=$bch_data->inv_type;?><?=$bch_data->batch_no;?>" readonly="" required tabindex="1" /></a>	 </td>

	 </tr>

								  <tr>

								    <td align="right" width="15%" ><strong> Factory Unit: </strong></td>

								    <td width="21%" >

									<select name="warehouse_id" id="warehouse_id" style="width:200px;" required >

								

										<? foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['warehouse_id'],'warehouse_id="'.$ms_data->warehouse_id.'"');?>

     								 </select>

							

									</td>

								    <td align="right" width="12%"><strong> Product Name: : </strong></td>

								    <td width="25%" align="right"><input name="item_name" type="text" id="item_name" style="width:90%; height:32px;" value="<?=find_a_field('item_info','item_name','item_id="'.$ms_data->fg_item_id.'"'); ?>" readonly="" required tabindex="1" /></td>

								    <td align="right" width="10%"><strong>Receive Qty: </strong></td>

								    <td width="17%"><input name="quantity" type="text" id="quantity" style="width:90%; height:32px;" value="<?=$ms_data->pr_qty;?> <?=$ms_data->unit_name;?>" readonly="" required tabindex="1" /></td>

							      </tr>
								  <tr>
								  <td>
									Section 
									</td>
									<td>
											<select name="section_id" id="section_id" style="width:200px;" required >



									<option></option>



										<? foreign_relation('warehouse','warehouse_id','warehouse_name',$ms_data->section_id,'use_type="SC"');?>



     								 </select>
									</td>
								  </tr>

								</table>



</div>


<div class="tabledesign2" style="width:100%">





<div class="box text-center">

	<b class="display-4">Raw Material</b>

</div>



<table width="100%" border="0" align="center" id="grp" cellpadding="0" cellspacing="0" style="font-size:12px; text-transform:uppercase;">



  <tr>

    <th width="19%">Category</th>



    <th width="25%">Item Name</th>

    <th width="5%">Unit Name </th>

	<th width="10%">Stock</th>

    <th width="10%"> Price </th>

    <th width="15%">Total Qty </th>

    <th width="10%">Amount</th>
    </tr>

  



  <?

  

  

  if($_POST['fdate']!=''&&$_POST['tdate']!='') $date_con .= ' and c.chalan_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';

  

  

		//if($_POST['dealer_code']!='')

// 		$dealer_con=" and m.dealer_code='".$_POST['dealer_code']."'";

//		

//		if($_POST['do_no']!='') 

//		$do_con .= ' and m.do_no in ('.$_POST['do_no'].') ';





    if($_POST['po_date']!='')

	$po_dt_con=" and m.po_date='".$_POST['po_date']."'";

	

	

	

	 	 $sql = "SELECT item_id, final_price
FROM journal_item AS ji1
WHERE id = (
    SELECT MAX(id)
    FROM journal_item AS ji2
    WHERE ji1.item_id = ji2.item_id
) group by item_id";

		 $query = db_query($sql);

		 while($info=mysqli_fetch_object($query)){

  		 $final_price[$info->item_id]=$info->final_price;

	

		}

		

		

		$sql = "select  item_id, sum(item_in-item_ex) as stock from journal_item where warehouse_id=".$ms_data->warehouse_id."  group by item_id   order by id desc ";

		 $query = db_query($sql);

		 while($info=mysqli_fetch_object($query)){

  		 $final_stock[$info->item_id]=$info->stock;

	

		}





    $sqlfg = "select a.*, s.sub_group_name, i.finish_goods_code, i.item_name, i.unit_name from batch_raw_material a, item_info i, item_sub_group s where i.item_id=a.item_id  and i.sub_group_id=s.sub_group_id and a.batch_no='".$ms_data->batch_no."' and a.final_qty>0 order by i.sub_group_id, i.item_name";



  $queryfg = db_query($sqlfg);



  while($datafg=mysqli_fetch_object($queryfg)){$i++;

  
$total_qty=$datafg->final_qty/$datafg->batch_qty*$ms_data->pr_qty; 
  

 $sstk=$final_stock[$datafg->item_id];



$checkStock[]=($sstk>0 && $sstk>=$total_qty)?1:0;



  ?>







<? //if($po_no[$data->po_no]==0) { } ?>



  <tr bgcolor="<?=($i%2)?'#E8F3FF':'#fff';?>">

    <td><?=$datafg->sub_group_name?></td>



    <td> <?=$datafg->item_name?> </td>

    <td bgcolor="#99CCFF"><?=$datafg->unit_name?></td>

	<td bgcolor="#99CCFF"><input name="stock_<?=$datafg->id?>" type="text" id="stock_<?=$datafg->id?>" value="<?=$sstk; ?>" onkeyup="TRcalculationpo(<?=$data->id?>)" style="width:80px; height:30px; " readonly="" /></td>

	

    <td bgcolor="#99CCFF"><input name="unit_price_<?=$datafg->id?>" type="text" id="unit_price_<?=$datafg->id?>" value="<?=$final_price[$datafg->item_id]; ?>" onkeyup="TRcalculationpo(<?=$data->id?>)" style="width:80px; height:30px; " readonly="" />
	<input name="item_id_<?=$datafg->id?>" type="hidden" id="item_id_<?=$datafg->id?>" value="<?=$datafg->item_id; ?>"  />
	</td>

	
    <td bgcolor="#99CCFF"><input name="actual_unit_<?=$datafg->id?>" type="text" id="actual_unit_<?=$datafg->id?>" value="<?=$total_qty ?>" onkeyup="TRcalculationpo(<?=$data->id?>)" style="width:80px; height:30px; " readonly="" /></td>

    <td bgcolor="#99CCFF"><input name="total_amt_<?=$datafg->id?>" type="text" id="total_amt_<?=$datafg->id?>" value="<?=$total_amt=$total_qty*$final_price[$datafg->item_id]; $grand_total_amt +=$total_amt; ?>" style="width:80px; height:30px; " readonly="" /></td>
    </tr>

	

	 <? } ?>

  <tr bgcolor="<?=($i%2)?'#E8F3FF':'#fff';?>">

    <td>&nbsp;</td>

    <td style="font-size:18px;"><strong>TOTAL</strong></td>

    <td bgcolor="#99CCFF">&nbsp;</td>

	<td bgcolor="#99CCFF">&nbsp;</td>

    <td bgcolor="#99CCFF">&nbsp;</td>

    <td bgcolor="#99CCFF">&nbsp;</td>

    <td bgcolor="#99CCFF" style="font-size:18px;"><strong><input name="tot_rm_cost" id="tot_rm_cost" type="text" size="10"  value="<?=$grand_total_amt;?>" style=" height:30px;" readonly="" /></strong></td>
  </tr>
</table>

</div>




<div class="tabledesign2" style="width:100%">



<div class="box text-center">



<b class="display-4">Rejected/By Product</b>



</div>



<table width="100%" border="0" align="center" id="grp" cellpadding="0" cellspacing="0" style="font-size:12px; text-transform:uppercase;">



<tr>



<th width="19%">Category</th>



<th width="10%">Item Code</th>



<th width="37%">Item Name</th>



<th width="10%">Unit Name </th>



<th width="10%">Receive Qty </th>



<th width="10%">Actual Qty </th>



<th>Action</th>



</tr>



<?



if($_POST['fdate']!=''&&$_POST['tdate']!='') $date_con .= ' and c.chalan_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';



//if($_POST['dealer_code']!='')



// 		$dealer_con=" and m.dealer_code='".$_POST['dealer_code']."'";



//		



//		if($_POST['do_no']!='') 



//		$do_con .= ' and m.do_no in ('.$_POST['do_no'].') ';



$sqlby = "select a.*, s.sub_group_name, i.finish_goods_code, i.item_name, i.unit_name from batch_by_product a, item_info i, item_sub_group s where i.item_id=a.item_id  and i.sub_group_id=s.sub_group_id and a.batch_no='".$ms_data->batch_no."' and a.final_qty>0 order by i.sub_group_id, i.item_name";



$queryby = db_query($sqlby);



while($databy=mysqli_fetch_object($queryby)){$i++;



?>



<tr bgcolor="<?=($i%2)?'#E8F3FF':'#fff';?>">



<td><?=$databy->sub_group_name?></td>



<td> <?=$databy->finish_goods_code;?> </td>



<td> <?=$databy->item_name?> </td>



<td bgcolor="#99CCFF"><?=$databy->unit_name?></td>



<td bgcolor="#99CCFF">



<input name="order_no_<?=$databy->id?>" type="hidden" id="order_no_<?=$databy->id?>" value="<?=$databy->id?>" />



<input name="item_id_by_<?=$databy->id?>" type="hidden" id="item_id_by_<?=$databy->id?>" value="<?=$databy->item_id?>" />



<input name="batch_qty_by_<?=$databy->id?>" type="hidden" id="batch_qty_by_<?=$databy->id?>" value="<?=$databy->batch_qty?>" />



<input name="total_unit_by_<?=$databy->id?>" type="text" id="total_unit_by_<?=$databy->id?>" value="<?=$total_qty_bom=$databy->bom_qty*$ms_data->pr_qty; ?>" onkeyup="TRcalculationpo(<?=$data->id?>)" style="width:80px; height:30px; "  readonly="readonly" /></td>



<td bgcolor="#99CCFF">



<input name="actual_unit_by_<?=$databy->id?>" type="text" id="actual_unit_by_<?=$databy->id?>" value="<?=$total_qty=$databy->final_unit; $grand_by_unit +=$total_qty;  ?>" onkeyup="TRcalculationpo(<?=$databy->id?>)" style="width:80px; height:30px; "  /></td>



<td>



<? if($reg_revise[$databy->id]>0) {?>



<center><b>Done!</b></center>



<? }else {?>



<span id="divi_<?=$databy->id?>">



<input name="flag_by_<?=$databy->id?>" type="hidden" id="flag_by_<?=$databy->id?>" value="0" />



<input type="button" name="Button" value="EDIT"  onclick="update_value2(<?=$databy->id?>)" style="width:70px; font-size:12px; height:30px; background-color:#66CC66; font-weight:700;"/>



</span><? }?></td>



</tr>



<? } ?>



<tr bgcolor="<?=($i%2)?'#E8F3FF':'#fff';?>">



<td>&nbsp;</td>



<td style="font-size:18px;" colspan="2"><strong>TOTAL</strong></td>



<td bgcolor="#99CCFF">&nbsp;</td>



<td bgcolor="#99CCFF">&nbsp;</td>



<td bgcolor="#99CCFF" style="font-size:18px;"><strong>



<input name="tot_by_unit" id="tot_by_unit" type="text" size="10"  value="<?=$grand_by_unit;?>" style=" height:30px;" readonly="" />



</strong></td>



<td bgcolor="#99CCFF">&nbsp;</td>



</tr>



</table>



</div>

















<div class="tabledesign2" style="width:100%">



<div class="box text-center">

	<b class="display-4">Factory Overhead</b>

</div>





<table width="100%" border="0" align="center" id="grp" cellpadding="0" cellspacing="0" style="font-size:12px; text-transform:uppercase;">



  <tr>

    <th width="16%">Ledger Group </th>



    <th width="27%">Ledger Name </th>

    <th width="11%">Amount </th>

    </tr>

  



  <?

  



		 $sql = "select ledger_id, ratio  from factory_overhead where 1 group by ledger_id";

		 $query = db_query($sql);

		 while($info=mysqli_fetch_object($query)){

  		 $ratio[$info->ledger_id]=($grand_total_amt*$info->ratio)/100;

		}



		 $sql = "select ledger_id, final_foe_amt as foe_expaa from production_factory_overhead where pr_no='".$pr_no."' group by ledger_id";
		 $query = db_query($sql);
		 while($info=mysqli_fetch_object($query)){
  		 $foe_exp[$info->ledger_id]=$info->foe_expaa;
		}
		

      $sql = "select l.group_name, a.ledger_id, a.ledger_name

	from ledger_group l, accounts_ledger a where l.group_id=a.ledger_group_id and a.ledger_group_id in (424002)

	order by l.group_id, a.ledger_id";



  $query = db_query($sql);



  while($data=mysqli_fetch_object($query)){$i++;



 // $op = find_all_field('journal_item','','warehouse_id="'.$_POST['warehouse_id'].'" and group_for="'.$_POST['group_for'].'" and item_id = "'.$data->item_id.'" and tr_from = "Opening" order by id desc');



  ?>







<? //if($po_no[$data->po_no]==0) { } ?>



  <tr bgcolor="<?=($i%2)?'#E8F3FF':'#fff';?>">

    <td><?=$data->group_name?></td>



    <td> <?=$data->ledger_name?> </td>

    <td bgcolor="#99CCFF" align="right">



<input name="ledger_id_<?=$data->ledger_id?>" type="hidden" id="ledger_id_<?=$data->ledger_id?>" value="<?=$data->ledger_id?>"  onkeyup="SUMcalculation(<?=$data->ledger_id?>)" />	

<input name="foe_amt_<?=$data->ledger_id?>" type="text" id="foe_amt_<?=$data->ledger_id?>" value="<?=$foe_exp[$data->ledger_id]; $tot_foe_exp +=$foe_exp[$data->ledger_id];?>" onkeyup="SUMcalculation(<?=$data->ledger_id?>)"  style="width:120px; height:30px;" readonly="" /></td>



 <? } ?>

    </tr>

  <tr>

    <td bgcolor="#90EE90">&nbsp;</td>

    <td align="center" bgcolor="#90EE90"><div align="center"><strong>Total</strong></div></td>

    <td bgcolor="#90EE90" align="right"><input name="tot_foe_cost" id="tot_foe_cost" type="text" size="10"  value="<?=$tot_foe_exp;?>" style="width:120px; height:30px;" readonly="" /></td>

  </tr>

  

</table>

</div>







<? }?>

<br />







<? if($data_found>0){ ?>




















<br />







<br /><br />







<div class="tabledesign2" style="width:100%">










</div>



<table width="100%" border="0">











<? if($bill_data->status!="COMPLETED") {

if (in_array(0, $checkStock)) {





?>



<h1><center><span style="background-color: yellow; color: red;font-size: 30px !important;">Check Item Stock</span></center></h1>





<? }

else{



?>



<tr>



<td align="center">&nbsp;

<input name="delete" type="submit" class="btn1" value="DELETE" style="width:200px; font-weight:bold; float:left; font-size:12px; height:30px; background:#FF0000;color: #000000; border-radius:50px 20px;" />

</td>



<td align="center">

<!--<input  name="do_no" type="hidden" id="do_no" value="<?=$_POST['do_no'];?>"/>-->

<input name="confirm" type="submit" class="btn1" value="CONFIRM &amp; SEND" style="width:270px; font-weight:bold; float:right; font-size:12px; height:30px;  background: #1E90FF; color: #000000; border-radius: 50px 20px;" /></td>



</tr>

<? } }?>





</table>



<? }?>





<p>&nbsp;</p>



</form>



</div>

<script>

function qtyLimit(data){

var qqqi=$('#batch_tot_qty').val();

var match = qqqi.match(/(\d+(\.\d+)?)/);



if (match) {

    var valuess = parseFloat(match[0]);  

}else{

 var valuess =qqqi;

}



if(valuess<data){



alert('limit');



$('#pr_qty').val('');

}



}



</script>





<?php

require_once SERVER_CORE."routing/layout.bottom.php";
?>