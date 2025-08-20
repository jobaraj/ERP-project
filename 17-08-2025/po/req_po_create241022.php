<?php
session_start();
ob_start();





require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE . "routing/layout.top.php";

$title = 'New Purchase Entry';

do_calander('#po_date');
do_calander('#quotation_date');
create_combobox('vendor_id_draft');
$table_master = 'purchase_master';
$table_details = 'purchase_invoice';
$unique = 'po_no';

$req_all = find_all_field('requisition_master', '', 'req_no="' . $_SESSION['selected_req_no'] . '"');

if (isset($_POST['new'])) {
	$folder = 'purchase';
	$field = 'file_upload';
	$file_name = $field . '-' . $_POST['po_no'];
	if ($_FILES['file_upload']['tmp_name'] != '') {
		$_POST['file_upload'] = upload_file($folder, $field, $file_name);
	}
	$crud = new crud($table_master);
	$_POST['vendor_id_draft_a'];

	if ($_POST['vendor_id_draft_a'] != '') {
		$get_vendor = explode("#", $_POST['vendor_id_draft_a']);
		$_POST['vendor_id'] = $get_vendor[1];
	}


	if (!isset($_SESSION[$unique])) {
		$_POST['entry_by'] = $_SESSION['user']['id'];
		$_POST['entry_at'] = date('Y-m-d h:s:i');
		$_POST['edit_by'] = $_SESSION['user']['id'];
		$_POST['edit_at'] = date('Y-m-d h:s:i');
		$$unique = $_SESSION[$unique] = $crud->insert();
		unset($$unique);
		$type = 1;
		$msg = 'Purchase Order No Created. (PO No :-' . $_SESSION[$unique] . ')';
	} else {
		$_POST['edit_by'] = $_SESSION['user']['id'];
		$_POST['edit_at'] = date('Y-m-d h:s:i');
		$crud->update($unique);
		$type = 1;
		$msg = 'Successfully Updated.';
	}
}

$$unique = $_SESSION[$unique];

if (isset($_POST['delete'])) {
	$crud = new crud($table_master);
	$condition = $unique . "=" . $$unique;
	$crud->delete($condition);
	$crud = new crud($table_details);
	$condition = $unique . "=" . $$unique;
	$crud->delete_all($condition);
	unset($$unique);
	unset($_SESSION[$unique]);
	$type = 1;
	$msg = 'Successfully Deleted.';
}

if ($_GET['del'] > 0) {
	$crud = new crud($table_details);
	$condition = "id=" . $_GET['del'];
	$crud->delete_all($condition);
	$type = 1;
	$msg = 'Successfully Deleted.';
}
if (isset($_POST['confirmm'])) {
	$del = db_query('delete from purchase_invoice where po_no="' . $_POST['po_no'] . '"');
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
		$po_details_insert = 'insert into purchase_invoice(`po_no`,`po_date`,`req_no`,`req_id`,`vendor_id`,`item_id`,`warehouse_id`,`rate`,`qty`,`amount`,`entry_by`,`entry_at`) value("' . $po_no . '","' . $po_date . '","' . $data->req_no . '","' . $data->id . '","' . $vendor_id . '","' . $data->item_code . '","' . $data->warehouse_id . '","' . $poRate . '","' . $poQty . '","' . $poAmount . '","' . $_SESSION['user']['id'] . '","' . date('Y-m-d H:s:i') . '")';
		db_query($po_details_insert);
	}


	unset($_POST);
	$_POST[$unique] = $$unique;
	$_POST['entry_by'] = $_SESSION['user']['id'];
	$_POST['entry_at'] = date('Y-m-d h:s:i');
	$_POST['status'] = 'UNCHECKED';


	$crud = new crud($table_master);
	$crud->update($unique);
	unset($$unique);
	unset($_SESSION[$unique]);
	$type = 1;
	$msg = 'Successfully Forwarded for Approval.';
}

if (isset($_POST['add']) && ($_POST[$unique] > 0)) {
	$crud = new crud($table_details);
	$iii = explode('#>', $_POST['item_id']);
	$_POST['item_id'] = $iii[1];
	$_POST['entry_by'] = $_SESSION['user']['id'];
	$_POST['entry_at'] = date('Y-m-d h:s:i');
	$_POST['edit_by'] = $_SESSION['user']['id'];
	$_POST['edit_at'] = date('Y-m-d h:s:i');
	$crud->insert();
}

if ($$unique > 0) {
	$condition = $unique . "=" . $$unique;
	$data = db_fetch_object($table_master, $condition);
	foreach ($data as $key => $value) {
		$$key = $value;
	}

}
if ($$unique > 0)
	$btn_name = 'Update PO Information';
else
	$btn_name = 'Initiate PO Information';
if ($_SESSION[$unique] < 1)
	$$unique = db_last_insert_id($table_master, $unique);

//auto_complete_start_from_db

//auto_complete_from_db($table,$show,$id,$con,$text_field_id);
auto_complete_from_db('item_info', 'item_name', 'concat(finish_goods_code,"-",item_name,"#>",item_id)', '1 ', 'item_id');

?>
<script language="javascript">
	function count() {
		var rate = (document.getElementById('rate').value) * 1;

		var qty = (document.getElementById('qty').value) * 1;


		document.getElementById('amount').value = (rate * qty);

		//var num=((document.getElementById('qty').value)*1)*((document.getElementById('rate').value)*1);
		//document.getElementById('amount').value = num.toFixed(2);	
	}
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



.style2 {
	color: #FFFFFF;
	font-weight: bold;
}
.style3 {font-weight: bold}
.style4 {font-weight: bold}

</style>-->


<div class="form-container_large">
	<form form action="" enctype="multipart/form-data" method="post" name="codz" id="codz"
		onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
		<!--        top form start hear-->
		<div class="container-fluid bg-form-titel">
			<div class="row">
				<!--left form-->
				<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
					<div class="container n-form2">
						<div class="form-group row m-0 pb-1">
							<? $field = 'po_no'; ?>
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PO
								No:</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">

								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" readonly />

							</div>
						</div>

						<div class="form-group row m-0 pb-1">
							<? $field = 'po_date';
							if ($po_date == '')
								$po_date = date('Y-m-d'); ?>
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PO
								Date :</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" required />
							</div>
						</div>

						<div class="form-group row m-0 pb-1">
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Company
								:</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

								<select id="group_for" name="group_for" required style="width:220px;">


									<? foreign_relation('user_group', 'id', 'group_name', $group_for, '1'); ?>

								</select>

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

							<? $field = 'file_upload'; ?>

							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">File
								Attachment:</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

								<input name="<?= $field ?>" type="file" id="<?= $field ?>" value="<?= $$field ?>"
									class="form-control" />
								<a href="<?= SERVER_CORE ?>core/upload_view.php?name=<?= $$field ?>&folder=purchase&proj_id=<?= $_SESSION['proj_id'] ?>"
									target="_blank">View Attachment</a>
							</div>


						</div>
					</div>



				</div>

				<!--Right form-->
				<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
					<div class="container n-form2">
						<div class="form-group row m-0 pb-1">

							<? $field = 'purchase_type'; ?>

							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Po
								Type</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">



								<select id="<?= $field ?>" name="<?= $field ?>" value="<?= $_POST['purchase_type'] ?>"
									required style="width:220px;">

									<option></option>

									<?= foreign_relation('purchase_type', 'id', 'po_type', $_POST['purchase_type'], '1'); ?>



								</select>



							</div>

						</div>
						<div class="form-group row m-0 pb-1">
							<? $field = 'remarks'; ?>
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Remarks:</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" />
							</div>
						</div>

						<div class="form-group row m-0 pb-1">
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vendor
								:</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
								<? if ($vendor_id < 1) { ?>
									<input type="text" list="vendor_id_draft_list" name="vendor_id_draft_a"
										id="vendor_id_draft_a" required>

									<datalist id="vendor_id_draft_list">
										<?php
										$v_sql = 'select * from vendor';
										$v_query = db_query($v_sql);
										while ($row = mysqli_fetch_object($v_query)) {
											?>
											<option value="<?php echo $row->vendor_name . "#" . $row->vendor_id; ?>">
											<?php } ?>

									</datalist>
									<!--		<select name="vendor_id_draft" id="vendor_id_draft" required   >
					
						<option></option>						
						<? //foreign_relation('vendor','vendor_id','vendor_name',$vendor_id_draft,'1'); ?>
						</select>-->

								<? } ?>

								<? if ($vendor_id > 0) { ?>

									<input name="vendor_id" type="hidden" id="vendor_id" value="<?= $vendor_id; ?>"
										readonly="" required />

									<input name="vendor_view" type="text" id="vendor_view"
										value="<?= find_a_field('vendor', 'vendor_name', 'vendor_id="' . $vendor_id . '"'); ?>"
										readonly="" required />

								<? } ?>

							</div>
						</div>

						<div class="form-group row m-0 pb-1">

							<? $field = 'vat'; ?>

							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vat(%)</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">



								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" />



							</div>

						</div>

						<div class="form-group row m-0 pb-1">

							<? $field = 'tax'; ?>

							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Tax(%)</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">



								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" />



							</div>

						</div>
						<div class="form-group row m-0 pb-1">

							<? $field = 'cash_discount'; ?>

							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Discount</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">



								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" />



							</div>

						</div>
						<div class="form-group row m-0 pb-1">

							<? $field = 'ait'; ?>

							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">AIT</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">



								<input name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>" />



							</div>

						</div>


						<!--<div class="form-group row m-0 pb-1">
								 <? $field = 'ait'; ?>
							<label for="<?= $field ?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">AIT (%):</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
						 <input  name="<?= $field ?>" type="text" id="<?= $field ?>" value="<?= $$field ?>"  />
							</div>
						</div>-->


						<? $field = 'warehouse_id';
						$table = 'warehouse';
						$get_field = 'warehouse_id';
						$show_field = 'warehouse_name';
						if ($$field == '')
							$$field = $req_all->warehouse_id; ?>
						<div class="form-group row m-0 pb-1">
							<label for="<?= $field ?>"
								class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Warehouse:</label>
							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
								<select id="<?= $field ?>" name="<?= $field ?>" required style="width:220px;">

									<option></option>

									<? foreign_relation($table, $get_field, $show_field, $$field, '1'); ?>

								</select>
							</div>
						</div>



					</div>



				</div>

			</div>
			<div class="n-form-btn-class">
				<input name="new" type="submit" class="btn1 btn1-submit-input" value="<?= $btn_name ?>" />
			</div>


		</div>

		<!--return Table design start-->

		<div class="container-fluid pt-5 p-0 ">
			<table class="table1  table-striped table-bordered table-hover table-sm">
				<thead class="thead1">
					<?
					$sql = 'select a.*,u.fname from approver_notes a, user_activity_management u where a.entry_by=u.user_id and master_id="' . $$unique . '" and type in ("PO","PR")';
					$row_check = mysqli_num_rows(db_query($sql));
					if ($row_check > 0) {
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
						while ($return_note = mysqli_fetch_object($qry)) {
							?>

							<tr>
								<td><?= $return_note->fname ?></td>
								<td><?= $return_note->entry_at ?></td>
								<td><?= $return_note->note ?></td>

							</tr>
						<? } ?>

					</tbody>
				</table>
			<? } ?>
		</div>
	</form>



	<? if ($_SESSION[$unique] > 0) { ?>


		<form action="?<?= $unique ?>=<?= $$unique ?>" method="post" name="cloud" id="cloud">
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
							<th>S/L</th>
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
							$this_po = find_all_field('purchase_invoice', '', 'req_no="' . $_SESSION['selected_req_no'] . '" and item_id="' . $data->item_code . '" and req_id="' . $data->id . '" and po_no="' . $$unique . '"');
							$rest_qty = $data->req_quantity - $already_po_qty;
							//if($rest_qty>0){
							?>

							<tr>
								<td><?= ++$i ?>
									<input type="hidden" name="po_no" value="<?= $po_no ?>" />
									<input type="hidden" name="po_date" value="<?= $po_date ?>" />
									<input type="hidden" name="vendor_id" value="<?= $vendor_id ?>" />
									<input type="hidden" name="req_qty<?= $i ?>" id="req_qty<?= $i ?>"
										value="<?= $data->req_quantity ?>" />
									<input type="hidden" name="po_qty<?= $i ?>" id="po_qty<?= $i ?>" value="<?= $already_po_qty ?>" />
								</td>

								<td><?= $data->item_description ?></td>

								<td><?= $data->req_quantity ?></td>

								<td><?= $already_po_qty; ?></td>

								<td><input type="text" onkeyup="count_1(<?= $i ?>);total_cal()"
										onblur="update_item(<?= $data->id ?>)" name="qty<?= $data->id ?>" id="qty<?= $i ?>"
										value="<?= $this_po->qty ?>" required /></td>

								<td><?= $data->Unit ?></td>

								<td><input type="hidden" required name="details_id<?= $data->id ?>"
										value="<? echo $data->id; ?>" />

									<input type="text" onkeyup="count_1(<?= $i ?>);total_cal()"
										onblur="update_item(<?= $data->id ?>)" required name="unit_price<?= $data->id ?>"
										id="unit_price<?= $i ?>" value="<?= $this_po->rate ?>" />
								</td>

								<td><input type="text" readonly required name="amount<?= $data->id ?>" id="amount<?= $i ?>"
										value="<?= $this_po->amount ?>" />
									<? $total_amt += $data->amount; ?></td>


							</tr>

						<?php } ?>
						<tr>
							<td colspan="7"><strong>Total: </strong></td>
							<input id="tot_count" type="hidden" value="<?= $i; ?>" />
							<td><strong id="total_amount">

									<?= number_format($total_amt, 2); ?>
								</strong></td>
						</tr>

					</tbody>
				</table>



			</div>

			<div class="container-fluid p-0 ">

				<div class="n-form-btn-class">
					<input name="delete" type="submit" class="btn1 btn1-bg-cancel" value="DELETE PURCHASE" />

					<input name="po_no" type="hidden" id="po_no" value="<?= $$unique ?>" />

					<input name="confirmm" type="submit" class="btn1 btn1-bg-submit" value="CONFIRM PURCHASE" />
				</div>

			</div>

		<? } ?>


	</form>




	<br />
	<br />
	<br />
	<br />
	<br />
	<br />





	<?php /*?><div class="form-container_large">
<form action="" method="post" name="codz" id="codz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
  <td valign="top"><fieldset>
 <? $field='po_no';?>

	<div>

	  <label for="<?=$field?>">PO  No: </label>

	  <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly/>

	</div>
	
	
   <? $field='po_date'; if($po_date=='') $po_date =date('Y-m-d');?>

	<div>

	  <label for="<?=$field?>">PO Date:</label>

	  <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" required/>

	</div>
  
	<div>

	  <label for="<?=$field?>">Company:</label>

	  <select id="group_for" name="group_for" required style="width:220px;"  >


	<? foreign_relation('user_group','id','group_name',$group_for,'1');?>

	  </select>

	</div>
	
	
	<? $field='remarks';?>

	  <div>

		<label for="<?=$field?>">Remarks:</label>

		<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>

	  </div>

	
	
	
	
		 <div></div>

  </fieldset></td>
  <td>
		  <fieldset>
		  


	<div>
	
  <div>

	  <label for="<?=$field?>">Vendor :</label>
	  
	  <? if($vendor_id<1) { ?>
	  <select name="vendor_id_draft" id="vendor_id_draft" required   >
  
	  <option></option>						
	  <? foreign_relation('vendor','vendor_id','vendor_name',$vendor_id_draft,'1');?>
	  </select>
	  
	  <? }?>
	  
	  <? if($vendor_id>0) { ?>
	  
	   <input style="width:190px;"  name="vendor_id" type="hidden" id="vendor_id" value="<?=$vendor_id;?>" readonly=""  required/>
	  
	   <input style="width:190px;"  name="vendor_view" type="text" id="vendor_view" value="<?=find_a_field('vendor','vendor_name','vendor_id="'.$vendor_id.'"');?>" readonly=""  required/>
	  
	  <? }?>
	</div>  
	
	
	
	

	 <div>



<? $field='tax';?>

<label for="<?=$field?>">VAT (%):</label>

<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />

</div>

	
<? $field='warehouse_id'; $table='warehouse';$get_field='warehouse_id';$show_field='warehouse_name'; if($$field=='') $$field=$req_all->warehouse_id;?>

	<div>

	  <label for="<?=$field?>">Warehouse:</label>

	  <select id="<?=$field?>" name="<?=$field?>" required style="width:220px;"    >

	  <option></option>

	  <? foreign_relation($table,$get_field,$show_field,$$field,'1');?>

	  </select>

	</div>
	
	<? $field='req_no';?>
	<div>

	  <label for="<?=$field?>">Requisition No. :</label>

	  <input type="text" name="req_no" id="req_no" value="<?php if($req_no>0) echo $req_no; else echo $_SESSION['selected_req_no']?>" readonly="readonly" />

	</div>


	  
  
  
			

	</div>
		  </fieldset>	</td>
</tr>
<tr>
  <td colspan="2"><div class="buttonrow" style="margin-left:390px;">
	<input name="new" type="submit" class="btn1" value="<?=$btn_name?>" style="width:250px; font-weight:bold; font-size:12px;" />
  </div></td>
  </tr>
  <?
  $sql = 'select a.*,u.fname from approver_notes a, user_activity_management u where a.entry_by=u.user_id and master_id="'.$$unique.'" and type in ("PO","PR")';
  $row_check = mysqli_num_rows(db_query($sql));
  if($row_check>0){
  ?>
  <tr>
	 <td colspan="2">
		<table border="1" cellpadding="0" cellspacing="0" style=" width:100%; margin:auto; border-collapse:collapse;">
		   <tr>
			  <th>Returned By</th>
			  <th>Returned At</th>
			  <th>Remarks</th>
		   </tr>
		   <?
			
			$qry = db_query($sql);
			while($return_note=mysqli_fetch_object($qry)){
		   ?>
		   <tr>
			  <td><?=$return_note->fname?></td>
			  <td><?=$return_note->entry_at?></td>
			  <td><?=$return_note->note?></td>
		   </tr>
		   <? } ?>
		</table>
	 </td>
  </tr>
  <? } ?>
</table>
</form>
<? if($_SESSION[$unique]>0){?>
<form action="?<?=$unique?>=<?=$$unique?>" method="post" name="cloud" id="cloud">

<? 
$group_for = find_a_field('warehouse','group_for','warehouse_id='.$warehouse_id.' ');
if(($vendor->ledger_id==0)&&($group_for==2||$group_for==3)){ ?>
<table width="80%" border="0" align="center" cellpadding="5" cellspacing="0">
<tr><td bgcolor="#FF3333"><div align="center" class="style1">VERDOR IS BLOCKED. NO ACCOUNT CODE FOUND</div></td></tr>
</table>

<? }else{?>
		  
<? }?>				  
<br /><br />



<div class="tabledesign2">
<table id="grp" cellspacing="0" cellpadding="0" width="100%">
	  <tbody>
		  <tr>
			  <th>S/L</th>
			  <th>Item Description</th>
			  <th>Req Qty</th>
			  <th>Already PO Qty</th>
			  <th>Order Qty</th>
			  <th>Unit</th>
			  <th>Unit Price</th>
			  <th>Amount</th>
			  
			  
		  </tr>
<? 
$res='select a.id,b.item_id as item_code, concat(b.item_name) as item_description , a.qty as req_quantity ,b.unit_name as Unit,a.warehouse_id
from requisition_order a,item_info b

where b.item_id=a.item_id and a.req_no='.$_SESSION['selected_req_no'];
$query=db_query($res);

while($data=mysqli_fetch_object($query)){

$already_po_qty = find_a_field('purchase_invoice','sum(qty)','req_no="'.$_SESSION['selected_req_no'].'" and item_id="'.$data->item_code.'" and req_id="'.$data->id.'"');
$this_po = find_all_field('purchase_invoice','','req_no="'.$_SESSION['selected_req_no'].'" and item_id="'.$data->item_code.'" and req_id="'.$data->id.'" and po_no="'.$$unique.'"');
$rest_qty = $data->req_quantity-$already_po_qty;
//if($rest_qty>0){
?>
<tr>
			  <td><?=++$i?>
			   <input type="hidden" name="po_no" value="<?=$po_no?>" />
			   <input type="hidden" name="po_date" value="<?=$po_date?>" />
			   <input type="hidden" name="vendor_id" value="<?=$vendor_id?>" />
			   <input type="hidden" name="req_qty<?=$i?>" id="req_qty<?=$i?>" value="<?=$data->req_quantity?>" />
			   <input type="hidden" name="po_qty<?=$i?>" id="po_qty<?=$i?>" value="<?=$already_po_qty?>" />
			  </td>
			  <td><?=$data->item_description ?></td>
			  
			  <td><?=$data->req_quantity ?></td>
			  <td><?=$already_po_qty; ?></td>
			  
			  <td><input type="text" onkeyup="count_1(<?=$i?>);total_cal()" onblur="update_item(<?=$data->id?>)" name="qty<?=$data->id?>" id="qty<?=$i?>" value="<?=$this_po->qty?>" required/></td>
			  <td><?=$data->Unit ?></td>
			  <td>
			  <input type="hidden" required name="details_id<?=$data->id?>"  value="<?  echo $data->id; ?>" />
			  
			  <input type="text" onkeyup="count_1(<?=$i?>);total_cal()" onblur="update_item(<?=$data->id?>)" required name="unit_price<?=$data->id?>" id="unit_price<?=$i?>" value="<?=$this_po->rate?>" />
			  </td>
			  <td><input type="text" readonly required name="amount<?=$data->id?>" id="amount<?=$i?>" value="<?=$this_po->amount?>" /> 
			  <? $total_amt += $data->amount; ?></td>
			  
			  
			  
			  
			  
		  </tr>
		  <?php  } ?>
		  <tr>
			  <td colspan="7"><strong>Total: </strong></td>
			  <input id="tot_count" type="hidden" value="<?=$i;?>"  />
			  <td><strong id="total_amount">
			  
			  <?=number_format($total_amt,2);?>
			  </strong></td>
		  </tr>


</table>
</div>

<br />



<table width="100%" border="0">
  <tr>
	<td align="center">

	<input name="delete"  type="submit" class="btn1" value="DELETE PURCHASE" style="width:270px; font-weight:bold; font-size:12px;color:#F00; height:30px" />

	</td>
	<td align="center">
	
	<input  name="po_no" type="hidden" id="po_no" value="<?=$$unique?>"/>

	<input name="confirmm" type="submit" class="btn1" value="CONFIRM PURCHASE" style="width:270px; font-weight:bold; font-size:12px; height:30px; color:#090" />
</tr>
</table>
</form>
<? }?>
</div><?php */ ?>
	<script>$("#codz").validate(); $("#cloud").validate();</script>
	<script>
		function total_cal() {
			var total = 0;
			var total_sl = document.getElementById("tot_count").value * 1;

			for (var i = 1; i <= total_sl; i++) {
				total += document.getElementById("amount" + i).value * 1;
				//alert(total);	
			}
			document.getElementById("total_amount").innerHTML = total;
		}


		function count_1(id) {

			var req_qty = (document.getElementById('req_qty' + id).value) * 1;
			var po_qty = (document.getElementById('po_qty' + id).value) * 1;
			var qty = (document.getElementById('qty' + id).value) * 1;
			var num_1 = ((document.getElementById('qty' + id).value) * 1) * ((document.getElementById('unit_price' + id).value) * 1);
			var already_po = po_qty;
			/*if(already_po>=req_qty){
			alert('PO Qty Should Not Overflow');
			document.getElementById('qty'+id).value = '';
			}else{
			document.getElementById('amount'+id).value = num_1.toFixed(2);	
			}*/
			document.getElementById('amount' + id).value = num_1.toFixed(2);

		}
	</script>
	<?

	require_once SERVER_CORE . "routing/layout.bottom.php";
	?>