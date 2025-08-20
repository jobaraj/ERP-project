<?php



require_once "../../../assets/template/layout.top.php";



$title='New Material Requisition Create';







do_calander('#exp_date');



do_calander('#req_date');








$table_master='master_requisition_master';



$table_details='master_requisition_details';



$unique='req_no';



if($_GET['mhafuz']>0){



	unset($_SESSION[$unique]); 

}

	









if(isset($_POST['new'])){



   $_SESSION[$unique];



 if(($_POST['pending_date']=='') ){







	if($_POST['manual_req_no']!= ''){



	$crud   = new crud($table_master); 



	if($_SESSION[$unique] =='') { 

	

		$_POST['rec_type']=$_SESSION['rec_type'];



		$_POST['entry_by']=$_SESSION['user']['id'];



		$_POST['entry_at']=date('Y-m-d H:i:s');



		$_POST['edit_by']=$_SESSION['user']['id'];



		$_POST['req_date']=date('Y-m-d');



		$_POST['edit_at']=date('Y-m-d H:i:s');



		$$unique=$_SESSION[$unique]=$crud->insert();



		unset($$unique);



		$type=1;



		$msg='Requisition No Created. (Req No :-'.$_SESSION[$unique].')';

		

		// Details insert start

		

	 $sql1="select b.* from production_requisition_order b,production_line_fg f where f.fg_item_id=b.item_id and f.line_id = '".$_POST['req_for']."' and b.req_no = '".$_POST['do_number']."'";

							   $data1=mysql_query($sql1);

							   while($info=mysql_fetch_object($data1)){

							   

							    $sql = 'select i.*,r.*,s.sub_group_name, sum(r.total_unit) as ba_qty, sum(m.quantity) as uba_size from item_info i, bom_raw_material r, item_sub_group s, bom_master m where m.bom_no=r.bom_no and i.sub_group_id=s.sub_group_id and i.item_id =r.item_id and r.fg_item_id="'.$info->item_id.'" and i.sub_group_id not in (50000000) group by r.item_id order by s.sub_group_name,i.item_id';

								$query = mysql_query($sql);

								while($data = mysql_fetch_object($query)){

									 $data->item_id;

									 $formula_qty = ($data->ba_qty/$data->uba_size); 

 									 $fg_pcs = find_a_field('item_info','pack_size','item_id='.$info->item_id);

 									 $total_fg_batch_qty = $info->qty;

									

									

 									$raw_qqty_u = $total_fg_batch_qty*$formula_qty;

									

									if($data->ratio>0){

									  

										$req_qty_1 = $raw_qqty_u/$data->ratio;

									}else{

										$req_qty_1 = $raw_qqty_u;

									}

									

									

										$req_qty = $req_qty_1;

									

									

 									if($req_qty>0){

									  $crud   = new crud($table_details);



										echo 't'.$_POST['item_id']=$data->item_id;

										//$_POST['req_no']=$$unique;

										//$_POST['warehouse_id']=$$unique;

										//$_POST['req_date']=$$unique;

										$last_p = find_all_field('purchase_invoice','','item_id="'.$data->item_id.'" order by id desc');

										

										$_POST['order_qty']=$req_qty;

										$_POST['unit_name']=$data->unit_name;

										$_POST['exp_date']=$_POST['req_date'];

										

										$_POST['rate'] = find_a_field('journal_item','final_price',' final_price > 0 and item_id="'.$data->item_id.'" and tr_from in ("Purchase","Local Purchase","Production Receive","Opening") order by id desc');

										

										$_POST['qoh']=$stock=find_a_field('journal_item','sum(item_in-item_ex)','warehouse_id='.$_POST['req_for'].' and item_id='.$data->item_id.' ');;

										$_POST['qty']=$req_qty;

										



										$_POST['entry_by']=$_SESSION['user']['id'];



										$_POST['entry_at']=date('Y-m-d H:i:s');



										$_POST['edit_by']=$_SESSION['user']['id'];



										$_POST['edit_at']=date('Y-m-d H:i:s');



										$crud->insert();

									}

							    }

							   }	

							   

	// Details insert end	









	}else {



		$_POST['edit_by']=$_SESSION['user']['id'];



		$_POST['edit_at']=date('Y-m-d H:i:s');



		$crud->update($unique);



		$type=1;



		$msg='Successfully Updated.';



	}



	}else{



		$message = "Please select PL Again For Manual Req No.";



			echo "<script type='text/javascript'>alert('$message');</script>";



	}



	



	



	}



	else {



		echo '<script> alert(" Please Receive your Product First.");



			window.location = "mr_create.php";



		</script>';



		



		



	}



}







$$unique=$_SESSION[$unique];



if(isset($_POST['delete'])){



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







if($_GET['del']>0){







		$crud   = new crud($table_details);











		$condition="id=".$_GET['del'];		











		$crud->delete_all($condition);











		$type=1;











		$msg='Successfully Deleted.';











}











if(isset($_POST['confirmm'])){



		// Details insert start

			 $res='select a.id,a.item_id,concat(b.item_name) as item_name,a.qoh as Floor_qty,a.exp_date,a.remarks,a.qty,"x" from master_requisition_details a,item_info b where b.item_id=a.item_id and  a.req_no='.$$unique;

 			  $q=mysql_query($res);

			  

			  while($r=mysql_fetch_object($q)){

			    if($_POST['qty_'.$r->id]>0){

					$update = "update master_requisition_details set qty='".$_POST['qty_'.$r->id]."', order_qty = '".$_POST['qty_'.$r->id]."' where id='".$r->id."' ";

					mysql_query($update);

				}else{

					$delete = "delete from master_requisition_details where id='".$r->id."' ";

					mysql_query($delete);

				}

			  }

							   

		// Details insert end	



		unset($_POST);



		$_POST[$unique]=$$unique;



		$_POST['entry_by']=$_SESSION['user']['id'];



		$_POST['entry_at']=date('Y-m-d H:i:s');



		$_POST['status']='PENDING';



		$crud   = new crud($table_master);



		$crud->update($unique);



		unset($$unique);



		unset($_SESSION[$unique]);



		$type=1;



		$msg='Successfully Forwarded for Approval.';

		unset($_POST);

		

		echo '<script> window.location.href = "mr_create.php"; </script>';



}



















$req =$_POST['req_for'];



if(prevent_multi_submit()){



if(isset($_POST['add'])&&($_POST[$unique]>0)){



	



//	if($_POST['rate'] != 0){

	

		



	$crud   = new crud($table_details);



	 $_POST['qty']=$_POST['order_qty'];

		if ($_POST['order_qty'] <= $_POST['cwh_qoh'] ) {



	$_POST['req_for'] = $req;



	$iii=explode('#>',$_POST['item_id']);



	 $_POST['item_id']=$iii[2];



	$_POST['remarks'];



	$_POST['entry_by']=$_SESSION['user']['id'];



	$_POST['entry_at']=date('Y-m-d H:i:s');



	$_POST['edit_by']=$_SESSION['user']['id'];



	$_POST['edit_at']=date('Y-m-d H:i:s');



	$crud->insert();

	}

	

	else {

		 echo '<script>alert("Qty Is More Than Central Warehouse")</script>';

	}



	//}



	


/*
	else {



	 echo '<script>alert("You can not add a Zero value item.")</script>';



	}*/



	}



}







if($$unique>0){



		$condition=$unique."=".$$unique;



		$data=db_fetch_object($table_master,$condition);



		while (list($key, $value)=each($data))



		{ $$key=$value;}



	}











if($$unique>0) $btn_name='Update Requisition Information'; else $btn_name='Initiate Requisition Information';











if($_SESSION[$unique]<1)











$$unique=db_last_insert_id($table_master,$unique);























//auto_complete_from_db($table,$show,$id,$con,$text_field_id);







if($_SESSION['session_sub_group']=='all'){



	$get_data = '';



}else{



	$get_data =$_SESSION['session_sub_group'];



}



















if($req_type=='DSTR'){ $con .= ' and s.status=0 and s.ledger_id_1 !=0';}



if($req_type=='Others'){ $con .= ' and (s.status=1 or s.ledger_id_1 =0) and s.group_id !=1100000000';}



if($req_type=='Asset'){ $con .= ' and s.group_id=1100000000';}



//auto_complete_from_db('item_info i,item_sub_group s','i.item_name','concat(i.item_name,"#>",i.item_id,"#>",i.item_id)',' 1 and i.sub_group_id=s.sub_group_id  '.$con.' ','item_id');











?>

<script language="javascript">



function sub_group_function(id){



	document.getElementById('sub_group_id').value=id;



	window.location.href = "../mr/mr_create.php?sub_group=" + id;



}



</script>









<div class="form-container_large">

	<form action="mr_create.php" method="post" name="codz" id="codz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">

      <div class="container-fluid bg-form-titel">

        <div class="row">

          <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">

		  <div class="container n-form2">



            <div class="form-group pb-1 row m-0">

			 <? $field='req_no';?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition No :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

                <input  name="<?=$field?>" type="text" id="<?=$field?>" style="margin-left: 11px;" value="<?=$$field?>"/>

              </div>

            </div>

			 

			 <div class="form-group pb-1 row m-0">

						 <? $field='req_date'; if($req_date=='') $req_date =date('Y-m-d');?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition Date :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

        			<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" style=""/>

              </div>

            </div>

			

			<?php /*?><div class="form-group pb-1 row m-0">

				<? $field='do_number';?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PP No :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

				<select name="do_number">

					<option></option>

					<? foreign_relation('production_requisition_master','req_no','req_no',$do_number,'status="CHECKED" ');?>

				</select>

		

              </div>

            </div><?php */?>

			 

			 <div class="form-group pb-1 row m-0">

			    <? $field='req_for'; $table='warehouse';$get_field='warehouse_id';$show_field='warehouse_name';$warehouse_id='"'.$_SESSION['user']['depot'].'"';?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition For :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

		<input name="req_for" type="hidden" id="req_for" value="<?=$_SESSION['user']['depot']?>" />

		<input name="req_for2" style="margin-left: 34px;" type="text" 
		id="req_for2" value="<?=find_a_field('warehouse','warehouse_name','warehouse_id="'.$_SESSION['user']['depot'].'"')?>" readonly />

              </div>

            </div>

</div>

          </div>

          <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">

		  <div class="container n-form2">

		      <? $field='warehouse_id';?>

            <div class="form-group pb-1 row m-0">

					<? $field = 'warehouse_id' ?>

              <label  for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition To :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

					<select id="<?=$field?>" style="margin-left: 12px;" name="<?=$field?>"  onchange="getData2('manual_req_no_ajax.php', 'manual_req_no', this.value, document.getElementById('req_no').value);" required  >

					

					<? 

					

					if($_POST['warehouse_id']>0)

					

					foreign_relation('warehouse','warehouse_id','warehouse_name',$_POST['req_for'],' use_type="PL"');

					

					elseif($warehouse_id>0)

					

					foreign_relation('warehouse','warehouse_id','warehouse_name',$req_for,' use_type="PL"');

					

					else{

					

					echo '<option></option>';

					

					foreign_relation('warehouse','warehouse_id','warehouse_name',$req_for,' use_type="PL"');

					

					}

					

					?>

					</select>

              </div>

            </div>

            <div class="form-group pb-1 row m-0">

			<? $field='manual_req_no';?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Line Req No :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

					<span id="manual_req_no">

							<input  name="<?=$field?>" style="margin-left: 32px;" type="text" id="<?=$field?>" value="<?=$$field?>"/>

							<!--<input  name="pending_date" type="text" id="pending_date"   value="<?=$pending_date?>"/>-->

					</span>

              </div>

            </div>

            <div class="form-group pb-1 row m-0">

			  <? $field='req_note';?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Additional Note :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">

       					 <input  name="<?=$field?>" style="margin-left: 10px;" type="text" id="<?=$field?>" value="<?=$$field?>"/>

              </div>

            </div>

            <div class="form-group pb-1 row m-0">

                <? $field='req_type';?>

              <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition Type :</label>

              <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">	

			<select name="<?=$field?>" id="<?=$field?>">	

			 <option <? if ($$field=='FG') echo ' selected="selected"';?>>FG </option>	

			 <!--<option <? if ($$field=='Others') echo ' selected="selected"';?>>Others</option>	

			 <option <? if ($$field=='Asset') echo ' selected="selected"';?>>Asset</option>-->	

			</select>

			

              </div>

            </div>

          </div>

		</div>

        </div>

		

		<div class="n-form-btn-class">

     			 <input name="new" type="submit" value="<?=$btn_name?>"  class="btn1 btn1-bg-submit"/>

		</div>

				

      </div>



</form>



<? if($_SESSION[$unique]>0){?>



<form action="?<?=$unique?>=<?=$$unique?>" method="post" name="cloud" id="cloud">

<div class="container-fluid pt-5 p-0">

       



<table  class="table1  table-striped table-bordered table-hover table-sm">

          <thead class="thead1">

                 <tr class="bgc-info">

						<td width="17%" align="center">

						<strong>Sub Group & Item Name</strong></td>

                        <td width="10%" align="center"><strong>Delivery Date</strong></td>

                        <td width="11%" align="center"><strong>CWH Qty</strong></td>

                        <td width="9%" align="center"><strong>Floor Qty</strong></td>

                        <td width="11%" align="center"><strong>Unit</strong></td>

                        <td width="8%" align="center"><strong>Req Qty</strong></td>

                       <? if($req_type=='Others'){?> <td width="15%" align="center" bgcolor="#0099FF">Purpose</td><? }?>

                        <td width="9%" align="center"><strong>Remark</strong></td>

                        <td width="10%"  rowspan="2" align="center">

						<strong>Action</strong>

						</td>

				  </tr>

				  </thead>

          <tbody class="tbody1">



                      <tr>

					<td align="center">

                        	<!--<select name="sub_group" id="sub_group" style="width:170px;" onChange="sub_group_function(this.value)">

                            	<option></option>



								<?php



								if($_SESSION['session_sub_group']=='all'){



									echo '<option value="all" selected>All</option>';



								}else{



									echo '<option value="all">All</option>';



								}



								



								if($req_type=='DSTR'){ $con1 .= '  status !=1 and ledger_id_1 !=0';}



								if($req_type=='Others'){ $con1 .= '  (status=1 or ledger_id_1 =0) and group_id !=1100000000';}



								if($req_type=='Asset'){ $con1 .= '  group_id=1100000000';}



								



                          $a2="select sub_group_id, sub_group_name from item_sub_group where ".$con1."";



                                echo $a2;



                                $a1=mysql_query($a2);



                                



                                while($a=mysql_fetch_row($a1)){



                                if($a[0]==$_SESSION['session_sub_group'])



                                	echo "<option value=\"".$a[0]."\" selected>".$a[1]."</option>";



                                else



                                	echo "<option value=\"".$a[0]."\">".$a[1]."</option>";



                                }



							?></select>-->             



							



							<input  name="<?=$unique?>"i="i" type="hidden" id="<?=$unique?>" value="<?=$$unique?>"/>



                            <input  name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$req_for?>"/>



                            <input  name="sub_group_id" type="hidden" id="sub_group_id" value="<?=$sub_group_id?>"/>



                            <input  name="req_for" type="hidden" id="req_for" value="<?=$warehouse_id?>"/>

                            <input  name="item_id" type="text" list="item_ids" id="item_id" value="<? echo $item_id; ?>" style="width:170px;" required="required" onblur="getData2('mr_ajax.php', 'mr', this.value, document.getElementById('req_for').value);" autocomplete="off"/>

                            <datalist id="item_ids">

 <? //i.sub_group_id like "%'.$get_data.'%"

  foreign_relation('item_info i,item_sub_group s','concat(i.item_name,"#>",i.item_id,"#>",i.item_id)','i.item_name',$item_id,'i.sub_group_id=s.sub_group_id and s.group_id  in ("200000000")');?>

	</datalist>			            </td>




                       



						<td align="center">	



                        	<? $field="exp_date"; ?>



                        	<input  name="<?=$field?>" type="text" id="<?=$field?>" value=" <? $field='req_date'; if($req_date=='') echo $req_date = date('Y-m-d'); else echo $req_date;?>" style="width:120px;" required/>  </td>



						



						 



<td colspan="3" align="center"><span id="mr">



<table width="100%" border="0" cellspacing="0" cellpadding="0">



  <tr>



  



  



  	<td><input name="cwh_qoh" type="text" class="input3" id="cwh_qoh" style="width:120px;" onFocus="focuson('qty')" readonly/></td>



    <td><input name="qoh" type="text" class="input3" id="qoh" style="width:120px;" onFocus="focuson('qty')" readonly/></td>



    <td><input name="unit_name" type="text" class="input3" id="unit_name"  maxlength="100" style="width:120px;" onFocus="focuson('qty')" readonly/></td>



	 <td><input name="rate" type="hidden" class="input3" id="rate"  maxlength="100" style="width:120px;" onFocus="focuson('qty')" readonly/></td>

  </tr>

</table>





</span></td>







<td align="center"><input name="order_qty" type="number" step="any" class="input3" id="order_qty"  maxlength="100" style="width:120px;" required/></td>







						 



                         <td align="center">

                           <input name="remarks" type="text" id="remarks" style="width:80px;">

						 </td>

						 <td>

						  <input name="add" type="submit" id="add" value="ADD" tabindex="12" class="btn1 p-2 btn1-bg-submit"/>                       



						</td>

      </tr>

	  </tbody>

    </table>

	</div>

</form>





	<form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">

      <div class="container-fluid pt-5 p-0">

<? 

$res='select a.id,a.item_id,concat(b.item_name) as item_name,a.qoh as Floor_qty,a.exp_date,a.remarks,b.sub_group_id,a.qty,"x" from master_requisition_details a,item_info b where b.item_id=a.item_id and  a.req_no='.$req_no.' order by a.item_id';

	 

	 $found = 0;

 

 $query = mysql_query($res);





?>



<? 

//$res='select * from tbl_receipt_details where rec_no='.$str.' limit 5';

//echo link_report_del($res); ?>

		

		<table id="grp" class="table1  table-striped table-bordered table-hover table-sm">

          <thead class="thead1">

		<tr class="bgc-info">

			<th>Item Name</th>

			<th>Floor Qty</th>

			<th>Date</th>

			<th>Remarks</th>

			<th>Required Qty</th>

			<th>X</th>

		</tr>

		</thead>

	<tbody class="tbody1">

	<?

		 while($roww=mysql_fetch_object($query)){

	?>	

		<tr>

			<td><?=$roww->item_name;?></td>

			<td><?=$roww->Floor_qty;?></td>

			<td><?=$roww->exp_date;?></td>

			<td><?=$roww->remarks;?></td>

			<td><input type="text" name="qty_<?=$roww->id;?>" id="qty_<?=$roww->id;?>" value="<?=$roww->qty;?>" /> 

			<input type="hidden" name="details_id" value="<?=$roww->id;?>" /></td>

			<td><a onclick="if(!confirm('Are You Sure Execute this?')){return false;}" href="?del=<?=$roww->id;?>">&nbsp;X&nbsp;</a></td>

		</tr>

<?

 }

?>

</tbody>

</table>

	  

	  <div class="n-form-btn-class">

<!--<input name="delete"  type="submit" value="DELETE AND CANCEL REQUISITION" class="btn1 btn1-bg-cancel" />-->

			 <?php $detai=find_a_field('master_requisition_details','count(id)','req_no='.$req_no); if($detai>0){?>

				  <input name="confirmm" type="submit" value="CONFIRM AND FORWARD REQUISITION" class="btn1 btn1-bg-submit"/>

			

			<?php }?>

		</div>

      </div>

</form>



	

<? }?>

  </div>

  





<script>$("#codz").validate();$("#cloud").validate();</script>

<?

$main_content=ob_get_contents();

ob_end_clean();

include ("../../template/main_layout.php");





?>