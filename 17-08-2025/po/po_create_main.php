<?php


require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

$title='New Purchase Entry';



 $_SESSION['po_no'];

do_calander('#po_date','-5');

do_calander('#quotation_date');
do_calander('#estimated_delivery_date');

$tr_type="Show";

//create_combobox('vendor_id_draft');

$table_master='purchase_master';

$table_details='purchase_invoice';

$unique='po_no';

//var_dump($_SESSION);


if($_GET['new']>0){

unset($_SESSION[$unique]);
unset($_SESSION['selected_req_no']);

}

$req_all = find_all_field('requisition_master','','req_no="'.$_SESSION['selected_req_no'].'"');



if(isset($_POST['new']))

{



	//$folder ='purchase';
	//$field = 'file_upload';
	//$file_name = $field.'-'.$_POST['req_no'];
	//if($_FILES['file_upload']['tmp_name']!=''){
	//$_POST['file_upload']=upload_file($folder,$field,$file_name);
	//}

	$folder ='purchase';
	$field = 'file_upload';
	$file_name = $field.'-'.$_POST['req_no'];
	if($_FILES['file_upload']['tmp_name']!=''){
	$random = random_int(10000,99999);
	$newFileName = 'purchase_'.$random;
	$ext = end(explode(".",$_FILES['file_upload']['name']));
	//$_POST['file_upload']=upload_file($folder,$field,$file_name);
	file_upload_aws('file_upload','purchase',$newFileName);
	$_POST['file_upload']= $newFileName.'.'.$ext;
	}
	


	
	
		$crud   = new crud($table_master);

		

		

		if($_POST['vendor_id_draft']>0) {

		$_POST['vendor_id']=$_POST['vendor_id_draft'];

		}





		if(!isset($_SESSION[$unique])) {

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['edit_by']=$_SESSION['user']['id'];
		$_POST['status']='MANUAL';

		$_POST['edit_at']=date('Y-m-d h:s:i');
		$_POST['vendor_id'] = end(explode("##",$_POST['vendor_id']));
        $highest_level =is_approval_layer_exist('Purchase');
		if($highest_level>0){

			$_POST['current_approval_level'] = $highest_level;


		}else{
			$_POST['checked_by']=$_SESSION['user']['id'];
            $_POST['status']='CHECKED';
			$_POST['checked_at']=date('Y-m-d h:s:i');
		}

		

		$$unique=$_SESSION[$unique]=$crud->insert();

		unset($$unique);
		
		$tr_type="Initiate";
		$type=1;

		$msg='Purchase Order No Created. (PO No :-'.$_SESSION[$unique].')';

		}

		else {

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d h:s:i');
		
		$_POST['vendor_id'] = end(explode("##",$_POST['vendor_id']));

		$crud->update($unique);

		$type=1;

		$msg='Successfully Updated.';

		}

}



$$unique=$_SESSION[$unique];



if(isset($_POST['delete']))

{
$quotation_no=find_a_field('purchase_master','quotation_no','po_no="'.$$unique.'"');

if($quotation_no>0){

$quotation_update='update purchase_quotation_master set is_po=0 where quotation_no="'.$quotation_no.'"';

db_query($quotation_update);

}



		$crud   = new crud($table_master);

		$condition=$unique."=".$$unique;		

		$crud->delete($condition);

		$crud   = new crud($table_details);

		$condition=$unique."=".$$unique;		

		$crud->delete_all($condition);

		unset($$unique);

		unset($_SESSION[$unique]);
		
		$tr_type="Delete";
		$type=1;

		$msg='Successfully Deleted.';

}



if($_GET['del']>0)

{
		$tr_type="Remove";
		$crud   = new crud($table_details);

		$condition="id=".$_GET['del'];		

		$crud->delete_all($condition);

		$type=1;

		$msg='Successfully Deleted.';

}

if(isset($_POST['confirmm']))

{

		

		$_POST[$unique]=$$unique;

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['status']='UNCHECKED';
		
		$po_date = $_POST['po_date'];
	    $po_no = $_POST['po_no'];
	    $vendor_id = $_POST['vendor_id'];
		
		$res = 'select a.id,a.req_no,b.item_id as item_code, concat(b.item_name) as item_description , a.qty as req_quantity ,b.unit_name as Unit ,a.warehouse_id
		from requisition_order a,item_info b where b.item_id=a.item_id and a.req_no=' . $_SESSION['selected_req_no'];
	$query = db_query($res);
	while ($data = mysqli_fetch_object($query)) {
		$poQty = $_POST['qty' . $data->id];
		$poRate = $_POST['unit_price' . $data->id];
		$poAmount = $_POST['amount' . $data->id];
		
		
		
		//$_POST['vat']=$_POST['vaat'];
		
		$tax=$_POST['taax'];
		$vat = $_POST['vaat'];
		$rate = $poAmount/$poQty;
		
		
		
		if($_POST['vaat_include']=='Including'){
		
			$vat_rate=(($rate/(100+$vat))*$vat);
			
			$vat_amt = $vat_rate*$poQty;
			if($_POST['rebate'] == 'Yes'){
			$with_vat_rate=$rate-$vat_rate;
			}
			else{
			$with_vat_rate=$rate;
			}
			
			$with_vat_amt =number_format(($with_vat_rate * $poQty),2,'.','');
		}else{
		
			//$vat_rate=number_format((($_POST['rate']*$_POST['vaat'])/100),2,'.','');
			$vat_rate=(($rate* $vat)/100);
			$vat_amt = $vat_rate*$poQty;
			
		
			
			
			
			if($_POST['rebate'] == 'Yes'){
			$with_vat_rate=$rate-$vat_rate;
			}
			else{
			$with_vat_rate=$rate;
			}
		
			$with_vat_amt =number_format(($with_vat_rate * $poQty),2,'.','');
		
		
		}
		
		//$tax_rate = number_format((($_POST['with_vat_rate']*$_POST['taax'])/100),2,'.','');
		$tax_rate = (($with_vat_rate*$tax)/100);
		$tax_amt=number_format(($tax_rate*$poQty),2,'.','');
		
		if($_POST['taax_include']=='Including'){
			if($_POST['rebate'] == 'Yes'){
				if($_POST['vaat_include']=='Excluding'){$grand_amount  = $poAmount+$vat_amt-$tax_amt;}
				else{ $grand_amount  = $poAmount-$tax_amt; }
			}
			else{ 
				$tax_rate = ((($with_vat_rate-$vat_rate)*$tax)/100);
				$tax_amt=number_format(($tax_rate*$poQty),2,'.','');
				if($_POST['vaat_include']=='Excluding'){$grand_amount  = $poAmount+$vat_amt-$tax_amt;}
				else{ $grand_amount  = $poAmount-$tax_amt; }
			}
		}else{
			if($_POST['vaat_include']=='Including'){ $tax_rate = (($with_vat_rate*$tax)/100); }
			else{ $tax_rate = (($rate*$tax)/100); }
			
			$tax_amt=number_format(($tax_rate*$poQty),2,'.','');
			$with_vat_rate=$with_vat_rate+$tax_rate;
			$with_vat_amt =$with_vat_rate * $poQty;
		
			
			if($_POST['rebate'] == 'Yes'){
				
				$grand_amount  = ($with_vat_amt-$tax_amt+$vat_amt);
			}
			else{
				
				$grand_amount  = ($with_vat_amt-$tax_amt);
			}
		
		}
		
		
		
		
		
		
		
		$tolerance_qty=$poQty+($poQty* (10/100));

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$po_details_insert = 'insert into purchase_invoice(`po_no`,`po_date`,`req_no`,`req_id`,`vendor_id`,`item_id`,`warehouse_id`,`rate`,`qty`,`amount`,`entry_by`,`entry_at`,vat,tax,vat_amt,tax_amt,grand_amount,tolerance_qty,vat_rate,tax_rate,with_vat_rate,with_vat_amt) 
		
		value("' . $po_no . '","' . $po_date . '","' . $data->req_no . '","' . $data->id . '","' . $vendor_id . '","' . $data->item_code . '","' . $data->warehouse_id . '","' . $poRate . '","' . $poQty . '","' . $poAmount . '","' . $_SESSION['user']['id'] . '","' . date('Y-m-d H:s:i') . '","'.$vat.'","'.$tax.'","'.$vat_amt.'","'.$tax_amt.'","'.$grand_amount.'","'.$tolerance_qty.'","'.$vat_rate.'","'.$tax_rate.'","'.$with_vat_rate.'","'.$with_vat_amt.'")';
		
		
		db_query($po_details_insert);
	}
	
		$check_prev_entry=find_a_field('po_terms_condition','count(po_no)','po_no="'.$$unique.'"');
if($check_prev_entry<1){
		 $pi_sql = 'select * from terms_condition_setup where status="Active" order by sl_no';

		$pi_query = db_query($pi_sql);

		
		while($pi_data=mysqli_fetch_object($pi_query))

		{
	

  $pi_terms = 'INSERT INTO po_terms_condition ( po_no, terms_condition, sl_no, entry_at, entry_by)
  
  VALUES("'.$$unique.'", "'.$pi_data->terms_condition.'", "'.$pi_data->sl_no.'",  "'.$_POST['entry_at'].'", "'.$_POST['entry_by'].'")';

db_query($pi_terms);

}

}

//
//	  $note_sql = 'insert into approver_notes_purchase(`master_id`,`type`,`note`,`entry_at`,`entry_by`) value("'.$$unique.'","PO","'.$remarks.'","'.date('Y-m-d H:i:s').'","'.$_SESSION['user']['id'].'")';
//		db_query($note_sql);


		$crud   = new crud($table_master);

		$crud->update($unique);

		unset($$unique);

		unset($_SESSION[$unique]);
		unset($_SESSION['selected_req_no']);

		$type=1;
        $_SESSION['poMsg'] = '<span style="color:green; font-weight:bold;">PO Created Successfully!</span>';
		$msg='Successfully Forwarded for Approval.';
		header('Location:po_status.php');

}

if(isset($_POST['confirmm2']))

{

		

		$_POST[$unique]=$$unique;

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['status']='UNCHECKED';

		$check_prev_entry=find_a_field('po_terms_condition','count(po_no)','po_no="'.$$unique.'"');
if($check_prev_entry<1){
		 $pi_sql = 'select * from terms_condition_setup where status="Active" order by sl_no';

		$pi_query = db_query($pi_sql);

		
		while($pi_data=mysqli_fetch_object($pi_query))

		{
	

  $pi_terms = 'INSERT INTO po_terms_condition ( po_no, terms_condition, sl_no, entry_at, entry_by)
  
  VALUES("'.$$unique.'", "'.$pi_data->terms_condition.'", "'.$pi_data->sl_no.'",  "'.$_POST['entry_at'].'", "'.$_POST['entry_by'].'")';

db_query($pi_terms);

}

}

		$crud   = new crud($table_master);

		$crud->update($unique);

		unset($$unique);

		unset($_SESSION[$unique]);
		unset($_SESSION['selected_req_no']);

		$type=1;
        $_SESSION['poMsg'] = '<span style="color:green; font-weight:bold;">PO Created Successfully!</span>';
		$msg='Successfully Forwarded for Approval.';
		header('Location:po_status.php');

}



if(isset($_POST['add'])&&($_POST[$unique]>0) && $_SESSION['csrf_token']===$_POST['csrf_token'])

{
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));



   		$tr_type="Add";
		$crud   = new crud($table_details);

		$iii=explode('##',$_POST['item_id']);

		$_POST['item_id']=$iii[2];
		
		$_POST['item_description']= preg_replace('/[^a-zA-Z0-9]/', ' ', $_POST['item_description']);
		
		$xxx =explode('##',$_POST['expense_head']); 
		
		$_POST['expense_head'] = $xxx[1];


		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d h:s:i');

		$_POST['unit_name']=find_a_field('item_info','unit_name','item_id="'.$iii[2].'"');

		$_POST['rate']=$_POST['amount']/$_POST['qty'];
		
		$_POST['vat']=$_POST['vaat'];
		
		$_POST['tax']=$_POST['taax'];
		$vat = $_POST['vaat'];
		$rate = $_POST['amount']/$_POST['qty'];
		
		
		if($_POST['vaat_include']=='Including'){
		
			$vat_rate=number_format((($_POST['rate']/(100+$_POST['vaat']))*$_POST['vaat']),4,'.','');
			
			$_POST['vat_amt'] = $vat_rate*$_POST['qty'];
			if($_POST['rebate'] == 'Yes'){
			$_POST['with_vat_rate']=$_POST['rate']-$vat_rate;
			}
			else{
			$_POST['with_vat_rate']=$_POST['rate'];
			}
			
			$_POST['with_vat_amt'] =number_format(($_POST['with_vat_rate'] * $_POST['qty']),4,'.','');
		}else{
		
			//$vat_rate=number_format((($_POST['rate']*$_POST['vaat'])/100),2,'.','');
			$vat_rate=(($_POST['rate']* $_POST['vaat'])/100);
			$_POST['vat_amt'] = $vat_rate*$_POST['qty'];
			//$_POST['with_vat_rate'] =$rate+$vat_rate;
			
			if($_POST['rebate'] == 'Yes'){
			$_POST['with_vat_rate'] =$rate;
			}
			else{
			$_POST['with_vat_rate'] =$rate+$vat_rate;
			}
		
			$_POST['with_vat_amt'] =number_format(($_POST['with_vat_rate'] * $_POST['qty']),4,'.','');
		
		
		}
		
		//$tax_rate = number_format((($_POST['with_vat_rate']*$_POST['taax'])/100),2,'.','');
		$tax_rate = (($_POST['with_vat_rate']*$_POST['taax'])/100);
		$_POST['tax_amt']=number_format(($tax_rate*$_POST['qty']),4,'.','');
		
		if($_POST['taax_include']=='Including'){
			if($_POST['rebate'] == 'Yes'){
				if($_POST['vaat_include']=='Excluding'){$_POST['grand_amount']  = $_POST['amount']+$_POST['vat_amt']-$_POST['tax_amt'];}
				else{ $_POST['grand_amount']  = $_POST['amount']-$_POST['tax_amt']; }
			}
			else{ 
				$tax_rate = ((($_POST['with_vat_rate']-$vat_rate)*$_POST['taax'])/100);
				$_POST['tax_amt']=number_format(($tax_rate*$_POST['qty']),4,'.','');
				if($_POST['vaat_include']=='Excluding'){$_POST['grand_amount']  = $_POST['amount']+$_POST['vat_amt']-$_POST['tax_amt'];}
				else{ $_POST['grand_amount']  = $_POST['amount']-$_POST['tax_amt']; }
			}
		}else{
			if($_POST['vaat_include']=='Including'){ $tax_rate = (($_POST['with_vat_rate']*$_POST['taax'])/100); }
			else{ $tax_rate = (($_POST['rate']*$_POST['taax'])/100); }
			
			$_POST['tax_amt']=number_format(($tax_rate*$_POST['qty']),4,'.','');
			$_POST['with_vat_rate']=$_POST['with_vat_rate']+$tax_rate;
			$_POST['with_vat_amt'] =$_POST['with_vat_rate'] * $_POST['qty'];
		
			
			if($_POST['rebate'] == 'Yes'){
				
				$_POST['grand_amount']  = ($_POST['with_vat_amt']-$_POST['tax_amt']+$_POST['vat_amt']);
			}
			else{
				
				$_POST['grand_amount']  = ($_POST['with_vat_amt']-$_POST['tax_amt']);
			}
		
		}
		
		if($_POST['deductible']=='No'){
			if($_POST['rebate'] == 'Yes'){
				$_POST['grand_amount']=$_POST['grand_amount'];
			}
			else{
				$_POST['grand_amount']=$_POST['grand_amount'];
			}
		}


		
		$_POST['vat_rate']= $vat_rate;
		
		$_POST['tax_rate']= $tax_rate;
		
		$_POST['tolerance_qty']=$_POST['qty']+($_POST['qty']* (10/100));


		$crud->insert();

		//echo 'this is test';

}



if($$unique>0)

{

		$condition=$unique."=".$$unique;

		$data=db_fetch_object($table_master,$condition);

		foreach ($data as $key => $value)

		{ $$key=$value;}

		

}

if($$unique>0) $btn_name='Update PO Information'; else $btn_name='Initiate PO Information';

//if($_SESSION[$unique]<1)

//$$unique=db_last_insert_id($table_master,$unique);



//auto_complete_start_from_db



//auto_complete_from_db($table,$show,$id,$con,$text_field_id);

//auto_complete_from_db('item_info','item_name','concat(finish_goods_code,"-",item_name,"#>",item_id)','1 ','item_id');

auto_complete_from_db('item_info','item_name','concat(finish_goods_code,"##",item_name,"##",item_id)','status="Active" ','item_id');

 //$sql='select concat(finish_goods_code,"-",item_name,"-",item_id) from item_info where product_nature in ("Purchasable","Both") and group_for="'.$_SESSION['user']['group'].'" and status="Active"';

//auto_complete_start_from_db('item_info','concat(finish_goods_code,"#>",item_name)','finish_goods_code','product_nature="Purchasable" order by finish_goods_code ASC','item');

$tr_from="Purchase";


?>

<script language="javascript">

function count()

{

var rate=(document.getElementById('rate').value)*1;



var qty=(document.getElementById('qty').value)*1;





document.getElementById('amount').value=(rate*qty);



//var num=((document.getElementById('qty').value)*1)*((document.getElementById('rate').value)*1);

//document.getElementById('amount').value = num.toFixed(2);	

}


function itemDescription(id){

var itemdes =id;

var data = itemdes.split("##");

var itemname = data[1];

document.getElementById('item_description').value =itemname;

}





$(document).ready(function() {

var rebateable = $("#rebateable").val();

if(rebateable=='No'){

$("#data-box").hide();

}

$("#rebateable").on("change", function() {

var selectedOption = $(this).val();

if (selectedOption === "Yes") {

$("#data-box").show();

document.getElementById('rebate_percentage').value= 100;

} else {

$("#data-box").hide();
document.getElementById('rebate_percentage').value= 0;

}

});

});




</script>





<!--<style type="text/css">







/*.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited, a.ui-button, a:link.ui-button, a:visited.ui-button, .ui-button {

    color: #454545;

    text-decoration: none;

    display: none;

}*/





div.form-container_large input {

    width: 220px;

    height: 37px;

    border-radius: 0px !important;

}









.onhover:focus{

background-color:#66CBEA;



}

<!--Mr create 2 form with table-->
<style>
.cke_notification_warning{
	display:none !important;
}

</style>
<div class="form-container_large">

    <form action="po_create.php" method="post" name="codz" id="codz" enctype="multipart/form-data" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">

<!--        top form start hear-->

        <div class="container-fluid bg-form-titel">

            <div class="row">

                <!--left form-->

                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">

                    <div class="container n-form2">

                        <div class="form-group row m-0 pb-1">

						  <? $field='po_no';?>

                            <label  for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PO No</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">

                               

							

									<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly/>

							

                            </div>

                        </div>



                        <div class="form-group row m-0 pb-1">

								 <? $field='po_date'; if($po_date=='') $po_date =date('Y-m-d');?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PO Date</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                               

						

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" required/>



                            </div>

                        </div>



                        <div class="form-group row m-0 pb-1">

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Company</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                               



									<select id="group_for" name="group_for" required >
										<? user_company_access($group_for) ?>
									</select>



                            </div>

                        </div>
						<div class="form-group row m-0 pb-1">

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vendor Name</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">

                                

		

									<? if($vendor_id<1) { ?>
									<input type="text" list="vendor" name="vendor_id_draft" id="vendor_id_draft" value="<?=$_POST['vendor_id_draft'];?>" required autocomplete="off"   />

									<datalist id="vendor">

								

									<option></option>						

									<? foreign_relation('vendor v','concat(v.vendor_name,"##",v.vendor_id)','""',$vendor_id_draft,'v.status="ACTIVE" and group_for="'.$_SESSION['user']['group'].'"');?>

									</datalist>

									

									<? }?>

									

									<? if($vendor_id>0){ ?>

									

									 <input  name="vendor_id" type="hidden" id="vendor_id" value="<?=$vendor_id;?>" readonly=""  required/>

									

									 <input  name="vendor_view" type="text" id="vendor_view" value="<?=find_a_field('vendor','concat(vendor_name,"##",vendor_id)','vendor_id="'.$vendor_id.'"');?>" readonly=""  required/>

									

									<? }?>

                            </div>

                        </div>
						<div class="form-group row m-0 pb-1">
							<? $field = 'req_no'; ?>
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition
								No :</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

								<input type="text" name="req_no" id="req_no"
									value="<?php if ($req_no > 0)
										echo $req_no;
									else
										echo $_SESSION['selected_req_no'] ?>"
										readonly="readonly" />

								</div>
							</div>
						
						<div class="form-group row m-0 pb-1">

						<? $field='quotation_no';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Quotation No</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                	 

						<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly />



                            </div>

                        </div>
						
						<div class="form-group row m-0 pb-1">

						 <? $field='remarks';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Remarks</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

									  <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>
									  
                            </div>

                        </div>
						
						<div class="form-group row m-0 pb-1">

						 <? $field='tolerance';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Tolerance (%):</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

									  <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" max="10" min="0"/>
									  
                            </div>

                        </div>
						
						
						
						
						<div class="form-group row m-0 pb-1">

						<? $field='transport_bill';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Transport Bill</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                	 

							<select name="<?=$field?>"  id="<?=$field?>" <? if(!empty($$field)){ echo'disabled';} ?> >
											<option value="Own" <?= $$field == 'Own' ? 'selected' : '' ?>>Own</option>
    										<option value="Vendor" <?= $$field == 'Vendor' ? 'selected' : '' ?>>Vendor</option>
									</select>



                            </div>

                        </div>
						
						
						



                    </div>







                </div>



                <!--Right form-->

                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">

                    <div class="container n-form2">
					
					<div class="form-group row m-0 pb-1">

						<? $field='purchase_type';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Po Type</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                	 

							<select id="<?=$field?>" name="<?=$field?>" value="<?=$_POST['purchase_type']?>" required style="width:220px;" <? if(!empty($$field)){ echo'disabled';} ?>    >

								<option></option>

								<?=foreign_relation('purchase_type','id','po_type',$purchase_type,'1');?>

									

							</select>



                            </div>

                        </div>

                        



                      	<div class="form-group row m-0 pb-1">

									<? $field='vat';?>

                            <label  for="<?=$field?>"  class="col-sm-2 col-md-2 col-lg-2 col-xl-2 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vat(%)</label>

                            <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 p-0 pr-2">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  <? if(!empty($$field)){ echo'readonly';} ?> />



                            </div>
							
							
							
							
							<? $field='vat_include';?>

                            <label  for="<?=$field?>"  class="col-sm-2 col-md-2 col-lg-2 col-xl-2 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">**</label>

                            <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 p-0 pr-2">

                                

								<select name="<?=$field?>"  id="<?=$field?>" value="<?=$$field?>" <? if(!empty($$field)){ echo'disabled';} ?> required >
											<option value="Including" <?= $$field == 'Including' ? 'selected' : '' ?>>Including</option>
    										<option value="Excluding" <?= $$field == 'Excluding' ? 'selected' : '' ?>>Excluding</option>
									</select>



                            </div>
							
							

                        </div>
						
						<div class="form-group row m-0 pb-1">

									<? $field='tax';?>

                            <label  for="<?=$field?>"  class="col-sm-2 col-md-2 col-lg-2 col-xl-2 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Tax(%)</label>

                            <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 p-0 pr-2">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  <? if(!empty($$field)){ echo'readonly';} ?> />



                            </div>
							
							
							
							<? $field='tax_include';?>

                            <label  for="<?=$field?>"  class="col-sm-2 col-md-2 col-lg-2 col-xl-2 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">**</label>

                            <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 p-0 pr-2">

                                

								<select name="<?=$field?>"  id="<?=$field?>" value="<?=$$field?>" <? if(!empty($$field)){ echo'disabled';} ?> required >
											<option value="Including" <?= $$field == 'Including' ? 'selected' : '' ?>>Including</option>
    										<option value="Excluding" <?= $$field == 'Excluding' ? 'selected' : '' ?>>Excluding</option>
									</select>


                            </div>
							
							
							

                        </div>
						
						
						
						
						<div class="form-group row m-0 pb-1">

						<? $field='rebateable';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Rebateable</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                	 

							<select name="<?=$field?>"  id="<?=$field?>" <? if(!empty($$field)){ echo'disabled';} ?> >
											<option value="No" <?= $$field == 'No' ? 'selected' : '' ?>>No</option>
    										<option value="Yes" <?= $$field == 'Yes' ? 'selected' : '' ?>>Yes</option>
									</select>



                            </div>

                        </div>
						
						
						<div class="form-group row m-0 pb-1">

									<? $field='rebate_percentage';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Rebateable(%)</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2" id="data-box">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" <? if(!empty($$field)){ echo'readonly';} ?>  />



                            </div>

                        </div>
						
						
						
						<?php /*?><div class="form-group row m-0 pb-1">

									<? $field='tax';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Tax(%)</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />



                            </div>

                        </div><?php */?>
						<?php /*?><div class="form-group row m-0 pb-1">

									<? $field='cash_discount';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Discount</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />



                            </div>

                        </div><?php */?>
						<?php /*?><div class="form-group row m-0 pb-1">

									<? $field='ait';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">AIT</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />



                            </div>

                        </div><?php */?>
						
						
						<div class="form-group row m-0 pb-1">

									<? $field='deductible';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vds Payable</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                <select name="<?=$field?>"  id="<?=$field?>" <? if(!empty($$field)){ echo'disabled';} ?> >
											<option value="No" <?= $$field == 'No' ? 'selected' : '' ?>>No</option>
    										<option value="Yes" <?= $$field == 'Yes' ? 'selected' : '' ?>>Yes</option>
									</select>

                            </div>

                        </div>
						
						<!--<div class="form-group row m-0 pb-1">

									<? $field='ait';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">AIT(%)</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                

								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />



                            </div>

                        </div>-->



                        <div class="form-group row m-0 pb-1">

						<? $field='warehouse_id'; $table='warehouse';$get_field='warehouse_id';$show_field='warehouse_name'; if($$field=='') $$field=$req_all->warehouse_id;?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Warehouse</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

                                	 

							<select id="<?=$field?>" name="<?=$field?>" required style="width:220px;"    >

								<option></option>

							<? user_warehouse_access($warehouse_id);?>

									

							</select>



                            </div>

                        </div>
						
						<div class="form-group row m-0 pb-1">

						 <? $field='file_upload';?>

                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">File Attachment:</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

									  <input  name="<?=$field?>" type="file" id="<?=$field?>"  value="<?=$$field?>" class="form-control"/>
									  <?php /*?><a href="<?=SERVER_CORE?>core/upload_view.php?name=<?=$$field?>&folder=purchase&proj_id=<?=$_SESSION['proj_id']?>" target="_blank"><?php */?>
									  <a href="../../../controllers/uploader/upload_view.php?proj_id=<?=$_SESSION['proj_id']?>&&folder=purchase&&name=<?=$file_upload;?>" target="_blank">
									  
									  View Attachment</a>
                            </div>
		

                        </div>
						
						
						<div class="form-group row m-0 pb-1">

									<? $field='estimated_delivery_date';?>

                            <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Estimated Delivery Date</label>

                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
 
<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"    />
                            </div>

                        </div>
						
						
						



                    </div>

                </div>





            </div>
			
<?php  

//$TandC="<p>1. Delivery Period: Within 30 - 60 days.<br />
//2. Please send all delivery Challan/Legal Documents/COA/MSDS/TDS along with goods during delivery.<br />
//3. Payment will be made within 30 days for Raw Materials &amp; 45 days for Packaging Materials after receiving the original Commercial Invoices.<br />
//4. Statutory (VAT &amp; AIT) deduction will be made at source as per Govt. Law.<br />
//5. The Supply can be totally rejected or the unit price can be reduced in case of failure to deliver the materials in due time.</p>"
	
$TandC = ''; // initialize
$id = 0;

$sql = 'SELECT * FROM terms_condition_setup WHERE 1 and status="Active" ORDER BY id';
$tc_query = db_query($sql);

$TandC .= '<div style="line-height:1.1; margin:0; padding:0;">';
while ($tc_data = mysqli_fetch_object($tc_query)) {
    $TandC .= ++$id . '. ' . $tc_data->terms_condition . '<br>';
}
$TandC .= '</div>';

 ?>

 <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
			
			<div class="row pt-3">
				<div class="col-md-12">
							<textarea name="terms_condition"  id="terms_condition"> <? if ($terms_condition!=''){echo $terms_condition;} else{echo $TandC;} ?> </textarea>
				</div>
			</div>
 

          
        </div>


            <div class="n-form-btn-class">

                

				<input name="new" type="submit" class="btn1 btn1-bg-submit" value="<?=$btn_name?>"  />

            </div>

        </div>



        <!--return Table design start-->

        <div class="container-fluid pt-5 p-0 ">

            <table class="table1  table-striped table-bordered table-hover table-sm">

                <thead class="thead1">

				<?

	 $sql = 'select a.*,u.fname from approval_matrix_history a, user_activity_management u where a.approved_by=u.user_id and a.tr_no="'.$$unique.'" and tr_no > 0 and tr_from="Purchase"';

	$row_check = mysqli_num_rows(db_query($sql));

	if($row_check>0){

	?>

                <tr class="bgc-info">

                    <th>Returned By</th>

                    <th>Returned At</th>

                    <th>Remarks</th>

                </tr>

                </thead>



                <tbody class="tbody1">



                <?

				  $qry = db_query($sql);

				  while($return_note=mysqli_fetch_object($qry)){

			 	?>

				<tr>

                    <td><?=$return_note->fname?></td>

					<td><?=$return_note->approved_by?></td>

					<td><?=$return_note->notes?></td>



                </tr>

				<? } ?>



                </tbody>

            </table>

			<? } ?>



        </div>

    </form>



	<form action="?<?= $unique ?>=<?= $$unique ?>" method="post" name="cloud" id="cloud">
	<? if ($_SESSION[$unique] > 0 && $_SESSION['selected_req_no']>0 && $_SESSION['invoice_no']=='') { ?>
			<?
			$group_for = find_a_field('warehouse', 'group_for', 'warehouse_id=' . $warehouse_id . ' ');
			if (($vendor->ledger_id == 0) && ($group_for == 2 || $group_for == 3)) { ?>
				<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td bgcolor="#FF3333">
							<div align="center" class="style1">VERDOR IS BLOCKED. NO ACCOUNT CODE FOUND</div>
						</td>
					</tr>
				</table>

			<? } else { ?>

			<? } ?>
			<!--Table input one design-->
			<div class="container-fluid pt-5 p-0 ">


				<table class="table1  table-striped table-bordered table-hover table-sm">
					<thead class="thead1">
						<tr class="bgc-info">
							<th>S/Lsss</th>
							<th>Item Description</th>
							<th>Req Qty</th>

							<th>Already PO Qty</th>
							<th>Order Qty</th>
							<th>Unit</th>
							<th>Unit Price</th>
							<th>Amount</th>

						</tr>
					</thead>
					<tbody class="tbody1">

						<?
						$res = 'select a.id,b.item_id as item_code, concat(b.item_name) as item_description , a.qty as req_quantity ,b.unit_name as Unit,a.warehouse_id
 from requisition_order a,item_info b
 
  where b.item_id=a.item_id and a.req_no=' . $_SESSION['selected_req_no'];
						$query = db_query($res);

						while ($data = mysqli_fetch_object($query)) {

							$already_po_qty = find_a_field('purchase_invoice', 'sum(qty)', 'req_no="' . $_SESSION['selected_req_no'] . '" and item_id="' . $data->item_code . '" and req_id="' . $data->id . '"');
							$this_po = find_all_field('purchase_invoice', '', 'req_no="' . $_SESSION['selected_req_no'] . '" and item_id="' . $data->item_code . '" and req_id="' . $data->id . '"');
							//and po_no="' . $$unique . '"
							$rest_qty = $data->req_quantity - $already_po_qty;
							
							?>

							<tr>
								<td><?= $i=$data->id; ?>
									<input type="hidden" name="po_no" value="<?= $po_no ?>" />
									<input type="hidden" name="po_date" value="<?= $po_date ?>" />
									<input type="hidden" name="vendor_id" value="<?=$vendor_id ?>" />
									<input type="hidden" name="req_qty<?= $i ?>" id="req_qty<?= $data->id ?>"
										value="<?= $data->req_quantity ?>" />
									<input type="hidden" name="po_qty<?= $i ?>" id="po_qty<?= $i ?>" value="<?= $already_po_qty ?>" />
								</td>

								<td><?= $data->item_description ?></td>

								<td><?=$data->req_quantity ?></td>

						<td><?= $already_po_qty; ?><input type="hidden" name="rest_qty<?= $data->id ?>" id="rest_qty<?= $data->id ?>" value="<?=$rest_qty?>" /></td>

								<td><input type="text" onkeyup="count_1(<?= $data->id ?>);total_cal();update_item(<?= $data->id ?>)"
										 name="qty<?= $data->id ?>" id="qty<?= $data->id ?>"
										value="<?= $data->req_quantity-$this_po->qty ?>" required /></td>

								<td><?= $data->Unit ?></td>

								<td><input type="hidden" required name="details_id<?= $data->id ?>"
										value="<? echo $data->id; ?>" />

									<input type="text" onkeyup="count_1(<?= $data->id ?>);total_cal();update_item(<?= $data->id ?>)"
										 required name="unit_price<?= $data->id ?>"
										id="unit_price<?= $data->id ?>" value="<?= $this_po->rate ?>" />
								</td>

								<td><input type="text" readonly required name="amount<?= $data->id ?>" id="amount<?= $data->id ?>"
										value="<?= $this_po->amount ?>" />
									<? $total_amt += $data->amount; ?></td>
							
							</tr>
                          <? } ?>
						
						<tr>
							<td colspan="7"><strong>Total: </strong></td>
							<input id="tot_count" type="hidden" value="<?= $i; ?>" />
							<td><strong id="total_amount"><?= number_format($total_amt, 2); ?></strong></td>
								
						</tr>

					</tbody>
				</table>



			</div>

			
<div class="container-fluid p-0 ">



            <div class="n-form-btn-class">

                <input  name="rebate" type="hidden" id="rebate" value="<?=$rebateable?>"/>
				<input  name="rebate_percentage" type="hidden" id="rebate_percentage" value="<?=$rebate_percentage?>"/>
				<input  name="vaat" type="hidden" id="vaat" value="<?=find_a_field('purchase_master', 'vat', 'po_no="'.$$unique.'"');?>"/>
				<input  name="taax" type="hidden" id="vaat" value="<?=$tax?>"/>
				<input  name="vaat_include" type="hidden" id="vaat_include" value="<?=$vat_include?>"/>
				<input  name="taax_include" type="hidden" id="taax_include" value="<?=$tax_include?>"/>
				<input  name="deductible" type="hidden" id="deductible" value="<?=$deductible?>"/>

<input type="hidden" name="confirm_remarks" id="confirm_remarks">
				<input name="delete"  type="submit" class="btn1 btn1-bg-cancel" value="DELETE PURCHASE" />
      			<input name="confirmm" type="submit" class="btn1 btn1-bg-submit"  value="CONFIRM PURCHASE"  />

            </div>



        </div>
		

 
	



<? }




elseif($_SESSION[$unique]>0){ ?>

	<? 

		$group_for = find_a_field('warehouse','group_for','warehouse_id='.$warehouse_id.' ');
		$vendor = find_all_field('vendor','','vendor_id='.$vendor_id.' ');
		?>
	

        <div class="container-fluid pt-5 p-0 ">
			<? if($purchase_type==2){ ?>
			<table class="table1  table-striped table-bordered table-hover table-sm">

                <thead class="thead1">

                <tr class="bgc-info">

                    <th width="40%">Item Name</th>

                    <th width="10%">Stock</th>

                    <th width="10%">Unit</th>



                    <th width="10%">Unit Price</th>

                    <th>Quantity</th>

                    <th>Amount</th>



                    <th>Action</th>

                </tr>

                </thead>



                <tbody class="tbody1">



                <tr>

                    <td align="center">

<input  name="<?=$unique?>" type="hidden" id="<?=$unique?>" value="<?=$$unique?>"/>

<input  name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$warehouse_id?>"/>

<input  name="po_date" type="hidden" id="po_date" value="<?=$po_date?>"/>

<input  name="vendor_id" type="hidden" id="vendor_id" value="<?=$vendor_id?>"/>

<input  name="rebate" type="hidden" id="rebate" value="<?=$rebateable?>"/>
<input  name="rebate_percentage" type="hidden" id="rebate_percentage" value="<?=$rebate_percentage?>"/>
<input  name="vaat" type="hidden" id="vaat" value="<?=find_a_field('purchase_master', 'vat', 'po_no="'.$$unique.'"');?>"/>
<input  name="taax" type="hidden" id="vaat" value="<?=$tax?>"/>
<input  name="vaat_include" type="hidden" id="vaat_include" value="<?=$vat_include?>"/>
<input  name="taax_include" type="hidden" id="taax_include" value="<?=$tax_include?>"/>
<input  name="deductible" type="hidden" id="deductible" value="<?=$deductible?>"/>

<input  name="csrf_token" type="hidden" id="csrf_token" value="<?=$_SESSION['csrf_token']?>"/>

<input  name="item_id" type="text" id="item_id" value="<?=$item_id?>" style="width:320px;" required onblur="getData2('po_ajax.php', 'po',this.value,document.getElementById('warehouse_id').value);"/>



</td>				

                    

						<td colspan="3">

							<div align="right">

								  <span id="po">

						<table style="width:100%;" border="1">

							<tr>



								<td ><input name="stk" type="text" class="input3" id="stk" readonly="readonly"/></td>



								<td ><input name="unit_name" type="text" class="input3" id="unit_name"  readonly="readonly"/></td>



								<td ><input name="rate" type="text" class="input3" id="rate"  /></td>

							</tr>

						</table>

						</span>

							</div>

						</td>



                    <td><input name="qty" type="text" class="input3" id="qty"  maxlength="100"  onchange="count()" required /></td>

                    



                    <td><input name="amount" type="text" class="input3" id="amount" readonly="readonly" required /></td>

                    <td><input name="add" type="submit" id="add" class="btn1 btn1-bg-submit" value="ADD" tabindex="12"  /></td>



                </tr>



                </tbody>

            </table>
				<? }else { ?>
					<table class="table1  table-striped table-bordered table-hover table-sm">

                <thead class="thead1">

                <tr class="bgc-info">

                    <th  >Item Name</th>

					<th >Description</th>
	
					<th  >Unit</th>

					<th  >Expense Head</th>

					<th  >Cost Center </th>

					<th  >Price</th>

					<th  >Quantity</th>

					<th  >Amount</th>



                <th>Action</th>

                </tr>

                </thead>



                <tbody class="tbody1">



                <tr>

                    <td align="center">

<input  name="<?=$unique?>" type="hidden" id="<?=$unique?>" value="<?=$$unique?>"/>

<input  name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$warehouse_id?>"/>

<input  name="po_date" type="hidden" id="po_date" value="<?=$po_date?>"/>

<input  name="vendor_id" type="hidden" id="vendor_id" value="<?=$vendor_id?>"/>

<input  name="rebate" type="hidden" id="rebate" value="<?=$rebate?>"/>

<input  name="rebate_percentage" type="hidden" id="rebate_percentage" value="<?=$rebate_percentage?>"/>

<input  name="vaat" type="hidden" id="vaat" value="<?=find_a_field('purchase_master', 'vat', 'po_no="'.$$unique.'"');?>"/>
					
					
					<input  name="taax" type="hidden" id="vaat" value="<?=$tax?>"/>
					
					
					<input  name="vaat_include" type="hidden" id="vaat_include" value="<?=$vat_include?>"/>
					<input  name="taax_include" type="hidden" id="taax_include" value="<?=$tax_include?>"/>
					<input  name="deductible" type="hidden" id="deductible" value="<?=$deductible?>"/>

<input  name="item_id" type="text" id="item_id" value="<?=$item_id?>" style="width:320px;" required onblur="getData2('po_ajax_service.php', 'po',this.value,document.getElementById('warehouse_id').value);itemDescription(this.value);"/>



</td>				

                 <td ><input name="item_description" type="text" class="input3" id="item_description" style="width:110px;" /></td>

							

													<td >								  <span id="po">
<input name="unit_name" type="text" class="input3" id="unit_name"  readonly="readonly"/></span></td>
						

				
						
<td width="10%"><input list="heads" name="expense_head" type="text" class="input3" id="expense_head" autocomplete="off" required style="width:80px;" />

<datalist id="heads">

<? foreign_relation('accounts_ledger','concat(ledger_name,"##",ledger_id)','ledger_name',$expense_head,'1 order by ledger_id asc');?>

</datalist></td>


<td width="10%"><select name="cc_code" id="cc_code" class="form-control" style="width:80px;" >

<option></option>

<? foreign_relation('cost_center','id','center_name',$cc_code,"1");?>

</select></td>





					<td ><input name="rate" type="text" class="input3" id="rate"  /></td>

                    <td><input name="qty" type="text" class="input3" id="qty"  maxlength="100"  onchange="count()" required /></td>

                    



                    <td><input name="amount" type="text" class="input3" id="amount" readonly="readonly" required /></td>

                    <td><input name="add" type="submit" id="add" class="btn1 btn1-bg-submit" value="ADD" tabindex="12"  /></td>



                </tr>



                </tbody>

            </table>
				<? } ?>
			<? } ?>
</form>










        </div>





        <!--Data multi Table design start-->

		<? if($$unique>0 && $_SESSION['selected_req_no']<1){?>

		 <? 


		
			if($purchase_type=='2'){

			$res='select a.id, b.finish_goods_code as item_code, a. with_vat_rate, a.vat_amt,a.tax_amt, a. with_vat_amt, concat(b.item_name) as item_name,b.unit_name,a.grand_amount, a.qty as total_unit,a.rate, a.amount,"x" from purchase_invoice a,item_info b where b.item_id=a.item_id and a.po_no='.$po_no;



?>

        <div class="container-fluid pt-5 p-0 ">
            <table class="table1  table-striped table-bordered table-hover table-sm">

                <thead class="thead1">

                <tr class="bgc-info">

                    <th>S/L</th>

                    <th>Item Code</th>

                    <th>Item Name</th>



                    <th>Unit</th>
					
					<th>Unit Price</th>

					<th>Unit Price  <? if($vat_include == 'Including'){echo $vat_include;} else{ echo $vat_include;} ?> VAT</th>

                    <th>Quantity</th>
					
					<th>Amount</th>

                    <th>Amount  <? if($vat_include == 'Including'){echo $vat_include;} else{ echo $vat_include;} ?> VAT</th>

					<th>X</th>

                </tr>

                </thead>



                <tbody class="tbody1">

				<?



				$i=1;

				

				$query = db_query($res);

				

				while($data=mysqli_fetch_object($query)){ ?>



                <tr>

                    <td><?=$i++?></td>

                    <td><?=$data->item_code?></td>

                    <td style="text-align:left"><?=$data->item_name?></td>



                    <td><?=$data->unit_name?></td>

					<td><?=$data->rate?></td>
					
					<td><?=number_format($data->with_vat_rate,3);?></td>

                    <td><?=$data->total_unit?></td>

					<td><?=$data->amount?></td>
					
					<td><?=number_format($data->with_vat_amt,3);?></td>

                    <td><a href="?del=<?=$data->id?>"><button type="button" class="btn2 btn1-bg-cancel"><i class="fa-solid fa-trash"></i></button></a></td>



                </tr>

				<?

 $total_pcs +=$data->total_unit;

 $total_amt +=$data->amount;
 
  $total_amt_withvat +=$data->with_vat_amt;
  
  
  $vat_amtt += $data->vat_amt;
 $tax_amtt += $data->tax_amt;
 
 $with_vat_amt = $data->with_vat_rate*$data->total_unit;
  $tot_with_vat_amt += $with_vat_amt;
  
  $tot_with_grandd_amt += $data->grand_amount;

 } ?>

 

 <tr>



<td colspan="6"><div align="right"><strong>  Total:</strong></div></td>

<td ><?=number_format($total_pcs,2);?></td>

<td ><?=number_format($total_amt,2);?></td>

<td><?=number_format($total_amt_withvat,2);?></td>


<td></td>


</tr>



<tr>
<td colspan="8"><div align="right"><strong> VAT Amount(<? echo $vat.'%';?>)</strong></div></td>

<td ><?=number_format($vat_amtt,2);?></td>
<td></td>
</tr>


<tr>
<td colspan="8"><div align="right"><strong> TAX Amount(<? echo $tax.'%';?>)</strong></div></td>

<td ><?=number_format($tax_amtt,2);?></td>
<td></td>
</tr>

<tr>
<td colspan="8"><div align="right"><strong> Grand Amount </strong></div></td>

<td >
<?
if($deductible == "Yes"){
echo number_format($tot_with_grandd_amt-$vat_amtt,2);
}else{
echo number_format($tot_with_grandd_amt,2);
}
?>
</td>
<td></td>
</tr>



                </tbody>

            </table>
        </div>
		
		<? }elseif($purchase_type=='1'){
		
		$res='SELECT a.id, a.req_id,a.item_description, a.unit_name, b.ledger_name, c.center_name, a.qty AS total_unit, a.rate, a.amount, "x" 
FROM purchase_invoice a LEFT JOIN accounts_ledger b ON a.expense_head = b.ledger_id LEFT JOIN cost_center c ON a.cc_code = c.id 
WHERE a.po_no='.$po_no;

		?>
			
			<div class="container-fluid pt-5 p-0 ">
            <table class="table1  table-striped table-bordered table-hover table-sm">
<thead class="thead1">
<tr class="bgc-info">

<th>S/L</th>


<th>Item Name</th>

<th>Req ID</th>

<th>Unit Name</th>

<th>Expense Head</th>

<th>Cost Center</th>

<th>Rate</th>

<th>Total Unit</th>

<th>Amount<? if($vat_include == 'Including'){echo $vat_include;} else{ echo $vat_include;} ?></th>

<th>Action</th>

</tr>
</thead>
<?

$sl=0;

$query = db_query($res);

while($data=mysqli_fetch_object($query)){

$sl++;

?>	
<tbody>
<tr class="tbody1">

<td><?=$sl;?></td>


<td><?=$data->item_description;?></td>

<td><?=$data->req_id;?></td>

<td><?=$data->unit_name;?></td>

<td><?=$data->ledger_name;?></td>

<td><?=$data->center_name;?></td>

<td><?=$data->rate;?></td>

<td><?=$data->total_unit;?></td>

<td><?=$data->amount; $totat_amt+=$data->amount;?></td>

<td><a href="?del=<?=$data->id?>"><button type="button" class="btn2 btn1-bg-cancel"><i class="fa-solid fa-trash"></i></button></a></td>

</tr>
</tbody>

<?	

}

?>




<tr>

<td colspan="7"></td>

<td>Total</td>

<td>

<? 

echo ($totat_amt+$vat_amt);

?>

</td>

<td></td>

</tr>

</table>
        </div>
			
		
		<? } ?>
    </form>



    <!--button design start-->
    <? if($total_amt>0){ ?>
    <form action="?" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">

        <div class="container-fluid p-0 ">


            <div class="n-form-btn-class">

                

				<input name="delete"  type="submit" class="btn1 btn1-bg-cancel" value="DELETE PURCHASE" />

                

				

				<input  name="po_no" type="hidden" id="po_no" value="<?=$$unique?>"/>



      			<input name="confirmm2" type="submit" class="btn1 btn1-bg-submit" value="CONFIRM PURCHASE"  />

            </div>



        </div>
		
		</form>
	<? } ?>
		<? } ?>

</div>


<script>
function confirm_function() {
  var notes = prompt("Why Confirm This Purchase ?","");
  if (notes!=null) {
    document.getElementById("confirm_remarks").value =notes;
	document.getElementById("cz").submit();
  }
  return false;
}
</script>



<!--<script>$("#codz").validate(); </script>-->

<script>
    CKEDITOR.replace( 'terms_condition' );
	
	function count_1(id) {

			
			var qty = (document.getElementById('qty' + id).value) * 1;
			var rest_qty = (document.getElementById('rest_qty' + id).value) * 1;
			var num_1 = qty * ((document.getElementById('unit_price' + id).value) * 1);
			
			if(qty>rest_qty){
			alert('PO Qty Should Not Overflow PR Qty!');
			document.getElementById('qty'+id).value = '';
			document.getElementById('amount'+id).value = '';
			}else{
			document.getElementById('amount'+id).value = num_1.toFixed(2);	
			}
        var total = 0;
        var tot_count = document.getElementById('tot_count').value;

        for (var i = 1; i <= tot_count; i++) {
            var amountField = document.getElementById('amount' + i);
            if (amountField && amountField.value) {
                total += parseFloat(amountField.value);
            }
        }

        document.getElementById('total_amount').innerText = total.toFixed(2);
    
		}
</script>
<script>$("#codz").validate(); </script>
<?

//$main_content=ob_get_contents();
//
//ob_end_clean();
//
//require_once SERVER_CORE."routing/layout.bottom.php";
require_once SERVER_CORE."routing/layout.bottom.php";

?>