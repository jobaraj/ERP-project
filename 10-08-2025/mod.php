<?

if(!isset($_SESSION))

session_start();

/////////////////////////////////////////////////////////////////

///////////////////// VOUCHER FUNCTIONS /////////////////////////

/////////////////////////////////////////////////////////////////



function sum_com($com,$fdate,$tdate)

{

$sql = 'select sum(j.dr_amt-j.cr_amt) as amt from journal j,accounts_ledger l, ledger_group g where j.group_for='.$_SESSION['user']['group'].' and j.jv_date between "'.$fdate.'" and "'.$tdate.'" and j.ledger_id=l.ledger_id and l.ledger_group_id=g.group_id and g.com_id in ('.$com.')';

$query=db_query($sql);

$a = mysqli_fetch_row($query);

$amount = $a[0];

return $amount;

	}

	

	

	function next_transection_no($depot_id,$chalan_date,$table,$tr_no)

		{

			$sdate = mysqli_format2stamp($chalan_date);

			$e_ch = date('y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'999';

			$s_ch = date('y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'000';

			$unit = 1;

			$sql = 'select max('.$tr_no.') from  '.$table.' where '.$tr_no.' between "'.$s_ch.'" and "'.$e_ch.'" ';

			$query = db_query($sql);

			

			$data=mysqli_fetch_row($query);

			if($data[0]<$s_ch)

			$ch_no = $s_ch+$unit;

			else

			$ch_no = $data[0]+$unit;

			return $ch_no;

		}

	

	

function next_invoice($field,$table,$tr_from='')

	{

			$start=date('y').sprintf("%02d",$_SESSION['user']['group']).'0'.'00001';

			$end=date('y').sprintf("%02d",($_SESSION['user']['group'])).'1'.'00000';

			

			if($tr_from=='')

			$sql="select max(".$field.") from ".$table." where ".$field." between '$start' and '$end'";

			else

			$sql="select max(".$field.") from ".$table." where tr_from='".$tr_from."' and ".$field." between '$start' and '$end'";

			$query=mysqli_fetch_row(db_query($sql));

			$value=$query[0]+1;

			if($query[0] == 0)

			$value=$start;

			return $value;

	}

function next_tr_no($tr_from)

	{

			$start=date('y').sprintf("%02d",$_SESSION['user']['group']).'0'.'00001';

			$end=date('y').sprintf("%02d",($_SESSION['user']['group'])).'1'.'00000';

			$sql="select max(tr_no) from secondary_journal where tr_from='".$tr_from."' and tr_no between '$start' and '$end'";

			$query=@mysqli_fetch_row(@db_query($sql));

			$value=$query[0]+1;

			if($query[0] == 0)

			$value=$start;

			return $value;

	}

function auto_insert_purchase($jv,$pdate,$vendor_ledger,$purchase_ledger,$order_no,$amt,$po_no,$tr_no){



$pdate=date_2_stamp_add_mon_duration($pdate);

//insert to bdt sales acc credit

$journal="INSERT INTO `journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

group_for

)

VALUES ('$jv', '$pdate', '$vendor_ledger', 'Pr No# $po_no (Order No# $order_no)', '0', '$amt', 'Purchase',$tr_no,'".$_SESSION['user']['group']."')";

db_query($journal);



$journal="INSERT INTO `journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

group_for

)

VALUES ( '$jv', '$pdate', '$purchase_ledger', 'Po No# $po_no (Order No# $order_no)', '$amt', '0', 'Purchase','$tr_no','".$_SESSION['user']['group']."')";

db_query($journal);

}



function reinsert_auto_insert_purchase_return_secoundary_update($pr_no){

$jv=next_journal_sec_voucher_id('','Purchase Return');

db_query('delete from secondary_journal where tr_from="Purchase Return" and tr_no = "'.$pr_no.'"');

db_query('delete from journal_item where tr_from="Purchase Return" and sr_no = "'.$pr_no.'"');

	$sql = 'select * from warehouse_other_issue_detail where oi_no = '.$pr_no;

	$query = db_query($sql);

	while($data=mysqli_fetch_object($query)){

			++$i;

			if($i==1){

			$pdate = $data->oi_date;

			$vendor = find_all_field('vendor','ledger_id',"vendor_id=".$data->issued_to);

			$vendor_ledger = $vendor->ledger_id;

			$purchase_ledger = find_a_field('warehouse','ledger_id','warehouse_id='.$data->warehouse_id);

			$amount = $amount + ($data->qty*$data->rate);

			}

	journal_item_control($data->item_id ,$data->warehouse_id,$pdate,$data->qty,0,'Purchase Return',$data->id,$data->rate,'',$data->oi_no);

	}

	$requisition_from = find_a_field('warehouse_other_issue','requisition_from','oi_no='.$pr_no);

	auto_insert_purchase_return_secoundary($jv,strtotime($pdate),$vendor_ledger,$purchase_ledger,$pr_no,$amount,$pr_no,$requisition_from);

}



function auto_insert_purchase_return_secoundary($jv,$pdate,$vendor_ledger,$purchase_ledger,$pr_no,$amt,$po_no,$tr_id,$group_for='',$user_id='',$entry_at=''){







if($group_for==0) $group_for = $_SESSION['user']['group'];

if($user_id==0)   $user_id = $_SESSION['user']['id'];

if($entry_at==0)  $entry_at = date('Y-m-d H:i:s');





$journal="INSERT INTO `secondary_journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('$jv', '$pdate', '$vendor_ledger', 'Purchase Return - $pr_no / SL NO - $tr_id',  '$amt','0', 'Purchase Return','$pr_no','$tr_id','$group_for','$entry_at','$user_id')";

db_query($journal);



$journal="INSERT INTO `secondary_journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ( '$jv', '$pdate', '$purchase_ledger', 'Purchase Return - $pr_no / SL NO - $tr_id',  '0','$amt', 'Purchase Return','$pr_no','$tr_id','$group_for','$entry_at','$user_id')";

db_query($journal);

}



function reinsert_auto_insert_process_issue_secoundary_update($pr_no){

$jv=next_journal_sec_voucher_id('','Reprocess Issue');

db_query('delete from secondary_journal where tr_from="Reprocess Issue" and tr_no = "'.$pr_no.'"');

db_query('delete from journal where tr_from="Reprocess Issue" and tr_no = "'.$pr_no.'"');

db_query('delete from journal_item where tr_from="Reprocess Issue" and sr_no = "'.$pr_no.'"');

$oi = find_all_field('warehouse_other_issue','requisition_from','oi_no='.$pr_no);

$config = find_all_field('config_group_class','issued_to','group_for='.$_SESSION['user']['group']);

	$sql = 'select * from warehouse_other_issue_detail where oi_no = '.$pr_no;

	$query = db_query($sql);

	while($data=mysqli_fetch_object($query)){

			++$i;

			if($i==1){

			$pdate = $data->oi_date;

			$issued_to = $oi->issued_to;

		$val = 'rp_'.$issued_to;

		$vendor_ledger = $config->{$val};

			$purchase_ledger = find_a_field('warehouse','ledger_id','warehouse_id='.$data->warehouse_id);

			$amount = $amount + ($data->qty*$data->rate);

			}

	journal_item_control($data->item_id ,$data->warehouse_id,$pdate,$data->qty,0,'Reprocess Issue',$data->id,$data->rate,'',$data->oi_no);

	}

	$requisition_from = $oi->requisition_from;

	$cc_code = find_a_field_sql('select c.id from cost_center c, warehouse w where c.cc_code=w.acc_code and w.warehouse_id='.$data->warehouse_id);

	auto_insert_process_issue_secoundary($jv,strtotime($pdate),$vendor_ledger,$purchase_ledger,$pr_no,$amount,$pr_no,$requisition_from,$cc_code);

}



function reinsert_auto_insert_process_receive_secoundary_update($pr_no){

$jv=next_journal_sec_voucher_id('','Reprocess Receive');

db_query('delete from secondary_journal where tr_from="Reprocess Receive" and tr_no = "'.$pr_no.'"');

db_query('delete from journal where tr_from="Reprocess Receive" and tr_no = "'.$pr_no.'"');

db_query('delete from journal_item where tr_from="Reprocess Receive" and sr_no = "'.$pr_no.'"');

$oi = find_all_field('warehouse_other_issue','requisition_from','oi_no='.$pr_no);

$config = find_all_field('config_group_class','issued_to','group_for='.$_SESSION['user']['group']);

	$sql = 'select * from warehouse_other_receive_detail where or_no = '.$pr_no;

	$query = db_query($sql);

	while($data=mysqli_fetch_object($query)){

			++$i;

			if($i==1){

			$pdate = $data->or_date;

			$issued_to = $oi->vendor_name;

		$val = 'rp_'.$issued_to;

		$vendor_ledger = $config->{$val};

			$purchase_ledger = find_a_field('warehouse','ledger_id','warehouse_id='.$data->warehouse_id);

			$amount = $amount + ($data->qty*$data->rate);

			}

	journal_item_control($data->item_id ,$data->warehouse_id,$pdate,$data->qty,0,'Reprocess Receive',$data->id,$data->rate,'',$data->or_no);

	}

	$requisition_from = $oi->requisition_from;

	$cc_code = find_a_field_sql('select c.id from cost_center c, warehouse w where c.cc_code=w.acc_code and w.warehouse_id='.$data->warehouse_id);

	auto_insert_process_receive_secoundary($jv,strtotime($pdate),$vendor_ledger,$purchase_ledger,$pr_no,$amount,$pr_no,$requisition_from,$cc_code);

}

function auto_insert_claim_issue_secoundary($jv,$pdate,$vendor_ledger,$purchase_ledger,$pr_no,$amt,$po_no,$tr_id,$cc_code=''){

add_to_sec_journal($proj_id, $jv, $pdate, $vendor_ledger,   'Claim Item Issue - '.$pr_no.' / SL NO - '.$tr_id,$amt,'0', 'Claim Item Issue', $pr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv, $pdate, $purchase_ledger, 'Claim Item Issue - '.$pr_no.' / SL NO - '.$tr_id,'0',$amt, 'Claim Item Issue', $pr_no,'',$tr_id,$cc_code);

}



function auto_insert_claim_receive_secoundary($jv,$pdate,$purchase_ledger,$vendor_ledger,$pr_no,$amt,$po_no,$tr_id,$cc_code=''){

add_to_sec_journal($proj_id, $jv, $pdate, $vendor_ledger,   'Claim Item Receive - '.$pr_no.' / SL NO - '.$tr_id,$amt,'0', 'Claim Item Receive', $pr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv, $pdate, $purchase_ledger, 'Claim Item Receive - '.$pr_no.' / SL NO - '.$tr_id,'0',$amt, 'Claim Item Receive', $pr_no,'',$tr_id,$cc_code);

}



function auto_insert_process_receive_secoundary($jv,$pdate,$purchase_ledger,$vendor_ledger,$pr_no,$amt,$po_no,$tr_id,$cc_code=''){

add_to_sec_journal($proj_id, $jv, $pdate, $vendor_ledger,   'Reprocess Receive - '.$pr_no.' / SL NO - '.$tr_id,$amt,'0', 'Reprocess Receive', $pr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv, $pdate, $purchase_ledger, 'Reprocess Receive - '.$pr_no.' / SL NO - '.$tr_id,'0',$amt, 'Reprocess Receive', $pr_no,'',$tr_id,$cc_code);

}



function auto_insert_process_issue_secoundary($jv,$pdate,$vendor_ledger,$purchase_ledger,$pr_no,$amt,$po_no,$tr_id,$cc_code=''){

add_to_sec_journal($proj_id, $jv, $pdate, $vendor_ledger,   'Reprocess Issue - '.$pr_no.' / SL NO - '.$tr_id,$amt,'0', 'Reprocess Issue', $pr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv, $pdate, $purchase_ledger, 'Reprocess Issue - '.$pr_no.' / SL NO - '.$tr_id,'0',$amt, 'Reprocess Issue', $pr_no,'',$tr_id,$cc_code);

}

function auto_insert_purchase_secoundary($jv,$pdate,$vendor_ledger,$purchase_ledger,$pr_no,$amt,$po_no,$tr_id,$group_for='',$user_id='',$entry_at=''){



$pdate=date_2_stamp_add_mon_duration($pdate);



if($group_for==0) $group_for = $_SESSION['user']['group'];

if($user_id==0) $user_id = $_SESSION['user']['id'];

if($entry_at==0) $entry_at = date('Y-m-d H:i:s');





$journal="INSERT INTO `secondary_journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('$jv', '$pdate', '$vendor_ledger', 'GR#$pr_no/$tr_id(PO#$po_no)', '0', '$amt', 'Purchase',$pr_no,$tr_id,'$group_for','$entry_at','$user_id')";

db_query($journal);



$journal="INSERT INTO `secondary_journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ( '$jv', '$pdate', '$purchase_ledger', 'GR#$pr_no/$tr_id(PO#$po_no)', '$amt', '0', 'Purchase',$pr_no,$tr_id,'$group_for','$entry_at','$user_id')";

db_query($journal);

}



function reinsert_auto_insert_purchase_secoundary_update($pr_no){



$jv=next_journal_sec_voucher_id('','Purchase',$_SESSION['user']['depot']);

db_query('delete from journal where tr_from="Purchase" and tr_no = "'.$pr_no.'"');

db_query('delete from secondary_journal where tr_from="Purchase" and tr_no = "'.$pr_no.'"');

db_query('delete from journal_item where tr_from="Purchase" and sr_no = "'.$pr_no.'"');

	$sql = 'select * from purchase_receive where pr_no = '.$pr_no;

	$query = db_query($sql);

	while($data=mysqli_fetch_object($query)){

			++$i;

			if($i==1){

			$vendor = find_all_field('vendor','ledger_id',"vendor_id=".$data->vendor_id);

			$vendor_ledger = $vendor->ledger_id;

			$group_for = $vendor->group_for;

			$main_item_id = $data->item_id;

			$main_warehouse_id = $data->warehouse_id;

			$rec_date = $data->rec_date;



			}

	journal_item_control($data->item_id ,$data->warehouse_id,$data->rec_date,$data->qty,0,'Purchase',$data->id,$data->rate,'',$data->pr_no);

	

	if($vendor->vendor_type==1){

$sub_group_ledger_sql= "select s.ledger_id_".$vendor->group_for." from item_sub_group s,item_info i where i.item_id='".$main_item_id."' and i.sub_group_id=s.sub_group_id";

$sub_group_ledger = $item_purchase_receive_head = find_a_field_sql($sub_group_ledger_sql);

auto_insert_purchase_secoundary($jv,$rec_date,$vendor_ledger,$item_purchase_receive_head,$pr_no,$data->amount,$data->po_no,$data->id,$vendor->group_for);

}

if($vendor->vendor_type==2){

$sales_ledger = find_a_field('warehouse','ledger_id','warehouse_id='.$main_warehouse_id);

auto_insert_purchase_secoundary($jv,$rec_date,$vendor_ledger,$sales_ledger,$pr_no,$data->amount,$data->po_no,$data->id,$vendor->group_for);

}

	}



	

}



function auto_insert_import_secoundary($or_no){

$or_detail = find_all_field_sql('select sum(amount) amount, or_date,warehouse_id from warehouse_other_receive_detail where or_no = '.$or_no);

$jv_no = next_journal_sec_voucher_id2('','Import');

$narration = 'Import Transection #'.$or_no;

if($or_detail->amount>0){

$warehouse = find_all_field_sql('select w.ledger_id,c.id from warehouse w,cost_center c where c.cc_code=w.acc_code and warehouse_id="'.$or_detail->warehouse_id.'"');

$warehouse_ledger_id = $warehouse->ledger_id;

$warehouse_cc_code = $warehouse->id;



$or_date = $or_detail->or_date;

$sale_ledger_id = '1078000200020000';

add_to_sec_journal('Sajeeb', $jv_no, strtotime($or_date), $warehouse_ledger_id , $narration, $or_detail->amount, 0, 'Import', $or_no,'','',$warehouse_cc_code);

add_to_sec_journal('Sajeeb', $jv_no, strtotime($or_date), $sale_ledger_id,       $narration, 0, $or_detail->amount, 'Import', $or_no,'','',$warehouse_cc_code);

	}

}





function reinsert_auto_insert_import_secoundary($or_no){

db_query('delete from journal where tr_from="Import" and tr_no = "'.$or_no.'"');

db_query('delete from secondary_journal where tr_from="Import" and tr_no = "'.$or_no.'"');

auto_insert_import_secoundary($or_no);

}



function auto_reinsert_sales_chalan_secoundary($chalan_no)

{

	$reinsert = 1;

	db_delete_all('secondary_journal','tr_from = "Sales" AND `tr_no` = "'.$chalan_no.'"');

	db_delete_all('journal','tr_from = "Sales" AND `tr_no` = "'.$chalan_no.'"');

	auto_insert_sales_chalan_secoundary($chalan_no);

}

















function auto_insert_rm_consumption_secoundary($issue_no)

{

	

	

	$sql = 'select issue_no, issue_date, group_for, sum(total_amt) as total_amt from rm_consumption

	 where issue_no="'.$issue_no.'" GROUP by issue_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'Consumption';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','Consumption',$_SESSION['user']['depot']);

			

			$tr_no = $data->issue_no;

			$tr_id = $data->issue_no;

			$jv_date = $data->issue_date;

			

			$narration_dr = 'RM Consumption';

			

			

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $config->wip_ledger, $narration_dr, ($data->total_amt), '0',  $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for,

	'','','','','','','');

				

	

	

	

		$sql2 = 'select s.ledger_id as item_ledger, sum(c.total_amt) as item_amt from rm_consumption c, item_info i, item_sub_group s 

		where c.item_id=i.item_id and i.sub_group_id=s.sub_group_id and c.issue_no="'.$issue_no.'" group by s.sub_group_id order by s.sub_group_id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

		//$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->item_ledger, $narration_dr,   '0',  ($data2->item_amt), $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for,

	'','','','','','','');



		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES", checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}























function auto_insert_fg_production_secoundary($receive_no)

{

	

	

	$sql = 'select receive_no, receive_date, group_for, sum(total_amt) as total_amt from fg_production

	 where receive_no="'.$receive_no.'" GROUP by receive_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'Receive';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','Receive',$_SESSION['user']['depot']);

			

			$tr_no = $data->receive_no;

			$tr_id = $data->receive_no;

			$jv_date = $data->receive_date;

			

			$narration = 'FG Production';

			

			$wip_amt = find_a_field('journal','sum(dr_amt)',"ledger_id='".$config->wip_ledger."' and jv_date='".$data->receive_date."'");

			

			$wip_diff_amt = $data->total_amt-$wip_amt;

			

			if($wip_diff_amt<0){

			$wip_adjustment_amt=$wip_diff_amt*(-1);

			}else{

			$wip_adjustment_amt=$wip_diff_amt;

			}	

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $config->wip_ledger, $narration, '0',  ($wip_amt), $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for);

	

	

	

	

			if($wip_diff_amt<0){

			add_to_sec_journal($proj_id, $jv_no, $jv_date, $config->wip_adjustment, $narration, ($wip_adjustment_amt), '0',   $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for);

			}else{

			add_to_sec_journal($proj_id, $jv_no, $jv_date, $config->wip_adjustment, $narration, '0',  ($wip_adjustment_amt), $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for);

			}		

	

	

	

		$sql2 = 'select s.ledger_id as item_ledger, sum(c.total_amt) as item_amt from fg_production c, item_info i, item_sub_group s 

		where c.item_id=i.item_id and i.sub_group_id=s.sub_group_id and c.receive_no="'.$receive_no.'" group by s.sub_group_id order by s.sub_group_id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

		//$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->item_ledger, $narration,  ($data2->item_amt), '0', $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for);



		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES", checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}















function auto_insert_sales_chalan_secoundary($chalan_no)

{


	$proj_id = 'clouderp'; 

	$do_ch =  find_all_field('sale_do_chalan','do_no','chalan_no='.$chalan_no);

	$group_for =  $_SESSION['user']['group'];

	$do_master = find_all_field('sale_do_master','do_no','do_no='.$do_ch->do_no);

    $dealer = find_all_field('dealer_info','',"dealer_code=".$do_ch->dealer_code);

	

	$jv_no=next_journal_sec_voucher_id('','Sales',$group_for);

    $tr_id = $do_ch->do_no;

	$tr_no = $chalan_no;

	$tr_from = 'Sales';

	$jv_date = $do_ch->chalan_date;

	$narration = 'CH# '.$chalan_no.' (SO# '.$do_ch->do_no.')';

    

	$sql = "select sum(total_amt) as total_amt from sale_do_chalan c where  chalan_no=".$chalan_no;

	

	$ch = find_all_field_sql($sql);



//$cash_discount = $do_master->cash_discount;

//$sales_amt = $ch->total_amt-$cash_discount;

//$vat_on_sales = ($sales_amt*$do_master->vat)/100;

//$invoice_amt = ($sales_amt + $vat_on_sales);



$sales_amt = $ch->total_amt;

$discount_amt = ($ch->total_amt*$do_master->discount)/100;

$amount_after_discount = $ch->total_amt-$discount_amt;

$vat_on_sales = ($sales_amt*$do_master->vat)/100;

$ait_on_sales = ($sales_amt*$do_master->ait)/100;

$invoice_amt = ($amount_after_discount + $vat_on_sales + $ait_on_sales);



	

	//$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$group_for);

	$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	$dealer_ledger= $dealer->account_code;

	$cc_code = 0;


$transport_cost=$do_ch->transport_cost;
//debit	 

if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration, ($invoice_amt+$transport_cost), '0', $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);


if($discount_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->sales_discount, $narration, ($discount_amt), '0', $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);





//credit

if($sales_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->sales_ledger, $narration, '0', ($sales_amt), $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);


$sql='select d.item_id,d.unit_price,d.total_unit, sum(d.unit_price_gift_item*d.total_unit) as tot_gift_amt,i.item_id,i.sub_group_id,s.sub_group_id,s.item_ledger,i.sub_ledger_id from sale_do_chalan d,item_info i,item_sub_group s  where d.item_id=i.item_id and i.sub_group_id=s.sub_group_id and d.chalan_no="'.$chalan_no.'" group by i.sub_group_id';
$query=db_query($sql);
while($srow=mysqli_fetch_object($query)){
if($srow->sub_group_id==$config_ledger->gift_sub_group_id){
$gift_item_group_sales_amt+=$srow->tot_gift_amt;
if($srow->tot_gift_amt>0){
add_to_sec_journal($pbroj_id, $jv_no, $jv_date, $srow->item_ledger, $narration, '0', ($srow->tot_gift_amt), $tr_from, $tr_no,$srow->sub_ledger_id,$tr_id,$cc_code,$group_for);
}
}
else{
$general_item_as_gift_sales+=$srow->tot_gift_amt;
if($srow->tot_gift_amt>0){
add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->free_product_sale, $narration, '0', ($srow->tot_gift_amt), $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);
}
}

$tot_gift_amt_get+=$srow->tot_gift_amt;
}


if($vat_on_sales>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->sales_vat, $narration, '0', ($vat_on_sales), $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);

if($ait_on_sales>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->ait_payable, $narration, '0', ($ait_on_sales), $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);


if($transport_cost>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->transport_challan, $narration, '0', ($transport_cost), $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);

if($tot_gift_amt_get>0)
add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->brand_promotion, $narration, ($tot_gift_amt_get), '0', $tr_from, $tr_no,$dealer->sub_ledger_id,$tr_id,$cc_code,$group_for);









if($chalan_no>0)

{

auto_insert_sales_cogs_secoundary($chalan_no,$jv_no);

}







$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}








function auto_insert_sales_cogs_secoundary($chalan_no,$jv_no)

{

	

	

	$sql = 'select chalan_no, chalan_date, do_no, group_for, sum(cost_amt) as total_amt from sale_do_chalan

	 where chalan_no="'.$chalan_no.'" GROUP by chalan_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'COGS';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','COGS',$_SESSION['user']['depot']);

			

			$tr_no = $data->chalan_no;

			$tr_id = $data->do_no;

			$jv_date = $data->chalan_date;


			$narration = 'CH# '.$chalan_no.' (SO# '.$data->do_no.')';


		 $sql2 = 'select s.item_ledger as item_ledger,s.cogs_ledger as cogs_ledger, c.cost_amt as item_amt,i.sub_ledger_id as item_sub_ledger,c.id as chalan_id,c.item_id,c.chalan_no from sale_do_chalan c, item_info i, item_sub_group s 

		where c.item_id=i.item_id and i.sub_group_id=s.sub_group_id and c.chalan_no="'.$chalan_no.'" order by s.sub_group_id';

		$query2 = db_query ($sql2);

		while($data2=mysqli_fetch_object($query2)){

$item_ji_row_data=find_all_field('journal_item','*','sr_no="'.$data2->chalan_no.'" and tr_no="'.$data2->chalan_id.'" and item_id="'.$data2->item_id.'"');
//echo 'select * from journal_item where sr_no="'.$data2->chalan_no.'" and tr_no="'.$data2->chalan_id.'" and item_id="'.$data2->item_id.'"';
		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->cogs_ledger, $narration,($data2->item_amt), '0',$tr_from, $tr_no,$data2->item_sub_ledger,$tr_id, $cc_code, $group_for,'','','','','','','','','','','','','','','',$data2->item_id,$item_ji_row_data->id);

		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->item_ledger, $narration,   '0', ($data2->item_amt), $tr_from, $tr_no,$data2->item_sub_ledger,$tr_id, $cc_code, $group_for,'','','','','','','','','','','','','','','',$data2->item_id,$item_ji_row_data->id);



		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES", checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}































function auto_insert_sales_receipt_secoundary($receipt_no)

{

	

	

	$sql = 'select receipt_no, receipt_date, dr_ledger_id,chalan_no,sub_ledger_id,sum(receipt_amt) as receipt_amt from receipt_from_customer where receipt_no="'.$receipt_no.'" GROUP by receipt_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);

			$group_for = $_SESSION['user']['group'];

			$cc_code = $group_for;

			$tr_from = 'Receipt';

			$proj_id="clouderp";

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','Receipt',$_SESSION['user']['depot']);

			$tr_no = $data->chalan_no;

			$tr_id = $data->receipt_no;

			$jv_date = $data->receipt_date;

			

			$narration_dr = 'Receipt from customer';

			

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->dr_ledger_id, $narration_dr, ($data->receipt_amt), '0', $tr_from,$tr_no,$data->sub_ledger_id,$tr_id, $cc_code, $group_for);

				
		
		$sql2 = 'select * from receipt_from_customer where receipt_no="'.$receipt_no.'" order by id';
		$query2 = db_query($sql2);

		while($data2=mysqli_fetch_object($query2)){

	

	

		

		$narration_cr ='Invoice# '.$data2->chalan_no.' (SO# '.$data2->do_no.')';

		

		

add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $narration_cr, '0', ($data2->receipt_amt),  $tr_from, $tr_no,$data2->sub_ledger_code, $tr_id,$cc_code,$group_for);



}

			

			

	



//$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



//if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



//$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





//if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





//}



//} else {



//$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



//db_query($sa_up);



//}





}





















function auto_insert_po_payment_secoundary($payment_no)

{

	

	

	$sql = 'select payment_no, payment_date, cr_ledger_id,sub_ledger_id,sum(payment_amt) as payment_amt from payment_vendor where payment_no="'.$payment_no.'" GROUP by payment_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = $group_for;

			$tr_from = 'Payment';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','Payment',$_SESSION['user']['depot']);

			

			$tr_no = $data->payment_no;

			$tr_id = $data->payment_no;

			$jv_date = $data->payment_date;

			

			$narration_cr = 'Vendor Payment';

			

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->cr_ledger_id, $narration_cr,  '0', ($data->payment_amt), $tr_from, $tr_no,$data->sub_ledger_id,$tr_id, $cc_code, $group_for);

				

	$sql2 = 'select * from payment_vendor where payment_no="'.$payment_no.'" order by id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

	

		

		$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

	
add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $narration_dr, ($data2->payment_amt),  '0',  $tr_from, $tr_no, $data2->sub_ledger_code, $data2->pr_no,$cc_code,$group_for);



}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}











function auto_insert_insurance_payment_secoundary($payment_no)

{

	

	

	$sql = 'select payment_no, payment_date, lc_no, lc_number, company_ledger, cr_ledger_id, sum(payment_amt) as payment_amt from insurance_payment_voucher where payment_no="'.$payment_no.'" GROUP by payment_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'LC Journal';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','LC Journal',$_SESSION['user']['depot']);

			

			$tr_no = $data->payment_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->payment_date;

			

			$narration_dr = $data->lc_number;

			

			$narration_cr = 'Insurance Payment';

			

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->company_ledger, $narration_dr,  ($data->payment_amt), '0',  $tr_from, $tr_no,$data->cr_ledger_id,$tr_id, $cc_code, $group_for,

	'','','','','','',$data->cr_ledger_id);

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->cr_ledger_id, $narration_cr,  '0', ($data->payment_amt), $tr_from, $tr_no,$data->company_ledger,$tr_id, $cc_code, $group_for,

	'','','','','','',$data->company_ledger);

				

	

	

	

		//$sql2 = 'select * from payment_vendor where payment_no="'.$payment_no.'" order by id';

//

//		$query2 = db_query($sql2);

//		

//		while($data2=mysqli_fetch_object($query2)){

//	

//		$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

//		

//		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $narration_dr, ($data2->payment_amt),  '0',  $tr_from, $tr_no, '', $data2->pr_no,$cc_code,$group_for);

//

//		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}















function auto_insert_insurance_refund_secoundary($receipt_no)

{

	

	

	$sql = 'select receipt_no, receipt_date, lc_no, lc_number, ledger_id, company_ledger, receipt_dr_ledger, sum(payment_amt) as payment_amt, sum(refund_amt) as refund_amt,

	 sum(insurance_exp) as insurance_exp from insurance_refund_voucher where receipt_no="'.$receipt_no.'" GROUP by receipt_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'LC Journal';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','LC Journal',$_SESSION['user']['depot']);

			

			$tr_no = $data->receipt_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->receipt_date;

			

			$narration_dr = 'Insurance Refund';

			

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->receipt_dr_ledger, $narration_dr,  ($data->payment_amt), '0',  $tr_from, $tr_no,$data->company_ledger,$tr_id, $cc_code, $group_for,

	'','','','','','',$data->company_ledger);

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->company_ledger, $narration_dr,  '0', ($data->payment_amt), $tr_from, $tr_no,$data->receipt_dr_ledger,$tr_id, $cc_code, $group_for,

	'','','','','','',$data->receipt_dr_ledger);

				

	

	

if($tr_no>0)

{

auto_insert_insurance_expense_secoundary($tr_no);



}

	

	

		//$sql2 = 'select * from payment_vendor where payment_no="'.$payment_no.'" order by id';

//

//		$query2 = db_query($sql2);

//		

//		while($data2=mysqli_fetch_object($query2)){

//	

//		$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

//		

//		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $narration_dr, ($data2->payment_amt),  '0',  $tr_from, $tr_no, '', $data2->pr_no,$cc_code,$group_for);

//

//		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}













function auto_insert_insurance_expense_secoundary($receipt_no)

{

	

	

	$sql = 'select receipt_no, receipt_date, lc_no, lc_number, ledger_id, company_ledger, receipt_dr_ledger, sum(payment_amt) as payment_amt, sum(refund_amt) as refund_amt,

	 sum(insurance_exp) as tot_insurance_exp from insurance_refund_voucher where receipt_no="'.$receipt_no.'" GROUP by receipt_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'LC Journal';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','LC Journal',$_SESSION['user']['depot']);

			

			$tr_no = $data->receipt_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->receipt_date;

			

			$narration_dr= find_a_field('lc_bill_category','bill_category',"id=1");

			$narration_cr = 'Insurance Paymeent';

			

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->receipt_dr_ledger, $narration_cr,   '0', ($data->tot_insurance_exp),  $tr_from, $tr_no,$data->company_ledger, $tr_id, $cc_code, $group_for,

	'','','','','','',$data->company_ledger);

	

	

	

		$sql2 = 'select * from insurance_refund_voucher where receipt_no="'.$receipt_no.'" order by id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

		//$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $narration_dr,  ($data2->insurance_exp), '0',  $tr_from, $tr_no, $data2->receipt_dr_ledger,$data2->lc_no, $cc_code, $group_for,

	'','','','','','',$data2->receipt_dr_ledger);



		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}













function auto_insert_lc_all_payment_secoundary($payment_no)

{

	

	 $sql = 'select payment_no, payment_date, payment_method, group_for, lc_no, lc_number, lc_part_no, bill_type, lc_ledger, cr_ledger_id, sum(pay_amt_in) as tot_pay_amt, loan_ledger,sub_ledger_id from lc_bill_payment

	 where payment_no="'.$payment_no.'" GROUP by payment_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



			$proj_id="clouderp";

			$group_for = $data->group_for;

			$cc_code = $group_for;

			

			$bank_cash_group=find_a_field('accounts_ledger','ledger_group_id','ledger_id="'.$data->cr_ledger_id.'"');

		

		//$tr_from = 'LC Journal';

			

			if($data->bill_type==1){

				$tr_from = 'Journal';

			}elseif($data->bill_type==2) {

				$tr_from = 'Payment';

			}elseif($data->bill_type==3) {	

				if($data->loan_ledger>0){

					$tr_from = 'Journal';

				}else{

					$tr_from = 'Payment';

				}	

			}elseif($data->bill_type==4) {

				$tr_from = 'Payment';

			}elseif($data->bill_type==5) {

				$tr_from = 'Payment';

			}elseif($data->bill_type==6) {

				$tr_from = 'Journal';

			}elseif($data->bill_type==7) {

				

				if($bank_cash_group==126001 || $bank_cash_group==126002){

					$tr_from = 'Payment';

				}else{

					$tr_from = 'Journal';

				}	

			

			}elseif($data->bill_type==8) {

				$tr_from = 'Payment';

			}else{

				$tr_from = 'Payment';

			}

			

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('',$tr_from,$_SESSION['user']['group']);

			

			$tr_no = $data->payment_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->payment_date;

			

			//$jv_date = strtotime($data->payment_date);

			

			if($data->bill_type==3 || $data->bill_type==4 || $data->bill_type==5 || $data->bill_type==6) {

			$narration_cr = $data->lc_part_no;

			}else{

			$narration_cr = $data->lc_number;

			}

	

	

//add_to_sec_journal($proj_id,$jv_no, $jv_date, $data->cr_ledger_id, $narration_cr,  0, $data->tot_pay_amt, $tr_from, $tr_no, $data->lc_ledger, $tr_id, $cc_code, $group_for, '','','','',$data->cheque_no, $data->cheque_date, $data->lc_ledger, 'NO', $data->payment_method, 'LC Journal');



add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->cr_ledger_id, $narration_cr,  '0', ($data->tot_pay_amt), $tr_from, $tr_no,$data->sub_ledger_id,$tr_id, $cc_code, $group_for,'','', '', '', '', '',$data->lc_ledger, 'NO', $data->payment_method,'','','','LC Journal', '' );

				

	

	

	

		$sql2 = 'select a.* from lc_bill_payment a, lc_bill_category b where a.bill_category=b.id and a.payment_no="'.$payment_no.'"  order by a.id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

		//$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

		//if($data->bill_type==3) {

//		$narration_dr = $narration_cr;

//		}else {

//		$narration_dr = $data2->category_view;

//		}	

	

	//add_to_sec_journal($proj_id,$jv_no, $jv_date, $data2->lc_ledger, $data2->category_view, ($data2->pay_amt_in),  0, $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for, '','','','',$data->cheque_no, $data->cheque_date, $data2->cr_ledger_id, 'NO', $data2->payment_method, 'LC Journal', $data2->bill_category);

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->lc_ledger, $data2->category_view, ($data2->pay_amt_in), '0', $tr_from, $tr_no, $data2->dr_sub_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data2->bill_category );



	



		}

		

		

		

//		$sql3 = 'select a.*, b.ledger_id from lc_bill_payment a, lc_bill_category b where a.bill_category=b.id and a.payment_no="'.$payment_no.'" and b.ledger_id>0 order by a.id';

//

//		$query3 = db_query($sql3);

//		

//		while($data3=mysqli_fetch_object($query3)){

//

//		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data3->ledger_id, $narration_cr,  ($data3->pay_amt_in), '0', $tr_from, $tr_no,$data3->cr_ledger_id,$tr_id, $cc_code, $group_for,

//		'','','','','','',$data3->cr_ledger_id,'NO');

//

//		}

			

			

	



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');



if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'",checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



}









}





function auto_insert_lc_all_payment_secoundary_new($payment_no)

{	





	

	 $sql = 'select payment_no, payment_date, payment_method, group_for, lc_no, lc_number, lc_part_no, bill_type, lc_ledger, cr_ledger_id, sum(pay_amt_in) as tot_pay_amt, loan_ledger from lc_bill_payment

	 where payment_no="'.$payment_no.'" GROUP by payment_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



			$proj_id="clouderp";

			$group_for = $data->group_for;

			$cc_code = $group_for;

			

			$bank_cash_group=find_a_field('accounts_ledger','ledger_group_id','ledger_id="'.$data->cr_ledger_id.'"');

			

			

			$cr_ledger = find_a_field('lc_bill_payment','cr_ledger_id','payment_no="'.$payment_no.'" and order_no>0');

		

			//$tr_from = 'LC Journal';

			

			

			$tr_from = 'LC Journal';

			

			

			// pre cost

			

			$pre_sql  = "select * from  lc_provision_payment where lc_no=".$data->lc_no." and bill_type=".$data->bill_type."";

			

			$pre_query = db_query($pre_sql);

			

			while($pre_data=mysqli_fetch_object($pre_query)){

			

				$pre_rate[$pre_data->bill_category][$pre_data->order_no] = $pre_data->rate;

			

			}

			

			

			

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('',$tr_from,$_SESSION['user']['group']);

			

			$tr_no = $data->payment_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->payment_date;

			

			//$jv_date = strtotime($data->payment_date);

			

			if($data->bill_type==3 || $data->bill_type==4 || $data->bill_type==5 || $data->bill_type==6 || $data->bill_type==7) {

			$narration_cr = $data->lc_part_no;

			}else{

			$narration_cr = $data->lc_number;

			}

	

	

//add_to_sec_journal($proj_id,$jv_no, $jv_date, $data->cr_ledger_id, $narration_cr,  0, $data->tot_pay_amt, $tr_from, $tr_no, $data->lc_ledger, $tr_id, $cc_code, $group_for, '','','','',$data->cheque_no, $data->cheque_date, $data->lc_ledger, 'NO', $data->payment_method, 'LC Journal');



add_to_sec_journal($proj_id, $jv_no, $jv_date,$cr_ledger, $narration_cr,  '0', ($data->tot_pay_amt), $tr_from, $tr_no,$data->lc_ledger,$tr_id, $cc_code, $group_for,'','', '', '', '', '',$data->lc_ledger, 'NO', $data->payment_method,'','','','LC Journal', '' );

				

	

	

	

		//$sql2 = 'select a.* from lc_bill_payment a, lc_bill_category b where a.bill_category=b.id and a.payment_no="'.$payment_no.'"  order by a.id';

		

		

		

		

		$sql2  ='select a.*,c.ledger_id,c.price_diff_ledger,i.item_name from lc_bill_payment a, lc_bill_category b ,lc_bill_type c,lc_purchase_invoice d,item_info i where b.bill_type=c.id and a.bill_category=b.id and a.order_no=d.id and i.item_id=d.item_id and a.payment_no="'.$payment_no.'" order by a.id'; 



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

		

		

		if($pre_rate[$data2->bill_category][$data2->order_no] < $data2->rate){

			

				$pre_amt = $pre_rate[$data2->bill_category][$data2->order_no] * $data2->qty;

				

				$diff = ($data2->pay_amt_in-$pre_amt);

				

				add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->price_diff_ledger, $data2->item_name, $diff, '0', $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data2->bill_category );

				

				add_to_sec_journal($proj_id, $jv_no, $jv_date, $cr_ledger, $data2->item_name,'0',$diff, $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data2->bill_category );

				

				

				 

				

		}else{

		

		

				$pre_amt = $pre_rate[$data2->bill_category][$data2->order_no] * $data2->qty;

				

				$diff = ( $pre_amt-$data2->pay_amt_in );

				

				add_to_sec_journal($proj_id, $jv_no, $jv_date, $cr_ledger, $data2->item_name, $diff, '0', $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data2->bill_category );

				

				add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->price_diff_ledger, $data2->item_name,'0',$diff, $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data2->bill_category);

		

		

		}

	

	

	//add_to_sec_journal($proj_id,$jv_no, $jv_date, $data2->lc_ledger, $data2->category_view, ($data2->pay_amt_in),  0, $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for, '','','','',$data->cheque_no, $data->cheque_date, $data2->cr_ledger_id, 'NO', $data2->payment_method, 'LC Journal', $data2->bill_category);

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $data2->item_name, ($data2->pay_amt_in), '0', $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data2->bill_category );



	



		}

		

		

		

		$sql3 = 'select a.* from lc_bill_payment a where  a.payment_no="'.$payment_no.'" and a.order_no=0 order by a.id'; 



		$query3 = db_query($sql3);

		

		while($data3=mysqli_fetch_object($query3)){

	

	

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data3->cr_ledger_id,'', ($data3->pay_amt_in), '0', $tr_from, $tr_no, $data3->cr_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '', $data3->cr_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', $data3->bill_category );



	



		}

		

		

		

//		$sql3 = 'select a.*, b.ledger_id from lc_bill_payment a, lc_bill_category b where a.bill_category=b.id and a.payment_no="'.$payment_no.'" and b.ledger_id>0 order by a.id';

//

//		$query3 = db_query($sql3);

//		

//		while($data3=mysqli_fetch_object($query3)){

//

//		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data3->ledger_id, $narration_cr,  ($data3->pay_amt_in), '0', $tr_from, $tr_no,$data3->cr_ledger_id,$tr_id, $cc_code, $group_for,

//		'','','','','','',$data3->cr_ledger_id,'NO');

//

//		}

			

			

	



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');



if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'",checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



}









}





function auto_insert_lc_shipment_secoundary_journal($payment_no)

{

	

		$proj_id="clouderp";

		$jv_no=next_journal_sec_voucher_id('',$tr_from,$_SESSION['user']['group']);

	

	 	$sql = 'select r.*,s.item_ledger,i.item_name from lc_purchase_receive r, item_info i ,item_sub_group s 

	 	where i.item_id=r.item_id and  i.sub_group_id=s.sub_group_id and  r.pr_no="'.$payment_no.'"';

	



		$query = db_query($sql);

		while($data=mysqli_fetch_object($query)){

		

			$group_for = $data->group_for;

			$cc_code = $group_for;

			$tr_from = 'LC Journal';

			$tr_no = $data->pr_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->rec_date;

			$narration_cr = $data->item_name;

			

			

		// add_to_sec_journal($proj_id, $jv_no, $jv_date, $ledger_id, $narration, $dr_amt, $cr_amt, $tr_from, $tr_no,$sub_ledger='',$tr_id='',$cc_code='',$group='',$entry_by='',$entry_at='',$received_from='', $bank='', $cheq_no='',$cheq_date='',$relavent_cash_head='',$checked='',$type='',$employee='',$remarks='',$reference_id='',$note='',$pay_id='')

		

		add_to_sec_journal($proj_id, $jv_no, $jv_date,$data->item_ledger,$narration_cr,($data->amount),'0', $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for,'','', '', '', '', '','', 'NO','','','','','LC Journal', '' );

		

		

		

			$sql2 = 'select a.*, sum(a.rate) as rate,b.ledger_id from lc_provision_payment a, lc_bill_type b 

	 where a.bill_type=b.id and a.lc_no="'.$data->lc_no.'" and a.order_no="'.$data->order_no.'"  GROUP by a.order_no,a.bill_type';

		

		

		$query2 = db_query($sql2);

		while($data2=mysqli_fetch_object($query2)){

		

		

			$cr_amt = ($data->qty * $data2->rate);

			add_to_sec_journal($proj_id, $jv_no,$jv_date,$data2->ledger_id,$narration_cr,'0',$cr_amt, $tr_from, $tr_no,'',$tr_id, $cc_code, $group_for,'','', '', '', '', '','', 'NO','','','','','LC Journal',$data2->bill_category);

		

		}

		

		}



			

$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');



if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'",checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



}









}















function auto_insert_lc_payment_adjustment_secoundary($payment_no)

{

	

	 $sql = 'select payment_no, payment_date, payment_method, group_for, lc_no, lc_number, lc_part_no, bill_type, lc_ledger, cr_ledger_id, sum(pay_amt_out) as tot_pay_amt, loan_ledger

	  from lc_bill_payment where payment_no="'.$payment_no.'" GROUP by payment_no';

	

	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



			$proj_id="clouderp";

			$group_for = $data->group_for;

			$cc_code = $group_for;

		

		//$tr_from = 'LC Journal';

		

			$ledger_group = find_a_field('accounts_ledger','ledger_group_id','ledger_id="'.$data->cr_ledger_id.'"');

			

			if($ledger_group==126001){

				$tr_from = 'Receipt';

			}elseif($ledger_group==126002) {

				$tr_from = 'Receipt';

			}else {

				$tr_from = 'Journal';	

			}

			

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('',$tr_from,$_SESSION['user']['group']);

			

			$tr_no = $data->payment_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->payment_date;

			

			//$jv_date = strtotime($data->payment_date);

			

			

			$narration_cr = $data->lc_number;

			

	

	

//add_to_sec_journal($proj_id,$jv_no, $jv_date, $data->cr_ledger_id, $narration_cr, $data->tot_pay_amt, 0,  $tr_from, $tr_no, $data->lc_ledger, $tr_id, $cc_code, $group_for, '','','','',$data->cheque_no, $data->cheque_date, $data->lc_ledger, 'NO', $data->payment_method, 'LC Journal');



add_to_sec_journal($proj_id,$jv_no, $jv_date, $data->cr_ledger_id, $narration_cr, $data->tot_pay_amt, 0,  $tr_from, $tr_no, $data->lc_ledger, $tr_id, $cc_code, $group_for,'','', '', '', '', '',$data->lc_ledger, 'NO', $data->payment_method,'','','','LC Journal', '' );









	

		$sql2 = 'select a.* from lc_bill_payment a, lc_bill_category b where a.bill_category=b.id and a.payment_no="'.$payment_no.'"  order by a.id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

		//$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

		//if($data->bill_type==3) {

//		$narration_dr = $narration_cr;

//		}else {

//		$narration_dr = $data2->category_view;

//		}	

	

	//add_to_sec_journal($proj_id,$jv_no, $jv_date, $data2->lc_ledger, $data2->category_view, 0, ($data2->pay_amt_out), $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for, '','','','',$data->cheque_no, $data->cheque_date, $data2->cr_ledger_id, 'NO', $data2->payment_method, 'LC Journal', $data2->bill_category);

	

	add_to_sec_journal($proj_id,$jv_no, $jv_date, $data2->lc_ledger, $data2->category_view, 0, ($data2->pay_amt_out), $tr_from, $tr_no, $data2->cr_ledger_id, $tr_id, $cc_code, $group_for, '','', '', '', '', '', $data2->cr_ledger_id, 'NO', $data2->payment_method,'','','','LC Journal', $data2->bill_category);

	

	

	



		}

		

		

		

//		$sql3 = 'select a.*, b.ledger_id from lc_bill_payment a, lc_bill_category b where a.bill_category=b.id and a.payment_no="'.$payment_no.'" and b.ledger_id>0 order by a.id';

//

//		$query3 = db_query($sql3);

//		

//		while($data3=mysqli_fetch_object($query3)){

//

//		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data3->ledger_id, $narration_cr,  ($data3->pay_amt_in), '0', $tr_from, $tr_no,$data3->cr_ledger_id,$tr_id, $cc_code, $group_for,

//		'','','','','','',$data3->cr_ledger_id,'NO');

//

//		}

			

			

	



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');



if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'",checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



}









}





















function auto_insert_lc_bank_payment_secoundary($payment_no)

{

	

	

	$sql = 'select * from lc_bank_payment where payment_no="'.$payment_no.'" GROUP by payment_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $_SESSION['user']['group'];

			$cc_code = 0;

			$tr_from = 'LC Journal';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','LC Journal',$_SESSION['user']['depot']);

			

			$tr_no = $data->payment_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->payment_date;

			

			$narration = $data->lc_number;

			

			$pay_amt_bdt = $data->bank_pay_amt+$data->loan_amt+$data->lc_margin_adjustment;

			

			$narration_dr = 'Document Discharge';

			

			$lc_margin_ledger = find_a_field('lc_ledger_config','lc_margin','id="1"');	

			

			

			

			

				if($pay_amt_bdt>0) {		

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->ledger_id, $narration_dr,  ($pay_amt_bdt), '0',  $tr_from, $tr_no,$data->lc_ltr_ledger, $tr_id, $cc_code, $group_for,

	'','','','','','',$data->lc_ltr_ledger);

	

	}

	

	

	if($data->bank_pay_amt>0) {		

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->lc_bank_ledger, $narration,   '0', ($data->bank_pay_amt), $tr_from, $tr_no,$data->ledger_id, $tr_id, $cc_code, $group_for,

	'','','','','','',$data->ledger_id);

	

	}

	

	

	if($data->loan_amt>0) {		

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->lc_ltr_ledger, $narration,   '0', ($data->loan_amt), $tr_from, $tr_no,$data->ledger_id, $tr_id, $cc_code, $group_for,

	'','','','','','',$data->ledger_id);

	

	}

	

	

	if($data->lc_margin_adjustment>0) {		

	add_to_sec_journal($proj_id, $jv_no, $jv_date, $lc_margin_ledger, $narration,   '0', ($data->lc_margin_adjustment), $tr_from, $tr_no,$data->ledger_id, $tr_id, $cc_code, $group_for,

	'','','','','','',$data->ledger_id);

	

	}

			

			



	

	

		//$sql2 = 'select * from payment_vendor where payment_no="'.$payment_no.'" order by id';

//

//		$query2 = db_query($sql2);

//		

//		while($data2=mysqli_fetch_object($query2)){

//	

//		$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

//		

//		add_to_sec_journal($proj_id, $jv_no, $jv_date, $data2->ledger_id, $narration_dr, ($data2->payment_amt),  '0',  $tr_from, $tr_no, '', $data2->pr_no,$cc_code,$group_for);

//

//		}

			

			

	



$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}





















function auto_insert_lc_closing_secoundary($closing_no)

{

	

	

	$sql = 'select closing_no, closing_date, group_for, lc_no, lc_number,  ledger_id,sub_ledger_id, total_pay_amt from lc_purchase_closing

	 where closing_no="'.$closing_no.'" GROUP by closing_no';

	



	$query = db_query($sql);

	$data=mysqli_fetch_object($query);



		

			$group_for = $data->group_for;

			$cc_code = $data->group_for;

			$tr_from = 'Journal';

			$proj_id="clouderp";

			

			$config = find_all_field('config_group_class','',"group_for=".$group_for);

			$jv_no=next_journal_sec_voucher_id('','Journal',$_SESSION['user']['depot']);

			

			$tr_no = $data->closing_no;

			$tr_id = $data->lc_no;

			$jv_date = $data->closing_date;

			

			$narration_dr = $data->lc_number;		

			$narration_cr = 'L/C Closing';



add_to_sec_journal($proj_id,$jv_no, $jv_date, $data->ledger_id, $narration_cr,  0, $data->total_pay_amt, $tr_from, $tr_no, $data->sub_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '',$data->sub_ledger_id, 'NO', $data->payment_method,'','','','LC Journal', '' );





	

		$sql2 = 'select sum(l.cost_amt_bdt) as cost_amt_bdt, i.ledger_id as item_ledger, s.item_ledger as item_group_ledger, l.ledger_id as lc_ledger 

		from lc_purchase_closing l, item_info i, item_sub_group s 

		where l.item_id=i.item_id and i.sub_group_id=s.sub_group_id and l.closing_no="'.$closing_no.'" group by i.item_id order by i.sub_group_id';



		$query2 = db_query($sql2);

		

		while($data2=mysqli_fetch_object($query2)){

	

		//$narration_dr ='PR# '.$data2->pr_no.' (PO# '.$data2->po_no.')';

		

		if($data2->item_ledger>0){

			$item_ledger = $data2->item_ledger;

		}else{

			$item_ledger = $data2->item_group_ledger;

		}



	add_to_sec_journal($proj_id,$jv_no, $jv_date, $item_ledger, $narration_dr, $data2->cost_amt_bdt, 0,  $tr_from, $tr_no, $data2->sub_ledger_id, $tr_id, $cc_code, $group_for,'','', '', '', '', '',$data2->sub_ledger_id, 'NO', $data2->payment_method,'','','','LC Journal', '' );



		}

			

			

	



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');



if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'",checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



}









}















function auto_insert_collection_from_cash_sales($chalan_no)

{





	

	

	$proj_id = 'clouderp'; 

	$do_ch =    find_all_field('sale_do_chalan','do_no','chalan_no='.$chalan_no);

	$group_for =  $_SESSION['user']['group'];

	$do_master = find_all_field('sale_do_master','do_no','do_no='.$do_ch->do_no);

    $dealer = find_all_field('dealer_info','',"dealer_code=".$do_ch->dealer_code);

	

	$jv_no=next_journal_sec_voucher_id('','Receipt',$group_for);

    $tr_id = $do_ch->do_no;

	$tr_no = $chalan_no;

	$tr_from = 'Receipt';

	$narration = 'Invoice# '.$chalan_no.' (SO# '.$do_ch->do_no.')';

    

	$sql = "select sum(total_amt) as total_amt from sale_do_chalan c where  chalan_no=".$chalan_no;

	

	$ch = find_all_field_sql($sql);



	$cash_discount = $do_master->cash_discount;





$sales_amt = $ch->total_amt-$cash_discount;



$vat_on_sales = ($sales_amt*$do_master->vat)/100;

	



	//$jv_date = strtotime($do->chalan_date);

	

	$jv_date = $do_ch->chalan_date;



	$invoice_amt = ($sales_amt + $vat_on_sales);

	

	//$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$group_for);

	$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	$dealer_ledger= $dealer->account_code;

	$cc_code = $do_master->depot_id;

	

	$cash_ledger = find_a_field('warehouse','cash_ledger','warehouse_id='.$do_master->depot_id);



//debit	 

if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $cash_ledger, $narration, ($invoice_amt), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);



if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration,  '0', ($invoice_amt), $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);





$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'",

type="CASH"  where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'", type="CASH"  where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}





} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}













function auto_insert_customer_collection_secoundary_journal($tr_no)

{





	

	

	$proj_id = 'clouderp'; 

	$group_for =  $_SESSION['user']['group'];

    $master_data = find_all_field('collection_from_customer','',"tr_no=".$tr_no);

	

	$jv_no=next_journal_sec_voucher_id('','Receipt',$group_for);

	$jv_date = $master_data->collection_date;

    $tr_id = $master_data->collection_no;

	$tr_no = $tr_no;

	$tr_from = 'Receipt';

	$narration = 'Collection No# '.$master_data->collection_no;

    

	$invoice_amt = $master_data->collection_amt;

	//$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	

	$cc_code = $master_data->warehouse_id;

	

	$customer_ledger = $master_data->ledger_id;

	

	$cash_ledger =  $master_data->dr_ledger_id;



//debit	 

if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $cash_ledger, $narration, ($invoice_amt), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);



if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $customer_ledger, $narration,  '0', ($invoice_amt), $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);





$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'",

type="CASH"  where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'", type="CASH"  where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}





} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}













function auto_insert_sales_return_secoundary($chalan_no)

{



	

	



	$proj_id = 'clouderp';

	

	$group_for =  $_SESSION['user']['group'];

	$do_master = find_all_field('sale_return_master','','invoice_no='.$chalan_no);

    $dealer = find_all_field('dealer_info','',"dealer_code=".$do_master->dealer_code);

	

	$return_type = find_a_field('sales_return_type','sales_return_type','id="'.$do_master->sales_type.'"');

	

	$jv_no=next_journal_sec_voucher_id('',$return_type,$group_for);

    $tr_id = $do_master->do_no;

	$tr_no = $chalan_no;

	$tr_from = $return_type;

	$narration = 'SR No# '.$chalan_no;

    

	$sql = "select sum(total_amt) as total_amt from sale_return_details c where  do_no=".$do_master->do_no;

	

	$ch = find_all_field_sql($sql);



	$sales_amt = $ch->total_amt;



	$jv_date = $do_master->do_date;



	$invoice_amt = ($sales_amt);

	

	//$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$group_for);

	$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	$dealer_ledger= $dealer->account_code;

	$cc_code = $do_master->depot_id;



//debit	



if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->sales_return, $narration, ($invoice_amt), '0',   $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for); 



if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration,  '0',  ($invoice_amt), $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);















$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}













function auto_reinsert_purchase_secoundary_journal($pr_no)

{

	$reinsert = 1;

	db_delete_all('secondary_journal','tr_from = "Sales" AND `tr_no` = "'.$chalan_no.'"');

	db_delete_all('journal','tr_from = "Sales" AND `tr_no` = "'.$chalan_no.'"');

	auto_insert_purchase_secoundary_journal($pr_no);

}



function auto_insert_purchase_receive_secoundary_journal($pr_no){

	$proj_id = 'clouderp'; 

	$po_no =    find_a_field('purchase_receive','po_no','pr_no='.$pr_no);

	$group_for =  $_SESSION['user']['group'];

	$po_master = find_all_field('purchase_master','','po_no='.$po_no);

    $vendor = find_all_field('vendor','',"vendor_id=".$po_master->vendor_id);

	

	 $sql = "select sum(amount) as amount, rec_date, transport_charge, other_charge from purchase_receive  where  pr_no=".$pr_no;

	

	$pr = find_all_field_sql($sql);

	

	

	

    $tr_id = $po_no;

	$tr_no = $pr_no;

	$jv_date = $pr->rec_date;

	$tr_from = 'Purchase';

	$jv_no=next_journal_sec_voucher_id('','Purchase',$_SESSION['user']['group']);

	$narration = 'PR#'.$pr_no.' (PO#'.$po_no.')';

    

	



$pr_amount = $pr->amount;



$vat_on_purchase = ($pr_amount*$po_master->tax)/100;

$ait_on_purchase = ($pr_amount*$po_master->ait)/100;



	//$jv_date = strtotime($do->chalan_date);

	

	



	$invoice_amt = ($pr_amount + $vat_on_purchase + $ait_on_purchase + $pr->transport_charge + $pr->other_charge);

	

	

	$config_ledger = find_all_field('config_group_class','',"group_for=".$_SESSION['user']['group']);

	$vendor_ledger= $vendor->ledger_id;

	$cc_code = 0;





//debit	

$d_sql = "select r.id as tr_id,r.amount,s.ledger_id_1 from purchase_receive r,item_info i,item_sub_group s  where  r.pr_no=".$pr_no." and i.item_id=r.item_id and i.sub_group_id=s.sub_group_id";

$d_query =db_query($d_sql);



while($data=mysqli_fetch_object($d_query)){

if($data->amount>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->ledger_id_1, $narration,$data->amount, '0', $tr_from, $pr_no,'',$data->tr_id,$cc_code,$group_for);



}

 

//if($pr_amount>0)



//add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_ledger, $narration, ($pr_amount), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);



if($vat_on_purchase>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_vat, $narration, ($vat_on_purchase), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);



if($ait_on_purchase>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_ait, $narration, ($ait_on_purchase), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);



if($pr->transport_charge>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->po_transport_charge, $narration, ($pr->transport_charge), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);



if($pr->other_charge>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->po_other_charge, $narration, ($pr->other_charge), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);





//credit

if($invoice_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $vendor_ledger, $narration, '0', ($invoice_amt), $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);













$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}



function auto_insert_purchase_secoundary_journal($pr_no)

{

	$proj_id = 'clouderp'; 

	$po_no =    find_a_field('purchase_receive','po_no','pr_no='.$pr_no);

	$group_for =  $_SESSION['user']['group'];

	$po_master = find_all_field('purchase_master','','po_no='.$po_no);

    $vendor = find_all_field('vendor','',"vendor_id=".$po_master->vendor_id);

	

	 $sql = "select sum(with_vat_amt) as amount, rec_date, transport_charge, other_charge from purchase_receive  where  pr_no=".$pr_no;

	

	$pr = find_all_field_sql($sql);

	


    $tr_id = $po_no;

	$tr_no = $pr_no;

	$jv_date = $pr->rec_date;

	$tr_from = 'Purchase';

	$jv_no=next_journal_sec_voucher_id('','Purchase',$_SESSION['user']['group']);

	$narration = 'PR#'.$pr_no.' (PO#'.$po_no.')';

    

	



$pr_amount = $pr->amount;


/*$ait_on_purchase = ($pr_amount*$po_master->ait)/100;*/
$discoount_on_purchase = ($pr_amount*$po_master->cash_discount)/100;
/*$tax_on_purchase = ($pr_amount*$po_master->tax)/100;*/


	$config_ledger = find_all_field('config_group_class','',"group_for=".$_SESSION['user']['group']);

	$vendor_ledger= $config_ledger->doc_ledger;
	$cc_code = 0;

//debit	

$d_sql = "select r.id as tr_id,r.with_vat_amt as amount,r.vat_amt,s.item_ledger,i.sub_ledger_id  from purchase_receive r,item_info i,item_sub_group s  where  r.pr_no=".$pr_no." and i.item_id=r.item_id and i.sub_group_id=s.sub_group_id";

$d_query =db_query($d_sql);



while($data=mysqli_fetch_object($d_query)){

if($data->amount>0){
//$vat_on_purchase = ($data->amount*$po_master->vat)/100;
$vat_on_purchase =$data->vat_amt;

if($po_master->rebate_percentage>0){

$rebate_amt = ($vat_on_purchase* $po_master->rebate_percentage )/100;

$vat_on_purchase = $vat_on_purchase-$rebate_amt;
$total_rebate_amt += $rebate_amt;
}

$total_vat +=$vat_on_purchase;

add_to_sec_journal($proj_id, $jv_no, $jv_date, $data->item_ledger, $narration,($data->amount), '0', $tr_from, $pr_no,$data->sub_ledger_id,$data->tr_id,$cc_code,$group_for);



}


}

if($total_rebate_amt>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date,$config_ledger->purchase_vat, $narration,($total_rebate_amt), '0', $tr_from, $pr_no,$vendor->sub_ledger_id,$data->id,$cc_code,$data->group_for);
}


 

/*if($tax_on_purchase>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_tax, $narration, ($tax_on_purchase), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);*/

/*if($discoount_on_purchase>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_discount, $narration,'0',($discoount_on_purchase), $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);
}*/

/*if($vat_on_purchase>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_vat, $narration, ($vat_on_purchase), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);
}*/


/*if($ait_on_purchase>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->purchase_ait, $narration, ($ait_on_purchase), '0', $tr_from, $tr_no,'',$tr_id,$cc_code,$group_for);*/



if($pr->transport_charge>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->po_transport_charge, $narration, ($pr->transport_charge), '0', $tr_from, $tr_no,$vendor->sub_ledger_id,$tr_id,$cc_code,$group_for);

}

if($pr->other_charge>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->po_other_charge, $narration, ($pr->other_charge), '0', $tr_from, $tr_no,$vendor->sub_ledger_id,$tr_id,$cc_code,$group_for);
}



//credit
//$invoice_amt = ($pr_amount + $total_vat + $total_rebate_amt + $pr->transport_charge + $pr->other_charge)-$discoount_on_purchase;
$invoice_amt = ($pr_amount  + $total_rebate_amt + $pr->transport_charge + $pr->other_charge);
if($invoice_amt>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $vendor_ledger, $narration, '0', ($invoice_amt), $tr_from, $tr_no,$vendor->sub_ledger_id,$tr_id,$cc_code,$group_for);

}

$sa_config = find_a_field('voucher_config','secondary_approval','voucher_type="'.$tr_from.'"');



$time_now = date('Y-m-d H:i:s');



if($sa_config=="Yes"){



$sa_up='update secondary_journal set secondary_approval="Yes", om_checked_at="'.$time_now.'", om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



$jv_config = find_a_field('voucher_config','direct_journal','voucher_type="'.$tr_from.'"');





if($jv_config=="Yes"){



sec_journal_journal($jv_no,$jv_no,$tr_from);



$time_now = date('Y-m-d H:i:s');



$up2='update secondary_journal set checked="YES",checked_at="'.$time_now.'", checked_by="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($up2);



$sa_up2='update journal set secondary_approval="Yes", checked="YES", checked_by="'.$_SESSION['user']['id'].'", checked_at="'.$time_now.'", om_checked_at="'.$time_now.'" ,om_checked="'.$_SESSION['user']['id'].'" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';

db_query($sa_up2);





}



} else {



$sa_up='update secondary_journal set secondary_approval="No" where jv_no="'.$jv_no.'" and tr_from="'.$tr_from.'"';



db_query($sa_up);



}





}

















function auto_insert_staff_sales_chalan_secoundary($chalan_no)

{

	$jv_no=next_journal_sec_voucher_id('','StaffSales',$_SESSION['user']['depot']);

	$proj_id = 'Sajeeb'; 

	$do = find_all_field('sale_other_chalan','do_no','chalan_no='.$chalan_no);

    

    $tr_id = $do_no = $do->do_no;

	$tr_no = $chalan_no;

	$tr_from = 'StaffSales';

	$narration = 'CH#'.$chalan_no.'(DO#'.$do_no.')';

    

	$data = mysqli_fetch_object(db_query("select sum(total_amt) as total_amt,id,chalan_no,do_no,chalan_date,dealer_code from sale_other_chalan c, item_info i where c.item_id=i.item_id and i.item_brand!='Memo' and unit_price>0 and  chalan_no=".$chalan_no));

	$total_amt_memo = mysqli_fetch_object(db_query("select sum(c.total_amt) as total_amt from sale_other_chalan c, item_info i where c.item_id=i.item_id and i.item_brand='Memo' and unit_price>0 and  chalan_no=".$chalan_no));

	$jv_date = strtotime($do->chalan_date);

	

	

	

	$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	$dealer = find_all_field('personnel_basic_info','','PBI_ID='.$do->dealer_code);

	$dealer_ledger= '1061000400000000';

	

	$acc_code = find_all_field_sql("select c.id,w.ledger_id from warehouse w, cost_center c where w.acc_code=c.cc_code and w.warehouse_id=".$do->depot_id);

	$cc_code = ($acc_code->id>0)?$acc_code->id:'0';

	



if(($data->total_amt+$total_amt_memo->total_amt)>0) 

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration,($data->total_amt+$total_amt_memo->total_amt), '0', $tr_from, $tr_no,'',$tr_id,$cc_code);

if($data->total_amt>0) 

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->sales_ledger, $narration, '0',  $data->total_amt, $tr_from, $tr_no,'',$tr_id,$cc_code);



						$dsql = "select  d.gift_id,i.finish_goods_code,sum(c.total_amt) total_amt,c.order_no,g.offer_name from sale_other_chalan c,sale_other_details d, item_info i,sale_gift_offer g where d.gift_on_item=i.item_id and d.id=c.order_no and c.unit_price<0 and g.id=d.gift_id and  c.chalan_no=".$chalan_no." group by c.order_no";

						$dquery = db_query($dsql);

						while($data1 = mysqli_fetch_object($dquery)){

						$narration = 'Offer:'.$data1->offer_name.'(CH#'.$chalan_no.'#'.$data1->finish_goods_code.')';

						if($data1->total_amt!=0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration, '0', ((-1)*$data1->total_amt), $tr_from, $tr_no,'',$tr_id,$cc_code);

						$ttotal_amt =  $ttotal_amt + ((-1)*$data1->total_amt);

						}

						if(($ttotal_amt>0)){$narration = 'CH#'.$do->chalan_no.'(DO#'.$do_no.')';

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4014000800010000', $narration, $ttotal_amt, '0', $tr_from, $tr_no,'',$tr_id,$cc_code);}

						

						

				$free = find_all_field_sql("select  sum(i.f_price*c.total_unit) as fprice,sum(i.d_price*c.total_unit) as dprice from sale_other_chalan c,item_info i where c.item_id=i.item_id and c.unit_price=0  and i.item_brand!='Memo' and i.item_brand!='PROMOTIONAL' and c.chalan_no=".$chalan_no);

				$free_price = $free->fprice;

				$promotional = find_all_field_sql("select  sum(i.f_price*c.total_unit) as fprice,sum(i.d_price*c.total_unit) as dprice from sale_other_chalan c,item_info i where c.item_id=i.item_id and c.unit_price=0  and i.item_brand='PROMOTIONAL' and c.chalan_no=".$chalan_no);

				$promotional_price = $promotional->fprice;

				$d_price = $free->dprice;

				$fd_price = ($d_price - $free_price);

				$CODS_price = find_a_field_sql("select  sum(i.f_price*c.total_unit) as price from sale_other_chalan c,item_info i where c.item_id=i.item_id and c.unit_price>0 and  i.item_brand!='Memo' and c.chalan_no=".$chalan_no);

				$memo_price = find_a_field_sql("select  sum(i.f_price*c.total_unit) as price from sale_other_chalan c,item_info i where c.item_id=i.item_id and  i.item_brand='Memo' and c.chalan_no=".$chalan_no);

				$store_price = $CODS_price + $free_price;

				



if($d_price>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4014000800020000', $narration, $d_price, '0', $tr_from, $tr_no,'',$tr_id,$cc_code);

if($fd_price!=0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4014000800050000', $narration,  '0',$fd_price, $tr_from, $tr_no,'',$tr_id,$cc_code);

if($CODS_price!=0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4001000300010000', $narration, $CODS_price,'0', $tr_from, $tr_no,'',$tr_id,$cc_code);









if($promotional_price>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4014000200010000', $narration, $promotional_price,'0', $tr_from, $tr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, '1078000600010000', $narration, '0', $promotional_price, $tr_from, $tr_no,'',$tr_id,$cc_code);

}

if($store_price>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $acc_code->ledger_id, $narration, '0', $store_price, $tr_from, $tr_no,'',$tr_id,$cc_code);

}

function auto_insert_bulk_sales_chalan_secoundary($chalan_no)

{

	$jv_no=next_journal_sec_voucher_id('','Bulk Sales',$_SESSION['user']['depot']);

	$proj_id = 'Sajeeb'; 

	$do = find_all_field('sale_other_chalan','do_no','chalan_no='.$chalan_no);

    

    $tr_id = $do_no = $do->do_no;

	$tr_no = $chalan_no;

	$tr_from = 'Bulk Sales';

	$narration = 'CH#'.$chalan_no.'(DO#'.$do_no.')';

    

	$data = mysqli_fetch_object(db_query("select sum(total_amt) as total_amt,id,chalan_no,do_no,chalan_date,dealer_code from sale_other_chalan c, item_info i where c.item_id=i.item_id and i.item_brand!='Memo' and unit_price>0 and  chalan_no=".$chalan_no));

	

	$jv_date = strtotime($do->chalan_date);

	

	

	$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	

	$dealer = find_all_field('dealer_info','','dealer_code='.$do->dealer_code);

	$dealer_ledger= $dealer->account_code;

	

	if($_SESSION['user']['group']==2){

	$acc_code = find_all_field_sql("select c.id,w.ledger_id from warehouse w, cost_center c where w.group_for='".$_SESSION['user']['group']."' and w.acc_code=c.cc_code and w.warehouse_id=".$do->depot_id);

	$cc_code = ($acc_code->id>0)?$acc_code->id:'0';

	}

	else

	{

	$acc_code = find_all_field_sql("select w.ledger_id from warehouse w where group_for='".$_SESSION['user']['group']."' and w.warehouse_id=".$do->depot_id);

	$cc_code = 0;

	}



if(($data->total_amt)>0) 

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration,$data->total_amt, '0', $tr_from, $tr_no,'',$tr_id,$cc_code);

if($data->total_amt>0) 

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->sales_ledger, $narration, '0',  $data->total_amt, $tr_from, $tr_no,'',$tr_id,$cc_code);





$CODS_price = find_a_field_sql("select  sum(i.f_price*c.total_unit) as price from sale_other_chalan c,item_info i where c.item_id=i.item_id and c.unit_price>0 and  i.item_brand!='Memo' and c.chalan_no=".$chalan_no);

				$store_price = $CODS_price;

				



if($CODS_price!=0){

if($_SESSION['user']['group']==2){

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4001000300010000', $narration, $CODS_price,'0', $tr_from, $tr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, $acc_code->ledger_id, $narration, '0', $store_price, $tr_from, $tr_no,'',$tr_id,$cc_code);}

//if($_SESSION['user']['group']==3)

//add_to_sec_journal($proj_id, $jv_no, $jv_date, '3002000100040000', $narration, $CODS_price,'0', $tr_from, $tr_no,'',$tr_id,$cc_code);



if($_SESSION['user']['group']==10){

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4080000100010000', $narration, $CODS_price,'0', $tr_from, $tr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, $acc_code->ledger_id, $narration, '0', $store_price, $tr_from, $tr_no,'',$tr_id,$cc_code);}



}

}

function auto_insert_other_sales_chalan_secoundary($chalan_no)

{

	$jv_no=next_journal_sec_voucher_id();

	$proj_id = 'Sajeeb'; 

	$do = find_all_field('sale_other_chalan','do_no','chalan_no='.$chalan_no);

    $issue_type = $tr_from = find_a_field('sale_other_master','issue_type','do_no='.$do->do_no);

    $tr_id = $do_no = $do->do_no;

	$tr_no = $chalan_no;

	$narration = 'CH#'.$chalan_no.'(DO#'.$do_no.')';

    

	$data = mysqli_fetch_object(db_query("select sum(total_amt) as total_amt,id,chalan_no,do_no,chalan_date,dealer_code from sale_other_chalan c, item_info i where c.item_id=i.item_id and i.item_brand!='Memo' and unit_price>0 and  chalan_no=".$chalan_no));

	$total_amt_memo = mysqli_fetch_object(db_query("select sum(c.total_amt) as total_amt from sale_other_chalan c, item_info i where c.item_id=i.item_id and i.item_brand='Memo' and unit_price>0 and  chalan_no=".$chalan_no));

	$jv_date = strtotime($do->chalan_date);

	

	$config_ledger = find_all_field('config_group_class','sales_ledger',"group_for=".$_SESSION['user']['group']);

	$acc_code = find_all_field_sql("select c.id,w.ledger_id from warehouse w, cost_center c where w.acc_code=c.cc_code and w.warehouse_id=".$do->depot_id);

	$cc_code = ($acc_code->id>0)?$acc_code->id:'0';





if(($data->total_amt+$total_amt_memo->total_amt)>0) 

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->other_sales_exp, $narration,($data->total_amt+$total_amt_memo->total_amt), '0', $tr_from, $tr_no,'',$tr_id,$cc_code);

if($data->total_amt>0) 

add_to_sec_journal($proj_id, $jv_no, $jv_date, $config_ledger->other_local_sales, $narration, '0',  $data->total_amt, $tr_from, $tr_no,'',$tr_id,$cc_code);



					









				$CODS_price = find_a_field_sql("select  sum(i.f_price*c.total_unit) as price from sale_other_chalan c,item_info i where c.item_id=i.item_id and c.unit_price>0 and  i.item_brand!='Memo' and c.chalan_no=".$chalan_no);



				



if($_SESSION['user']['group']==2){

if($CODS_price!=0) add_to_sec_journal($proj_id, $jv_no, $jv_date, '4001000300010000', $narration, $CODS_price,'0', $tr_from, $tr_no,'',$tr_id,$cc_code);

if($CODS_price>0)  add_to_sec_journal($proj_id, $jv_no, $jv_date, $acc_code->ledger_id, $narration, '0', $CODS_price, $tr_from, $tr_no,'',$tr_id,$cc_code);}



}





function auto_insert_secondary_claim_chalan_secoundary($chalan_no)

{

	$jv_no=next_journal_sec_voucher_id();

	$proj_id = 'Sajeeb'; 

    $tr_from = 'issue_type';

    $tr_id = $do_no = $do->do_no;

	$tr_no = $chalan_no;

	$narration = 'CH#'.$chalan_no.'(DO#'.$do_no.')';

    

	$data = mysqli_fetch_object(db_query("select o.offer_name,d.do_date,sum(total_amt) as total_amt from sale_so_details d,sale_so_offer o where d.item_id=o.id and  d.do_no=".$chalan_no));

$narration = $data->offer_name.',Claim Date#'.$data->do_date.')';

	$jv_date = strtotime($do->chalan_date);



	$dealer_ledger = '4014000800070001';

	$offer_ledger = '4014000800080001';



if($data->total_amt>0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration,$data->total_amt, '0',   $tr_from, $do_no,'',$do_no,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, $offer_ledger , $narration, '0',  $data->total_amt, $tr_from, $do_no,'',$do_no,$cc_code);

}



}





function auto_insert_sales_secoundary($jv,$s_date,$dr_ledger,$cr_ledger,$ch_no,$amt,$do_no,$ch_id,$narration=''){



$sdate=strtotime($s_date);

//insert to bdt sales acc credit

$now_time = date('Y-m-d H:i:s');

$narration = 'CH#'.$ch_no.'/'.$ch_id.'(DO#'.$do_no.')';

$narration_gift = 'CH#$pr_no/$tr_id(PO#$po_no)';

$journal="INSERT INTO `secondary_journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('$jv', '$sdate', '$dr_ledger', '$narration', '$amt','0',  'Sales',$ch_no,$ch_id,'".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);



$journal="INSERT INTO `secondary_journal` (

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from` ,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ( '$jv', '$sdate', '$cr_ledger', '$narration', '0', '$amt', 'Sales',$ch_no,$ch_id,'".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);

}



function auto_recalculate_sales_discount($chalan_no)

{

$now = date('Y-m-d H:i:s');

$do_no = find_a_field('sale_do_chalan','do_no','chalan_no='.$chalan_no);

$delete_discount =  db_delete_all('sale_do_chalan','(item_id=1096000100010239 or item_id=1096000100010312) and chalan_type="Delivery" and do_no = '.$do_no.' and chalan_no='.$chalan_no);



$depot_id	= find_a_field('sale_do_chalan','depot_id','do_no='.$do_no);



$sql = 'select s.*,d.* from sale_do_details d, sale_gift_offer s where s.id=d.gift_id and d.do_no = '.$do_no.' and (d.item_id=1096000100010239 or d.item_id=1096000100010312)';

$query = db_query($sql);	

while($data=mysqli_fetch_object($query))

{



$order	= find_all_field('sale_do_details','','id='.$data->gift_on_order);

$chalan	= find_all_field('sale_do_chalan','','order_no='.$order->id.' and chalan_no='.$chalan_no);

$gift   = find_all_field('sale_gift_offer','','id='.$data->gift_id);





$item_qty=$chalan->total_unit;

$data->pkt_size = '1.00';

$unit_price = (-1)*($gift->gift_qty);

$unit_qty =  (int)($item_qty/$gift->item_qty);

$total_amt = $unit_qty * $unit_price;

$chalan_pkt = '0.00';

$chalan_dist = $unit_qty;

if($chalan_dist>0){

$dealer_code = $_POST['dealer_code'];

$q = "INSERT INTO  sale_do_chalan (order_no, chalan_no, do_no, item_id, dealer_code, unit_price, pkt_size, pkt_unit, dist_unit, total_unit, total_amt, chalan_date, depot_id, driver_name, driver_name_real, vehicle_no, delivery_man, entry_by, entry_at,do_date)

VALUES 

('".$data->id."', '".$chalan_no."', '".$do_no."', '".$data->item_id."', '".$data->dealer_code."', '".$unit_price."', '".$data->pkt_size."', '".$chalan_pkt."', '".$chalan_dist."', '".$unit_qty."', '".$total_amt."', '".$chalan->chalan_date."', '".$depot_id."', '".$chalan->driver_name."','".$chalan->driver_name_real."', '".$chalan->vehicle_no."', '".$chalan->delivery_man."', '".$_SESSION['user']['id']."', '".$now."','".$order->do_date."')";

db_query($q);

}		

		}

}



function auto_insert_sale_hfl($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no=''){

$now_time = date('Y-m-d H:i:s');

$sdate=date_2_stamp_add_mon_duration($sdate);

$jv=next_journal_sec_voucher_id();

//insert to bdt sales acc credit

$journal="INSERT INTO `journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`,

`tr_no`,

group_for,

entry_at,

user_id

)

VALUES ('', '$jv', '$sdate', '$sales_ledger', 'Delivery No# $do_no', '0', '$amt', 'Sales','$chalan_no','".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);



$journal="INSERT INTO `journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`,

`tr_no`,

group_for,

entry_at,

user_id

)

VALUES ('', '$jv', '$sdate', '$ledger', 'Delivery No# $do_no', '$amt', '0', 'Sales','$chalan_no','".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);

}

function auto_insert_store_transfer_issue_sj_auto($pi_no){

$pi = find_all_field('production_issue_master','pi_no','pi_no='.$pi_no);

if($pi->warehouse_from==5) return false;

db_delete_all('journal','tr_from="StoreIssued" and tr_no = "'.$pi_no.'"');

db_delete_all('secondary_journal','tr_from="StoreIssued" and tr_no = "'.$pi_no.'"');

$ledger = 1078000200010000;

$cc_code = find_a_field_sql('select c.id from cost_center c, warehouse w where c.cc_code=w.acc_code and w.warehouse_id='.$pi->warehouse_from);



$warehouse = find_all_field('warehouse','ledger_id','warehouse_id='.$pi->warehouse_from);	

$sales_ledger = $warehouse->ledger_id;





$jv_date=date_2_stamp_add_mon_duration($pi->pi_date);

$jv=next_journal_sec_voucher_id($pi->pi_date);

$tr_from = 'StoreIssued';

$narration = 'PI No-'.$pi_no.'||SendDt:'.$pi->pi_date;



auto_insert_store_transfer_issue($pi->pi_date,$ledger,$sales_ledger,$pi_no,find_a_field('production_issue_detail','sum(total_amt)','pi_no='.$pi_no),$pi_no,$cc_code,$narration,'','',$warehouse->group_for);

}



function auto_insert_store_transfer_issue($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no='',$cc_code='',$narration='',$entry_by='',$entry_at='',$group_for=''){

$jv_date=date_2_stamp_add_mon_duration($sdate);

$jv=next_journal_sec_voucher_id($sdate);

$tr_from = 'StoreIssued';

if($narration!='') $narration = $narration;

else $narration = 'PI No# '.$do_no;



if($group_for==0)

$group_for = $_SESSION['user']['group'];

add_to_sec_journal($proj_id, $jv, $jv_date, $ledger, $narration, $amt, '0', $tr_from, $do_no,'','',$cc_code,$group_for,$entry_by,$entry_at);

add_to_sec_journal($proj_id, $jv, $jv_date, $sales_ledger, $narration,'0',  $amt, $tr_from, $do_no,'','',$cc_code,$group_for,$entry_by,$entry_at);



}



function auto_insert_store_transfer_issue_receive_sj_auto($pi_no){

auto_insert_store_transfer_issue_sj_auto($pi_no);

auto_insert_store_transfer_receive_sj_auto($pi_no);

}

function auto_insert_store_transfer_receive_sj_auto($pi_no){

$pi = find_all_field('production_issue_master','pi_no','pi_no='.$pi_no);

if($pi->warehouse_to==5)return false;

db_delete_all('journal','tr_from="StoreReceived" and tr_no = "'.$pi_no.'"');

db_delete_all('secondary_journal','tr_from="StoreReceived" and tr_no = "'.$pi_no.'"');

$ledger = 1078000200010000;

$cc_code = find_a_field_sql('select c.id from cost_center c, warehouse w where c.cc_code=w.acc_code and w.warehouse_id='.$pi->warehouse_to);

$sales_ledger = find_a_field('warehouse','ledger_id','warehouse_id='.$pi->warehouse_to);	





$jv_date=date_2_stamp_add_mon_duration($pi->pi_date);

$jv=next_journal_sec_voucher_id($pi->pi_date);

$tr_from = 'StoreReceived';

$narration = 'PI No-'.$pi_no.'||RSL-'.$pi->rec_sl_no.'||RecDt:'.$pi->receive_date;



auto_insert_store_transfer_receive($pi->pi_date,$ledger,$sales_ledger,$pi_no,find_a_field('production_issue_detail','sum(total_amt)','pi_no='.$pi_no),$pi_no,$cc_code,$narration);

}

function auto_insert_store_transfer_receive($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no='',$cc_code='',$narration='',$entry_by='',$entry_at=''){

$jv_date=date_2_stamp_add_mon_duration($sdate);

$jv=next_journal_sec_voucher_id($sdate);

$tr_from = 'StoreReceived';

if($narration!='') $narration = $narration;

else $narration = 'PI No# '.$do_no;



add_to_sec_journal($proj_id, $jv, $jv_date, $ledger, $narration,  $amt,'0', $tr_from, $do_no,'','',$cc_code,$_SESSION['user']['group'],$entry_by,$entry_at);

add_to_sec_journal($proj_id, $jv, $jv_date, $sales_ledger, $narration, '0', $amt, $tr_from, $do_no,'','',$cc_code,$_SESSION['user']['group'],$entry_by,$entry_at);



}



function auto_insert_store_transfer_all($pi_no){

$pi = find_all_field('production_issue_master','','pi_no='.$pi_no);

$ledger_from = find_a_field('warehouse','ledger_id','warehouse_id='.$pi->warehouse_from);	

$ledger_to = find_a_field('warehouse','ledger_id','warehouse_id='.$pi->warehouse_to);

$cc_to = find_a_field_sql('select c.id from cost_center c, warehouse w where c.cc_code=w.acc_code and w.warehouse_id='.$pi->warehouse_to);

$cc_from = find_a_field_sql('select c.id from cost_center c, warehouse w where c.cc_code=w.acc_code and w.warehouse_id='.$pi->warehouse_from);

$amt = find_a_field('production_issue_detail','sum(total_amt)','pi_no='.$pi_no);

$sales_ledger = 1078000200010000;	

$narration = 'PI No-'.$pi_no.'||SendDt:'.$pi->pi_date;

	if($pi->status=='SEND')

	{

	auto_insert_store_transfer_issue($pi->pi_date,$sales_ledger,$ledger_from,$pi_no,$amt,$pi_no,$cc_from,$narration,$pi->entry_by,$pi->entry_at);

	}

	elseif($pi->status=='RECEIVED')

	{

	auto_insert_store_transfer_issue($pi->pi_date,$sales_ledger,$ledger_from,$pi_no,$amt,$pi_no,$cc_from,$narration,$pi->entry_by,$pi->entry_at);

	$narration = 'PI No-'.$pi_no.'||RSL-'.$pi->rec_sl_no.'||RecDt:'.$pi->receive_date;

	auto_insert_store_transfer_receive($pi->receive_date,$ledger_to,$sales_ledger,$pi_no,$amt,$pi_no,$cc_to,$narration,$pi->received_by,$pi->received_at);

	}

}



function auto_insert_sale_sc($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no='',$cc_code='',$narration=''){

$jv_date=date_2_stamp_add_mon_duration($sdate);

$jv=next_journal_sec_voucher_id('','InterPurchase',$_SESSION['user']['depot']);

$tr_from = 'InterPurchase';

if($narration!='') $narration = $narration;

else $narration = 'PI No# '.$do_no;

add_to_sec_journal($proj_id, $jv, $jv_date, $sales_ledger, $narration, '0', $amt, $tr_from, $do_no,'','',$cc_code);

add_to_sec_journal($proj_id, $jv, $jv_date, $ledger, $narration,  $amt,'0', $tr_from, $do_no,'','',$cc_code);

}



function auto_insert_sale_sc_in($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no='',$cc_code='',$narration='',$group_for=''){

$jv_date=date_2_stamp_add_mon_duration($sdate);

$jv=next_journal_sec_voucher_id('','InterPurchaseIN',$_SESSION['user']['depot']);

$tr_from = 'InterPurchaseIN';

if($narration!='') $narration = $narration;

else $narration = 'PI No# '.$do_no;

add_to_sec_journal($proj_id, $jv, $jv_date, $sales_ledger, $narration, '0', $amt, $tr_from, $do_no,'','',$cc_code,'$group_for');

add_to_sec_journal($proj_id, $jv, $jv_date, $ledger, $narration,  $amt,'0', $tr_from, $do_no,'','',$cc_code,'$group_for');

}

function auto_insert_sale_sc_out($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no='',$cc_code='',$narration='',$group_for=''){

$jv_date=date_2_stamp_add_mon_duration($sdate);

$jv=next_journal_sec_voucher_id('','InterPurchaseOUT',$_SESSION['user']['depot']);

$tr_from = 'InterPurchaseOUT';

if($narration!='') $narration = $narration;

else $narration = 'PI No# '.$do_no;

add_to_sec_journal($proj_id, $jv, $jv_date, $sales_ledger, $narration, '0', $amt, $tr_from, $do_no,'','',$cc_code,'$group_for');

add_to_sec_journal($proj_id, $jv, $jv_date, $ledger, $narration,  $amt,'0', $tr_from, $do_no,'','',$cc_code,'$group_for');

}

function auto_insert_other_issue($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no){

$now_time = date('Y-m-d H:i:s');

$jv=next_journal_sec_voucher_id('','Sales',$_SESSION['user']['depot']);

//insert to bdt sales acc credit

$journal="INSERT INTO `secondary_journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('', '$jv', '$sdate', '$ledger', 'Chalan#$chalan_no (DO#$do_no)', '0', '$amt', 'Sales','$chalan_no','$do_no','".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);



$journal="INSERT INTO `secondary_journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('', '$jv', '$sdate', '$sales_ledger', 'Chalan#$chalan_no (DO#$do_no)', '$amt', '0', 'Sales','$chalan_no','$do_no','".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);

}





function auto_insert_sale($sdate,$ledger,$sales_ledger,$do_no,$amt,$chalan_no){

$now_time = date('Y-m-d H:i:s');

$jv=next_journal_sec_voucher_id('','Sales',$_SESSION['user']['depot']);

//insert to bdt sales acc credit

$journal="INSERT INTO `secondary_journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('', '$jv', '$sdate', '$ledger', 'Chalan#$chalan_no (DO#$do_no)', '0', '$amt', 'Sales','$chalan_no','$do_no','".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);



$journal="INSERT INTO `secondary_journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`,

`tr_no`,

`tr_id`,

group_for,

entry_at,

user_id

)

VALUES ('', '$jv', '$sdate', '$sales_ledger', 'Chalan#$chalan_no (DO#$do_no)', '$amt', '0', 'Sales','$chalan_no','$do_no','".$_SESSION['user']['group']."','$now_time','".$_SESSION['user']['id']."')";

db_query($journal);

}





	function reinsert_damage_return_secoundary($damage_no)

	{

	$sql = 'delete from secondary_journal where tr_from="DamageReturn" and tr_no="'.$damage_no.'"';

	db_query($sql);

	auto_insert_damage_return_secoundary($damage_no);

	}



function auto_insert_damage_return_secoundary($damage_no)

{

$jv_no=next_journal_sec_voucher_id('','DamageReturn',$_SESSION['user']['depot']);

$tr_from = 'DamageReturn';

$sql ="select sum(a.amount) as total_amt,a.id,a.or_no,a.or_date,a.vendor_id from warehouse_damage_receive_detail a,damage_cause c where 

a.receive_type=c.id and c.payable='Yes' and  a.or_no=".$damage_no;

$data = find_all_field_sql($sql);





$sql ="select a.* from warehouse_damage_receive_detail a,damage_cause c where 

a.receive_type=c.id and c.payable='Yes' and c.id<3 and  a.or_no=".$damage_no;

$sqld = db_query($sql);

if(mysqli_num_rows($sqld)>0)

{

	while($frash = mysqli_fetch_object($sqld))

	{

		$frash_amt = $frash_amt + $frash->amount;

		journal_item_control($frash->item_id ,$frash->warehouse_id,$frash->or_date,$frash->qty,0,$tr_from,$frash->id,$frash->rate,'',$frash->or_no);

	}

}



$jv_date = strtotime($data->or_date);



$master = find_all_field('warehouse_damage_receive','manual_or_no',"or_no=".$damage_no);

$damage_warehouse_ledger = find_a_field('warehouse','ledger_id',"warehouse_id=51");

$dealer= find_all_field('dealer_info','account_code',"dealer_code=".$data->vendor_id);

$dealer_ledger= $dealer->account_code;

$acc_code = find_a_field_sql("select c.id from warehouse w, cost_center c where w.acc_code=c.cc_code and w.warehouse_id=".$dealer->depot);

$cc_code = ($acc_code>0)?$acc_code:'0';

$tr_id = $data->or_no;

$tr_no = $data->or_no;





$narration = 'Damage Return DR#'.$damage_no.'(SC-'.$master->manual_or_no.')'.'(PS-Date:'.$master->party_sent_date.')';

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration, '0', $data->total_amt, $tr_from, $tr_no,'',$tr_id,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, '4026000100010000', $narration, ($data->total_amt-$frash_amt), '0', $tr_from, $tr_no,'',$tr_id,$cc_code);

if($frash_amt>0)

add_to_sec_journal($proj_id, $jv_no, $jv_date, $damage_warehouse_ledger, $narration, $frash_amt, '0', $tr_from, $tr_no,'',$tr_id,$cc_code);



}







function auto_insert_bulk_sales_return_secoundary($or_no)

{



$tr_from = 'BulkReturn';

$narration = 'Sales Return SR#'.$or_no;

$or = find_all_field('warehouse_other_receive','',"or_no=".$or_no);

$chalan_date = $or->receive_date;

$jv_date = strtotime($or->or_date);

$dealer= find_all_field('dealer_info','account_code',"dealer_code=".$or->vendor_id);

$dealer_ledger = $dealer->account_code;



$jv_no=next_journal_sec_voucher_id('','BulkReturn',$dealer->depot);



if($_SESSION['user']['group']==2){

$acc_code = find_all_field_sql("select c.id,w.ledger_id from warehouse w, cost_center c where w.acc_code=c.cc_code and w.warehouse_id=".$or->warehouse_id);

$cc_code = ($acc_code->id>0)?$acc_code->id:'0';}

else{

$acc_code = find_all_field_sql("select ledger_id from warehouse w where  w.warehouse_id=".$or->warehouse_id); 

$cc_code = 0;}



$return_ledger = find_all_field('config_group_class','sales_return',"group_for=".$_SESSION['user']['group']);



$COGR_price = find_a_field_sql("select  sum(i.f_price*c.qty) as price from warehouse_other_receive_detail c,item_info i where c.item_id=i.item_id and c.rate>0 and  i.item_brand!='Memo' and c.or_no=".$or_no);



$sold_price = find_a_field_sql("select  sum(c.rate*c.qty) as price from warehouse_other_receive_detail c,item_info i where c.item_id=i.item_id and c.rate>0 and  i.item_brand!='Memo' and c.or_no=".$or_no);







if($sold_price!=0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $dealer_ledger, $narration, '0',($sold_price - $total_free_price), $tr_from, $or_no,'',$or_no,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, $return_ledger->sales_return, $narration,  $sold_price - $total_free_price,'0', $tr_from, $or_no,'',$or_no,$cc_code);

}



if($COGR_price!=0){

add_to_sec_journal($proj_id, $jv_no, $jv_date, $return_ledger->cost_of_goods_return, $narration, '0',$COGR_price-$free_unrealised_total, $tr_from, $or_no,'',$or_no,$cc_code);

add_to_sec_journal($proj_id, $jv_no, $jv_date, $acc_code->ledger_id, $narration,  $COGR_price,'0', $tr_from, $or_no,'',$or_no,$cc_code);

}





}



function auto_insert_sales($sdate,$ledger,$sales_ledger,$order_id,$amt,$wo_id){

$amount_bdt=number_format((($amt)*75),2,'.','');

$amount_usd=number_format((($amt)),2,'.','');





$jv=next_journal_sec_voucher_id();

//insert to bdt sales acc credit

$journal="INSERT INTO `journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`

)

VALUES ('', '$jv', '$sdate', '$sales_ledger', 'Wo No # $wo_id (Order No# $order_id)', '0', '$amount_bdt', 'AUTO')";

db_query($journal);

//insert to usd sales acc credit

$journal="INSERT INTO `lc_usd_journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`

)

VALUES ('', '$jv', '$sdate', '$sales_ledger', 'Order No# $order_id', '0', '$amount_usd', 'AUTO')";

db_query($journal);



//insert to bdt customer acc debit

$jv=next_journal_voucher_id();

$journal="INSERT INTO `journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`

)

VALUES ('', '$jv', '$sdate', '$ledger', 'Order No# $order_id', '$amount_bdt', '0', 'AUTO')";

db_query($journal);

//insert to usd customer acc debit

$journal="INSERT INTO `lc_usd_journal` (

`proj_id` ,

`jv_no` ,

`jv_date` ,

`ledger_id` ,

`narration` ,

`dr_amt` ,

`cr_amt` ,

`tr_from`

)

VALUES ('', '$jv', '$sdate', '$ledger', 'Order No# $order_id', '$amount_usd', '0', 'AUTO')";

db_query($journal);

}

function next_journal_voucher_id($date='',$tr_from = '',$depot_id='')

	{

	if($tr_from 	== 'Receipt') 			$tr = '01';

	elseif($tr_from == 'Payment') 			$tr = '02';

	elseif($tr_from == 'Ledger') 			$tr = '03';

	elseif($tr_from == 'Journal_info') 		$tr = '04';

	elseif($tr_from == 'Sales') 			$tr = '05';

	elseif($tr_from == 'Purchase') 			$tr = '06';

	elseif($tr_from == 'Closing') 			$tr = '07';

	elseif($tr_from == 'Opening') 			$tr = '08';

	elseif($tr_from == 'Contra') 			$tr = '09';

	elseif($tr_from == 'Collection') 		$tr = '10';

	elseif($tr_from == 'DamageReturn') 		$tr = '11';

	elseif($tr_from == 'SalesReturn') 		$tr = '12';

	elseif($tr_from == 'InterPurchase') 	$tr = '13';

	elseif($tr_from == 'StoreIssued') 		$tr = '14';

	elseif($tr_from == 'StoreReceived') 	$tr = '15';

	elseif($tr_from == 'InterPurchaseIN') 	$tr = '16';

	elseif($tr_from == 'InterPurchaseOUT') 	$tr = '17';

	elseif($tr_from == 'Purchase Return') 	$tr = '18';

	elseif($tr_from == 'Reprocess Issue') 	$tr = '19';

	elseif($tr_from == 'Reprocess Receive') $tr = '20';

	elseif($tr_from == 'Secondary Claim') 	$tr = '21';

	elseif($tr_from == 'StaffSales') 		$tr = '22';

	elseif($tr_from == 'Sample Issue') 		$tr = '23';

	elseif($tr_from == 'Other Issue') 		$tr = '24';

	elseif($tr_from == 'Other Sales') 		$tr = '25';

	elseif($tr_from == 'Consumption') 		$tr = '26';

	elseif($tr_from == 'Direct Sales') 		$tr = '27';

	elseif($tr_from == 'Entertainment Issue')$tr = '28';

	elseif($tr_from == 'Bulk Sales') 		$tr = '29';

	elseif($tr_from == 'BulkReturn') 		$tr = '30';

	elseif($tr_from == 'Co-Operative') 		$tr = '31';

	elseif($tr_from == 'Auto Journal') 		$tr = '32';

	elseif($tr_from == 'Import') 			$tr = '33';	

	else $tr = '00';

	

	if($depot_id>0)

	$depot_id = sprintf('%02d', $depot_id);

	elseif($_SESSION['user']['depot']>0)

	$depot_id = sprintf('%02d', $_SESSION['user']['depot']);

	else

	$depot_id = sprintf('%02d', $_SESSION['user']['group']);



	$min = date("ymd").$tr.$depot_id."0000";$max = $min+10000;

	

	$sql ="select MAX(jv_no) jv_no from journal where jv_no between '".$min."' and '".$max."'";

			$jv_no=mysqli_fetch_row(db_query($sql));

			if($jv_no[0]>$min)

			$jv=$jv_no[0]+1;

			else

			$jv=$min+1;

			

			

			return $jv;

	}

	

	function next_journal_voucher_id2($date_format='',$date='',$tr_from = '',$depot_id='')

	{

			

		

    if($tr_from 	== 'Receipt') 			$tr = '01';

	elseif($tr_from == 'Payment') 			$tr = '02';

	elseif($tr_from == 'Ledger') 			$tr = '03';

	elseif($tr_from == 'Journal_info') 		$tr = '04';

	elseif($tr_from == 'Sales') 			$tr = '05';

	elseif($tr_from == 'Purchase') 			$tr = '06';

	elseif($tr_from == 'Closing') 			$tr = '07';

	elseif($tr_from == 'Opening') 			$tr = '08';

	elseif($tr_from == 'Contra') 			$tr = '09';

	elseif($tr_from == 'Collection') 		$tr = '10';

	elseif($tr_from == 'DamageReturn') 		$tr = '11';

	elseif($tr_from == 'SalesReturn') 		$tr = '12';

	elseif($tr_from == 'InterPurchase') 	$tr = '13';

	elseif($tr_from == 'StoreIssued') 		$tr = '14';

	elseif($tr_from == 'StoreReceived') 	$tr = '15';

	elseif($tr_from == 'InterPurchaseIN') 	$tr = '16';

	elseif($tr_from == 'InterPurchaseOUT') 	$tr = '17';

	elseif($tr_from == 'Purchase Return') 	$tr = '18';

	elseif($tr_from == 'Reprocess Issue') 	$tr = '19';

	elseif($tr_from == 'Reprocess Receive') $tr = '20';

	elseif($tr_from == 'Secondary Claim') 	$tr = '21';

	elseif($tr_from == 'StaffSales') 		$tr = '22';

	elseif($tr_from == 'Sample Issue') 		$tr = '23';

	elseif($tr_from == 'Other Issue') 		$tr = '24';

	elseif($tr_from == 'Other Sales') 		$tr = '25';

	elseif($tr_from == 'Consumption') 		$tr = '26';

	elseif($tr_from == 'Direct Sales') 		$tr = '27';

	elseif($tr_from == 'Entertainment Issue')$tr = '28';

	elseif($tr_from == 'Bulk Sales') 		$tr = '29';

	elseif($tr_from == 'BulkReturn') 		$tr = '30';

	elseif($tr_from == 'Co-Operative') 		$tr = '31';

	elseif($tr_from == 'Auto Journal') 		$tr = '32';

	elseif($tr_from == 'Import') 			$tr = '33';	

	else $tr = '00';

	

	$depot_id = sprintf('%02d', $depot_id);



	$min = date("ymd").$tr.$depot_id."0000";$max = $min+10000;

	

	$sql ="select MAX(jv_no) jv_no from journal where jv_no between '".$min."' and '".$max."'";

			$jv_no=mysqli_fetch_row(db_query($sql));

			if($jv_no[0]>$min)

			$jv=$jv_no[0]+1;

			else

			$jv=$min+1;

			

			

			return $jv;

	}

		function next_journal_sec_voucher_id2($date_format='',$date='',$tr_from = '',$depot_id='')

	{	

	

	if($tr_from 	== 'Receipt') 			$tr = '01';

	elseif($tr_from == 'Payment') 			$tr = '02';

	elseif($tr_from == 'Ledger') 			$tr = '03';

	elseif($tr_from == 'Journal_info') 		$tr = '04';

	elseif($tr_from == 'Sales') 			$tr = '05';

	elseif($tr_from == 'Purchase') 			$tr = '06';

	elseif($tr_from == 'Closing') 			$tr = '07';

	elseif($tr_from == 'Opening') 			$tr = '08';

	elseif($tr_from == 'Contra') 			$tr = '09';

	elseif($tr_from == 'Collection') 		$tr = '10';

	elseif($tr_from == 'DamageReturn') 		$tr = '11';

	elseif($tr_from == 'SalesReturn') 		$tr = '12';

	elseif($tr_from == 'InterPurchase') 	$tr = '13';

	elseif($tr_from == 'StoreIssued') 		$tr = '14';

	elseif($tr_from == 'StoreReceived') 	$tr = '15';

	elseif($tr_from == 'InterPurchaseIN') 	$tr = '16';

	elseif($tr_from == 'InterPurchaseOUT') 	$tr = '17';

	elseif($tr_from == 'Purchase Return') 	$tr = '18';

	elseif($tr_from == 'Reprocess Issue') 	$tr = '19';

	elseif($tr_from == 'Reprocess Receive') $tr = '20';

	elseif($tr_from == 'Secondary Claim') 	$tr = '21';

	elseif($tr_from == 'StaffSales') 		$tr = '22';

	elseif($tr_from == 'Sample Issue') 		$tr = '23';

	elseif($tr_from == 'Other Issue') 		$tr = '24';

	elseif($tr_from == 'Other Sales') 		$tr = '25';

	elseif($tr_from == 'Consumption') 		$tr = '26';

	elseif($tr_from == 'Direct Sales') 		$tr = '27';

	elseif($tr_from == 'Entertainment Issue')$tr = '28';

	elseif($tr_from == 'Bulk Sales') 		$tr = '29';

	elseif($tr_from == 'BulkReturn') 		$tr = '30';

	elseif($tr_from == 'Co-Operative') 		$tr = '31';

	elseif($tr_from == 'Auto Journal') 		$tr = '32';

	elseif($tr_from == 'Import') 			$tr = '33';

	else $tr = '00';

	

	$depot_id = sprintf('%02d', $depot_id);



	$min = date("ymd").$tr.$depot_id."0000";$max = $min+10000;

	

	$s ="select MAX(jv_no) jv_no from secondary_journal where jv_no between '".$min."' and '".$max."'";

				$jv_no=@mysqli_fetch_row(db_query($s));

	

				if($jv_no[0]>$min)

				$jv=$jv_no[0]+1;

				else

				$jv=$min+1;

				

				return $jv;

	}

function next_journal_sec_voucher_id($date='',$tr_from = '',$depot_id='')

{

	

	if($tr_from 	== 'Receipt') $tr = '01';

	elseif($tr_from == 'Payment') $tr = '02';

	elseif($tr_from == 'Contra') $tr = '03';

	elseif($tr_from == 'Journal') $tr = '04';

	elseif($tr_from == 'Sales') $tr = '05';

	elseif($tr_from == 'Purchase') $tr = '06';

	elseif($tr_from == 'Sales Return') $tr = '07';

	elseif($tr_from == 'Goods Return') $tr = '08';

	elseif($tr_from == 'Damage Return') $tr = '09';

	elseif($tr_from == 'Collection') $tr = '10';

	elseif($tr_from == 'DamageReturn') $tr = '11';

	elseif($tr_from == 'SalesReturn') $tr = '12';

	elseif($tr_from == 'InterPurchase') $tr = '13';

	elseif($tr_from == 'StoreIssued') $tr = '14';

	elseif($tr_from == 'StoreReceived') $tr = '15';

	elseif($tr_from == 'InterPurchaseIN') $tr = '16';

	elseif($tr_from == 'InterPurchaseOUT') $tr = '17';

	elseif($tr_from == 'Purchase Return') $tr = '18';

	elseif($tr_from == 'Reprocess Issue') $tr = '19';

	elseif($tr_from == 'Reprocess Receive') $tr = '20';

	else $tr = '00';

	

	$depot_id = sprintf('%02d', $depot_id);



	$min = date("ymd").$tr.$depot_id.$user_id."0000";$max = $min+10000;

	

	$s ="select MAX(jv_no) jv_no from secondary_journal where jv_no between '".$min."' and '".$max."'";

				$jv_no=@mysqli_fetch_row(db_query($s));

	

				if($jv_no[0]>$min)

				$jv=$jv_no[0]+1;

				else

				$jv=$min+1;

				

				return $jv;

}

function reentry_do_collection($do_no){

$sql = db_query('delete from journal where tr_from = "Collection" and tr_no = "'.$do_no.'"');

$do = find_all_field('sale_do_master','s','do_no='.$do_no);

auto_entry_do_collection($do_no,$do->received_amt,$do->acc_note,$do->receive_acc_head,$do->rec_date,$_SESSION['user']['id']);

}

function auto_entry_do_collection($do_no,$received_amt,$acc_note,$receive_acc_head,$rec_date,$user_id){





$found = find_a_field('journal','1','tr_no="'.$do_no.'" and tr_from="Collection"');

if($found==0){

			$jv=next_journal_voucher_id('','Collection',$_SESSION['user']['depot']);

			$do = find_all_field('sale_do_master','s','do_no='.$do_no);

			$jv_no=next_journal_voucher_id('','Collection');

			$jv_date = time();

			$ledger_cr = find_a_field('dealer_info','account_code','dealer_code='.$do->dealer_code);

			$ledger_dr = $receive_acc_head;

			if($rec_date!=date('Y-m-d'))

			$ex = '/Rec Date:'.$rec_date;

$narration_cr = 'DO#'.$do->do_no.'/'.find_a_field('accounts_ledger','ledger_name','ledger_id='.$ledger_dr).'/'.$do->payment_by.'/'.$do->branch.'/'.$acc_note.'/'.$do->remarks.$ex;

$narration_dr = 'DO#'.$do->do_no.'/'.find_a_field('accounts_ledger','ledger_name','ledger_id='.$ledger_cr).'/'.$do->payment_by.'/'.$do->branch.'/'.$acc_note.'/'.$do->remarks.$ex;

			$tr_from = 'Collection';

			$jv = 0;

		if($ledger_dr==0&&$do->payment_by!='Cash'){

		echo 'CASH ERROR!';

		}

		else

		{

		if($received_amt>0&&$do->payment_by!='Cash')

		{

			add_to_journal($proj_id, $jv_no, $jv_date, $ledger_cr, $narration_cr, '', $received_amt, $tr_from, $do_no);

			add_to_journal($proj_id, $jv_no, $jv_date, $ledger_dr, $narration_dr, $received_amt, '', $tr_from, $do_no);

		}

			$journal = "UPDATE `sale_do_master` SET `receive_acc_head` = '".$receive_acc_head."',`rec_date` = '".$rec_date."',`received_amt` = '".$received_amt."',`checked_at` = '".date('Y-m-d H:i:s')."',`checked_by` = '".$user_id."',`received_status` = 'received',final_jv_no='".$jv_no."',acc_note='".$acc_note."' WHERE `do_no` ='".$do_no."'";

			db_query($journal);

			echo 'Received!';

			}

			}

			else echo 'ERROR! CALL MIS.';

}

function js_ledger_subledger_autocomplete_new($ledger_type,$proj_id,$type='',$group_for='')

{

if(	$ledger_type	==	'receipt'	) $balance_type = " and balance_type IN ('Credit','Both') ";

if(	$ledger_type	==	'payment'	) $balance_type = " and balance_type IN ('Debit','Both') ";

//if(	$ledger_type	==	'contra'	) $balance_type = " and ledger_group_id in (select group_id from ledger_group where group_sub_class='1020')";

//if(	$ledger_type	==	'journal' && $_SESSION['user']['group']==2) $balance_type = " and ledger_group_id!=1086  ";

//if(	$ledger_type	==	'journal' ) $balance_type = " and ledger_group_id NOT IN (126001,126002)  ";

if(	$ledger_type	==	'journal' ) $balance_type = " ";

if(	$ledger_type	==	'contra' ) $balance_type  = "   ";

//if(	$ledger_type	==	'journal' && $_SESSION['user']['group']!=2) $balance_type = " and balance_type IN ('Credit','Debit','Both')";

if(	$group_for	>	1	) $groupfor = " and group_for =".$group_for;



$under_ledger = '[';

	   $a2="select 

			ledger_id, 

			ledger_name ,

			ledger_group_id

		from 

			accounts_ledger 

		where 

		parent=0 

			".$balance_type.$groupfor." 

		order by ledger_id ";

		$a1	=	db_query($a2);

		

	  	while($a = mysqli_fetch_row($a1)){

		$father_ledger_name=father_ledger_name($a[2]);

		$l_name = $a[1].$father_ledger_name;

		$l_name = str_replace('#','@',$l_name);

		//$under_ledger .= '{ value: "'.$a[0].'",  label: "'.$a[0].'::'.$l_name.'"},';}

	 	$under_ledger .= '{ value: "'.$a[0].'",  label: "'.$a[0].'::'.$l_name.'"},';}

        $under_ledger = substr($under_ledger, 0, -1);

	    $under_ledger .= ']';	

echo '<script type="text/javascript">





var data = '.$under_ledger.';



$(function() {

$("#ledger_id").autocomplete({

		source:data, 

		matchContains: false,

		minChars: 0,

		minLength:0,

		scroll: true,

		scrollHeight: 300

	}).bind("focus", function(){ $(this).autocomplete("search"); });



});



</script>';

}



//function father_ledger_name($ledger_id)

//{

//	if (strpos($ledger_id,'00000000') == false) 

//	{

//		if (strpos(substr($ledger_id, -4),'0000') >0) 

//		$f = substr($ledger_id, 0,12).'0000'; 		// S S L

//		else

//		$f = substr($ledger_id, 0,8).'00000000'; 	//   S L

//		

//		

//		$father_ledger_name=find_a_field('accounts_ledger','ledger_name','ledger_id='.$f);

//		return ' >>> '.$father_ledger_name;

//	}

//}











function father_ledger_name($ledger_id)

{

	if ($ledger_id>0) 

	{

	



		$father_ledger_name=find_a_field('ledger_group','group_name','group_id='.$ledger_id);

		return ' >>> '.$father_ledger_name;

	}

}



function group_class($rp){

	if($rp==100) 		$cls='Asset';

	elseif($rp==200) 	$cls='Income';

	elseif($rp==300) 	$cls='Expense';

	elseif($rp==400) 	$cls='Liabilities';

	else $cls=NULL;

	return $cls;

}

function next_group_id($cls)

{

$max=(ceil(($cls+1)/1000))*1000;

$min=$cls;

$s='select max(group_id) from ledger_group where group_id>'.$min.' and group_id<'.$max;

$sql=db_query($s);

if(mysqli_num_rows($sql)>0)

$data=mysqli_fetch_row($sql);

else

$acc_no=$min+1;

if(!isset($acc_no)&&(is_null($data[0]))) 

$acc_no=$min+1;

else

$acc_no=$data[0]+1;

return $acc_no;

}







function next_ledger_id($group_id)

{

	 $max=($group_id*1000000000000)+1000000000000;

	 $min=($group_id*1000000000000);

	 $s='select max(ledger_id) from accounts_ledger where ledger_id like "%00000000" and ledger_id>'.$min.' and ledger_id<'.$max;

	$sql=db_query($s);

	if(mysqli_num_rows($sql)>0)

	$data=mysqli_fetch_row($sql);

	else

	$acc_no=$min+100000000;

	if(!isset($acc_no)&&(is_null($data[0]))) 

	$acc_no=$min+100000000;

	else

	$acc_no=$data[0]+100000000;

	return $acc_no;

}





function under_ledger_id($id)

{

//***-***-***

if(($id%100000000)==0)// group level

{

	$max=($id)+100000000;

	$min=($id)-1;

	$add=10000;		// make ledger

$s='select ledger_id from accounts_ledger where ledger_id>'.$min.' and ledger_id<'.$max.' order by ledger_id desc limit 1';

}

elseif(($ledger_id%10000)==0)// ledger level

{

	$max=($id)+10000;

	$min=($id)-1;

	$add=1;		// make ledger

$s='select sub_ledger_id from sub_ledger where sub_ledger_id>'.$min.' and sub_ledger_id<'.$max.' order by sub_ledger_id desc limit 1';

}



$sql=db_query($s);

if(mysqli_num_rows($sql)>0)

$data=mysqli_fetch_row($sql);

else

$acc_no=($id)+$add;

if(!isset($acc_no)) 

$acc_no=$data[0]+$add;

return $acc_no;

}

function next_sub_ledger_id($ledger_id)

{

$max=$ledger_id+100000000;

$min=$ledger_id;

$s='select max(ledger_id) from accounts_ledger where ledger_id like "%0000" and ledger_id>'.$min.' and ledger_id<'.$max;

$sql=db_query($s);

if(mysqli_num_rows($sql)>0)

$data=mysqli_fetch_row($sql);

else

$acc_no=$min+10000;

if(!isset($acc_no)&&(is_null($data[0]))) 

$acc_no=$min+10000;

else

$acc_no=$data[0]+10000;

return $acc_no;

}



function next_sub_sub_ledger_id($ledger_id)

{

 $max=number_format(($ledger_id+10000),0,'','');

 $min=number_format($ledger_id,0,'','');

$s='select max(ledger_id) from accounts_ledger where ledger_id>'.$min.' and ledger_id<'.$max;

$sql=db_query($s);

$c=mysqli_num_rows($sql);

if($c>0)

$data=mysqli_fetch_row($sql);

else

$acc_no=number_format(($min+1),0,'','');

if(!isset($acc_no)&&(is_null($data[0]))) 

$acc_no=number_format(($min+1),0,'','');

else

$acc_no=number_format(($data[0]+1),0,'','');

return $acc_no;

}



function group_ledger_id($group_id)

{

return $group_id*1000000;

}

function sub_ledger_create($sub_ledger_id,$name, $under, $balance, $now, $proj_id)

{

			$sql="INSERT INTO `sub_ledger` (

			`sub_ledger_id` ,

			`sub_ledger` ,

			`ledger_id` ,

			`opening_balance` ,

			`created_on` ,

			`proj_id`

			)

			VALUES ('$sub_ledger_id','$name', '$under', '$balance', '$now', '$proj_id')";



		$query=db_query($sql);

}

function ledger_create($ledger_id,$ledger_name,$ledger_group_id,$opening_balance,$balance_type,$depreciation_rate,$credit_limit, $opening_balance_on,$proj_id,$budget_enable='NO',$account_no='',$bank_name='',$swift_code='',$purpose='',$address='')

{

		echo $sql="INSERT INTO `accounts_ledger` 

		(`ledger_id`,

		`ledger_name` ,

		`ledger_group_id` ,

		`opening_balance` ,

		`balance_type` ,

		`depreciation_rate` ,

		`credit_limit` ,

		`opening_balance_on` ,

		`proj_id`,

		`budget_enable`,`accounts_number`,`bank_name`,`swift_code`,`purpose`,`address`)

		VALUES ('$ledger_id','$ledger_name', '$ledger_group_id', '$opening_balance', '$balance_type', '$depreciation_rate', '$credit_limit', '$opening_balance_on','$proj_id','$budget_enable','$account_no','$bank_name','$swift_code','$purpose','$address')";

		if(db_query($sql))

		return TRUE;

		else 

		return FALSE;

}

function ledger_redundancy($ledger_name,$ledger_id='')

{	

	if($ledger_id!='')

	$advance_check=" and ledger_id!='$ledger_id'";

	$check="select ledger_id from accounts_ledger where ledger_name='$ledger_name'".$advance_check;

	if(mysqli_num_rows(db_query($check))>0)

	return FALSE;

	else

	return TRUE;

}

function group_redundancy($group_name,$manual_group_code='',$group_id='')

{

	if($manual_group_code!='')

	$add_check=" or manual_group_code='$manual_group_code'";

	if($group_id!='')

	$advance_check=" and group_id!='$group_id'";

	$check="select group_id from ledger_group where group_name='$group_name'".$add_check.$advance_check;

	if(mysqli_num_rows(db_query($check))>0)

	return FALSE;

	else

	return TRUE;

}

function add_to_secondary_journal($proj_id, $jv_no, $jv_date, $ledger_id, $narration, $dr_amt, $cr_amt, $tr_from, $tr_no,$sub_ledger='',$tr_id='',$cc_code='',

$checked_at='',$checked_by='',$checked='')

{

	$journal="INSERT INTO `secondary_journal` (

	`proj_id` ,

	`jv_no` ,

	`jv_date` ,

	`ledger_id` ,

	`narration` ,

	`dr_amt` ,

	`cr_amt` ,

	`tr_from` ,

	`tr_no` ,

	`sub_ledger`,

	user_id,

	entry_at,

	group_for,

	tr_id,

	cc_code,

	checked_at,

	checked_by,

	checked

	)VALUES ('$proj_id', '$jv_no', '$jv_date', '$ledger_id', '$narration', '$dr_amt', '$cr_amt', '$tr_from', '$tr_no','$sub_ledger','".$_SESSION['user']['id']."','".date('Y-m-d H:i:s')."','".$_SESSION['user']['group']."','$tr_id','$cc_code','$checked_at','$checked_by','$checked')";

	$query_journal=db_query($journal);

}

function add_to_journal($proj_id, $jv_no, $jv_date, $ledger_id, $narration, $dr_amt, $cr_amt, $tr_from, $tr_no,$sub_ledger='',$tr_id='',$cc_code='',$group='',$entry_by,$entry_at)

{

	$journal="INSERT INTO `journal` (

	`proj_id` ,

	`jv_no` ,

	`jv_date` ,

	`ledger_id` ,

	`narration` ,

	`dr_amt` ,

	`cr_amt` ,

	`tr_from` ,

	`tr_no` ,

	`sub_ledger`,

	entry_at,

	group_for,

	tr_id,

	cc_code

	)VALUES ('$proj_id', '$jv_no', '$jv_date', '$ledger_id', '$narration', '$dr_amt', '$cr_amt', '$tr_from', '$tr_no','$sub_ledger','".date('Y-m-d H:i:s')."','".$_SESSION['user']['group']."','$tr_id','$cc_code')";

	$query_journal=db_query($journal);

}

function add_to_vtype($proj_id, $jv_no, $jv_date, $ledger_id, $narration, $dr_amt, $cr_amt, $tr_from, $tr_no,$sub_ledger='',$tr_id='',$cc_code='')

{

	$journal="INSERT INTO `journal` (

	`proj_id` ,

	`jv_no` ,

	`jv_date` ,

	`ledger_id` ,

	`narration` ,

	`dr_amt` ,

	`cr_amt` ,

	`tr_from` ,

	`tr_no` ,

	`sub_ledger`,

	user_id,

	entry_at,

	group_for,

	tr_id,

	cc_code

	)VALUES ('$proj_id', '$jv_no', '$jv_date', '$ledger_id', '$narration', '$dr_amt', '$cr_amt', '$tr_from', '$tr_no','$sub_ledger','".$_SESSION['user']['id']."','".date('Y-m-d H:i:s')."','".$_SESSION['user']['group']."','$tr_id','$cc_code')";

	$query_journal=db_query($journal);

}



function add_to_sec_journal($proj_id, $jv_no, $jv_date, $ledger_id, $narration, $dr_amt, $cr_amt, $tr_from, $tr_no,$sub_ledger='',$tr_id='',$cc_code='',$group='',$entry_by='',$entry_at='',$received_from='', $bank='', $cheq_no='',$cheq_date='',$relavent_cash_head='',$checked='',$type='',$employee='',$remarks='',$file_upload='',$reference_id='',$note='',$pay_id='',$item_id='',$ji_id='') 



{

	if($group>0) $group_id = $group; else $group_id = $_SESSION['user']['group'];

	if($entry_at=='') $entry_at = date('Y-m-d H:i:s');

	if($entry_by=='') $entry_by = $_SESSION['user']['id']; 
 
      $journal="INSERT INTO `secondary_journal` (

	proj_id ,

	jv_no,

	jv_date ,

	ledger_id ,

	narration ,

	dr_amt ,

	cr_amt ,

	tr_from ,

	received_from,

	tr_no ,

	sub_ledger,

	entry_by,

	entry_at,

	group_for,

	tr_id,

	cc_code,

	bank,

	cheq_no,

	cheq_date,

	relavent_cash_head,

	checked,

	type,

	employee_id,

	remarks,

	file_upload,

	reference_id,

	note,

	pay_id,
		item_id,
			ji_id

	

	)VALUES ('$proj_id', '$jv_no', '$jv_date', '$ledger_id', '$narration', '$dr_amt', '$cr_amt', '$tr_from', '$received_from', '$tr_no','$sub_ledger','$entry_by','$entry_at','$group_id','$tr_id','$cc_code'

	,'$bank','$cheq_no','$cheq_date','$relavent_cash_head','$checked','$type','$employee','$remarks','$file_upload','$reference_id','$note','$pay_id','$item_id','$ji_id')";

	$query_journal=db_query($journal);

}



function sec_journal_journal($sec_jv_no,$jv_no,$tr_froms)

{

   $sql = 'select * from secondary_journal where jv_no = "'.$sec_jv_no.'" and tr_from = "'.$tr_froms.'"';
    $query = db_query($sql);
    while($data = mysqli_fetch_object($query))
    {	
        
    if($jv_no==0)  {$jv_no = $data->jv_no;}
    
    $journal="INSERT INTO `journal` (
    	`proj_id` ,
    	`jv_no` ,
    	`jv_date` ,
    	`ledger_id`,
    	`narration`,
    	`dr_amt`,
    	`cr_amt` ,
    	`tr_from` ,
    	`tr_no` ,
    	`tr_id` ,
    	`sub_ledger`,
    	entry_at,
    	group_for,
		entry_by,
    	cc_code,
		reconsilation_status,
		reconsilation_note
    	)VALUES 
            ('$data->proj_id', '$jv_no', '$data->jv_date', '$data->ledger_id', '$data->narration', '$data->dr_amt', '$data->cr_amt', '$data->tr_from', '$data->tr_no', 
            '$data->tr_id','$data->sub_ledger','".date('Y-m-d H:i:s')."', '$data->group_for','".$data->entry_by."','".$data->cc_code."','".$data->reconsilation_status."','".$data->reconsilation_note."')";
    	
    	$query_journal=db_query($journal);
    }

}

function pay_invoice_amount($proj_id, $jv_no, $jv_date, $cr_ledger_id,$dr_ledger_id, $narration, $amount, $tr_from, $jv)

{	

add_to_journal($proj_id, $jv_no, $jv_date, $cr_ledger_id, $narration, '0', $amount, $tr_from, $jv);

add_to_journal($proj_id, $jv_no, $jv_date, $dr_ledger_id, $narration, $amount, '0', $tr_from, $jv);

}

function warehouse_product_stock($item_id ,$warehouse_id,$start_date='',$end_date='')

{

	if($start_date == '') $start_date = '2000-01-01';

	if($end_date   == '') $end_date   = '2301-01-01';

	

return find_a_field('journal_item','sum(item_in-item_ex)','warehouse_id = "'.$warehouse_id.'" and item_id = "'.$item_id .'" and ji_date between "'.$start_date .'" and "'.$end_date .'"');

}

function warehouse_section_product_stock($item_id ,$section_id,$start_date='',$end_date='')

{

	if($start_date == '') $start_date = '2001-01-01';

	if($end_date   == '') $end_date   = '2201-01-01';

	$sql = 'select sum(item_in-item_ex) from warehouse w, journal_item j where w.warehouse_id=j.warehouse_id  and item_id = "'.$item_id .'" and w.section_id = "'.$section_id.'"  and ji_date between "'.$start_date .'" and "'.$end_date .'" ';

	return find_a_field_sql($sql);

}

function next_return_no($depot_id,$chalan_date)

{

	$sdate = mysqli_format2stamp($chalan_date);

	$e_ch = date('Y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'999';

	$s_ch = date('Y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'000';

	$unit = 1;

	  $sql = 'select max(return_no) from sale_do_chalan where return_no between "'.$s_ch.'" and "'.$e_ch.'" ';

	$query = db_query($sql);

	

	$data=mysqli_fetch_row($query);

if($data[0]<$s_ch)

$ch_no = $s_ch+$unit;

else

$ch_no = $data[0]+$unit;

	return $ch_no;

}



function next_chalan_no($depot_id,$chalan_date)

{

	$sdate = mysqli_format2stamp($chalan_date);

	$e_ch = date('y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'999';

	$s_ch = date('y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'000';

	$unit = 1;

	$sql = 'select max(chalan_no) from sale_do_chalan where chalan_no between "'.$s_ch.'" and "'.$e_ch.'" ';

	$query = db_query($sql);

	

	$data=mysqli_fetch_row($query);

if($data[0]<$s_ch)

$ch_no = $s_ch+$unit;

else

$ch_no = $data[0]+$unit;

	return $ch_no;

}

function next_bs_no($depot_id)

{

	$e_ch = date('y').sprintf('%02s', $depot_id).'99999';

	$s_ch = date('y').sprintf('%02s', $depot_id).'00000';

	$unit = 1;

	$sql = 'select max(chalan_no) from sale_other_chalan where chalan_no between "'.$s_ch.'" and "'.$e_ch.'" ';

	$query = db_query($sql);

	

	$data=mysqli_fetch_row($query);

if($data[0]<$s_ch)

$ch_no = $s_ch+$unit;

else

$ch_no = $data[0]+$unit;

	return $ch_no;

}

function next_pr_no($depot_id,$chalan_date)

{

	$sdate = mysqli_format2stamp($chalan_date);

	$e_ch = date('y',$sdate).$_SESSION['user']['id'].'99999';

	$s_ch = date('y',$sdate).$_SESSION['user']['id'].'00000';

	$unit = 1;

	$sql = 'select max(pr_no) from purchase_receive where pr_no between "'.$s_ch.'" and "'.$e_ch.'" ';

	$query = db_query($sql);

	

	$data=mysqli_fetch_row($query);

if($data[0]<$s_ch)

$ch_no = $s_ch+$unit;

else

$ch_no = $data[0]+$unit;

	return $ch_no;

}



function next_ss_chalan_no($depot_id,$chalan_date)

{

	$sdate = mysqli_format2stamp($chalan_date);

	$e_ch = date('y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'999';

	$s_ch = date('y',$sdate).date('m',$sdate).date('d',$sdate).sprintf('%02s', $depot_id).'000';

	$unit = 1;

	$sql = 'select max(chalan_no) from sale_other_chalan where chalan_no between "'.$s_ch.'" and "'.$e_ch.'" ';

	$query = db_query($sql);

	

	$data=mysqli_fetch_row($query);

if($data[0]<$s_ch)

$ch_no = $s_ch+$unit;

else

$ch_no = $data[0]+$unit;

	return $ch_no;

}







function journal_item_control($item_id, $warehouse_id,$ji_date,$item_in,$item_ex,$tr_from,$tr_no,$rate='',$r_warehouse='',$sr_no='',$bag_size='',$bag_unit='',$group_for='',$final_price='',$barcode='',$serial_no='')

{

$pre_stock=find_all_field('journal_item','final_stock',' item_id = "'.$item_id .'"  order by id desc');
$final_stock=($pre_stock->final_stock+$item_in)-$item_ex; 

//$final_price = ((($pre_stock->final_price*$pre_stock->final_stock)+($item_price*$item_in))/($pre_stock->final_stock+$item_in));

 

if($item_in>0){

	

	if($tr_from=='Transfered'){

	

		$item_price =find_a_field('journal_item','final_price',' item_id = "'.$item_id .'" and  tr_from="Transit" and  item_ex>0 order by id desc');

		$final_price =$item_price;

		$c_price=$final_price;

		}
			else
			if($tr_from=='Issue'){

			$item_price =find_a_field('journal_item','final_price',' item_id = "'.$item_id .'" and  tr_from="Issue" and  item_ex>0 and tr_no="'.$tr_no.'" order by id desc');

				if($item_price>0){$item_price=$item_price;}else{$item_price=$rate;}


		$final_price =$item_price;

		$c_price=$final_price;

		}

	else{	

		$item_price = $rate;

		$final_price = ((($pre_stock->final_price*$pre_stock->final_stock)+($item_price*$item_in))/($pre_stock->final_stock+$item_in));

		$c_price=$final_price;

		}

}

else{

if($tr_from=='Purchase Return' || $tr_from=='Sales Return'){
		$item_price =$rate;
	}else{	
	$item_price=find_a_field('journal_item','final_price','item_id = "'.$item_id .'" and final_price > 0  order by id desc');
	}if($final_stock>0){
	$final_price =((($pre_stock->final_price*$pre_stock->final_stock)+($item_price*($item_in-$item_ex)))/($final_stock));
	}
	if($final_price>0){$final_price=$final_price;}
	
	else{$final_price = $pre_stock->final_price;}
	
	$c_price=$pre_stock->final_price;



}



$pre_id=find_a_field('journal_item','id','item_id="'.$item_id.'" order by id desc');

  

    $sql="INSERT INTO journal_item 

  (ji_date, item_id, warehouse_id, pre_stock, pre_price, item_in, item_ex, item_price, final_stock, final_price,tr_from, tr_no, entry_by, entry_at,relevant_warehouse,sr_no,c_price,bag_size,bag_unit,group_for, barcode,pre_id,serial_no) 

  VALUES 

  ('".$ji_date."', '".$item_id."', '".$warehouse_id."', '".$pre_stock->final_stock."', '".$pre_stock->final_price."', '".$item_in."', '".$item_ex."', '".$item_price."', '".$final_stock."', '".$final_price."', '".$tr_from."', '".$tr_no."', '".$_SESSION['user']['id']."', '".date('Y-m-d H:i:s')."', '".$r_warehouse."', '".$sr_no."','".$c_price."','".$bag_size."', '".$bag_unit."', '".$group_for."', '".$barcode."','".$pre_id."','".$serial_no."')";

  db_query($sql);

}



function journal_asset_item_control($item_id ,$warehouse_id,$ji_date,$item_in,$item_ex,$tr_from,$tr_no,$rate='',$r_warehouse='',$sr_no='',$c_price='',$lot_no='',$vendor_id='',$sl_no)
{
	$pre_stock=find_all_field('journal_item','final_stock','warehouse_id = "'.$warehouse_id.'" and item_id = "'.$item_id .'" and lot_no = "'.$lot_no .'" order by id desc');
	$final_stock=($pre_stock->final_stock+$item_in)-$item_ex;
	
	if(($tr_from == 'Purchase')||($tr_from == 'Other Receive')||($tr_from == 'Local Purchase')||($tr_from == 'Sample Receive'))
	{
$item_price = $rate;
$final_price = ((($pre_stock->final_price*$pre_stock->final_stock)+($item_price*$item_in))/($pre_stock->final_stock+$item_in));
	}
	else
	{
$item_price = find_a_field('item_info','cost_price','item_id='.$item_id);
$final_price = $item_price;
if($rate!=''){$item_price = $final_price = $rate;}
	}
     $sql="INSERT INTO `journal_asset_item` 
	(`ji_date`, `item_id`, `warehouse_id`, `pre_stock`, `pre_price`, `item_in`, `item_ex`, `item_price`, `final_stock`, `final_price`,`tr_from`, `tr_no`, `entry_by`, `entry_at`,relevant_warehouse,sr_no,c_price,lot_no,serial_no) 
	VALUES 
	('".$ji_date."', '".$item_id."', '".$warehouse_id."', '".$pre_stock->final_stock."', '".$item_price."', '".$item_in."', '".$item_ex."', '".$item_price."', '".$final_stock."', '".$final_price."', '".$tr_from."', '".$tr_no."', '".$_SESSION['user']['id']."', '".date('Y-m-d H:i:s')."', '".$r_warehouse."', '".$sr_no."', '".$c_price."', '".$lot_no."','".$sl_no."')";
	db_query($sql);
}








function auto_complete_from_db($table,$show,$id,$con,$text_field_id)

{

if($con!='') $condition = " where ".$con;

  $query="Select ".$id.", ".$show." from ".$table.$condition;


if($query){
$led=db_query($query);

	if(mysqli_num_rows($led) > 0)

	{

		$ledger = '[';

		while($ledg = mysqli_fetch_row($led)){

		  $ledger .= '{ value: "'.$ledg[0].'", label: "'.$ledg[1].'" },';

		}

		$ledger = substr($ledger, 0, -1);

		$ledger .= ']';

	}

	else

	{

		$ledger = '[{ value: "empty", label: "" }]';

	}
}



echo '<script type="text/javascript">

var data = '.$ledger.';

$(function() {

    $("#'.$text_field_id.'").autocomplete({

		source:data, 

		matchContains: true,

		minChars: 0,

		minLength:0,scroll: true,

		scrollHeight: 300,

	}).bind("focus", function(){ $(this).autocomplete("search"); });

  });

</script>';

}

function auto_complete_from_db_sql($query,$text_field_id)

{





$led=db_query($query);

	if(mysqli_num_rows($led) > 0)

	{

		$ledger = '[';

		while($ledg = mysqli_fetch_row($led)){

		  $ledger .= '{ value: "'.$ledg[0].'", label: "'.$ledg[1].'" },';

		}

		$ledger = substr($ledger, 0, -1);

		$ledger .= ']';

	}

	else

	{

		$ledger = '[{ value: "empty", label: "" }]';

	}



echo '<script type="text/javascript">

$(document).ready(function(){

    var data = '.$ledger.';

    $("#'.$text_field_id.'").autocomplete({

		source:data, 

		matchContains: true,

		minChars: 0,

		minLength:0,scroll: true,

		scrollHeight: 300,

	}).bind("focus", function(){ $(this).autocomplete("search"); });

  });

</script>';

}

function auto_complete_start_from_db($table,$show,$id,$con,$text_field_id)

{

if($con!='') $condition = " where ".$con;

$query="Select ".$id.", ".$show." from ".$table.$condition;



$led=db_query($query);

	if(mysqli_num_rows($led) > 0)

	{

		$ledger = '[';

		while($ledg = mysqli_fetch_row($led)){

		  $ledger .= '{ value: "'.$ledg[0].'", label: "'.$ledg[1].'" },';

		}

		$ledger = substr($ledger, 0, -1);

		$ledger .= ']';

	}

	else

	{

		$ledger = '[{ value: "empty", label: "" }]';

	}



echo '<script type="text/javascript">

$( function() {

    var data = '.$ledger.';

    $("#'.$text_field_id.'").autocomplete({source:data});

  });

</script>';

}

function create_combobox($field_id)

{



echo '  <script>

  $( function() {

    $.widget( "custom.combobox", {

      _create: function() {

        this.wrapper = $( "<span>" )

          .addClass( "form-controls" )

          .insertAfter( this.element );

 

        this.element.hide();

        this._createAutocomplete();

        this._createShowAllButton();

      },

 

      _createAutocomplete: function() {

        var selected = this.element.children( ":selected" ),

          value = selected.val() ? selected.text() : "";

 

        this.input = $( "<input>" )

          .appendTo( this.wrapper )

          .val( value )

          .attr( "title", "" )

          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )

          .autocomplete({

            delay: 0,

            minLength: 0,

            source: $.proxy( this, "_source" )

          })

          .tooltip({

            classes: {

              "ui-tooltip": "ui-state-highlight"

            }

          });

 

        this._on( this.input, {

          autocompleteselect: function( event, ui ) {

            ui.item.option.selected = true;

            this._trigger( "select", event, {

              item: ui.item.option

            });

          },

 

          autocompletechange: "_removeIfInvalid"

        });

      },

 

      _createShowAllButton: function() {

        var input = this.input,

          wasOpen = false;

 

        $( "<a>" )

          .attr( "tabIndex", -1 )

          .attr( "title", "Show All Items" )

          .tooltip()

          .appendTo( this.wrapper )

          .button({

            icons: {

              primary: "ui-icon-triangle-1-s"

            },

            text: false

          })

          .removeClass( "ui-corner-all" )

          .addClass( "custom-combobox-toggle ui-corner-right" )

          .on( "mousedown", function() {

            wasOpen = input.autocomplete( "widget" ).is( ":visible" );

          })

          .on( "click", function() {

            input.trigger( "focus" );

 

            // Close if already visible

            if ( wasOpen ) {

              return;

            }

 

            // Pass empty string as value to search for, displaying all results

            input.autocomplete( "search", "" );

          });

      },

 

      _source: function( request, response ) {

        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );

        response( this.element.children( "option" ).map(function() {

          var text = $( this ).text();

          if ( this.value && ( !request.term || matcher.test(text) ) )

            return {

              label: text,

              value: text,

              option: this

            };

        }) );

      },

 

      _removeIfInvalid: function( event, ui ) {

 

        // Selected an item, nothing to do

        if ( ui.item ) {

          return;

        }

 

        // Search for a match (case-insensitive)

        var value = this.input.val(),

          valueLowerCase = value.toLowerCase(),

          valid = false;

        this.element.children( "option" ).each(function() {

          if ( $( this ).text().toLowerCase() === valueLowerCase ) {

            this.selected = valid = true;

            return false;

          }

        });

 

        // Found a match, nothing to do

        if ( valid ) {

          return;

        }

 

        // Remove invalid value

        this.input

          .val( "" )

          .attr( "title", value + " didnt match any item" )

          .tooltip( "open" );

        this.element.val( "" );

        this._delay(function() {

          this.input.tooltip( "close" ).attr( "title", "" );

        }, 2500 );

        this.input.autocomplete( "instance" ).term = "";

      },

 

      _destroy: function() {

        this.wrapper.remove();

        this.element.show();

      }

    });

 

    $( "#'.$field_id.'" ).combobox();

    $( "#toggle" ).on( "click", function() {

      $( "#'.$field_id.'" ).toggle();

    });

  } );

  </script>';

}





function auto_complete_start_from_db_OLD($table,$show,$id,$con,$text_field_id)

{

if($con!='') $condition = " where ".$con;

$query="Select ".$id.", ".$show." from ".$table.$condition;



$led=db_query($query);

	if(mysqli_num_rows($led) > 0)

	{

		$ledger = '[';

		while($ledg = mysqli_fetch_row($led)){

		  $ledger .= '{ name: "'.$ledg[1].'", id: "'.$ledg[0].'" },';

		}

		$ledger = substr($ledger, 0, -1);

		$ledger .= ']';

	}

	else

	{

		$ledger = '[{ name: "empty", id: "" }]';

	}



echo '<script type="text/javascript">

$(document).ready(function(){

    var data = '.$ledger.';

    $("#'.$text_field_id.'").autocomplete(data, {

		matchContains: false,

		minChars: 0,

		minLength:0,scroll: true,

		scrollHeight: 300,

        formatItem: function(row, i, max, term) {

            return row.id + " [" + row.name + "]";

		},

		formatResult: function(row) {

			return row.id;

		}

	});

  });

</script>';

}



function select_dates($past_day,$next_day,$select_day='')

{

for($i=$next_day;$i>0;$i--)

{

	echo '<option>'.date('Y-m-d',mktime(1,1,1,date('m'),(date('d')+$i),date('Y'))).'</option>';

}

if($select_day=='')

echo '<option selected>'.date('Y-m-d').'</option>';

else

{

echo '<option>'.date('Y-m-d').'</option>';

echo '<option selected>'.$select_day.'</option>';

}



for($i=1;$i<($past_day+1);$i++)

{

	echo '<option>'.date('Y-m-d',mktime(1,1,1,date('m'),(date('d')-$i),date('Y'))).'</option>';

}

}





function auto_insert_recipe_pr_id($pr)

{

	db_query('delete from production_floor_receive_detail_receipe_consumption where pr_id = "'.$pr.'"');

	$sql = 'select id from production_floor_receive_detail where pr_no='.$pr;

	$query = db_query($sql);

	while($data=mysqli_fetch_object($query))

	{

		auto_insert_recipe_pr_detail_id($data->id);

	}

}

function auto_do_collection($do_no)

{



$sql = "select concat('DO#',m.do_no,'/',l.ledger_name,'/',m.payment_by,'/',m.branch,'/',m.acc_note,'.') as narration,tr_no from sale_do_master m,journal jo,accounts_ledger l where jo.cr_amt>0 and jo.tr_from='Collection' and jo.tr_no=m.do_no and jo.ledger_id=l.ledger_id and jo.tr_no = '".$do_no."' limit 1";

$data=db_query($sql);

while($x = mysqli_fetch_object($data)){

$update1 = db_query("update journal j set narration = '".addslashes($x->narration)."' where dr_amt>0 and tr_from='Collection' and tr_no='".$x->tr_no."'");



$xx++;

echo $xx.'<BR>';

}







$sql = "select concat('DO#',m.do_no,'/',l.ledger_name,'/',m.payment_by,'/',m.branch,'/',m.acc_note,'.') as narration,tr_no from sale_do_master m,journal jo,accounts_ledger l where jo.dr_amt>0 and jo.tr_from='Collection' and jo.tr_no=m.do_no and jo.ledger_id=l.ledger_id and jo.tr_no = '".$do_no."' limit 1";

$data=db_query($sql);

while($x = mysqli_fetch_object($data)){

$update1 = db_query("update journal j set narration = '".addslashes($x->narration)."' where cr_amt>0 and tr_from='Collection' and tr_no='".$x->tr_no."'");

$xx++;

echo $xx.'<BR>';

}



}

function auto_insert_recipe_pr_detail_id($detail_id)

{

	

	$sql = 'select d.id,d.pr_no,d.item_id,d.total_unit,r.raw_item_id,r.unit_qty,r.unit_percent,d.warehouse_to,d.pr_date,r.system_loss_percentage,r.process_loss_percentage from production_floor_receive_detail d, production_ingredient_detail r where d.item_id = r.item_id and d.id='.$detail_id;

	$query = db_query($sql);

	while($data=mysqli_fetch_object($query))

	{

		

		$pr_date = $data->pr_date;

		$warehouse_id = $data->warehouse_to;

		$prd_id = $data->id;

		$pr_id = $data->pr_no;

		$received_item_id = $data->item_id;

		$received_item_qty = $data->total_unit;

		

		$recipe_item_id = $data->raw_item_id;

		$per_qty_receipe = $data->unit_qty;

		$per_qty_receipe_sloss = ($data->unit_qty*$data->system_loss_percentage)/100;

		$per_qty_receipe_ploss = ($data->unit_qty*$data->process_loss_percentage)/100;

		$total_qty_receipe = $per_qty_receipe*$received_item_qty;

		$total_qty_receipe_sloss = (($per_qty_receipe_sloss)*($received_item_qty));

		$total_qty_receipe_ploss = (($per_qty_receipe_ploss)*($received_item_qty));

		

		$insert = 'INSERT INTO `production_floor_receive_detail_receipe_consumption` 

		(`prd_id`, `pr_id`, `warehouse_id`, `pr_date`,`received_item_id`, `received_item_qty`, `recipe_item_id`, `per_qty_receipe`, per_qty_receipe_sloss,per_qty_receipe_ploss,`total_qty_receipe`, total_qty_receipe_sloss,total_qty_receipe_ploss) 

		VALUES 

		("'.$prd_id.'", "'.$pr_id.'", "'.$warehouse_id.'", "'.$pr_date.'", "'.$received_item_id.'", "'.$received_item_qty.'", "'.$recipe_item_id.'", "'.$per_qty_receipe.'", "'.$per_qty_receipe_sloss.'","'.$per_qty_receipe_ploss.'", "'.$total_qty_receipe.'","'.$total_qty_receipe_sloss.'", "'.$total_qty_receipe_ploss.'") ';

		

		db_query($insert);

		

	}

}

function jv_double_check(){

	$sql = 'SELECT jv_no , count(distinct tr_no) as tr_count,tr_no,tr_from FROM journal WHERE jv_no LIKE "17%" group by jv_no';

	$query = db_query($sql);

	while($data = mysqli_fetch_object($query))

	{

		if($data->tr_count>1)

		{

		$date_format = substr($data->jv_no,0,8);

		$new_jv_no = next_journal_voucher_id2($date_format);

		$update_sql = 'update journal set jv_no = "'.$new_jv_no.'"  where jv_no = "'.$data->jv_no.'" and tr_from="'.$data->tr_from.'" and tr_no="'.$data->tr_no.'" ';

		$update = db_query($update_sql);

		}

	}

}



function voucher_double_id_check($tr_no,$tr_from){

$sql = 'SELECT jv_no , count(distinct tr_no) as tr_count,tr_no,count(distinct jv_no) as jv_count,jv_no,tr_from,count(distinct user_id) as user_count,max(user_id) user_id FROM journal where tr_no ="'.$tr_no.'" and tr_from = "'.$tr_from.'" group by tr_no order by id asc';

	$query = db_query($sql);

	while($data = mysqli_fetch_object($query))

	{

		$user_id = $data->user_id;

		$tr_no = $data->tr_no;

		$jv_no = $data->jv_no;

		$tr_from = $data->tr_from;

		if($data->user_count>1)

		{

			if($data->jv_count==1)

				{

					$new_jv = next_journal_voucher_id('',$tr_from);

					$update_sql = 'update journal set jv_no = "'.$new_jv.'"  where jv_no = "'.$jv_no.'" and tr_from="'.$tr_from.'" and user_id="'.$data->user_id.'" ';

					$update = db_query($update_sql);

				}

			if($data->tr_count==1)

				{

					if($data->jv_count==1) $jv_no = $new_jv;

					

					if($data->tr_from =='Payment'){

					$new_tr = next_invoice('payment_no','payment');

					$update_sql = 'update payment set payment_no = "'.$new_tr.'"  where payment_no = "'.$tr_no.'" and entry_by="'.$data->user_id.'" ';

					$update = db_query($update_sql);

					}

					elseif($data->tr_from=='Receipt'){

					$new_tr = next_invoice('receipt_no','receipt');

					$update_sql = 'update receipt set receipt_no = "'.$new_tr.'"  where receipt_no = "'.$tr_no.'" and entry_by="'.$data->user_id.'" ';

					$update = db_query($update_sql);

					}

					elseif($data->tr_from=='Journal_info')

					{

					$new_tr = next_invoice('journal_info_no','journal_info');

					$update_sql = 'update journal_info set journal_info_no = "'.$new_tr.'"  where journal_info_no = "'.$tr_no.'" and entry_by="'.$data->user_id.'" ';

					$update = db_query($update_sql);

					}

					elseif($data->tr_from=='Contra')

					{

					$new_tr = next_invoice('coutra_no','coutra');

					$update_sql = 'update journal_info set journal_info_no = "'.$new_tr.'"  where journal_info_no = "'.$tr_no.'" and entry_by="'.$data->user_id.'" ';

					$update = db_query($update_sql);

					}

					

					$update_sql = 'update journal set tr_no = "'.$new_tr.'"  where jv_no = "'.$jv_no.'" and tr_from="'.$data->tr_from.'" and user_id="'.$data->user_id.'" ';

					$update = db_query($update_sql);

				}



		}

	}

	



}

function numberFormatNoy($number){

echo 11;
   $no = round($number);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'One', '2' => 'Two',
    '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
    '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
    '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
    '13' => 'Thirteen', '14' => 'Fourteen',
    '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
    '18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
    '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
    '60' => 'Sixty', '70' => 'Seventy',
    '80' => 'Eighty', '90' => 'Ninety');
   $digits = array('', 'Hundred', 'Thousand', 'Lac', 'Crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;


     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);


  $points = ($point) ?
    "" . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : ''; 

  if($points != ''){        
  echo $result . "Taka  " . $points . " Paisa Only";
} else {

    echo $result . "Taka Only";
}

}



?>
