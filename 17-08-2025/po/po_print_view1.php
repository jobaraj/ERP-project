<?php

session_start();

//====================== EOF ===================



 

 

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

//require "../../../engine/tools/class.numbertoword.php";

require_once ('../../../acc_mod/common/class.numbertoword.php');

$po_no		= $_REQUEST['po_no'];
$group_data = find_all_field('user_group','group_name','id='.$_SESSION['user']['group']);
if(isset($_POST['cash_discount']))
{
	$po_no = $_POST['po_no'];
	$cash_discount = $_POST['cash_discount'];
	$ssql='update purchase_master set cash_discount="'.$_POST['cash_discount'].'" where po_no="'.$po_no.'"';
	db_query($ssql);
}

$sql1="select * from purchase_master where po_no='$po_no'";
$data=mysqli_fetch_object(db_query($sql1));
$vendor=find_all_field('vendor','','vendor_id='.$data->vendor_id );
$whouse=find_all_field('warehouse','','warehouse_id='.$data->warehouse_id);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Purchase Order :.</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
	<link href="../../../assets/css/invoice.css" type="text/css" rel="stylesheet"/>
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
<script type="text/javascript">
	function hide()
	{
		document.getElementById("pr").style.display="none";
	}
</script>

<style>

table.table-bordered > thead > tr > th{
  border:1px solid black;
  font-size:12px;
}
table.table-bordered > tbody > tr > td{
  border:1px solid black;
    font-size:12px;
}

   .mb-3{
margin-bottom:4px!important;
}
.input-group-text{
font-size:12px;
}
      * {
    margin: 0;
    padding: 0;
	font-size:13px;
  }
  p {
    margin: 0;
    padding: 0;
  }
  h1,
  h2,
  h3,
  h4,
  h5,
  h6
   {
    margin: 0 !important;
    padding: 0 !important;
  }
  

#pr input[type="button"] {
	width: 70px;
	height: 25px;
	background-color: #6cff36;
	color: #333;
	font-weight: bolder;
	border-radius: 5px;
	border: 1px solid #333;
	cursor: pointer;
}
    </style>
</head>

<body style="font-family:Tahoma, Geneva, sans-serif">

<div class="container">
	<div class="row">
	
		<div class="col-2 text-center">
			<img src="<?=SERVER_ROOT?>public/uploads/logo/<?=$_SESSION['proj_id']?>.png"  width="100%" />
		</div>
		
		<div class="col-8 text-center">
			<h1 style="font-family:tahoma;"><?=$group_data->group_name?> </h1>			
			<?=$group_data->address?><br>
			Cell: <?=$group_data->mobile?>. Email: <?=$group_data->email?> <br> <?=$group_data->vat_reg?>
		</div>
		<div class="col-2"></div>
	</div>
	
	   <div class="text-center" >
              <button class="btn btn-default outline border rounded-pill border border-dark  text-black "><h4 style="font-size:15px;font-weight:bold; margin:0 auto;">PURCHASE ORDER</h4></button>
            </div><br />
	<div class="row">
      <div class="col-6">
		    			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;"> Supplier Name :</span>
			  </div>
			   <input type="text" disabled class="form-control" id="no" value="<?= $vendor->vendor_name;?>">
			</div>
					    			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;"> Contract Person :</span>
			  </div>
			   <input type="text" disabled class="form-control" id="no" value="<?= $vendor->contact_person_name;?>">
			</div>
			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">Mobile No : </span>
			  </div>
			  <input type="text" disabled class="form-control" id="no" value="<?= $vendor->contact_no;?>">
			</div>
			
			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;"> Address :</span>
			  </div>
			   <input type="text" disabled class="form-control" id="no" value="<?= $vendor->address;?>">
			</div>
			

			
			 
			<div id="pr">
			  <div align="left">
					<input name="button" type="button" onclick="hide();window.print();" value="Print" />
			  </div>
		    </div>

			
		  </div>
		  <div class="col-6">
		    

			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">PO No:</span>
			  </div>
			   <input type="text" disabled class="form-control" id="no" value="<?=$_GET['po_no']?>">
			</div>
			
	<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">PO Date</span>
			  </div>
			  <input type="text" class="form-control" readonly="readonly" value="<?= date("d-m-Y",strtotime($data->po_date));?>">
			</div>
			
			

		  </div>
              
            </div>

  

      

<table class="table table-bordered table-condensed">
<thead>
       <tr>
			<th>SL</th>
			<th>Item Code</th>
			<th>Item Name</th>
			<th>UOM</th>
			<th>Quantity</th>
			<th>Unit Price</th>
			<th>Amount</th>
        </tr>
</thead>
       
<tbody>
<?php
	$final_amt=0;
	$pi=0;
	$total=0;
	$sql2="select * from purchase_invoice where po_no='$po_no'";
	$data2=db_query($sql2);
	//echo $sql2;
	while($info=mysqli_fetch_object($data2)){
	$pi++;
	$amount=$info->qty*$info->rate;
	$total=$total+($info->qty*$info->rate);
	$supplementary_duty_amt=(($total*$data->supplementary_duty)/100);
	$net_total= ($total+$supplementary_duty_amt);
	$sl=$pi;
	//$item=find_a_field('item_info','concat(item_name," ; ",	item_description)','item_id='.$info->item_id);
	$item=find_a_field('item_info','concat(item_name)','item_id='.$info->item_id);
	$item_code=find_a_field('item_info','finish_goods_code','item_id='.$info->item_id);
	$fg_code=find_a_field('item_info','concat(finish_goods_code)','item_id='.$info->item_id);
	$sku_code=find_a_field('item_info','concat(sku_code)','item_id='.$info->item_id);
	$qty=$info->qty;
	$unit_name=find_a_field('item_info','concat(unit_name)','item_id='.$info->item_id);
	$rate=$info->rate;
	$disc=$info->disc;
?>

<tr>
        <td><?=$sl?></td>
		<td><?=$item_code?></td>
        <td><?=$item?></td>
        <td><?=$unit_name?></td>
        <td><?=$qty?></td>
        <td><?=number_format($rate,2)?></td>
        <td><?=number_format($amount,2)?></td>
      </tr>
<? }?>



      <tr>
        <td align="right" colspan="6"><strong>Sub Total:</strong></td>
        <td align="left"><strong><?php echo number_format($total,2);?></strong></td>
      </tr>
	  <? if($data->tax>0) {?>
      <tr  align="right">
        <td colspan="6"><strong>VAT (<?=number_format($data->tax,2)?>%):</strong></td>
        <td align="left"><strong>
          <?  echo number_format($tax_total=(($total*$data->tax)/100),2);?>
        </strong></td>
      </tr>
	  <? }?>
	  
	  <? if($data->ait>0) {?>
      <tr  align="right">
        <td colspan="6"><strong>AIT (<?=number_format($data->ait,0)?>%):</strong></td>
        <td align="left"><strong>
          <?  echo number_format($ait_total=(($total*$data->ait)/100),2);?>
        </strong></td>
      </tr>
	  <? }?>
      <tr>
        <td   align="right" colspan="6"><strong>Net Amount: </strong></td>
        <td align="left"><strong><? echo number_format($payable_amount=($total+$tax_total+$ait_total),2);?></strong></td>
      </tr>
	  

		</tbody>
		
    </table>
	
	<div class="row col-12">
	<table width="100%" border="0" bordercolor="#000000" cellspacing="3" cellpadding="3" class="tabledesign1" >



        <tr style=" font-weight:500; letter-spacing:.3px;">
          <td colspan="4" width="100%">&nbsp;</td>
        </tr>
        <tr style="font-size:16px; font-weight:500; letter-spacing:.3px;">



		<td colspan="4">
		
		In Word: <?

		

		$scs =  $payable_amount;

			 $credit_amt = explode('.',$scs);

	 if($credit_amt[0]>0){

	 

	 echo convertNumberToWordsForIndia($credit_amt[0]);}

	 if($credit_amt[1]>0){

	 if($credit_amt[1]<10) $credit_amt[1] = $credit_amt[1]*10;

	 echo  ' & '.convertNumberToWordsForIndia($credit_amt[1]).' paisa. ';}

	 echo ' Only';

		?>.		</td>
          </tr>





        <tr>
          <td colspan="4" align="right">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
          </tr>
        <tr>
          <td colspan="4" align="right">&nbsp;</td>
          </tr>

        <tr>
          <td width="25%" align="center"><?=find_a_field('user_activity_management','fname','user_id='.$data->entry_by);?></td>
          <td  width="25%" align="center">&nbsp;</td>
          <td width="25%" align="center">&nbsp;</td>
          <td width="25%" align="center">&nbsp;</td>
        </tr>
        <tr>
          <td align="center">-------------------------------</td>
          <td align="center">-------------------------------</td>
          <td align="center">-------------------------------</td>
          <td align="center">-------------------------------</td>
        </tr>
        <tr>
          <td align="center"><strong>Prepared By</strong></td>
          <td align="center"><strong>Store Incharge</strong></td>
          <td align="center"><strong>Production Manager</strong></td>
          <td align="center"><strong>Executive Director</strong></td>
        </tr>
      </table>
	

	</div>
	
	
	
</div>
</body>

</html>

