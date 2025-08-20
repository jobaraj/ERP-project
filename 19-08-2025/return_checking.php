<?php

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

$title='Other Issue';

do_calander('#oi_date');
do_calander('#delivery_date');

do_calander('#customer_po_date');

create_combobox('vendor_id_combo');

$now = date('Y-m-d H:s:i');

if($_GET['cbm_no']>0)
$cbm_no =$_SESSION['cbm_no'] = $_GET['cbm_no'];

$cdm_data = find_all_field('raw_input_sheet','','cbm_no='.$cbm_no);

do_calander('#est_date');

$page = 'return_checking.php';

$table_master='warehouse_other_issue';

$unique_master='oi_no';

$table_detail='warehouse_other_issue_detail';

$unique_detail='id';


//$table_chalan='sale_do_chalan';
//
//$unique_chalan='id';


if($_REQUEST['oi_no']>0)

$$unique_master=$_REQUEST['oi_no'];

elseif(isset($_GET['del']))

{$$unique_master=find_a_field('warehouse_other_issue_detail','oi_no','id='.$_GET['del']); $del = $_GET['del'];}

else

$$unique_master=$_REQUEST[$unique_master];

if(prevent_multi_submit()){
if(isset($_POST['new']))
{

		
		if($_POST['vendor_id_combo']>0) {
		$_POST['vendor_id'] = $_POST['vendor_id_combo'];
		}
	
		$job_date = $_POST['oi_date'];
		
		$YR = date('Y',strtotime($job_date));
  
  		$yer = date('y',strtotime($job_date));
  		$month = date('m',strtotime($job_date));

  		$job_cy_id = find_a_field('warehouse_other_issue','max(job_id)','year="'.$YR.'"')+1;
		
   		$cy_id = sprintf("%06d", $job_cy_id);
	
   		$job_no_generate='SO'.$yer.''.$month.''.$cy_id;

		$_POST['job_no'] = $job_no_generate;
		$_POST['job_id'] = $job_cy_id;
		$_POST['year'] = $YR;

		

		$crud   = new crud($table_master);



		$_POST['entry_at']=date('Y-m-d H:i:s');



		$_POST['entry_by']=$_SESSION['user']['id'];
		
		//$merchandizer_exp=explode('->',$_POST['merchandizer']);
		
		//$_POST['merchandizer_code']=$merchandizer_exp[0];


		if($_POST['flag']<1){



		$_POST['oi_no'] = find_a_field($table_master,'max(oi_no)','1')+1;



		$$unique_master=$crud->insert();



		



		$type=1;



		$msg='Sales Return Initialized. (Sales Return No-'.$$unique_master.')';



		}



		else {
		
		
		unset($_POST['job_no']);
		unset($_POST['job_id']);
		unset($_POST['year']);



		$crud->update($unique_master);



		$type=1;



		$msg='Successfully Updated.';



		}



}




if(isset($_POST['add'])&&($_POST[$unique_master]>0))

{
    

$table		=$table_detail;
$crud      	=new crud($table);
$_POST['remarks']=$_POST['remarks11'];
$_POST['entry_at']=date('Y-m-d H:i:s');
$_POST['entry_by']=$_SESSION['user']['id'];
$_POST['rate']=$_POST['unit_price'];
$_POST['qty']=$_POST['dist_unit'];
$_POST['amount']=$_POST['total_amt'];
$xid = $crud->insert();
}

}

else

{

$type=0;

$msg='Data Re-Submit Error!';

}

if($del>0)

{	

$crud   = new crud($table_detail);

if($del>0)

{

$condition=$unique_detail."=".$del;		

$crud->delete_all($condition);

$condition="gift_on_order=".$del;		

$crud->delete_all($condition);

}

$type=1;

$msg='Successfully Deleted.';

}

if($$unique_master>0)

{

$condition=$unique_master."=".$$unique_master;

$data=db_fetch_object($table_master,$condition);

foreach($data as $key => $value)

{ $$key=$value;}

}

if(isset($_POST['confirm'])){
  
  
  unset($_POST);
  $crud   = new crud($table_master);
  $_POST[$unique_master] = $$unique_master;
  $_POST['entry_by'] = $_SESSION['user']['id'];
  $_POST['entry_at'] = date('Y-m-d H:i:s');
  $_POST['status'] = 'UNCHECKED';
  $crud->update($unique_master);
  unset($$unique_master);
  unset($_SESSION[$unique]);
  echo '<span style="color:green;">Success! Other Issue Added</span>';
 
}

if(isset($_POST['delete']))
{
		$crud   = new crud($table_master);
		$condition=$unique_master."=".$$unique_master;		
		$crud->delete($condition);
		$crud   = new crud($table_detail);
		$crud->delete_all($condition);
		$crud   = new crud($table_chalan);
		$crud->delete_all($condition);
		unset($$unique_master);
		unset($_SESSION[$unique_master]);
		$type=1;
		$msg='Successfully Deleted.';
}

// $dealer = find_all_field('dealer_info','','vendor_id='.$vendor_id);

// auto_complete_from_db('dealer_info','item_name','concat(item_name,"#>",finish_goods_code)','','vai_cutomer');

// auto_complete_from_db('area','area_name','area_code','','district');

// auto_complete_from_db('customer_info','customer_name_e ','customer_code',' vendor_id='.$vendor_id,'via_customer1');

?>

<script language="javascript">
function count()
{


if(document.getElementById('unit_price').value!=''){

var vat = ((document.getElementById('vat').value)*1);
var pkt_size = ((document.getElementById('pkt_size').value)*1);
var unit_price = ((document.getElementById('unit_price').value)*1);
var pkt_unit = ((document.getElementById('pkt_unit').value)*1);
var dist_unit = ((document.getElementById('dist_unit').value)*1);

var total_unit = (document.getElementById('total_unit').value)=(pkt_unit*pkt_size)+dist_unit;

var total_amt = (document.getElementById('total_amt').value) = unit_price*total_unit;

var discount = ((document.getElementById('discount').value)*1);

var amt_after_discount = (document.getElementById('amt_after_discount').value) = total_amt-discount;

var vat_amt = (document.getElementById('vat_amt').value) = (amt_after_discount*vat)/100;

//document.getElementById('total_unit').value=dist_unit;

var total_amt_with_vat = (document.getElementById('total_amt_with_vat').value) = amt_after_discount+vat_amt ;


document.getElementById('total_amt_with_vat').value  = total_amt_with_vat.toFixed(2);




}



}



</script>



<script language="javascript">




//function TRcalculation(){
//    
//var unit_name = document.getElementById('unit_name').value;
//var unit_price = document.getElementById('unit_price').value*1;
//var total_unit = document.getElementById('total_unit').value*1;
//var qty_kg = document.getElementById('qty_kg').value*1;
//
//
//if(unit_name=="Pcs"){
//var total_amt = document.getElementById('total_amt').value= (unit_price*total_unit);}
//
//else {
//    var total_amt = document.getElementById('total_amt').value= (unit_price*qty_kg);
//}
//
//
// if(unit_price<final_price)
//  {
//alert('You can`t reduce the price');
//document.getElementById('unit_price').value='';
//  } 
//  
//  
//}






</script>
<!--<style type="text/css">



/*.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited, a.ui-button, a:link.ui-button, a:visited.ui-button, .ui-button {
    color: #454545;
    text-decoration: none;
    display: none;
}*/


div.form-container_large input {
    width: 250px;
    height: 37px;
    border-radius: 0px !important;
}




.onhover:focus{
background-color:#66CBEA;

}


<!--
.style2 {
	color: #FFFFFF;
	font-weight: bold;
}

</style>-->




<!--Mr create 2 form with table-->
<div class="form-container_large">
    <form action="<?=$page?>" method="post" name="codz2" id="codz2">
<!--        top form start hear-->
        <div class="container-fluid bg-form-titel">
		<? if($oi_date!=date('Y-m-d')){ ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Warning!</h4>
                        <p>Today is not the Issue date.This data will post on current Date after submit.</p>
					</div>
                <? } ?>
            <div class="row">
			
                <!--left form-->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="container n-form2">
                        <div class="form-group row m-0 pb-1">
                            <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Issue No:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
                                <input style="width:250px;"  name="oi_no" type="text" id="oi_no" value="<? if($$unique_master>0) echo $$unique_master; else echo (find_a_field($table_master,'max('.$unique_master.')','1')+1);?>" readonly/>

						<input name="group_for" type="hidden" id="group_for" required readonly="" style="width:250px;" value="<?=$_SESSION['user']['group']?>" tabindex="105" />
						 <input name="vat" type="hidden" class="form-control" readonly="" id="vat"  value="15" tabindex="101" />
						 <input type="hidden" name="issue_type" id="issue_type" value="Other Issue" />
                            </div>
                        </div>



						<div class="form-group row m-0 pb-1">

                            <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Issue Date:</label>

							<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                                <input style="width:250px;"  name="oi_date" type="text" id="oi_date" value="<?=($oi_date!='')?$oi_date:date('Y-m-d')?>"  required />
                            </div>
                        </div>


                        <div class="form-group row m-0 pb-1">
                            <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Warehouse:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                                <select  id="warehouse_id" name="warehouse_id" class="form-control"  style="width:250px;" >
								   <? foreign_relation('warehouse','warehouse_id','warehouse_name',$warehouse_id,'warehouse_id="'.$warehouse_id.'"');?>
								</select>

                            </div>
                        </div>

                    </div>



                </div>

                <!--Right form-->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <div class="container n-form2">
                        <div class="form-group row m-0 pb-1">
                            <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Issue To:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
                                <input type="text" id="issued_to" name="issued_to" class="form-control" value="<?=$issued_to?>"  required style="width:250px;" />
									  
                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">

                            <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Authorized By:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                                <input name="approved_by" type="text" required id="approved_by" value="<?=$approved_by?>" style="width:250px;" class="form-control" />
								 
                            </div>
                        </div>

                        <div class="form-group row m-0 pb-1">
                            <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Remarks:</label>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
                                <input style="width:250px;"  name="oi_details" type="text" id="oi_details" value="<?=$oi_details;?>" />

                            </div>
                        </div>

                    </div>



                </div>


            </div>

            <?php /*?><div class="n-form-btn-class">
				<? if($$unique_master>0) {?>



		<input name="new" type="submit" class="btn1 btn1-bg-submit" value="Update Entry"  tabindex="12" />



		<input name="flag" id="flag" type="hidden" value="1" />



		<? }else{?>



		<input name="new" type="submit" class="btn1 btn1-bg-submit" value="Initiate Entry"  tabindex="12" />



		<input name="flag" id="flag" type="hidden" value="0" />



		<? }?>
            </div><?php */?>
        </div>

        <!--return Table design start-->
        <div class="container-fluid pt-2 p-0 ">
		<?
	$sql = 'select a.*,u.fname from approver_notes a, user_activity_management u where a.entry_by=u.user_id and master_id="'.$$unique_master.'" and type in ("PurchaseReturn") and a.master_id>0';
	$row_check = mysqli_num_rows(db_query($sql));
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

			  $qry = db_query($sql);
			  while($return_note=mysqli_fetch_object($qry)){
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
    </form>



<? if($$unique_master>0) {?>
    <form action="" method="post" name="cloud" id="cloud">
        <!--Table input one design-->
        <div class="container-fluid p-0 ">


            <?php /*?><table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th>Unit</th>

                    <th>Stock</th>
                    <th>Unit-Price</th>
                    <th>Quantity</th>

                    <th>Value</th>
                    <th>ADD</th>
                </tr>
                </thead>

                <tbody class="tbody1">


				<tr>

                    <td><span class="style2">




<span id="sub">
<?

auto_complete_from_db('item_info','concat(item_id,"-> ",item_name)','item_id',' product_nature="Salable"','item_id');

//$do_details="SELECT a.*, i.item_name FROM sale_do_details a, item_info i WHERE  a.item_id=i.item_id and oi_no=".$oi_no." order by id desc limit 1";
//$do_data = find_all_field_sql($do_details);

?>


<input name="item_id" type="text" class="input3"  value="" id="item_id"  onblur="getData2('return_ajax.php', 'so_data_found', this.value, document.getElementById('oi_no').value);"/>


<!--<select  name="item_id" id="item_id"  style="width:90%;" required onchange="getData2('sales_invoice_ajax.php', 'so_data_found', this.value, document.getElementById('oi_no').value);">

		<option></option>

      <? foreign_relation('item_info','item_id','item_name',$item_id,'1');?>
 </select>-->



<input type="hidden" id="<?=$unique_master?>" name="<?=$unique_master?>" value="<?=$$unique_master?>"  />
<input type="hidden" id="oi_date" name="oi_date" value="<?=$oi_date?>"  />
<input type="hidden" id="group_for" name="group_for" value="<?=$group_for?>"  />
<input type="hidden" id="warehouse_id" name="warehouse_id" value="<?=$warehouse_id?>"  />
<input name="oi_date" type="hidden" id="oi_date" value="<?=$oi_date;?>"/>
<input name="issued_to" type="hidden" id="issued_to" value="<?=$issued_to;?>"/>
<input name="issue_type" type="hidden" id="issue_type" value="<?=$issue_type;?>"/>
</span>




 </span></td>

                    <td colspan="4">
							<div align="right">
								  <span id="so_data_found">
						<table >
							<tr>

								<td width="20%"><input name="item_name" type="text" class="input3"  readonly=""  autocomplete="off"  value="" id="item_name"  /> </td>

								<td width="20%"><input name="pcs_stock" type="text" class="input3"  readonly=""  autocomplete="off"  value="" id="pcs_stock"  /></td>

								<td width="20%"><input name="ctn_price" type="text" class="input3" id="ctn_price" readonly=""    required  value="<?=$do_data->ctn_price;?>"   /></td>

								<td width="20%"><input name="pcs_price" type="text" class="input3" id="pcs_price" readonly=""    required="required"  value="<?=$do_data->pcs_price;?>"  /></td>
							</tr>
						</table>
						</span>
							</div>
						</td>
                    <td><span>

<input  name="dist_unit" type="text" class="input3" id="dist_unit"value=""    onkeyup="count()"   />
 </span></td>

                    <td><span>



<input name="total_unit" type="hidden"    id="total_unit" readonly/>


		<input name="total_amt" type="text"  id="total_amt"     readonly/>

 </span></td>
                    <td><input name="add" type="submit" id="add" value="ADD" class="btn1 btn1-bg-submit" /></td>
					

                </tr>

                </tbody>
            </table><?php */?>





        </div>


        <!--Data multi Table design start-->
		<? if($$unique_master>0){?>
		<?

  $res='select a.id, b.item_name, a.rate AS unit_price,a.item_id, a.warehouse_id, a.qty, a.unit_name, a.amount, l.ledger_name,s.sub_ledger_name FROM warehouse_other_issue_detail a
						LEFT JOIN 
							item_info b 
						ON 
							b.item_id = a.item_id
						LEFT JOIN 
							accounts_ledger l 
						ON 
							l.ledger_id = a.expense_ledger
						LEFT JOIN 
							general_sub_ledger s 
						ON 
							s.sub_ledger_id = a.sub_ledger
						WHERE 
							a.oi_no ="'.$$unique_master.'"';

?>
        <div class="container-fluid pt-2 p-0 ">

            <table class="table1  table-striped table-bordered table-hover table-sm">
                <thead class="thead1">
                <tr class="bgc-info">
                    <th>SL</th>
                    <th>Item Description</th>
                    <th>Unit</th>
                    <th>Expense Ledger</th>
                    <th>Sub Ledger</th>
                    <th>Qty</th>
                    <th>Rate</th>
					<th>Amount</th>

                    <th>Action</th>
                </tr>
                </thead>

                <tbody class="tbody1">

                <?

$i=1;

$query = db_query($res);

while($data=mysqli_fetch_object($query)){ 

$flag = 0;

 $stock_final = find_a_field('journal_item','sum(item_in-item_ex)','item_id="'.$data->item_id.'" and warehouse_id="'.$data->warehouse_id.'"');

if($stock_final<$data->qty){
$flag++;
}

?>
				<tr>
                    <td><?=$i++?></td>
                    <td><?=$data->item_name?></td>
                    <td><?=$data->unit_name?></td>
                    <td><?=$data->ledger_name?></td>
                    <td><?=$data->sub_ledger_name?></td>
                    <td><?=$data->qty?></td>
					<td><?=$data->unit_price?></td>
                    <td><?=$data->amount?></td>
                    <td>
						<a href="?del=<?=$data->id?>"> 
							<button type="button"class="btn2 btn1-bg-cancel">
								<i class="fa-solid fa-trash"></i>
							</button> 
						</a>
					</td>

                </tr>
				<?
 $total_pcs +=$data->qty;
 $total_amt +=$data->amount;
 } ?>

<tr>

<td colspan="4"><div align="right"><strong>  Total:</strong></div></td>

<td>&nbsp;</td>
<td align="right"><?=number_format($total_pcs,2);?></td>
<td>&nbsp;</td>
<td align="right"><?=number_format($total_amt,2);?></td>
<td>&nbsp;</td>

</tr>

                </tbody>
				<? }?>	
            </table>


        </div>
    </form>

    <!--button design start-->
    <form action="unapproved_pr.php" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
        <div class="container-fluid p-0 ">

            <div class="n-form-btn-class">
			
			<? 
						
						
					?>
               
					<input name="return"  type="submit" class="btn1 btn1-bg-cancel" value="RETURN"  />
					<input  name="oi_no" type="hidden" id="oi_no" value="<?=$$unique_master?>"/>
					<input  name="oi_date" type="hidden" id="oi_date" value="<?=$oi_date?>"/></td>
					<? if($flag==0){ ?>
					<input name="confirm" type="submit" class="btn1 btn1-submit-input" value="CONFIRM" />
					<? } else{ echo "Check stock ..."; } ?>
            </div>

        </div>
		<? }?>
    </form>

</div>




<!--<script>$("#cz").validate();$("#cloud").validate();</script>-->
<script language="javascript">
function count()
{


if(document.getElementById('unit_price').value!=''){

var vat = ((document.getElementById('vat').value)*1);

var unit_price = ((document.getElementById('unit_price').value)*1);

var dist_unit = ((document.getElementById('dist_unit').value)*1);

var total_unit = (document.getElementById('total_unit').value)=dist_unit;



var total_amt = (document.getElementById('total_amt').value) = total_unit*unit_price;


}



}



</script>
<?
require_once SERVER_CORE."routing/layout.bottom.php";




?>