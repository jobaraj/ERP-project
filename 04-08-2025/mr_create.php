<?php

require_once "../../../assets/template/layout.top.php";


$title='New Purchase Requisition Create';


do_calander('#req_date');
do_calander('#need_by');

$table_master='requisition_master';
$table_details='requisition_order';
$unique='req_no';

$user_code = find_a_field('user_activity_management','fname','user_id="'.$_SESSION['user']['id'].'"');

if($_GET['mhafuz']>0)
unset($_SESSION[$unique]);

if(isset($_POST['new']))
{
		$crud   = new crud($table_master);

		if(!isset($_SESSION[$unique])) {
		$_POST['status']='MANUAL';
		$_POST['entry_by']=$_SESSION['user']['id'];
		$_POST['entry_at']=date('Y-m-d h:s:i');
		$_POST['edit_by']=$_SESSION['user']['id'];
		$_POST['edit_at']=date('Y-m-d h:s:i');
		$$unique=$_SESSION[$unique]=$crud->insert();
		unset($$unique);
		$type=1;
		$msg='Requisition No Created. (Req No : '.$_SESSION[$unique].')';
		}
		else {
		$_POST['edit_by']=$_SESSION['user']['id'];
		$_POST['edit_at']=date('Y-m-d h:s:i');
		$crud->update($unique);
		$type=1;
		$msg='Successfully Updated.';
		}
}

$$unique=$_SESSION[$unique];

if(isset($_POST['delete']))
{
		$crud   = new crud($table_master);
		$condition=$unique."=".$$unique;		
		$crud->delete($condition);
		$crud   = new crud($table_details);
		$condition=$unique."=".$$unique;		
		$crud->delete_all($condition);
		unset($$unique);
		unset($_SESSION[$unique]);
		$type=1;
		$msg='Successfully Deleted.';
}

if($_GET['del']>0)
{
		$crud   = new crud($table_details);
		$condition="id=".$_GET['del'];		
		$crud->delete_all($condition);
		$type=1;
		$msg='Successfully Deleted.';
}
if(isset($_POST['confirmm']))
{
		unset($_POST);
		$_POST[$unique]=$$unique;
		$_POST['entry_by']=$_SESSION['user']['id'];
		$_POST['entry_at']=date('Y-m-d h:s:i');

if($_SESSION['user']['group']==5){ 
$_POST['status']='UNCHECKED-BOQ'; 
}else{ 
	if($_SESSION['user']['depot']==71){$_POST['status']='PRE-CHECKED'; }else{$_POST['status']='UNCHECKED';}
}



		$crud   = new crud($table_master);
		$crud->update($unique);
		unset($$unique);
		unset($_SESSION[$unique]);
		$type=1;
		$msg='Successfully Forwarded for Approval.';
}

if(isset($_POST['add'])&&($_POST[$unique]>0))
{
		$crud   = new crud($table_details);
		$iii=explode('#>',$_POST['item_id']);
		
		$_POST['item_id']=find_a_field('item_info','item_id','finish_goods_code="'.$iii[0].'"');
		$_POST['entry_by']=$_SESSION['user']['id'];
		$_POST['entry_at']=date('Y-m-d h:s:i');
		$_POST['edit_by']=$_SESSION['user']['id'];
		$_POST['edit_at']=date('Y-m-d h:s:i');
		$crud->insert();
}

if($$unique>0)
{
		$condition=$unique."=".$$unique;
		$data=db_fetch_object($table_master,$condition);
		while (list($key, $value)=each($data))
		{ $$key=$value;}
		
}
if($$unique>0) $btn_name='Update Requsition Information'; else $btn_name='Initiate Requsition Information';
if($_SESSION[$unique]<1)
$$unique=db_last_insert_id($table_master,$unique);

//auto_complete_from_db($table,$show,$id,$con,$text_field_id);
// auto_complete_from_db('item_info','item_name','item_id','1','item_id');
// auto_complete_from_db('item_info','concat(item_name,"#>#>",item_id)','concat(item_name,"#>",item_description,"#>",item_id)', '1 order by item_id asc', 'item_id');



?>
<script language="javascript">
function focuson(id) {
  if(document.getElementById('item_id').value=='')
  document.getElementById('item_id').focus();
  else
  document.getElementById(id).focus();
}
window.onload = function() {
if(document.getElementById("warehouse_id").value>0)
  document.getElementById("item_id").focus();
  else
  document.getElementById("req_date").focus();
}
</script>




	<div class="form-container_large">
		<form action="mr_create.php" method="post" name="codz" id="codz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
			<div class="container-fluid bg-form-titel">
				<div class="row">
					<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
						<div class="container n-form2">
							<div class="form-group row m-0 pb-1">
								<? $field='req_no';?>
								<label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition No :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
									<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>
								</div>
							</div>

							<div class="form-group row m-0 pb-1">
								<? $field='req_date'; if($req_date=='') $req_date =date('Y-m-d');?>
								<label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition Date :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" />
								</div>
							</div>
							<div class="form-group row m-0 pb-1">
								<? $field='warehouse_id'; $table='warehouse';$get_field='warehouse_id';$show_field='warehouse_name';?>
								<label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Warehouse :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$_SESSION['user']['depot']?>" />
									<input name="warehouse_id2" type="text" id="warehouse_id2" value="<?=find_a_field('warehouse','warehouse_name','warehouse_id='.$_SESSION['user']['depot'])?>" readonly="" />
								</div>
							</div>

						</div>



					</div>


					<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6 ">
						<div class="container n-form2">
							<div class="form-group row m-0 pb-1">
							<? //$field='user_id'; $table='user_activity_management';$get_field='user_id';$show_field='fname';?>
								<label for="<?=$req_for?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition By :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
							
								
								<select name="req_for" id="req_for">
									  <? 
									  if($_SESSION['user']['level']==5){
									  echo '<option></option>';
									  foreign_relation('user_activity_management','user_id','fname',$_POST['user_id'],'1');
									  }else{
									  foreign_relation('user_activity_management','user_id','fname',$_POST['user_id'],'user_id="'.$_SESSION['user']['id'].'"');
									  }
									  ?>
								</select>	
									
									

								</div>
							</div>

							<div class="form-group row m-0 pb-1">
								<? $field='need_by';?>
								<label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Delivery Deadline :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" autocomplete="off"/>

								</div>
							</div>

							<div class="form-group row m-0 pb-1">
								<? $field='req_note';?>
								<label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Additional Note :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>

								</div>
							</div>
							
							
							<div class="form-group row m-0 pb-1">
								<? $field='req_type';?>
								<label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition Type :</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<select  name="<?=$field?>" id="<?=$field?>" value="<?=$$field?>" required>
									<option></option>
									<option <?=($req_type=='General')?'selected':''?>>General</option>
									<option <?=($req_type=='Sample DO')?'selected':''?>>Sample DO</option>
									</select>

								</div>
							</div>


						</div>






					</div>

				</div>

				<div class="n-form-btn-class">
					<input name="new" type="submit" class="btn1 btn1-bg-submit" value="<?=$btn_name?>" />
				</div>
			</div>

			<!--return Table design start-->
			<div class="container-fluid pt-1 p-0 ">
				<?
				$sql = 'select a.*,u.fname from approver_notes a, user_activity_management u where a.entry_by=u.user_id and master_id="'.$$unique.'" and type="MR"';
				$row_check = mysql_num_rows(mysql_query($sql));
				if($row_check>0){
					?>
				<table class="table1  table-striped table-bordered table-hover table-sm">
					<thead class="thead1">
					<tr class="bgc-info">
						<th>Returned By</th>
						<th>Returned At</th>
						<th>Remarks</th>
					</tr>
					</thead>

					<tbody class="tbody1">
					<?
					$qry = mysql_query($sql);
					while($return_note=mysql_fetch_object($qry)){
					?>

					<tr>
						<td><?=$return_note->fname?></td>
						<td><?=$return_note->entry_at?></td>
						<td><?=$return_note->note?></td>

					</tr>
					<? } ?>
					</tbody>
				</table>
				<? } ?>

			</div>
<div class="container-fluid p-0 ">
            
			
				<div class="d-flex justify-content-center">
						<table border="1" align="center" cellpadding="0" cellspacing="0">
					
					  <tbody><tr>
					
						<td colspan="3" align="center"><strong>Entry Information</strong></td>
					
						</tr>
					
					  <tr>
					
						<td align="right"> Entry By:</td>
					
						<td align="left">&nbsp;&nbsp;<?=find_a_field('user_activity_management','fname','user_id='.$entry_by);?></td>
					
						<td rowspan="2" align="center"><a href="mr_print_view.php?req_no=<?=$req_no?>" target="_blank"><img src="../../../images/icons/print.png" width="36" height="36" /></a></td>
					
					  </tr>
					
					  <tr>
					
						<td align="right">Entry On:</td>
					
						<td align="left">&nbsp;&nbsp;<?=$entry_at?></td>
					
						</tr>
					
					</tbody></table>
	
			</div>
			
			
		

        </div>
		</form>




		<? if($_SESSION[$unique]>0){?>

		<form action="?<?=$unique?>=<?=$$unique?>" method="post" name="cloud" id="cloud">
			<!--Table input one design-->
			<div class="container-fluid pt-2 p-0 ">



				<table class="table1  table-striped table-bordered table-hover table-sm">
					<thead class="thead1">
					<tr class="bgc-info">
						<th>Item Name</th>
						<th>Remraks</th>
						<th>Stk Qty</th>
						<th>LPQ</th>
						<th>LPD</th>
						<th>Unit</th>
						<th>Specification</th>
						<th>Req Qty</th>
						<th>Action</th>
					</tr>
					</thead>

					<tbody class="tbody1">


					<tr>

						<td>
							<input  name="<?=$unique?>"i="i" type="hidden" id="<?=$unique?>" value="<?=$$unique?>"/>
							<input  name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$warehouse_id?>"/>
							<input  name="req_date" type="hidden" id="req_date" value="<?=$req_date?>"/>
							
							<input list="item_list" name="item_id" type="text" id="item_id" value="<?=$item_id?>" required="required" onblur="getData2('mr_ajax.php', 'mr',this.value,document.getElementById('warehouse_id').value);" >

<datalist id="item_list">
<?php 
$sql='select * from item_info where 1 ';
$query=mysql_query($sql);
while($row=mysql_fetch_object($query)){

?> 
  <option value="<?php echo $row->finish_goods_code."#>".$row->item_name;?>">
  <?php 
  }
  ?>
</datalist>
							
							
							
							
							<?php /*?><input  name="item_id" type="text" id="item_id" value="<?=$item_id?>" required="required" onblur="getData2('mr_ajax.php', 'mr', this.value, document.getElementById('warehouse_id').value);"/><?php */?>
							
							
							
						</td>

						<td>
							<input name="item_for" id="item_for" type="text" />
						</td>






						<td colspan="4">
							<div align="right">
								  <span id="mr">
						<table style="width:100%;" border="1">
							<tr>

								<td width="20%"><input name="qoh" type="text"  id="qoh" class="form-control" onfocus="focuson('qty')" /></td>

								<td width="20%"><input name="last_p_qty" type="text"  id="last_p_qty" class="form-control" onfocus="focuson('qty')" /></td>

								<td width="20%"><input name="last_p_date" type="text" id=" 	last_p_date" class="form-control" onfocus="focuson('qty')" /></td>

								<td width="20%"><input name="unit_name" type="text" id="unit_name"  maxlength="100" class="form-control" onfocus="focuson('qty')" /></td>
							</tr>
						</table>
						</span>
							</div>
						</td>

						
	<td><input name="specification" type="text" class="input3" id="specification" required /></td>
						<td><input name="qty" type="text" class="input3" id="qty" required /></td>


						<td><input name="add" type="submit" id="add" class="btn1 btn1-bg-submit" value="ADD" /></td>

					</tr>






					</tbody>
				</table>





			</div>




			<!--Data multi Table design start-->
			<div class="container-fluid pt-2 p-0 ">
				<?
				$res='select a.id,b.item_name as item_name,a.qoh as stock_qty,a.last_p_qty as last_pur_qty, b.finish_goods_code, a.last_p_date as last_pur_date,a.qty,"x" from requisition_order a,item_info b where b.item_id=a.item_id and a.req_no='.$req_no;
				?>

				<table class="table1  table-striped table-bordered table-hover table-sm">
					<thead class="thead1">
					<tr class="bgc-info">
						<th>Item Name</th>
						<th>Stock Qty</th>
						<th>Last Pur Qty</th>

						<th>Last Pur Date</th>
						<th>Unit</th>
						<th>Specification</th>
						<th>Qty</th>
						<th>Action</th>
					</tr>
					</thead>

					<tbody class="tbody1">
						<?
						 $res='select a.id,b.item_name as item_name,a.qoh as stock_qty,a.last_p_qty as last_pur_qty, b.finish_goods_code, a.last_p_date as last_pur_date,a.qty,a.specification,a.unit_name,"x" from requisition_order a,item_info b where b.item_id=a.item_id and a.req_no='.$req_no;
                        $qry = mysql_query($res);
						while($data=mysql_fetch_object($qry)){
						?>

					<tr>
						<td><?=$data->item_name?></td>
						<td><?=$data->stock_qty?></td>
						<td><?=$data->last_pur_qty?></td>

						<td><?=$data->last_pur_date?></td>
						<td><?=$data->unit_name?></td>
						<td><?php echo $data->specification;?></td>
						<td><?=$data->qty?></td>
						<td><a href="?del=<?=$data->id?>"> X </a></td>

					</tr>
                    <? } ?>
					</tbody>
				</table>

			</div>
		</form>



		<!--button design start-->
		<form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
			<div class="container-fluid p-0 ">

				<div class="n-form-btn-class">
					<input name="delete" type="submit" class="btn1 btn1-bg-cancel" value="DELETE AND CANCEL REQUSITION" />
					<input name="confirmm" type="submit" class="btn1 btn1-bg-submit" value="CONFIRM AND FORWARD REQUISITION" />
				</div>

			</div>
		</form>

		<? }?>
	</div>
	<script>$("#codz").validate();$("#cloud").validate();</script>








<br>
<br>
<br>
<br>







<?php /*?><div class="form-container_large">
<form action="mr_create.php" method="post" name="codz" id="codz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
		<fieldset style="border:1px solid #0099FF">
    <? $field='req_no';?>
      <div>
        <label for="<?=$field?>">Requisition No: </label>
        <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>
      </div>

    <? $field='req_date'; if($req_date=='') $req_date =date('Y-m-d');?>
      <div>
        <label for="<?=$field?>">Requisition Date:</label>
        <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" />
      </div>

    <? $field='warehouse_id'; $table='warehouse';$get_field='warehouse_id';$show_field='warehouse_name';?>
      <div>
        <label for="<?=$field?>">Warehouse:</label>
		<input name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$_SESSION['user']['depot']?>" />
		<input name="warehouse_id2" type="text" id="warehouse_id2" value="<?=find_a_field('warehouse','warehouse_name','warehouse_id='.$_SESSION['user']['depot'])?>" readonly="" />
      </div>
    </fieldset>
	</td>

    <td>
			<fieldset style="border:1px solid #0099FF">
			
    <? $field='req_for';?>
      <div>
        <label for="<?=$field?>">Requisition By:</label>
      <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" />
      </div>
    <? $field='need_by';?>
      <div>
        <label for="<?=$field?>">Delivery Deadline(Date):</label>
        <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" autocomplete="off"/>
      </div>
    <? $field='req_note';?>
      <div>
        <label for="<?=$field?>">Additional Note:</label>
        <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>
      </div>
	      
			</fieldset>	</td>
  </tr>
  <tr>
    <td colspan="2"><div class="buttonrow" style="margin-left:240px;">
      <input name="new" type="submit" class="btn1" value="<?=$btn_name?>" style="width:250px; font-weight:bold; font-size:12px;" />
    </div></td>
    </tr>

	<tr>
	 <td colspan="2">&nbsp;</td>
	
	</tr>

	<?
	$sql = 'select a.*,u.fname from approver_notes a, user_activity_management u where a.entry_by=u.user_id and master_id="'.$$unique.'" and type="MR"';
	$row_check = mysql_num_rows(mysql_query($sql));
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
			  
			  $qry = mysql_query($sql);
			  while($return_note=mysql_fetch_object($qry)){
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
<table  width="100%" border="1" align="center"  style="border-collapse:collapse; border:1px solid #caf5a5;" cellpadding="2" cellspacing="2">
                      <tr>
                        <td align="center" bgcolor="#0099FF"><strong>Item Name</strong></td>
                        <td align="center" bgcolor="#0099FF"><strong>Remraks</strong></td>
                        <td align="center" bgcolor="#0099FF"><strong>Stk Qty</strong></td>
                        <td align="center" bgcolor="#0099FF"><strong>LPQ</strong></td>
                        <td align="center" bgcolor="#0099FF"><strong>LPD</strong></td>
                        <td align="center" bgcolor="#0099FF"><strong>Unit</strong></td>
                        <td align="center" bgcolor="#0099FF"><strong>Req Qty</strong></td>
                          <td  rowspan="2" align="center" bgcolor="#FF0000">
						  <div class="button">
						  <input name="add" type="submit" id="add" value="ADD" tabindex="12" class="update"/>                       
						  </div>
						  </td>
      </tr>
                      <tr>

                        <td align="center" bgcolor="#CCCCCC"><input  name="<?=$unique?>"i="i" type="hidden" id="<?=$unique?>" value="<?=$$unique?>"/>
                            <input  name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$warehouse_id?>"/>
                            <input  name="req_date" type="hidden" id="req_date" value="<?=$req_date?>"/>
                            <input  name="item_id" type="text" id="item_id" value="<?=$item_id?>" style="width:300px;" required="required" onblur="getData2('mr_ajax.php', 'mr', this.value, document.getElementById('warehouse_id').value);"/></td>

						  <td align="center" bgcolor="#CCCCCC">
							
                        <input name="item_for" id="item_for" type="text" maxlength="100%" style="width:80px;" />
						</td>




						  <td colspan="4" align="center" bgcolor="#CCCCCC">
							  <div align="right">
								  <span id="mr">
						<table style="width:100%;" border="1">
							<tr>

								<td width="20%"><input name="qoh" type="text"  id="qoh" class="form-control" onfocus="focuson('qty')" /></td>

								<td width="20%"><input name="last_p_qty" type="text"  id="last_p_qty" class="form-control" onfocus="focuson('qty')" /></td>

								<td width="20%"><input name="last_p_date" type="text" id=" 	last_p_date" class="form-control" onfocus="focuson('qty')" /></td>

								<td width="20%"><input name="unit_name" type="text" id="unit_name"  maxlength="100" class="form-control" onfocus="focuson('qty')" /></td>
							</tr>
						</table>
						</span>
							  </div>
						  </td>

<!--                        <td colspan="4" align="center" bgcolor="#CCCCCC"><span id="mr">-->
<!--<input name="qoh" type="text" class="input3" id="qoh" style="width:40px;" onfocus="focuson('qty')" readonly="readonly"/>-->
<!--<input name="last_p_qty" type="text" class="input3" id="last_p_qty" style="width:30px;" onfocus="focuson('qty')" readonly="readonly"/>-->
<!--<input name="last_p_date" type="text" class="input3" id=" 	last_p_date"  style="width:30px;" onfocus="focuson('qty')" readonly="readonly"/>-->
<!--<input name="unit_name" type="text" class="input3" id="unit_name"  maxlength="100" style="width:30px;" onfocus="focuson('qty')" readonly="readonly"/>-->
<!--</span></td>-->

<td align="center" bgcolor="#CCCCCC"><input name="qty" type="text" class="input3" id="qty"  maxlength="100%" style="width:60px;" required/></td>
      </tr>
    </table>
	<br />
	<br />
	<br />
	<br />

<? 
$res='select a.id,b.item_name as item_name,a.qoh as stock_qty,a.last_p_qty as last_pur_qty,a.last_p_date as last_pur_date,a.qty,"x" from requisition_order a,item_info b where b.item_id=a.item_id and a.req_no='.$req_no;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr>
      <td><div class="tabledesign2">
        <? 
//$res='select * from tbl_receipt_details where rec_no='.$str.' limit 5';
echo link_report_del($res);
		?>

      </div>
	  </td>
    </tr>
	    	
	

				
    <tr>
     <td>

 </td>
    </tr>
  </table>
</form>


<form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
  <table width="100%" border="0">
    <tr>
      <td align="center">

      <input name="delete"  type="submit" class="btn1" value="DELETE AND CANCEL REQUSITION" style="width:270px; font-weight:bold; font-size:12px;color:#F00; height:30px" />

      </td>
      <td align="center">

      <input name="confirmm" type="submit" class="btn1" value="CONFIRM AND FORWARD REQUSITION" style="width:280px; font-weight:bold; font-size:12px; height:30px; color:#090" />

      </td>
    </tr>
  </table>
</form>
<? }?>
</div>
<script>$("#codz").validate();$("#cloud").validate();</script>

<?php */?>


<?

require_once "../../../assets/template/layout.bottom.php";

?>