<?php

//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//error_reporting(-1);

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE.'core/init.php';
require_once SERVER_CORE."routing/layout.top.php";

$title='Create Sales Order';


$help= "1. Select a warehouse from where you want to deliver the order.
2. Select a Customer whom you want to deliver the order.
3. In dealer information, if the 'Acc Verification'  is yes, then it will send to Accounts department first to verify. Then you can find it in 'Sales Module - Unapproved SO List'.
4. If you want to change the 'Warehouse' after a item entry, then you need to delete the order and need to re-entry the Item after 'Warehouse Update'.
5. VAT & AIT Included.";



$module_name = find_a_field('user_module_manage','module_file','id='.$_SESSION["mod"]);





do_calander('#do_date');
do_calander('#delivery_date');

do_calander('#customer_po_date');
//create_combobox('dealer_code');
$tr_type="Show";
//create_combobox('dealer_code_combo');
//echo constant('SERVER_FILE_PATH');
$now = date('Y-m-d H:s:i');

if($_GET['cbm_no']>0)
$cbm_no =$_SESSION['cbm_no'] = $_GET['cbm_no'];

$cdm_data = find_all_field('raw_input_sheet','','cbm_no='.$cbm_no);

do_calander('#est_date');

$page = 'do.php';

$table_master='sale_do_master';

$unique_master='do_no';

$table_detail='sale_do_details';

$unique_detail='id';


$table_chalan='sale_do_chalan';

$unique_chalan='id';


if($_REQUEST['old_do_no']>0)

$$unique_master=$_REQUEST['old_do_no'];

elseif(isset($_GET['del']))

{$$unique_master=find_a_field('sale_do_details','do_no','id='.$_GET['del']); $del = $_GET['del'];}

else

$$unique_master=$_REQUEST[$unique_master];

if(prevent_multi_submit()){





if(isset($_POST['new']))
{
	
	$_POST['dealer_code'] = end(explode("#",$_POST['dealer_code_combo']));
	$_POST['rec_ledger']= find_a_field('dealer_info','account_code','dealer_code="'.$_POST['dealer_code'].'"');
	
	
	//$folder ='do';
	//$field = 'file_upload';
	//$file_name = $field.'-'.$_POST['do_no'];
	//if($_FILES['file_upload']['tmp_name']!=''){
	//$_POST['file_upload']=upload_file($folder,$field,$file_name);
	//}
	
	$folder ='do';
	$field = 'file_upload';
	$file_name = $field.'-'.$_POST['do_no'];
	if($_FILES['file_upload']['tmp_name']!=''){
	$random = random_int(10000,99999);
	$newFileName = 'sales_'.$random;
	$ext = end(explode(".",$_FILES['file_upload']['name']));
	//$_POST['file_upload']=upload_file($folder,$field,$file_name);
	file_upload_aws('file_upload','sales',$newFileName);
	$_POST['file_upload']= $newFileName.'.'.$ext;
	}
	
		$job_date = $_POST['do_date'];
		
		$YR = date('Y',strtotime($job_date));
  
  		$yer = date('y',strtotime($job_date));
  		$month = date('m',strtotime($job_date));

  		$job_cy_id = find_a_field('sale_do_master','max(job_id)','year="'.$YR.'"')+1;
		
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



		$_POST['do_no'] = find_a_field($table_master,'max(do_no)','1')+1;
		
		
		        $highest_level =is_approval_layer_exist('Sales');
					if($highest_level>0){

					$_POST['current_approval_level'] = $highest_level;


				}else{
				$_POST['checked_by']=$_SESSION['user']['id'];
           		$_POST['status']='CHECKED';
				$_POST['checked_at']=date('Y-m-d h:s:i');
				}



		$$unique_master=$crud->insert();



		


        $tr_type="Initiate";
		$type=1;



		$msg='Sales Return Initialized. (Sales Return No-'.$$unique_master.')';



		}



		else {
		
		
		unset($_POST['job_no']);
		unset($_POST['job_id']);
		unset($_POST['year']);


        $field = 'file_upload';
    	$file_name = $field.'-'.$_POST['do_no'];
    	if($_FILES['file_upload']['tmp_name']!=''){
    	$_POST['file_upload']=upload_file($folder,$field,$file_name);
	}

        $_POST['dealer_code'] = end(explode("#",$_POST['dealer_code_combo']));
		$crud->update($unique_master);



		$type=1;



		$msg='Successfully Updated.';



		}



}




if(isset($_POST['add'])&&($_POST[$unique_master]>0) && $_SESSION['csrf_token']===$_POST['csrf_token'])

{
 $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
//$crud   = new crud($table_master);
//
//$_POST['edit_at']=date('Y-m-d H:i:s');
//$_POST['edit_at']=$_SESSION['user']['id'];
//$dealer = explode('-',$_POST['dealer_code']);
//$dealer_code = $_POST['dealer_code'] = $dealer[0];
//
//
// $dealer_ledger =  find_a_field('dealer_info','account_code','dealer_code="'.$dealer_code.'"');
// $dealer_balance = find_a_field('journal','sum(dr_amt-cr_amt)','ledger_id="'.$dealer_ledger.'"');
//
//
// if($dealer_balance<0) {
//  $closing_balance =  $dealer_balance*(-1); 
// }else {
//  $closing_balance = $dealer_balance;
// }
// 
// $dealer_total_sale = find_a_field('journal','sum(dr_amt)','ledger_id="'.$dealer_ledger.'"');
// 
//  $sales_percentage = ($closing_balance/$dealer_total_sale)*100;
// 		

// if($sales_percentage<20){
// 	$_POST['order_create'] = 'Yes';
// }else {
//  $_POST['order_create'] = 'No';
// }
 
 //
//  if($dealer_balance<0 ) {
//  	$_POST['order_create'] = 'Yes';
//	} elseif($dealer_balance==0) {
//	$_POST['order_create'] = 'Yes';
//	}elseif ($dealer_balance>0 & $sales_percentage<20 ) {
//	$_POST['sales_percentage'] = $sales_percentage;
//	$_POST['order_create'] = 'Yes';
//	}else {
//	$_POST['sales_percentage'] = $sales_percentage;
// 	$_POST['order_create'] = 'No';
// 	}
//	
//	


//
//$customer = explode('-',$_POST['via_customer']);
//$via_customer = $_POST['via_customer'] = $customer[0];
//
//if($_POST['flag']<1){
//$_POST['entry_at']=date('Y-m-d H:i:s');
//$_POST['entry_by']=$_SESSION['user']['id'];
//$_POST['do_no'] = find_a_field($table_master,'max(do_no)','1')+1;
//$$unique_master=$crud->insert();
//unset($$unique);
//$type=1;
//$msg='Work Order Initialized. (Demand Order No-'.$$unique_master.')';
//}

$table		=$table_detail;

if($_POST['sub_group_id']!=0){
$_SESSION['sub_group'] = $_POST['sub_group_id'];
$_SESSION['dealer_code'] = $_POST['dealer_code'];

}

$crud      	=new crud($table);



$_POST['remarks']=$_POST['remarks11'];
$_POST['entry_at']=date('Y-m-d H:i:s');
$_POST['entry_by']=$_SESSION['user']['id'];


if($_REQUEST['init_bag_unit']<1){

$_POST['init_bag_unit'] = $_REQUEST['bag_unit'];
$_POST['init_dist_unit'] = $_REQUEST['total_unit'];
$_POST['init_total_unit'] = $_REQUEST['total_unit'];
$_POST['init_total_amt'] = $_REQUEST['total_amt'];

}


   $tr_type="Add";

$xid = $crud->insert();
if($_REQUEST['bag_unit']>0){
$item_id = $_POST['item_id'];
 $r_sql = "select i.item_id,g.gunny_bag as gunny,g.poly_bag as poly from item_info i,item_sub_group g where  i.sub_group_id=g.sub_group_id and i.item_id=".$item_id;
$r1=db_query($r_sql);
while($rs1=mysqli_fetch_object($r1))
{
			$item_id = $rs1->item_id;
			$item_gunny=$rs1->gunny;
			$item_poly=$rs1->poly;
if($item_gunny>0){
$gunny_price =find_a_field('item_info','d_price',' item_id='.$item_gunny);
$_REQUEST['total_amt'] = $gunny_price*$_REQUEST['bag_unit']; 

 $gunny_sql = "INSERT INTO `sale_do_details` 
(`do_no`,  `do_date`, dealer_code,  via_customer, `item_id`, depot_id,`unit_price`, `bag_unit`, dist_unit, total_unit, `total_amt`,   entry_by, entry_at, remarks) VALUES
('".$do_no."',  '".$_POST['do_date']."', '".$_POST['dealer_code']."',  '".$_POST['via_customer']."',  '".$item_gunny."', 
 '".$_POST['depot_id']."', '".$gunny_price."', '".$_REQUEST['bag_unit']."', '".$_REQUEST['bag_unit']."', '".$_REQUEST['bag_unit']."',
  '".$_REQUEST['total_amt']."', '".$_SESSION['user']['id']."', '".$now."', '".$_POST['remarks']."')";

db_query($gunny_sql);
}
			
			
if($item_poly>0){
$poly_price=find_a_field('item_info','d_price',' item_id='.$item_poly);

$_REQUEST['total_amt'] = $poly_price*$_REQUEST['bag_unit'];

 $gunny_sql = "INSERT INTO `sale_do_details` 
(`do_no`,  `do_date`, dealer_code,  via_customer,  `item_id`, depot_id, `unit_price`, `bag_unit`, dist_unit, total_unit, `total_amt`,   entry_by, entry_at, remarks) VALUES
('".$do_no."',  '".$_POST['do_date']."', '".$_POST['dealer_code']."',  '".$_POST['via_customer']."',  '".$item_poly."',  
 '".$_POST['depot_id']."',  '".$poly_price."', '".$_REQUEST['bag_unit']."',  '".$_REQUEST['bag_unit']."', '".$_REQUEST['bag_unit']."', 
 '".$_REQUEST['total_amt']."',   '".$_SESSION['user']['id']."', '".$now."', '".$_POST['remarks']."')";

db_query($gunny_sql);

}
}

//if($_POST['group_for']==5){
//
//$gunny =find_all_field('item_info','',' item_id="900120001" ');
//
//$_REQUEST['init_total_amt'] = $gunny->d_price*$_REQUEST['init_bag_unit'];
//
//  $gunny_sql = "INSERT INTO `sale_do_details` 
//(`do_no`, `do_date`, dealer_code, `item_id`, `unit_price`, `init_bag_unit`,`init_total_amt`) VALUES
//('".$do_no."', '".$do_date."', '".$dealer_code."', '".$gunny->item_id."',  '".$gunny->d_price."', '".$_REQUEST['init_bag_unit']."',  '".$_REQUEST['init_total_amt']."')";
//
//db_query($gunny_sql);
//
//}

}



//$table_ch		=$table_chalan;
//$crud      	=new crud($table_ch);
//$cid = $crud->insert();
  //$challan_sql = "INSERT INTO `sale_do_chalan` 
// (`chalan_no`, `order_no`, do_no, `item_id`, `dealer_code`, `unit_price`,`pkt_unit`, bag_unit, dist_unit, total_unit, total_amt, chalan_date, depot_id, group_for, entry_by, entry_at) VALUES
//('".$_POST['chalan_no']."', '".$xid."', '".$_POST['do_no']."', '".$_POST['item_id']."',  '".$_POST['dealer_code']."', '".$_POST['unit_price']."',  '".$_POST['pkt_unit']."', '".$_POST['bag_unit']."', '".$_POST['dist_unit']."' , '".$_POST['total_unit']."' , '".$_POST['total_amt']."' , '".$_POST['do_date']."' ,'4', '".$_POST['group_for']."', '".$_SESSION['user']['id']."', '".$now."' )";
//
//db_query($challan_sql);

//$_POST['init_total_unit'] = $_POST['init_dist_unit'];

//$_POST['in_stock_kg']=$_POST['in_stock'];

//$_POST['init_total_amt'] = ($_POST['init_total_unit'] * $_POST['unit_price']);

//$_POST['t_price'] = 0;

//$_POST['gift_on_order'] = $crud->insert();


//Gift Offer
$do_date = $_POST['do_date'];
$dealer_type = find_a_field('dealer_info','dealer_type','dealer_code="'.$_POST['dealer_code'].'"');
$dealer_type_id = find_a_field('dealer_info','dealer_type','dealer_code="'.$_POST['dealer_code'].'"');
$dealer_type = find_a_field('dealer_type','dealer_type','id="'.$dealer_type_id.'"');
$main_item_qty=$_POST['total_unit'];
  $sss = "select * from sale_gift_offer where item_id='".$_POST['item_id']."' and start_date<='".$do_date."' and end_date>='".$do_date."'  ";

// and (region_id=0 or region_id='".$dealer->region_id."') and (zone_id=0 or zone_id='".$dealer->zone_id."') and (area_id=0 or area_id='".$dealer->area_id."')
		$qqq = db_query($sss);

		while($gift=mysqli_fetch_object($qqq)){
		
		if($gift->item_qty<=$main_item_qty)
		{ 
	
		 
	    $gift_item = find_all_field('item_info','','item_id="'.$gift->gift_id.'"');
		 $_POST['item_id'] = $gift->gift_id;
        $_POST['gift_id']=$gift->id;
		$_POST['dp_price'] = $gift_item->d_price;
		$_POST['fp_price'] = $gift_item->f_price;
		$_POST['unit_price_gift_item']=find_a_field('journal_item','final_price','item_id="'.$gift->gift_id.'" order by id desc');
		
		$_POST['unit_price'] = '0.00';
		$_POST['total_amt'] = '0.00';
		 
	 
		 $test_code=(int)($main_item_qty/$gift->item_qty);
		 
	 
 		$gift_test_qty=($test_code*$gift->gift_qty);
 
		$_POST['total_unit'] = $gift_test_qty;
	
		$_POST['pkt_unit'] = (int)($_POST['total_unit']/$gift_item->pack_size);
		$_POST['dist_unit'] = (int)($_POST['total_unit']%$gift_item->pack_size);
		$_POST['pkt_size'] = $gift_item->pack_size;
		$_POST['t_price'] = '0.00';
		$crud->insert();
		

}
}
}
}

else

{

$type=0;

$msg='Data Re-Submit Error!';

}

if($del>0)

{	

$tr_type="Remove";
$main_del = find_a_field($table_detail,'gift_on_order','id = '.$del);

$crud   = new crud($table_detail);

if($del>0)

{
$tr_type="Remove";
$condition=$unique_detail."=".$del;		

$crud->delete_all($condition);

$condition="gift_on_order=".$del;		

$crud->delete_all($condition);

if($main_del>0){

$condition=$unique_detail."=".$main_del;		

$crud->delete_all($condition);

$condition="gift_on_order=".$main_del;		

$crud->delete_all($condition);}

}


 $sql1 = "delete from journal_item where tr_from = 'Sales' and tr_no = '".$del."'";


		db_query($sql1);




$type=1;

$msg='Successfully Deleted.';

}

if($$unique_master>0)

{

$condition=$unique_master."=".$$unique_master;

$data=db_fetch_object($table_master,$condition);

foreach ($data as $key => $value)

{ $$key=$value;}

}

$dealer = find_all_field('dealer_info','','dealer_code='.$dealer_code);

auto_complete_from_db('item_info','item_name','concat(item_name,"#>",finish_goods_code)','','item_id');

auto_complete_from_db('area','area_name','area_code','','district');

//auto_complete_from_db('customer_info','customer_name_e ','customer_code',' dealer_code='.$dealer_code,'via_customer1');
$tr_from="Sales";
?>

<script language="javascript">

function count() {
    var unit_price = parseFloat(document.getElementById('unit_price').value) || 0;
    var pkt_size = parseFloat(document.getElementById('pkt_size').value) || 1; // pcs per packet
    var ctn_size = parseFloat(document.getElementById('ctn_size').value) || 1; // packets per carton

    // Read raw input values
    var ctn_unit = parseFloat(document.getElementById('ctn_unit').value) || 0; // cartons
    var pkt_unit = parseFloat(document.getElementById('pkt_unit').value) || 0; // packets
    var dist_unit = parseFloat(document.getElementById('dist_unit').value) || 0; // pieces

    // Convert everything into total pieces
    var total_unit = (ctn_unit * ctn_size * pkt_size) + (pkt_unit * pkt_size) + dist_unit;

    // Break down again into cartons, packets, and loose pieces
    var new_ctn_unit = Math.floor(total_unit / (ctn_size * pkt_size));
    var remainder_after_ctn = total_unit % (ctn_size * pkt_size);

    var new_pkt_unit = Math.floor(remainder_after_ctn / pkt_size);
    var new_dist_unit = remainder_after_ctn % pkt_size;

    // Update fields
    document.getElementById('ctn_unit').value = new_ctn_unit;
    document.getElementById('pkt_unit').value = new_pkt_unit;
    document.getElementById('dist_unit').value = new_dist_unit;

    // Calculate total amount
    var total_amt = total_unit * unit_price;
    document.getElementById('total_unit').value = total_unit.toFixed(2);
    document.getElementById('total_amt').value = total_amt.toFixed(2);
}




</script>

<script language="javascript">
	  function update_qty(id){
	  
	   var details_id = id;
	   
	  if (document.getElementById('unit_price_new').value !== '') {
        var unit_price_new = parseFloat(document.getElementById('unit_price_new').value) || 0;
        var pkt_size_new = parseFloat(document.getElementById('pkt_size_new').value) || 1; // Default pkt_size to 1 to avoid division by zero
        var dist_unit_new = parseFloat(document.getElementById('dist_unit_new').value) || 0;
        var pkt_unit_new = parseFloat(document.getElementById('pkt_unit_new').value) || 0;

        // Perform conversion only if dist_unit >= pkt_size
        if (dist_unit_new >= pkt_unit_new) {
            var additional_pkt_new = Math.floor(dist_unit_new / pkt_size_new); // Calculate how many packets can be created
            pkt_unit_new += additional_pkt_new; // Add new packets to existing packets
            dist_unit_new %= pkt_size_new; // Keep the remainder in dist_unit

            // Update the input fields with calculated values
            document.getElementById('pkt_unit_new').value = pkt_unit_new;
            document.getElementById('dist_unit_new').value = dist_unit_new;
        }
		else{
            document.getElementById('pkt_unit_new').value = 0;
            document.getElementById('dist_unit_new').value = dist_unit_new;
        }

        // Calculate total units and amount
        var total_unit_new = (pkt_unit_new * pkt_size_new) + dist_unit_new;
        var total_amt_new = total_unit_new * unit_price_new;

        // Update total_unit and total_amt fields
        document.getElementById('total_unit_new').value = total_unit_new.toFixed(2);
        document.getElementById('total_amt_new').value = total_amt_new.toFixed(2);
    }
	  }
</script>

<script language="javascript">
function data_update(id,do_no){
    var details_id = id;
	var do_no = do_no;
	
    var dist_unit = document.getElementById('dist_unit_new_' + id).value;
	
    var strURL = "update_do_details_ajax.php?do_no=" + do_no + "&details_id=" + details_id + "&dist_unit=" + dist_unit;

    var req = getXMLHTTP();
    if (req) {
        req.onreadystatechange = function () {
            if (req.readyState === 4) {
                if (req.status === 200) {
                    document.getElementById('divi_' + id).style.display = 'inline';
                    document.getElementById('divi_' + id).innerHTML = req.responseText;
                } else {
                    alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                }
            }
        };

        req.open("GET", strURL, true);
        req.send(null);
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
<style type="text/css">



/*.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited, a.ui-button, a:link.ui-button, a:visited.ui-button, .ui-button {
    color: #454545;
    text-decoration: none;
    display: none;
}*/


/*div.form-container_large input {*/
    /*width: 250px;*/
    /*height: 37px;*/
    /*border-radius: 0px !important;*/
/*}*/




.onhover:focus{
background-color:#66CBEA;

}


<!--
.style2 {
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>


	<!--DO create 2 form with table-->
	<div class="form-container_large">
		<form action="<?=$page?>" method="post" name="codz2" id="codz2" enctype="multipart/form-data">
			<!--        top form start hear-->
			<div class="container-fluid bg-form-titel">
				<div class="row">
					<!--left form-->
					<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
						<div class="container n-form2">
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Order No</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">

									<input name="do_no" type="text" id="do_no" value="<? if($$unique_master>0) echo $$unique_master; else echo (find_a_field($table_master,'max('.$unique_master.')','1')+1);?>" readonly/>


								</div>
							</div>

							<? if($do_date=="") {?>
								<div class="form-group row m-0 pb-1">

									<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text req-input">Order Date</label>
									<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
										<input name="do_date" type="text" id="do_date" value="<?=($do_date!='')?$do_date:date('Y-m-d')?>"  required />
									</div>
								</div>
							<? }?>


							<? if($do_date!="") {?>
								<div class="form-group row m-0 pb-1">

									<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Order Date</label>
									<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
										<input name="do_date" type="hidden" id="do_date" value="<?=$do_date;?>"  required/>

										<input name="do_date2" type="text" id="do_date2" value="<?=$do_date;?>" readonly="" required/>

									</div>
								</div>
							<? }?>


							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Company</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<select  id="group_for" name="group_for" class="form-control">
										<? user_company_access($group_for); ?>
									</select>
								</div>
							</div>

							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text req-input">Warehouse</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									
									
									
									<select name="depot_id" id="depot_id" required>
			 							<option></option>
                  						<? user_warehouse_access($depot_id);?>
									</select>
									
									


								</div>
							</div>
							
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Delivery Address</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="delivery_address" type="text" id="delivery_address" value="<?=$delivery_address;?>" />

								</div>
							</div>
							
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Contact Person</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="contact_person" type="text" id="contact_person" value="<?=$contact_person;?>" />

								</div>
							</div>
							
							<div class="form-group row m-0 pb-1 <?=($credit_limit>0) ? '' : 'd-none'?> " id="credit_box">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Credit Limit</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="credit_limit" type="text" id="credit_limit" value="<?=$credit_limit;?>" readonly="readonly" />

								</div>
							</div>

							<div class="form-group row m-0 pb-1 <?=($received_amt>0) ? '' : 'd-none'?> adv_box">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Advance Amount</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="received_amt" type="text" id="received_amt" value="<?=$received_amt;?>" />

								</div>
							</div>

							<div class="form-group row m-0 pb-1 <?=($receive_acc_head>0) ? '' : 'd-none'?> adv_box">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Bank/Cash Ledger</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<select name="receive_acc_head" id="receive_acc_head">
					<? foreign_relation('general_sub_ledger','sub_ledger_id','sub_ledger_name',$receive_acc_head,' type in ("Cash In Hand","Cash at Bank") and group_for='.$_SESSION['user']['group'].' ');?>

									</select>
								</div>
							</div>
							
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Remarks</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="remarks" type="text" id="remarks" value="<?=$remarks;?>" />

								</div>
							</div>
							
							

						</div>


					</div>

					<!--Right form-->
					<div class="col-sm-6 col-md-6 col-lg-6 col-xl-6">
						<div class="container n-form2">

							<? if($job_no!="") {?>
								<div class="form-group row m-0 pb-1">
									<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Job No</label>
									<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
										<input name="job_no_duplicate" type="text" id="job_no_duplicate" value="<?=$job_no?>" readonly="" tabindex="105" />
									</div>
								</div>
							<? }?>


							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text req-input">Customer</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2 ">
									<?php	if($dealer_code){ ?>
											<input type="text" name="dealer_code_combo" id="dealer_code_combo" value="<?=find_a_field('dealer_info','concat(dealer_name_e,"#",dealer_code)','dealer_code="'.$dealer_code.'"')?>" readonly />
									<?	}else{ ?>
											<input type="text" list="dealer" value="<?=find_a_field('dealer_info','concat(dealer_name_e,"#",dealer_code)','dealer_code="'.$dealer_code.'"')?>" name="dealer_code_combo" id="dealer_code_combo" onchange="get_dealer_info(this.value)" required autocomplete="off" />
										<datalist id="dealer">
											<option></option>
											<? foreign_relation('dealer_info','concat(dealer_name_e,"#",dealer_code)','""',$dealer_code_combo,'1');?>
										</datalist>

										<? } ?>
										<!--<select  id="dealer_code" name="dealer_code"  class="form-control" onchange="get_dealer_info()"  required />
										
												<option></option>
											<? foreign_relation('dealer_info','concat(dealer_name_e,"#",dealer_code)','""',$dealer_code,'1');?>

										</select>-->

									

									
								</div>
							</div>

							<div class="form-group row m-0 pb-1">

								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">VAT (%)</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="vat" type="text" id="vat" value="<?=$vat;?>" />
								</div>
							</div>
							
							<div class="form-group row m-0 pb-1">

								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">AIT (%)</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="ait" type="text" id="ait" value="<?=$ait;?>"   />
								</div>
							</div>

							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Discount (%)</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="discount" type="text" id="discount" value="<?=$discount;?>"   />
								</div>
							</div>
							
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Contact Number</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="contact_num" type="text" id="contact_num" value="<?=$contact_num;?>" />

								</div>
							</div>
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Balance</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
								<? if($dealer_code){

							

							$dealer = find_all_field('dealer_info','','dealer_code="'.$dealer_code.'"');
							
							$c_balance=find_a_field('journal','sum(dr_amt-cr_amt)','sub_ledger="'.$dealer->sub_ledger_id.'"');

							$p_chalan=find_a_field('secondary_journal','sum(dr_amt)','checked ="" and sub_ledger='.$dealer->sub_ledger_id);


							if($c_balance<0) {
								$c_balance=$c_balance*(-1);	
							}

							$do_value = find_a_field('sale_do_master m,sale_do_details d','sum(d.total_amt)','d.do_no=m.do_no and m.status in ("CHECKED","PROCESSING","UNCHECKED")  and m.dealer_code='.$dealer_code.'');


							$chalan_amt = find_a_field('sale_do_chalan d, sale_do_master m','sum(d.total_amt)','m.status in ("PROCESSING","CHECKED","COMPLETE") and m.do_no=d.do_no and d.unit_price > 0 and m.dealer_code='.$dealer_code.' ');

							$pending_amt=$do_value-$chalan_amt;
							$c_balance=$c_balance-$pending_amt-$p_chalan;

								} ?>
									<input  name="c_balance" type="text" id="c_balance" value="<?=$c_balance-$pending_amt-$p_chalan;?>" readonly="readonly" />

								</div>
							</div>
							
							
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Attachment</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="file_upload" type="file" id="file_upload" value="<?=$file_upload;?>"   />
									<?php if ($file_upload!=''){?>
		<?php /*?><a href="<?=SERVER_CORE?>core/upload_view.php?name=<?=$file_upload?>&folder=do&proj_id=<?=$_SESSION['proj_id']?>" target="_blank"><?php */?>
		<a href="../../../controllers/uploader/upload_view.php?proj_id=<?=$_SESSION['proj_id']?>&&folder=sales&&name=<?=$file_upload;?>" target="_blank">
		View Attachment</a>
			<?php } ?>
								</div>
							</div>
							
							
							
							
							 <span id="rec_filter">	    </span>
							 <?php /*?> <?php 
							 if($received_amt>0){
							 ?>
							 <div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Receive Ledger</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
								 
									<select name="rec_ledger" id="rec_ledger">
										<option value=""></option>
									<? foreign_relation('accounts_ledger','ledger_id','ledger_name',$rec_ledger,'1 and ledger_group_id in(127001,127002)');?>
									</select>

								</div>
							</div>
							<div class="form-group row m-0 pb-1">
								<label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Advance Receipt</label>
								<div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0 pr-2">
									<input  name="received_amt" type="text" id="received_amt" value="<?=$received_amt;?>" />

								</div>
							</div>
							
							<?php } ?> <?php */?>
						</div>



					</div>


				</div>

				<div class="n-form-btn-class">

					<? if($$unique_master>0) {?>
						<input name="new" type="submit" class="btn1 btn1-bg-update" value="Update Sales Order"  tabindex="12" />
						<input name="flag" id="flag" type="hidden" value="1" />
					<? }

					else{?>

						<input name="new" type="submit" class="btn1 btn1-bg-submit" value="Initiate Sales Order" tabindex="12" />
						<input name="flag" id="flag" type="hidden" value="0" />

					<? }?>

				</div>

			</div>

			<?
			$sql = 'select a.*,u.fname from approver_notes a, user_activity_management u where a.entry_by=u.user_id and master_id="'.$$unique_master.'" 
			and type in ("DO","CHALAN")';
			$row_check = mysqli_num_rows(db_query($sql));
			if($row_check>0){
			?>
			<!--return Table design start-->
			<div class="container-fluid pt-5 p-0 ">
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

			</div>

			<? } ?>
		</form>



		<? if($$unique_master>0) {?>
		<form action="<?=$page?>" method="post" name="codz2" id="codz2">
			<!--Table input one design-->
			<div class="container-fluid pt-5 p-0 ">


				<table class="table1  table-striped table-bordered table-hover table-sm">
					<thead class="thead1">
					<tr class="bgc-info">
						<th>Item Code</th>
						<th>Item Description</th>
						<th>Unit</th>

						<th>Stock</th>
						<th>Unit-Price</th>
						<th>Ctn/MB</th>
						<th>Bag/Poly</th>
						<th>Pcs</th>
						<th>Total Unit</th>
						<th>Amount</th>
						<th>Action</th>
					</tr>
					</thead>

					<tbody class="tbody1">

					<tr>
						<td id="sub">

							<?

							auto_complete_from_db('item_info','concat(item_id,"-> ",item_name)','item_id',' product_nature in ("Salable","Both") and status="Active"','item_id');

							//$do_details="SELECT a.*, i.item_name FROM sale_do_details a, item_info i WHERE  a.item_id=i.item_id and do_no=".$do_no." order by id desc limit 1";
							//$do_data = find_all_field_sql($do_details);

							?>
							<input name="item_id" type="text" value="" id="item_id" onblur="getData2('sales_invoice_ajax.php', 'so_data_found', this.value, document.getElementById('do_no').value);" autofocus />
							<!--<select  name="item_id" id="item_id"  style="width:90%;" required onchange="getData2('sales_invoice_ajax.php', 'so_data_found', this.value, document.getElementById('do_no').value);">

									<option></option>

								  <? foreign_relation('item_info','item_id','item_name',$item_id,'1');?>
							 </select>-->



							<input type="hidden" id="<?=$unique_master?>" name="<?=$unique_master?>" value="<?=$$unique_master?>"  />
							<input type="hidden" id="do_date" name="do_date" value="<?=$do_date?>"  />
							<input type="hidden" id="group_for" name="group_for" value="<?=$group_for?>"  />
							<input type="hidden" id="depot_id" name="depot_id" value="<?=$depot_id?>"  />
							<input type="hidden" id="dealer_code" name="dealer_code" value="<?=$dealer_code?>"  />
							<input name="do_date" type="hidden" id="do_date" value="<?=$do_date;?>"/>
							<input name="job_no" type="hidden" id="job_no" value="<?=$job_no;?>"/>
							<input  name="csrf_token" type="hidden" id="csrf_token" value="<?=$_SESSION['csrf_token']?>"/>					  </td>

						 <td id="so_data_found" colspan="4">

							<table  width="100%" border="1" align="left" cellpadding="0" cellspacing="2">
							<tr>
								 <td width="40%"><input name="item_name" type="text" readonly=""  autocomplete="off"  value="" id="item_name" /> </td>
								 <td width="20%"><input name="pcs_stock" type="text" readonly=""  autocomplete="off"  value="" id="pcs_stock" /></td>
								 <td width="20%"><input name="ctn_price" type="text" id="ctn_price" readonly="" required  value="<?=$do_data->ctn_price;?>" /></td>
								<td width="20%"><input name="pcs_price" type="text" id="pcs_price" readonly="" required="required"  value="<?=$do_data->pcs_price;?>"  /></td>
							</tr>
							</table>						 </td>
						 <td><input name="ctn_unit" type="text" id="ctn_unit"  required="required"  value="" onchange="count()" /></td>
					    <td>
							<input name="pkt_unit" type="text" id="pkt_unit"  required="required"  value="" onchange="count()"  />	    </td>
						<td>
							<input  name="dist_unit" type="text" class="input3" id="dist_unit" value=""  onchange="count()" /> 						</td>
						
						<td>
							<input name="total_unit" type="text" id="total_unit" readonly/>						</td>

						<td>
							<input name="total_amt" type="text" id="total_amt" readonly/>						</td>

						<td><input name="add" type="submit" id="add" value="ADD" class="btn1 btn1-bg-submit" /></td>
					</tr>
					</tbody>
				</table>





			</div>

		<? if($$unique_master>0){?>
			<!--Data multi Table design start-->
			<div class="container-fluid pt-5 p-0 ">
			<?
			  $res='select a.id,b.item_name,a.item_id,a.unit_name, a.unit_price,a.ctn_unit,a.pkt_unit,a.pkt_size,a.dist_unit, a.total_unit, 
			  a.total_amt as Net_sale, a.discount, a.vat_amt, a.total_amt_with_vat,a.do_no,a.gift_id 
			  from sale_do_details a,item_info b 
			  where b.item_id=a.item_id and a.do_no='.$$unique_master.' order by a.id';
			?>

				<table class="table1  table-striped table-bordered table-hover table-sm">
					<thead class="thead1">
					<tr class="bgc-info">
						<th>SL</th>
						<th>Item Code</th>
						<th>Item Description</th>
						<th>Unit</th>
						<th>Unit-Price</th>
						<th>Ctn/MB</th>
						<th>Bag/Poly</th>
						<th>Pcs</th>
						<th>Total Pcs</th>
						<th>Amount</th>
						<th>Action</th>
					</tr>
					</thead>

					<tbody class="tbody1">

					<?

					$i=1;

					$query = db_query($res);

					while($data=mysqli_fetch_object($query)){ 
                    $ch_exist=find_a_field('sale_do_chalan','count(id)','do_no="'.$data->do_no.'" and item_id="'.$data->item_id.'" and order_no="'.$data->id.'"');
					?>
					<tr>

						<td><?=$i++?></td>

						<td><?=$data->item_id?></td>
						<td style="text-align:left"><?=$data->item_name?>
						<?

		   $gsql = 'select g.offer_name,d.* from sale_gift_offer g, sale_do_details d where  g.id=d.gift_id and  g.id='.$data->gift_id.' and d.item_id="'.$data->item_id.'" and d.do_no="'.$data->do_no.'"';
		$gquery = db_query($gsql);
		while($qdata=mysqli_fetch_object($gquery)){
		echo '<span style="color:green;"><b>[Offer-'.$qdata->offer_name.']</b></span>';
		}
		?>

						
						</td>

						<td><?=$data->unit_name?></td>
						<td><?=$data->unit_price; ?></td>
						<td><?=$data->ctn_unit;?></td>
						<td><?=$data->pkt_unit; ?></td>
					
					<!--	onkeyup="update_qty(<?=$data->id?>)" -->	
						<td><? if($ch_exist==0){echo $data->dist_unit;} else{ ?>
						<input  name="dist_unit_new_<?=$data->id;?>" type="text" class="input3" id="dist_unit_new_<?=$data->id;?>" value="" />
						<? } ?>
						</td>
											
						<td><?=$data->total_unit; $tot_pcs +=$data->total_unit;?></td>
						<td><?=$data->Net_sale; $tot_Net_sale +=$data->Net_sale;?>

						<? $data->vat_amt; $tot_vat_amt +=$data->vat_amt;?>
						<? $data->total_amt_with_vat; $tot_total_amt_with_vat +=$data->total_amt_with_vat;?></td>
						<? if($ch_exist==0){?>
						<td><a href="?del=<?=$data->id?>">X</a></td>
						<? } else {?>
						<td><input type="button" class="btn1 btn1-bg-update" value="UPDATE" onclick="data_update(<?=$data->id?>,<?=$data->do_no?>)" /></td>
						<? } ?>
					</tr>

					<? } ?>



					<tr>
						<td colspan="7"><strong>Total:</strong></td>
						<td>&nbsp;</td>
						<td><strong><?=number_format($tot_pcs,2);?></strong></td>
						<td><strong><?=number_format($tot_Net_sale,2);?></strong></td>
						<td>&nbsp;</td>
					</tr>

					</tbody>
				</table>

			</div>
		<? }?>
		</form>


		<!--button design start-->
		<form action="select_dealer_do.php" method="post" name="cz" id="cz" onSubmit="if(!confirm('Are You Sure Execute this?')){return false;}">
			<div class="container-fluid p-0 ">

			  <div class="n-form-btn-class">
					<? 
						$available_amt = ($credit_limit+$received_amt)+$c_balance;
						$tot_Net_sale;
					?>
					<input name="delete"  type="submit" class="btn1 btn1-bg-cancel" value="DELETE SO" />
					<input  name="do_no" type="hidden" id="do_no" value="<?=$$unique_master?>"/>
					<input  name="do_date" type="hidden" id="do_date" value="<?=$do_date?>"/>

					<? if(($dealer->credit_limit_appli=='YES') && ($tot_Net_sale <= $available_amt)){ ?>
					<input name="confirm" type="submit" class="btn1 btn1-bg-submit" value="CONFIRM SO" />
						<? }elseif($dealer->credit_limit_appli=='NO') { ?>
						<input name="confirm" type="submit" class="btn1 btn1-bg-submit" value="CONFIRM SO."  />
						<? } else{ echo "Check credit limit activition or Balance ..."; } ?>
				</div>

			</div>
		</form>

		<? }?>
	</div>




<script>
 $(document).ready(function() {
   window.get_dealer_info = function(dealer) {
     $.ajax({
       url: 'dealer_info_ajax.php', 
       type: 'POST',
       data: {
         dealer: dealer
       },
       success: function(response) {
         var res = JSON.parse(response);
         if(res) {
           if(res['credit_limit_appli']=='YES' && res['credit_limit']>0) {
             $('#credit_box').removeClass('d-none');
           }
           if(res['bank_reconsila']=='YES') {
             $('.adv_box').removeClass('d-none');
           } else {
             $('#received_amt').val('');
			 $('.adv_box').addClass('d-none');

           }
           $('#credit_limit').val(res['credit_limit']);
           $('#c_balance').val(res['pending_amt']);

		   


         } else {
           $('#credit_box').addClass('d-none');
           $('#credit_limit').val('');
           $('#c_balance').val('');
           $('.adv_box').addClass('d-none');
		   $('#c_balance').val('');
         }
       },
       error: function(xhr, status, error) {
         console.error(error); 
       }
     });
   }
 });
</script>



<?

require_once SERVER_CORE."routing/layout.bottom.php";

?>