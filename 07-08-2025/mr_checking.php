<?php

require_once "../../../assets/template/layout.top.php";



$title='Approving Purchase Requisition';



do_calander('#req_date');

do_calander('#need_by');



$table_master='master_requisition_master';

$table_details='master_requisition_details';

$unique='req_no';

$req_all = find_all_field('master_requisition_master','','req_no="'.$_GET['req_no'].'"');


if($_GET['req_no']){
$req_no=$_SESSION[$unique]=$_GET['req_no'];
}


if(isset($_POST['new']))

{

		$crud   = new crud($table_master);



		if(!isset($_SESSION[$unique])) {

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d H:i:s');

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d H:i:s');

		$$unique=$_SESSION[$unique]=$crud->insert();

		unset($$unique);

		$type=1;

		$msg='Requisition No Created. (Req No :-'.$_SESSION[$unique].')';

		}

		else {

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d H:i:s');

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

		$_POST['checked_at']=date('Y-m-d H:i:s');

		$_POST['status']='UNCHECKED';

		$crud   = new crud($table_master);

		$crud->update($unique);
		
		$master= find_all_field('master_requisition_master','','req_no="'.$req_no.'"');
		  $sql3='select * from master_requisition_details where req_no="'.$req_no.'"';
		
		$rs = mysql_query($sql3);
		while($row=mysql_fetch_object($rs)){
		
		$issue_qty = $_POST['id'.$row->id];
		$xid= mysql_insert_id();
		
		journal_item_control($row->item_id ,$master->warehouse_id,$master->req_date,0,$row->qty,'FG_Issue',$row->id,'',$master->req_for,$master->req_no,$master->req_note);
		
		//$item_id, $warehouse_id,$ji_date,$item_in,$item_ex,$tr_from,$tr_no,$rate='',$r_warehouse='',$sr_no='',$bag_size='',$bag_unit='',$group_for='',$final_price='',$barcode=''
		
			}
		
		

		unset($$unique);

		unset($_SESSION[$unique]);

		$type=1;
       
		//$msg='Successfully Forwarded to Relevant Department.';
		header('location:select_unapproved_mr.php');

}

if(isset($_POST['cancel']))

{

		unset($_POST);

		$_POST[$unique]=$$unique;

		$_POST['checked_by']=$_SESSION['user']['id'];

		$_POST['checked_at']=date('Y-m-d H:i:s');

		$_POST['status']='CANCELED';

		$crud   = new crud($table_master);

		$crud->update($unique);

		unset($$unique);

		unset($_SESSION[$unique]);

		$type=1;

		header('location:select_unapproved_mr.php');

}




if(isset($_POST['add'])&&($_POST[$unique]>0))

{

		$crud   = new crud($table_details);

		$iii=explode('#>',$_POST['item_id']);

		$_POST['item_id']=$iii[2];

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d H:i:s');

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d H:i:s');

		$crud->insert();

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
		
		
		$note_sql = 'insert into approver_notes(`master_id`,`type`,`note`,`entry_at`,`entry_by`) value("'.$$unique.'","MR","'.$remarks.'","'.date('Y-m-d H:i:s').'","'.$_SESSION['user']['id'].'")';
		mysql_query($note_sql);

		unset($$unique);

		unset($_SESSION[$unique]);

		$type=1;
        
		
		
		header('location:select_unapproved_mr.php');


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



//auto_complete_from_db('item_info','item_name','concat(item_name,"#>",item_description,"#>",item_id)','1 and sub_group_id="1096001000010000"','item_id');
auto_complete_from_db('item_info','item_name','concat(item_name,"#>",item_name,"#>",item_id)','1 and sub_group_id!="1096000900010000"','item_id');

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




<!--Mr create 2 form with table-->
<div class="form-container_large">
    <form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
<!--        top form start hear-->
        <div class="container-fluid bg-form-titel">
            <div class="row">
                <!--left form-->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="container n-form2">
                        <div class="form-group row m-0 pb-1">
								 <? $field='req_no';?>
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition No</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
                             
         					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" class="form-control" readonly="readonly"/>

                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">
								<? $field='req_date'; if($req_date=='') $req_date =date('Y-m-d');?>
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Requisition Date</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
             
      							<input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" required readonly="" />
                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">
								<? $field='req_note';?>
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Addition Note:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                     
        						 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$req_all->req_note?>" class="form-control" autocomplete="off" />

                            </div>
                        </div>

                    </div>



                </div>

                <!--Right form-->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="container n-form2">
                        <div class="form-group row m-0 pb-1">
								 <? $field='depot';?>
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Depot:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
                         <select name="depot" id="depot">
                      			<? foreign_relation('warehouse','warehouse_id','warehouse_name',$depot,'warehouse_id="'.$req_all->req_for.'"');?>
						</select>
                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">
								 <? $field='need_by';?>
                            <label for="<?=$field?>" class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Need By Date</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                                
			
          					 <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=date('Y-m-d')?>" class="form-control" autocomplete="off" />
       
                            </div>
                        </div>

                        

                    </div>



                </div>


            </div>

            
        </div>

        <!--return Table design start-->
		
        <div class="container-fluid pt-5 p-0 ">
            
			
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


    <form action="" method="post" name="cloud" id="cloud">
        <!--Table input one design-->
        <div class="container-fluid pt-5 p-0 ">


            <table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">
                    <th>Item Name</th>
                    <th>Stock Qty</th>
                    <th>Last Pur Qty</th>

                    <th>Last Pur Date</th>
					<th>Specification</th>
                    <th>Qty</th>
                    <th>X</th>

                </tr>
                </thead>

                <tbody class="tbody1">
				
				<?
						 $res='select a.id,b.item_name as item_name,a.qoh as stock_qty,a.last_p_qty as last_pur_qty,a.last_p_date as last_pur_date,a.qty,"x" from master_requisition_details a,item_info b where b.item_id=a.item_id and a.req_no='.$req_no; 
						
						$query=mysql_query($res);
						while($row=mysql_fetch_object($query)){
						
						
				
				 ?>

                <tr>
                    <td><?=$row->item_name?></td>
                    <td><?=$row->stock_qty?></td>
					<td><?=$row->last_pur_qty?></td>
					<td><?=$row->last_pur_date?></td>
					<td><?=$row->specification?></td>
					<td><?=$row->qty?></td>
					<td><a href="?del=<?=$data->id?>">X</a></td>
                    

                </tr>
				
				<? } ?>

                </tbody>
            </table>





        </div>


        </div>
    </form>

    <!--button design start-->
    <form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
        <div class="container-fluid p-0 ">

            <div class="n-form-btn-class">
              
				   <input name="return"  type="submit" class="btn btn-warning" value="RETURN TO USER" onclick="return_function()"  />
				   <input  name="req_no" type="hidden" id="req_no" value="<?=$req_no?>"/><input type="hidden" name="return_remarks" id="return_remarks">
				  
				   <input name="cancel"  type="submit" class="btn btn-danger" value="CANCEL" />
				  
					<input type="button" class="btn btn-primary" value="CLOSE"  onclick="window.location.href='select_unapproved_mr.php'" /></td>
			
					 <input name="confirmm" type="submit" class="btn btn-info" value="CONFIRM AND FORWARD REQUISITION" />
	  
            </div>

        </div>
    </form>

	<? } ?>


</div>



<?php /*?><div class="form-container_large">

<form action="" method="post" name="codz" id="codz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">


<div class="row ">
	      <div class="col-md-3 form-group">
		   <? $field='req_no';?>
            <label for="<?=$field?>" >Requisition No : </label>
          <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" class="form-control" readonly="readonly"/>
          </div>
		  
		  <div class="col-md-3 form-group">
		   <? $field='req_date'; if($req_date=='') $req_date =date('Y-m-d');?>
            <label for="<?=$field?>">Requisition Date : </label>
      <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" required readonly="" class="form-control"/>
          </div>
		  
		  
		 <div class="col-md-3 form-group">
		  <? $field='req_note';?>
            <label for="<?=$field?>"> Additional Note : </label>
             <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" class="form-control"/>
          </div>
		  
		  
		   <div class="col-md-3 form-group">
		    <? $field='depot';?>
            <label for="<?=$field?>">Depot: </label>
        <select name="depot" id="depot"  class="form-control">
                      <? foreign_relation('warehouse','warehouse_id','warehouse_name',$depot,'warehouse_id="'.$_SESSION['user']['depot'].'"');?>
             </select>
          </div>
		  
		  
		  
		  
		    <div class="col-md-3 form-group">
			 <? $field='need_by';?>
            <label for="<?=$field?>">Need By Date : </label>
           <input  name="<?=$field?>" type="text" id="<?=$field?>" value="<?=$$field?>" class="form-control" autocomplete="off" />
          </div>
		  
		  
          <div class="col-md-3 form-group" style="display:none;">
            <label for="depot_id"> Warehouse: </label>
 <input name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$_SESSION['user']['depot']?>" />
            <!--<input style="width:155px;"  name="wo_detail" type="text" id="wo_detail" value="<?=$depot_id?>" readonly="readonly"/>-->
          </div>
		  
          
	  
   </div>
<table width="99%" border="0" cellspacing="0" cellpadding="0" align="center"><?php */?>

  

  <!--<tr>

    <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="buttonrow" style="margin-left: 157px;"><span class="buttonrow" style="margin-left:240px;">
      <input name="new" type="submit" class="btn btn-success" value="<?=$btn_name?>" style="width:250px; font-weight:bold; font-size:12px;" />
    </span></div></td>
    </tr>-->
</table>

</form>




<?php /*?>
<? if($_SESSION[$unique]>0){?>

<table width="40%" border="1" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse">

  <tr>

    <td colspan="3" align="center" bgcolor="#CCFF99"><strong>Entry Information</strong></td>

    </tr>

  <tr>

    <td align="right" bgcolor="#CCFF99">Created By:</td>

    <td align="left" bgcolor="#CCFF99">&nbsp;&nbsp;<?=find_a_field('user_activity_management','fname','user_id='.$entry_by);?></td>

    <td rowspan="2" align="center" bgcolor="#CCFF99"><a href="mr_print_view.php?req_no=<?=$req_no?>" target="_blank"><img src="../../../images/icons/print.png" width="36" height="36" /></a></td>

  </tr>

  <tr>

    <td align="right" bgcolor="#CCFF99">Created On:</td>

    <td align="left" bgcolor="#CCFF99">&nbsp;&nbsp;<?=$entry_at?></td>

    </tr>

</table>

<form action="?<?=$unique?>=<?=$$unique?>" method="post" name="cloud" id="cloud" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}"><?php */?>

<!--<table  width="100%" border="1" align="left"  style="border-collapse:collapse; border:1px solid #caf5a5;" cellpadding="2" cellspacing="2">

                      <tr>

                        <td width="30%" align="center" bgcolor="#0099FF"><strong>Item Name</strong></td>

                        <td width="11%" align="center" bgcolor="#0099FF"><strong>Stock Qty</strong></td>

                        <td width="11%" align="center" bgcolor="#0099FF"><strong>L P QTY</strong></td>

                        <td width="11%" align="center" bgcolor="#0099FF"><strong>L P Date</strong></td>

                        <td width="11%" align="center" bgcolor="#0099FF"><strong>Unit</strong></td>

                        <td width="11%" align="center" bgcolor="#0099FF"><strong>Req Qty</strong></td>

                          <td width="11%"  rowspan="2" align="center" bgcolor="#FF0000">

						  <div class="button">

						  <input name="add" type="submit" id="add" value="ADD" tabindex="12" class="update"/>                       
					    </div>				        </td>
      </tr>

                      <tr>

 <td align="center" bgcolor="#CCCCCC"><input  name="<?=$unique?>"i="i" type="hidden" id="<?=$unique?>" value="<?=$$unique?>"/>

                            <input  name="warehouse_id" type="hidden" id="warehouse_id" value="<?=$warehouse_id?>"/>

                            <input  name="req_date" type="hidden" id="req_date" value="<?=$req_date?>"/>

                            <input  name="item_id" type="text" id="item_id" value="<?=$item_id?>" class="form-control" required="required" onblur="getData2('mr_ajax.php', 'mr', this.value, document.getElementById('warehouse_id').value);"/></td>

<td colspan="4" align="center" bgcolor="#CCCCCC">
<div align="right"><span id="mr">
						<table style="width:100%;" border="1">
						 <tr>
						
                        <td width="25%"><input name="qoh" type="text" id="qoh" class="form-control" onfocus="focuson('qty')" /></td>
                        
                        <td width="25%"><input name="last_p_qty" type="text" id="last_p_qty" class="form-control" onfocus="focuson('qty')" /></td>
                        
                        <td width="25%"><input name="last_p_date" type="text"  id="last_p_date"  class="form-control" onfocus="focuson('qty')" /></td>
                          
                        <td width="25%"><input name="unit_name" type="text"  id="unit_name"  maxlength="100" class="form-control" onfocus="focuson('qty')" /></td>
                          
						</tr>	
						</table>
						</span></div>	
                          </td>


<td align="center" bgcolor="#CCCCCC"><input name="qty" type="text"  id="qty"  maxlength="100" class="form-control"/></td>
      </tr>
    </table>-->

					  <br /><br />




<?php /*?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">

<tr>

<td><div class="tabledesign2">

<? 

$res='select a.id,b.item_name as item_name,a.qoh as stock_qty,a.last_p_qty as last_pur_qty,a.last_p_date as last_pur_date,a.qty,"x" from master_requisition_details a,item_info b where b.item_id=a.item_id and a.req_no='.$req_no;

echo link_report_del($res);

?>

</div></td>

</tr>

</table>

</form>

<form action="" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">

  <table width="100%" border="0">

    <tr>

      <td align="center">
	   <input name="return"  type="submit" class="btn btn-warning" value="RETURN TO USER" onclick="return_function()" style="width:270px; font-weight:bold; font-size:12px;color:white; height:30px" />
	   <input  name="req_no" type="hidden" id="req_no" value="<?=$req_no?>"/><input type="hidden" name="return_remarks" id="return_remarks">
      </td>
	  
	  <td align="center">
	   <input name="cancel"  type="submit" class="btn btn-danger" value="CANCEL" style="width:270px; font-weight:bold; font-size:12px;color:white; height:30px" />
      </td>
	  <td><input type="button" class="btn btn-primary" value="CLOSE" style="width:240px; font-weight:bold; font-size:12px;color:white; height:30px" onclick="window.location.href='select_unapproved_mr.php'" /></td>

      <td align="center">
	  <input name="confirmm" type="submit" class="btn btn-info" value="CONFIRM AND FORWARD REQUSITION" style="width:270px; font-weight:bold; font-size:12px; height:30px; color:white;" />
	  </td>

    </tr>

  </table>

</form>

<? }?>

</div><?php */?>

<script>$("#codz").validate();$("#cloud").validate();</script>
<script>
function return_function() {
  var notes = prompt("Why Return This Requisition?","");
  if (notes!=null) {
    document.getElementById("return_remarks").value =notes;
	document.getElementById("cz").submit();
  }
  return false;
}
</script>
<?

require_once "../../../assets/template/layout.bottom.php";

?>