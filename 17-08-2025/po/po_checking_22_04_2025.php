<?php
require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";



$title='Approving Purchase Order';

$module_name = find_a_field('user_module_manage','module_file','id='.$_SESSION["mod"]);

do_calander('#po_date');

do_calander('#invoice_date');

do_calander('#quotation_date');



$table_master='purchase_master';
$table_history='approval_matrix_history';

$table_details='purchase_invoice';

$unique='po_no';



$all=find_all_field('purchase_master','','po_no="'.url_decode($_GET['po_no']).'"');

// $next_level=find_a_field('approval_matrix_setup','MAX(level)','level<"'.$all->current_approval_level.'" and tr_from="Purchase" and status="ACTIVE"');














if(isset($_REQUEST['po_no']) && $_SESSION[$unique]<1)
{

$$unique=$_SESSION[$unique]=url_decode($_REQUEST['po_no']);
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



$$unique=$_SESSION[$unique]=url_decode($_REQUEST['po_no']);



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
  $$unique=$_POST['po_no'];
  $entry_time=$_POST['entry_time'];
  $next_level_avaliable=approval_confirmation_checking($$unique,'Purchase',$entry_time);
  //die();
 //unset($_POST);
    if($next_level_avaliable>0){
      $_POST[$unique]=$$unique; 
      $crud   = new crud($table_master);
      $crud->update($unique);
	  
    }else{
      $_POST[$unique]=$$unique;
      $_POST['checked_by']=$_SESSION['user']['id'];
      $_POST['checked_at']= $entry_time;
      $_POST['status']='CHECKED';
      $crud   = new crud($table_master);
      $crud->update($unique);
    }
    
		//auto_insert_purchase_secoundary_update_packing($$unique);
		unset($$unique);
		unset($_SESSION[$unique]);
		$type=1;
		header('location:select_unapproved_po.php');

}


if(isset($_POST['cancel_remarks']) && $_POST['cancel_remarks'] !='')

{

  $cancel_remarks=$_POST['cancel_remarks'];
		unset($_POST);
		 $_POST[$unique]=$$unique;
		$_POST['checked_by']=$_SESSION['user']['id'];
		$_POST['checked_at']=date('Y-m-d h:s:i');
		$_POST['current_approval_level']=0;
		$_POST['status']='CANCELED';
		$crud   = new crud($table_master);
		$crud->update($unique);


      //insert into approval history
      $_POST['tr_no']=$$unique;
      $_POST['tr_from']='Purchase';
      $_POST['approved_by']=$_SESSION['user']['id'];
      $_POST['approved_at']=date('Y-m-d h:s:i');
      $_POST['approval_level']=$all->current_approval_level;
      $_POST['remarks']="CANCELED";
      $_POST['notes']=$cancel_remarks;

        
  
        $crud_history  = new crud($table_history);
        $crud_history->insert();


		//auto_insert_purchase_secoundary_update_packing($$unique);
		unset($$unique);
		unset($_SESSION[$unique]);
		$type=1;
		header('location:select_unapproved_po.php');

}

if(isset($_POST['return_remarks']) && $_POST['return_remarks'] !=''){
  $entry_time=$_POST['entry_time'];
		
		$remarks = $_POST['return_remarks'];
        unset($_POST);

		$_POST[$unique]=$$unique;

		$_POST['checked_by']=$_SESSION['user']['id'];

		$_POST['checked_at']= $entry_time;

		$_POST['status']='MANUAL';

		$crud   = new crud($table_master);

		$crud->update($unique);

    approval_return_checking($$unique,'Purchase', $entry_time);
      
		
		
		$note_sql = 'insert into approver_notes(`master_id`,`type`,`note`,`entry_at`,`entry_by`) value("'.$$unique.'","PO","'.$remarks.'","'.date('Y-m-d H:i:s').'","'.$_SESSION['user']['id'].'")';
		db_query($note_sql);

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

		foreach ($data as $key => $value)

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
                           

       						 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly="" required/>

  
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
                       
         					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly=""/>

                            </div>
                        </div>
						
						
						<div class="form-group row m-0 pb-1">
								   <? $field='req_no';?>
                            <label for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition :</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                       
         					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly=""/>

                            </div>
                        </div>
						
						
                        <div class="form-group row m-0 pb-1">

<? $field='file_upload';?>

               <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">File Attachment:</label>

               <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

       <input  name="<?=$field?>" type="hidden" id="<?=$field?>"  value="<?=$$field?>" class="form-control"/>
       <a href="<?=SERVER_CORE?>core/upload_view.php?name=<?=$$field?>&folder=purchase&proj_id=<?=$_SESSION['proj_id']?>" target="_blank">View Attachment</a>
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

             

    <select id="<?=$field?>" name="<?=$field?>" value="<?=$_POST['purchase_type']?>" required style="width:220px;"    >

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

             

   <?php /*?> <select id="<?= $field ?>" name="<?= $field ?>" required style="width:220px;">
        <option value="" <?= empty($$field) ? 'selected' : '' ?>></option>
        <option value="Yes" <?= ($$field == 'Yes') ? 'selected' : '' ?>>Yes</option>
        <option value="No" <?= ($$field == 'No') ? 'selected' : '' ?>>No</option>
    </select><?php */?>
	
	
	<select name="<?=$field?>"  id="<?=$field?>" <? if(!empty($$field)){ echo'disabled';} ?> >
                    <option value="No" <?= $$field == 'No' ? 'selected' : '' ?>>No</option>
                    <option value="Yes" <?= $$field == 'Yes' ? 'selected' : '' ?>>Yes</option>
            </select>



    </div>

</div>


<div class="form-group row m-0 pb-1">

            <? $field='rebate_percentage';?>

    <label  for="<?=$field?>"  class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Rebateable(%)</label>

    <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">

        

        <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" readonly=""  />



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

             

    <select id="<?=$field?>" name="<?=$field?>" required style="width:220px;" <? if(!empty($$field)){ echo'disabled';} ?>    >

        <option></option>

        <? foreign_relation($table,$get_field,$show_field,$$field,'1 and group_for="'.$_SESSION['user']['group'].'"');?>

            

    </select>



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

    <td rowspan="2" align="center" ><a href="po_print_view.php?po_no=<?=url_encode($po_no)?>" target="_blank"><img src="../../../../public/assets/images/print.png"  style=" width: 50px; "/></a></td>

  </tr>

  <tr>

    <td align="right" >Entry On:</td>

    <td align="left" >&nbsp;&nbsp;<?=$entry_at?></td>

    </tr>

</table>
	
</div>





<br />




    <form action="" method="post" name="cloud" id="cloud">
        <!--Table input one design-->
        <div class="container-fluid pt-5 p-0 ">
<? if($all->purchase_type !=1){ ?>

            <table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">
                    <th>SL</th>
					<th>Item Code</th>
                    <th>Item Name</th>
                  	<th>Unit</th>
					<th>Rate</th>
                    <th>Total Unit</th>
                    <th>Amount</th>
                    <!--<th>X</th>-->

                    
                </tr>
                </thead>

                <tbody class="tbody1">
				
				
				<?  
					$res='select a.id, b.finish_goods_code as item_code, concat(b.item_name) as item_name,b.unit_name as unit,a.rate,a.qty as total_unit, a.amount,"x" from purchase_invoice a,item_info b where b.item_id=a.item_id and a.po_no='.$po_no;
					
					$query=db_query($res);
					
					while($data=mysqli_fetch_object($query)){
				?>

                <tr>
                    <td><?=++$s?></td>
					 <td><?=$data->item_code?></td>
					 <td style="text-align:left"><?=$data->item_name?></td>
                    <td><?=$data->unit?></td>
					<td><?=$data->rate?></td>
                    <td><?=$data->total_unit; $unit_total+=$data->total_unit;?> </td>

                    <td>
							<?=$data->amount; $total_amt=$total_amt+$data->amount;?>
							
				    </td>
                   
					<?php /*?><td>
						<a href="?del=<?=$data->id?>"><button type="button" class="btn2 btn1-bg-cancel"><i class="fa-solid fa-trash"></i></button></a>
					</td><?php */?>
             
					

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
						
						
					
					
					</tr>
            </table>
	<? }else{ 
	
	$res='select a.id, b.finish_goods_code as item_code, concat(a.item_description) as item_name,a.unit_name as unit,a.qty as total_unit,a.rate, a.amount,"x" from purchase_invoice a,item_info b where b.item_id=a.item_id and a.po_no='.$po_no;

	
	
	 ?>

<table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">

<th>S/L</th>

<th>FG Code</th>

<th>Item Name</th>

<th>Unit</th>

<th>LPR</th>
<th>Rate</th>

<th>Total Unit</th>

<th>Amount</th>

<th>Action</th>

</tr>

</thead>
<?

$sl=0;

$query = db_query($res);

while($data=mysqli_fetch_object($query)){

$sl++;

?>	
<tbody class="tbody1">
<tr>

<td><?=$sl;?></td>

<td><?=$data->item_code;?></td>

<td><?=$data->item_name;?></td>



<td><?=$data->unit;?></td>
<td><?=find_a_field('journal_item','item_price','item_id="'.$data->item_id.'" and tr_from="Purchase" order by id desc');?></td>
<td><?=$data->rate;?></td>

<td><?=$data->total_unit;?></td>

<td><?=$data->amount; $totat_amt+=$data->amount;?></td>

<td><a href="?del=<?=$data->id;?>">X</a></td>

</tr>
</tbody>
<?	
}
?>


<tr>

<td colspan="6"></td>

<td>Total</td>

<td>

<? 

echo ($totat_amt+$vat_amt);

?>

</td>

<td></td>

</tr>

</table>

<? } ?>
        </div>


        
    </form>

    <!--button design start-->
    <form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">



	  <div class="n-form-btn-class">
           <input type="text" name='entry_time' id='entry_time' value='<?=date('Y-m-d h:s:i');?>' readonly="">
				   <input name="return"  type="submit" class="btn1 btn1-bg-update" value="RETURN TO USER" onclick="return_function()" />
				   <input  name="po_no" type="hidden" id="po_no" value="<?=$po_no?>"/><input type="hidden" name="return_remarks" id="return_remarks">
				   <input type="hidden" name="cancel_remarks" id="cancel_remarks">
				 
				   <input name="cancel"  type="submit" class="btn1 btn1-bg-cancel" value="CANCEL" onclick="cancel_function()"  />
				
				  <td><input type="button" class="btn1 btn1-bg-help" value="CLOSE"  onclick="window.location.href='select_unapproved_po.php'" /></td>
			
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
function cancel_function() {
  var notes2 = prompt("Why Cancel This PO?","");
  if (notes2!=null) {
    document.getElementById("cancel_remarks").value =notes2;
	document.getElementById("cz").submit();
  }
  return false;
}
</script>

<?

require_once SERVER_CORE."routing/layout.bottom.php";

?>