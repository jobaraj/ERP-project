<?php

require_once "../../../assets/template/layout.top.php";



$title='Approving Purchase Order';



do_calander('#po_date');

do_calander('#invoice_date');

do_calander('#quotation_date');



$table_master='purchase_master';

$table_details='purchase_invoice';

$unique='po_no';

if(isset($_REQUEST['po_no']))
{
$$unique=$_SESSION[$unique]=$_REQUEST['po_no'];
}



if(isset($_POST['new']))

{

		$crud   = new crud($table_master);



		if(!isset($_SESSION[$unique])) {

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d h:s:i');

		$$unique=$_SESSION[$unique]=$crud->insert();

		unset($$unique);

		$type=1;

		$msg='Requisition No Created. (PO No :-'.$_SESSION[$unique].')';

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
		$_POST['checked_by']=$_SESSION['user']['id'];
			$_POST['lm_approve']=$_SESSION['user']['id'];
			$_POST['lm_approve_at']=date('Y-m-d h:s:i');
		$_POST['checked_at']=date('Y-m-d h:s:i');
		$_POST['status']='CHECKED';
			$tot_amount=find_a_field('purchase_invoice','sum(amount)','po_no='.$$unique);
			
		if($tot_amount<=50000){
		$_POST['complete_approve']=1;
		}
		$crud   = new crud($table_master);
		$crud->update($unique);
		//auto_insert_purchase_secoundary_update_packing($$unique);
		unset($$unique);
		unset($_SESSION[$unique]);
		$type=1;
		header('location:select_unapproved_po.php');

}


if(isset($_POST['cancel']))

{

		unset($_POST);
		 $_POST[$unique]=$$unique;
		$_POST['checked_by']=$_SESSION['user']['id'];
		$_POST['checked_at']=date('Y-m-d h:s:i');
		$_POST['status']='CANCELED';
		$crud   = new crud($table_master);
		$crud->update($unique);
		//auto_insert_purchase_secoundary_update_packing($$unique);
		unset($$unique);
		unset($_SESSION[$unique]);
		$type=1;
		header('location:select_unapproved_po.php');

}

if(isset($_POST['return_remarks'])){
        
		
		$remarks = $_POST['return_remarks'];
        unset($_POST);

		$_POST[$unique]=$$unique;

		$_POST['checked_by']=$_SESSION['user']['id'];

		$_POST['checked_at']=date('Y-m-d H:i:s');

		$_POST['status']='MANUAL';

		$crud   = new crud($table_master);

		$crud->update($unique);
		
		
		$note_sql = 'insert into approver_notes(`master_id`,`type`,`note`,`entry_at`,`entry_by`) value("'.$$unique.'","PO","'.$remarks.'","'.date('Y-m-d H:i:s').'","'.$_SESSION['user']['id'].'")';
		mysql_query($note_sql);

		unset($$unique);

		unset($_SESSION[$unique]);

		$type=1;
        
		
		
		header('location:select_unapproved_po.php');


}

if(isset($_POST['add'])&&($_POST[$unique]>0))

{

		$crud   = new crud($table_details);

		$iii=explode('#>',$_POST['item_id']);

		$_POST['item_id']=$iii[1];


		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d h:s:i');

		$crud->insert();

		unset($item_id);

}



if($$unique>0)

{

		$condition=$unique."=".$$unique;

		$data=db_fetch_object($table_master,$condition);

		while (list($key, $value)=each($data))

		{ $$key=$value;}

		

}

if($$unique>0) $btn_name='Update PO Information'; else $btn_name='Initiate PO Information';

if($_SESSION[$unique]<1)

$$unique=db_last_insert_id($table_master,$unique);





auto_complete_from_db('item_info','item_name','concat(finish_goods_code,"-",item_name,"#>",item_id)','1 ','item_id');


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
</script>


<style type="text/css">



/*.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited, a.ui-button, a:link.ui-button, a:visited.ui-button, .ui-button {
    color: #454545;
    text-decoration: none;
    display: none;
}*/


/*div.form-container_large input {
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
.style4 {font-weight: bold}*/

</style>


<div class="form-container_large">
    <form action="mr_create.php" method="post" name="codz" id="codz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
<!--        top form start hear-->
        <div class="container-fluid bg-form-titel">
            <div class="row">
                <!--left form-->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="container n-form2">
                        <div class="form-group row m-0 pb-1">
						   <? $field='po_no';?>
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PO No : </label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
               

      						  <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly/>
					
   
                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">
							<? $field='po_date'; if($po_date=='') $po_date =date('Y-m-d');?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">PO Date :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                           

       						 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" required/>

  
                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">
						
                            <label  for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Company :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                            
								<select id="group_for" name="group_for"  >
						
						
							 		 <? foreign_relation('user_group','id','group_name',$group_for,'1');?>
						
								</select>


                            </div>
                        </div>
						<div class="form-group row m-0 pb-1">
								   <? $field='remarks';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Remarks :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                       
         					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>

                            </div>
                        </div>
						
						<div class="form-group row m-0 pb-1">
								   <? $field='quotation_no';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Quotation :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                       
         					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>

                            </div>
                        </div>
						
						<div class="form-group row m-0 pb-1">
							<td>
								<td width="20" align="right">CS Report : <a href="../quotation/quotations_comparison_print_view.php?req_no=<?php echo $req_no; ?> "target="_blank"><strong>CS</strong></a></td>
							<td>
						</div>

                    </div>



                </div>

                <!--Right form-->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="container n-form2">
                        <div class="form-group row m-0 pb-1">
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vendor Name :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
                                
								
										<? if($vendor_id<1) { ?>
										<select name="vendor_id_draft" id="vendor_id_draft" required   >
									
										<option></option>						
										<? foreign_relation('vendor','vendor_id','vendor_name',$vendor_id_draft,'1');?>
										</select>
										
										<? }?>
										
										<? if($vendor_id>0) { ?>
										
										 <input   name="vendor_id" type="hidden" id="vendor_id" value="<?=$vendor_id;?>" readonly=""  required/>
										
										 <input   name="vendor_view" type="text" id="vendor_view" value="<?=find_a_field('vendor','vendor_name','vendor_id="'.$vendor_id.'"');?>" readonly=""  required/>
										
										<? }?>
                            </div>
                        </div>
						
						<div class="form-group row m-0 pb-1">
									<? $field='tax';?>
                            <label for="<?=$field?>"   class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Vat(%) :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
              
							<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />
  

                            </div>
                        </div>
                        
						<div class="form-group row m-0 pb-1">
																	
									<? $field='ait';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">AIT(%) :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                          
								<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"  />

                            </div>
                        </div>
						
						

                        <div class="form-group row m-0 pb-1">
								  <? $field='warehouse_id'; $table='warehouse';$get_field='warehouse_id';$show_field='warehouse_name';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Warehouse :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                 

							<select id="<?=$field?>" name="<?=$field?>" required >
								
										<option></option>
								
										<? foreign_relation($table,$get_field,$show_field,$$field,'1');?>
					
							</select>

								  

                            </div>
                        </div>
						
						<div class="form-group row m-0 pb-1">
								   <? $field='deli_point';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Delivery Point :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                       
         					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>"/>

                            </div>
                        </div>
						
						
						<div class="form-group row m-0 pb-1">
																	
									<? $field='quotation';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Attached :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                          <? $quotation=find_a_field('purchase_master','quotation_no','po_no="'.$$unique.'"');
						     $att = find_a_field('quotation_master','quotation','quotation_no="'.$quotation.'"');
							 
						   if ( $att!=''){ $ext = explode(".", $att);?>
							<a href="../../../assets/support/upload_view.php?name=<?=$att?>&folder=purchase_quotation&ext=<?=$ext[1]?>" target="_blank">View Attachment</a>
							<?php } ?>
                            </div>
                        </div>
						
						

                    </div>



                </div>


            </div>

           
        </div>

      
       <!--<div class="container-fluid pt-5 p-0 ">
            <table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">
                    <th>Returned By</th>
                    <th>Returned At</th>
                    <th>Remarks</th>
                </tr>
                </thead>

                <tbody class="tbody1">

                <tr>
                    <td>Row name 1</td>
                    <td>Row name 2</td>
                    <td>Row name 3</td>

                </tr>

                </tbody>
            </table>

        </div>-->
    </form>



<br /><br />

<? if($_SESSION[$unique]>0){?>

<div class="d-flex justify-content-center">
    <table  border="1" align="center" cellpadding="0" cellspacing="0" >

  <tr>

    <td colspan="3" align="center"><strong>Entry Information</strong></td>

    </tr>

  <tr>

    <td align="right" > Entry By:</td>

    <td align="left" >&nbsp;&nbsp;<?=find_a_field('user_activity_management','fname','user_id='.$entry_by);?></td>

    <td rowspan="2" align="center" ><a href="po_print_view.php?po_no=<?=$po_no?>" target="_blank"><img src="../../../images/print.png"  /></a></td>

  </tr>

  <tr>

    <td align="right" >Entry On:</td>

    <td align="left" >&nbsp;&nbsp;<?=$edit_at?></td>

    </tr>

</table>
	
</div>





<br />




    <form action="" method="post" name="cloud" id="cloud">
        <!--Table input one design-->
        <div class="container-fluid pt-5 p-0 ">


            <table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">
                    <th>SL</th>
					<th>Item Code</th>
                    <th>Item Name</th>
                  	<th>Unit</th>
					<th>Price</th>
                    <th>Total Unit</th>
                    <th>Amount</th>
					<th>Special Note</th>
					<th>Schedule</th>
					<th>LPR</th>
					<th>LPD</th>
                    <th>X</th>

                    
                </tr>
                </thead>

                <tbody class="tbody1">
				
				
				<?
					$res='select a.id,a.po_no, b.finish_goods_code as item_code, concat(b.item_name) as item_name,b.unit_name as unit,b.item_id,a.rate,a.qty as total_unit, a.amount,  a.special,a.delivery_schedule,"x" from purchase_invoice a,item_info b where b.item_id=a.item_id and a.po_no='.$po_no;
					
					$query=mysql_query($res);
					
					while($data=mysql_fetch_object($query)){
				?>

                <tr>
                    <td><?=++$s?></td>
					 <td><?=$data->item_code?></td>
					 <td><?=$data->item_name?></td>
                    <td><?=$data->unit?></td>
					<td><?=$data->rate?></td>
                    <td><?=$data->total_unit; $unit_total+=$data->total_unit;?> </td>

                    <td>
							<?=$data->amount; $total_amt=$total_amt+$data->amount;?>
							
				    </td>
					
					<td><?=$data->special?></td>
					
					<td><?=$data->delivery_schedule?></td>
					<td><?=find_a_field('purchase_invoice','rate','item_id="'.$data->item_id.'" and po_no != "'.$data->po_no.'" order by id desc ');?></td>
                   <td><?=find_a_field('purchase_invoice','po_date','item_id="'.$data->item_id.'" and po_no != "'.$data->po_no.'"  order by id desc ');?></td>
                   
					<td>
						<a href="?del=<?=$data->id?>">X</a>
					</td>
             
					

                </tr>
					<? }?>
					
				
					
                </tbody>
					<tr>
						<td><strong>Total</strong></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><strong><?=number_format($unit_total,2);?></strong></td>
						<td><strong><?=number_format($total_amt,2);?></strong></td>
						<td></td>
						
					
					
					</tr>
            </table>





        </div>


        
    </form>

    <!--button design start-->
    <form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">



			<div class="n-form-btn-class">
                 
				   <input name="return"  type="submit" class="btn1 btn1-bg-update" value="RETURN TO USER" onclick="return_function()" />
				   <input  name="po_no" type="hidden" id="po_no" value="<?=$po_no?>"/><input type="hidden" name="return_remarks" id="return_remarks">
				 
				   <input name="cancel"  type="submit" class="btn1 btn1-bg-cancel" value="CANCEL"  />
				
				  <td><input type="button" class="btn1 btn1-bg-cancel" value="CLOSE"  onclick="window.location.href='select_unapproved_po.php'" /></td>
			
				  <input name="confirmm" type="submit" class="btn1 btn1-bg-submit" value="CONFIRM AND FORWARD PO"  />
				
		
				
				
            </div>
		  
</form>
<? } ?>
</div>








<script>$("#codz").validate();$("#cloud").validate();</script>
<script>
function return_function() {
  var notes = prompt("Why Return This PO?","");
  if (notes!=null) {
    document.getElementById("return_remarks").value =notes;
	document.getElementById("cz").submit();
  }
  return false;
}
</script>

<?

$main_content=ob_get_contents();

ob_end_clean();

include ("../../template/main_layout.php");

?>