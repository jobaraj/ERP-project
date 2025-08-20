<?php 

 

 

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
require_once ('../../../acc_mod/common/class.numbertoword.php');
$po_no=$_GET['po_no'];


	$req_bar_no = $po_no;
	$barcode_content = $req_bar_no;
	$barcodeText = $barcode_content;
    $barcodeType='code128';
	$barcodeDisplay='horizontal';
    $barcodeSize=40;
    $printText='';


$group_data = find_all_field('user_group','group_name','id='.$_SESSION['user']['group']);
$master= find_all_field('purchase_master','*','po_no='.$po_no);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Invoice View</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <style>
  table .table-bordered  thead  tr  th{
  border:1px solid black !important;
}
 table .table-bordered  tbody  tr, table .table-bordered  tbody td{
  border:1px solid black !important;
}

body{
	font-size:16px;
}
#pr input[type="button"] {
	background-color: #c1f4ff;
	color: #333;
	font-weight: bolder;
	border-radius: 5px;
	border: 1px solid #333;
	cursor: pointer;
}

@media print {
thead {display: table-header-group;}
table tr.page-break{
  page-break-after:always
} 

}

@media print {
/*    .header {
        position: fixed;
        top: 0;
    }
	*/
/*	.mid{
    	margin-top: 170px;
	    margin-bottom: 170px;
	}*/
	#pr{
	display:none;
	
	}
	
    .footer {
        position: fixed;
        bottom: 0;
		width: 100%;
    }
}
  </style>
</head>
<script type="text/javascript">
function hide()
{
<?php /*?>    document.getElementById("pr").style.display="none";<?php */?>
<?php /*?>	document.getElementById('footer').style.position="fixed";<?php */?>
<?php /*?>	document.getElementById('footer').style.display="contents";	<?php */?>    
<?php /*?>	document.getElementById('footer').style.width="100%";
	document.getElementById('footer').style.bottom="0px";<?php */?>
}
</script>
<body>
  
<div style="width:1186px;margin:0 auto;">

	<table width="100%">
	<tr>
	<td style="width:10%;">
		<table width="100%">
				<tr>
				<td>
				<img src="<?=SERVER_ROOT?>public/uploads/logo/<?=$_SESSION['proj_id']?>.png" style=" width:180%" />
				</td>
				</tr>
		</table>
		
	</td>
	
	<td style="width:80%">
	<table width="100%">
	<tr>
			<th style="text-align:center; font-size:30px; font-family:tahoma;"> <?=$group_data->group_name?> </th>			
		</tr>
		<tr>
			<th style="text-align:center;"><?=$group_data->address?></th>
		</tr>
		<tr>
			<th style="text-align:center;">Cell: <?=$group_data->mobile?>. Email: <?=$group_data->email?> <br> <?=$group_data->vat_reg?></th>
		</tr>
		</table>
	</td>
	
	<td style="width:10%;">
		<table width="100%">
				<tr>
				<td>
				<?='<img style="font-size: 12px; width: 200px; height: 50px;" class="barcode" alt="'.$barcodeText.'" src="barcode.php?text='.$barcodeText.'&codetype='.$barcodeType.'&orientation='.$barcodeDisplay.'&size='.$barcodeSize.'&print='.$printText.'"/>' ?>
				</td>
				</tr>
		</table>
		
	</td>
	
	</tr>
	 
	</table>
	<hr style="color:black;border:2px solid black;" />
		<h5 align="center" style="font-weight:bold">PURCHASE ORDER</h5>
	<br>


	<div class="row" style="width:100%; margin:0px;">

		<div class="col-md-6 col-sm-6 col-lg-6" style="width:50%; float: left;">
			
			<p style="font-weight:bold; border: 1px solid black; margin: 0px; padding:2px;">Purchase Order No: <span style="font-weight: normal"><?php echo $master->po_no;?></span></p>
			<p style="font-weight:bold; border: 1px solid black; margin: 0px; padding:2px;">Purchase Order Date: <span style="font-weight: normal"><?php echo $master->po_date;?></span></p>
			<p style="font-weight:bold; border: 1px solid black; margin: 0px; padding:2px;">Purchase Requisition No: <span style="font-weight: normal"> <?php echo $master->req_no;?></span></p>
			
			<br/>
			<div style="border: 1px solid black;">
				<p style="font-weight: bold; text-align: center; border-bottom: 1px solid black; margin: 0px;">Business Partner</p>


				<p style="font-weight:bold; margin: 0px; padding:2px;">Vendor Code: <span style="font-weight: normal"> <?php echo find_a_field('vendor','vendor_id','vendor_id="'.$master->vendor_id.'"');?></span></p>
				<p style="font-weight:bold; margin: 0px; padding:2px;">Vendor Name: <span style="font-weight: normal"><?php echo find_a_field('vendor','vendor_name','vendor_id="'.$master->vendor_id.'"');?></span></p>
				<p style="font-weight:bold; margin: 0px; padding:2px;">Vendor Address: <span style="font-weight: normal"><?php echo find_a_field('vendor','address','vendor_id="'.$master->vendor_id.'"');?></span></p>
				<p style="font-weight:bold; margin: 0px; padding:2px;">Vendor Contact No: <span style="font-weight: normal"><?php echo find_a_field('vendor','contact_no','vendor_id="'.$master->vendor_id.'"');?> </span></p>
				<p style="font-weight:bold; margin: 0px; padding:2px;">Vendor Email: <span style="font-weight: normal"><?php echo find_a_field('vendor','email','vendor_id="'.$master->vendor_id.'"');?> </span></p>


			</div>

		</div>

		<div class="col-md-6 col-sm-6 col-lg-6"  style="width:50%; float: right;">

			<p style="font-weight:bold; border: 1px solid black; margin: 0px; padding:2px;">RFQ No: <span style="font-weight: normal"><?php echo $master->quotation_no;?>  </span></p>
			<p style="font-weight:bold; border: 1px solid black; margin: 0px; padding:2px;">Purchaser Code: <span style="font-weight: normal">[<?php echo $master->entry_by;?>] 
			<?=find_a_field('user_activity_management','fname','user_id='.$master->entry_by)?>  </span></p>
			<p style="font-weight:bold; border: 1px solid black; margin: 0px; padding:2px;">Purchaser Email ID: <span style="font-weight: normal"> <?=find_a_field('user_activity_management','email','user_id='.$master->entry_by)?>  </span></p>
			<br>
			<div style="border: 1px solid black;">
				<p style="font-weight: bold; text-align: center; border-bottom: 1px solid black; margin: 0px;">Bill To</p>

				<p style="margin: 0px;">
					</br>
					<span style="font-weight: bold; padding:2px;">
						<?=$group_data->group_name?>
					</span><br/>

					<span style="padding:2px;">
						 Address: <?=$group_data->address?>

					</span>
					<br/><br/><br/>

				</p>

			</div>


		</div>


	</div>

	<br>
<!--	<hr style="color:black;border:2px solid black;" />-->
	<span>Dear Sir/Madam,
		<br/>
		We are pleased to issue Purchase Order for the following goods/services as per below mentioned terms & conditions:

		<br />
		</span>
		<div id="pr">
        <div align="left">
          <p>
            <input name="button" type="button" onClick="hide();window.print();" value="Print" />
          </p>
          <nobr>
          <!--<a href="chalan_bill_view.php?v_no=<?=$_REQUEST['v_no']?>">Bill</a>&nbsp;&nbsp;--><!--<a href="do_view.php?v_no=<?=$_REQUEST['v_no']?>" target="_blank"><span style="display:inline-block; font-size:14px; color: #0033FF;">Bill Copy</span></a>-->
          </nobr>
		  <nobr>
          
          <!--<a href="chalan_bill_distributor_vat_copy.php?v_no=<?=$_REQUEST['v_no']?>" target="_blank">Vat Copy</a>-->
          </nobr>	    </div>
      </div>
	<table class="table table-bordered table-sm" style="border: 2px solid black;">
		<thead >
			<tr style="border: 2px solid black; text-align:center">
				<th style="border: 2px solid black;">SL</th>
				<th style="border: 2px solid black;" width="8%">Item Code</th>
				<th style="border: 2px solid black;">Item Name</th>
				<th style="border: 2px solid black;">UOM</th>
				<th style="border: 2px solid black;">Quantity</th>
				<th style="border: 2px solid black;">Unit Price</th>
				<th style="border: 2px solid black;">Amount</th>
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

<tr style="border: 2px solid black;">
        <td style="border: 2px solid black; text-align:center;"><?=$sl?></td>
		<td style="border: 2px solid black; text-align:center;"><?=$item_code?></td>
        <td style="border: 2px solid black;"><?=$item?></td>
        <td style="border: 2px solid black; text-align:center;"><?=$unit_name?></td>
        <td style="border: 2px solid black; text-align:right;"><?=$qty?></td>
        <td style="border: 2px solid black; text-align:right;"><?=number_format($rate,2)?></td>
        <td style="border: 2px solid black; text-align:right;"><?=number_format($amount,2)?></td>
      </tr>
<? }?>



      <tr>
        <td align="right" colspan="6" style="border: 2px solid black; text-align:right;"><strong>Sub Total:</strong></td>
        <td align="right" style="border: 2px solid black; text-align:right;"><strong><?php echo number_format($total,2);?></strong></td>
      </tr>
	  <? if($master->tax>0) {?>
      <tr  align="right">
        <td colspan="6" style="border: 2px solid black; text-align:right;"><strong>VAT (<?=number_format($master->tax,2)?>%):</strong></td>
        <td align="right" style="border: 2px solid black; text-align:right;"><strong>
          <?  echo number_format($tax_total=(($total*$master->tax)/100),2);?>
        </strong></td>
      </tr>
	  <? }?>
	  
	  <? if($data->ait>0) {?>
      <tr  align="right">
        <td colspan="6" style="border: 2px solid black; text-align:right;"><strong>AIT (<?=number_format($data->ait,0)?>%):</strong></td>
        <td align="right" style="border: 2px solid black; text-align:right;"><strong>
          <?  echo number_format($ait_total=(($total*$data->ait)/100),2);?>
        </strong></td>
      </tr>
	  <? }?>
	  
      <tr >
        <td   align="right" colspan="6" style="border: 2px solid black; text-align:right;"><strong>Net Amount: </strong></td>
        <td align="right" style="border: 2px solid black; text-align:right;"><strong><? echo number_format($payable_amount=($total+$tax_total+$ait_total),2);?></strong></td>
      </tr>
	  

		</tbody>
		
    </table>
	

	<span>
		<p style="font-weight:bold;">In Words : <? $scs =  $payable_amount;
				$credit_amt = explode('.',$scs);

	 if($credit_amt[0]>0){
	  echo convertNumberToWordsForIndia($credit_amt[0]);}

		 if($credit_amt[1]>0){
		 if($credit_amt[1]<10) $credit_amt[1] = $credit_amt[1]*10;
		 echo  ' & '.convertNumberToWordsForIndia($credit_amt[1]).' paisa ';
							 }
	  echo ' Only.';

		?> </p>
	</span><br>
	<table>
	<tr <?=($sl%20==0)? 'class="page-break"' : '' ?> >
	</tr>
	</table>
	<span>
		<p>
	The Vendor has agreed to provide the above goods/services to <?=$group_data->group_name?> agreed to accept the merchandise from the Vendor in the quantities, at the prices, at the time and subject to the terms, provisions and conditions stated below:
	<br>
	
	
<table>
        <tr>
          <td colspan="4" align="left" style=" font-weight:600; letter-spacing:.3px; text-transform:uppercase;">Terms &amp; Condition : </td>
        </tr>
		
		 <? 		
		$sql = 'select * from terms_condition_setup where type="PO" group by id order by sl_no';
			$tc_query=db_query($sql);
			while($tc_data= mysqli_fetch_object($tc_query)){
			?>		
		<tr style="letter-spacing:.3px; line-height: 16px;">
		  <td width="5%" align="center" ><?=++$id;?>.</td>
		  <td width="95%" align="left"><?=$tc_data->terms_condition;?></td>
		  </tr>        
         <? }?>
        
</table>
	
	<br>
	<br>
	For <?=$group_data->group_name?>.
		</p>
	</span>




	 
<div class="footer"  id="footer">
	
	<table width="100%" cellspacing="0" cellpadding="0"  >
	

        <tr>
          <td colspan="4" align="right">&nbsp;</td>
        </tr>

        <tr>
          <td width="25%" align="center">
		  <p style="font-weight:600; margin: 0;"> <?=find_a_field('user_activity_management','fname','user_id='.$master->entry_by);?> </p>
		  <p style="font-size:11px; margin: 0;">(<?=find_a_field('user_activity_management','designation','user_id='.$master->entry_by);?>) <br/> <?=$master->entry_at?></p>
		  </td>
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
	
	
	
		<tr>
		  <td colspan="4">  	
	<hr style="color:black;border:1px solid black;" />
	<table width="100%" cellspacing="0" cellpadding="0">
			<tr style=" font-size: 12px; font-weight: 500;">
				<td style="width:33%; text-align:left">Printed by: <?=find_a_field('user_activity_management','user_id','user_id='.$_SESSION['user']['id'])?></td>
				<td style="width:33%; text-align:center"><?=date("h:i A")?></td>
				<td style="width:33%; text-align:right"><?=date("d-m-Y")?></td>
				
				
			</tr>
			<tr>
			<td colspan="4" style="text-align: center;font-size: 11px;color: #444;"> This is an ERP generated report. That is Powered By www.erp.com.bd</td>
			</tr>
	</table>
		  </td>
		  </tr>
		

	</table>

	  </div>
	  
	  
	  
</div>
</body>
</html>