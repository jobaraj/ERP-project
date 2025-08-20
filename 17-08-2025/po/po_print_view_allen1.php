<?php 
require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";


$po_no = url_decode($_GET['po_no']);


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
  <?php include("../../../../public/assets/css/theme_responsib_new_table_report1.php");?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link href="../css/invoice.css" type="text/css" rel="stylesheet"/>
  <style>
  
  
	body {
    width: 1186px;
    margin: 0 auto;
    font-size: 16px;
}
@font-face {
  font-family: 'TradeGothicLTStd-Extended';
  src: url('TradeGothicLTStd-Extended.otf'); 
}
table {
  width: 100%;
  border-collapse: collapse;
}

td {
  /*border: 1px solid #ddd;*/
  word-break: break-all;
}


@media print {
	body{
		width:  100% !important;
		font-size: 18px !important;
	 }
}
  </style>
</head>
<body style="font-family: Poppins, serif;">

<table width="100%" cellspacing="0" cellpadding="2" border="0">
  <thead>
  <tr>
    <td><table width="100%" cellspacing="0" cellpadding="2" border="0">
      <tr>
        <td width="20%" ><img src="<?=SERVER_ROOT?>public/uploads/logo/<?=$_SESSION['proj_id']?>.png" class="logo-img"/></td>
        <td width="60%" align="center">
				<p style="font-size:28px; color:#000000; margin:0; padding: 0 0 5px 0; text-transform:uppercase;  font-weight:700; font-family: 'TradeGothicLTStd-Extended';"> <?=$group_data->group_name?> </p>			  
				<p class="text"><strong>Address: </strong><?=$group_data->address?></p>
				<p class="text"><strong>Cell:</strong> <?=$group_data->mobile?>. <strong>Email: </strong><i><?=$group_data->email?> </i><br><strong> <?=$group_data->vat_reg?> </strong></p>
		</td>
        <td class="qrl-text">
			<?='<img class="barcode Qrl_code_barcode mt-4" alt="'.$barcodeText.'" src="barcode.php?text='.$barcodeText.'&codetype='.$barcodeType.'&orientation='.$barcodeDisplay.'&size='.$barcodeSize.'&print='.$printText.'"/>' ?>
			<p style="font-size:14px; padding: 2px 15px 0px 0px; letter-spacing:7px;"><?php echo $master->po_no;?></p>
		</td>
		
      </tr>
	 
    </table>      </td>
  </tr>
   <tr><td><hr class="hr mt-1 mb-1"/></td></tr>
  </thead>
  <tbody>
  <tr>
    <td>
	<table width="100%" cellspacing="0" cellpadding="2" border="0">
      <tr>
        <td>
		<h5 class=" text-center font-weight-bold mt-0 ml-0 mb-2 ">PURCHASE ORDER</h5>
		
		</td>
      </tr>
      <tr>
        <td>
		<table width="100%" cellspacing="0" cellpadding="2" border="0" >
          <tr>
            <td width="40%">
				<table>
				<tr>
				
				<td colspan="2" > <strong> <?php echo find_a_field('vendor','vendor_name','vendor_id="'.$master->vendor_id.'"');?> (<?php echo find_a_field('vendor','vendor_id','vendor_id="'.$master->vendor_id.'"');?>)</strong></td>
				</tr>
				<tr>
					<td width="32%">Address </td>
					<td width="68%"> : <?php $address= find_a_field('vendor','address','vendor_id="'.$master->vendor_id.'"');
					echo !empty($address) ? $address: 'N/A';
					?></td>
				</tr>
				<tr>
    		<td>Contact No. </td>
    		<td>: <?php 
			$contact_no = find_a_field('vendor', 'contact_no', 'vendor_id="'.$master->vendor_id.'"');
			echo !empty($contact_no) ? $contact_no : 'N/A'; 
			?></td>
			</tr>

				<tr>
					<td>Email</td>
					<td>:
					<?php $email = find_a_field('vendor','email','vendor_id="'.$master->vendor_id.'"');
					echo !empty($email) ? $email : 'N/A'; 
					?>
					</td>
				</tr>
				</table>
				</td>
            <td width="20%"></td>
            <td width="40%" >
				<table width="100%" cellspacing="0" cellpadding="2" border="0">
				
				<tr>
					<td width="35%"><strong>PO No.</strong></td>
					<td width="65%">: <strong><?php echo $master->po_no;?></strong></td>
				</tr>
				<tr>
					<td >PO Date</td>
					<td >: <?php echo $master->po_date;?></td>
				</tr>
				<tr>
   				 <td>PR No.</td>
   				 <td>: <?php echo !empty($master->req_no) ? $master->req_no : 'N/A'; ?></td>
				</tr>

				<tr>
					<td>PR Date</td>
					<td>: <?php echo !empty($master->req_date) ? $master->req_date : 'N/A'; ?></td>
				</tr>
				<?php /*?><tr>
					<td><p class="text1 mb-0"><strong>Purchase Order No: </strong><?php echo $master->po_no;?></p>
			<p class="text1 mb-0">Purchase Order Date: <?php echo $master->po_date;?></p>
			<p class="text1">Purchase Requisition No:  <?php echo $master->req_no;?></p></td>
				</tr><?php */?>
				</table>
			</td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><p class="p-text  ">
		Dear Sir/Maam,
			<br/>
		We are pleased to issue Purchase Order for the following goods as per below mentioned terms & conditions:</p>	
	</td>
      </tr>
      <tr>
        <td>
		<div id="pr">
        <div align="left">
          <p>
            <input name="button" type="button" onClick="hide();window.print();" value="Print" />
          </p>	    
		  </div>
      </div>
		<table width="100%" cellspacing="0" cellpadding="3" border="1">
		<tr>
				<th class="text-center">SL</th>
			<!--	<th class="w-10 text-center">Item Code</th>-->
				<th colspan="2" class="w-25 ">Item Description</th>
				<th class="text-center w-10">UOM</th>
				<th class="text-center">Quantity</th>
				<th class="text-center w-15">Unit Price</th>
				<th class="text-center">Amount</th>
			</tr>
			
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
				<td style="text-align:center;"><?=$sl?></td>
				<!--<td style="text-align:center;"><?php /*?><?=$item_code?><?php */?></td>-->
				<td colspan="2"><?=$item_code?>-<?=$item?></td>
				<td class="text-center"><?=$unit_name?></td>
				<td class="text-right"><?=$qty?></td>
				<td class="text-right"><?=number_format($rate,2)?></td>
				<td class="text-right"  ><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong><?=number_format($amount,2)?></td>
			  </tr>
		<? }?>
		
		
		
			  <tr>
				<td align="right" colspan="6" style="text-align:right;"><strong>Sub Total(BDT):</strong></td>
				<td align="right" style="text-align:right;"><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong><strong><?php echo number_format($total,2);?></strong></td>
			  </tr>
			  <? if($master->cash_discount>0) {?>
			  <tr  align="right">
				<td colspan="6" style="text-align:right;"><strong>Discount(%) (<?=number_format($master->cash_discount,2)?>%):</strong></td>
				<td align="right" style="text-align:right; "><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong>
				  <?  echo number_format($discount_total=(($total*$master->cash_discount)/100),2);?>
				</td>
			  </tr>
			  <? }?>
			  <? if($master->vat>0) {?>
			  <tr  align="right">
				<td colspan="6" style="text-align:right;"><strong>VAT (<?=number_format($master->vat,2)?>%):</strong></td>
				<td align="right" style="text-align:right;  "><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong><strong>
				  <?  echo number_format($vat_total=(($total*$master->vat)/100),2);?>
				</strong></td>
			  </tr>
			  <? }?>
			   <? if($master->tax>0) {?>
			  <tr  align="right">
				<td colspan="6" style="text-align:right;"><strong>Tax (<?=number_format($master->tax,2)?>%):</strong></td>
				<td align="right" style="text-align:right;  "><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong><strong>
				  <?  echo number_format($tax_total=(($total*$master->tax)/100),2);?>
				</strong></td>
			  </tr>
			  <? }?>
			  <? if($master->ait>0) {?>
			  <tr  align="right">
				<td colspan="6" style="text-align:right;"><strong>Ait(%) (<?=number_format($master->ait,2)?>%):</strong></td>
				<td align="right" style="text-align:right;  "><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong><strong>
				  <?  echo number_format($ait_total=(($total*$master->ait)/100),2);?>
				</strong></td>
			  </tr>
			  <? }?>
			  
			  
			  
			  
			  <tr >
				<td   align="right" colspan="6" style="text-align:right;"><strong>Net Amount(BDT): </strong></td>
				<td align="right" style="text-align:right;  "><strong><?=find_a_field('currency_type','icon','currency_type="BDT"');?> </strong> <strong><? echo number_format($payable_amount=($total+$tax_total+$ait_total+$vat_total-$discount_total),2);?></strong></td>
			  </tr>
			  
        
        </table></td>
      </tr>
      <tr>
        <td><p class="p mb-1 mt-1"><strong>In Words : </strong>
		<?php
$currency_type = 'taka'; // Set this to 'taka' or 'dollar' based on user input

$scs =  $payable_amount;
$credit_amt = explode('.',$scs);

if($credit_amt[0]>0){
    echo convertNumberToWordsForIndia($credit_amt[0]);
    echo ' ' . ($currency_type == 'taka' ? 'Taka' : 'Dollars');
}

if($credit_amt[1]>0){
    if($credit_amt[1]<10) $credit_amt[1] = $credit_amt[1]*10;
    echo ' & ' . convertNumberToWordsForIndia($credit_amt[1]);
    echo ' ' . ($currency_type == 'taka' ? 'Paisa' : 'Cents');
}

echo ' Only.';
?>
		</p></td>
      </tr>
      <tr>
        <td >
		<table width="100%" cellspacing="0" cellpadding="7" border="1" class="mb-1">
			<tr>
				<td><p class=" mb-1 ml-1 mt-1"><strong>Remarks: </strong>Good Products</p></td>
				

			</tr>
		</table></td>
      </tr>
      <tr>
         <td colspan="4" class="terms-titel">Terms &amp; Condition : </td>
      </tr>
	  
	  <tr>
		  <td>
			<table width="100%" cellspacing="0" cellpadding="2" border="0" >
			  <? $sql = 'select * from terms_condition_setup where type="PO" order by sl_no';
						$tc_query=db_query($sql);
						while($tc_data= mysqli_fetch_object($tc_query)){
				?>	
							  <tr style="letter-spacing:.3px; line-height: 22px;">
							  	 <td width="2%">&nbsp;</td>
								 <td width="1%"><?=$tc_data->sl_no;?>.</td>
								 <td width="18%"><?=$tc_data->category;?></td>
								 <td width="1%"> : </td>
								 <td width="78%"> <?=$tc_data->terms_condition;?></td>
								 
							  </tr>
							 
						   <? }?>
				</table>		  
			</td>
	   </tr>

    </table></td>
	
  </tr>
  <tr>
  <td><p class="p-text mt-2 mb-5">	Thanking You, </p></td>
  </tr>
   
  <?
 $res_approval_member = '
SELECT a.*,u.fname,am.layer_name,am.signatory_tittle FROM approval_matrix_history a JOIN user_activity_management u ON a.approved_by=u.user_id JOIN approval_matrix_layer_setup am ON a.approval_level=am.layer_level  WHERE a.tr_no = "'.$po_no.'" and a.tr_from="Purchase" and a.status="1" ORDER BY a.approval_level DESC;
';



$query_approval_member=db_query($res_approval_member);
$query_approval_member2=db_query($res_approval_member);
$query_approval_member3=db_query($res_approval_member);



?>
  <tr class="mb-10">
    <td >
	<table width="100%" cellspacing="0" cellpadding="2" border="0" style=" margin-bottom: 100px;">
      <tr>
        <td class="text-center w-25">
		<p style="font-weight:600; margin: 0;" class="text-center"> <?=find_a_field('user_activity_management','fname','user_id='.$master->entry_by);?> </p>
		  <p style="font-size:11px; margin: 0;" class="text-center">(<?=find_a_field('user_activity_management','designation','user_id='.$master->entry_by);?>) <br/> <?=$master->entry_at?></p>
		</td>
		 <? While($row4=mysqli_fetch_object($query_approval_member3)){ ?>
          <td class="text-center w-25">
			<? if (!empty($row4->approved_at)) {?>
					<p style="font-weight:600; margin: 0;"> <?=$row4->fname?> </p>
					<p style="font-size:11px; margin: 0;">(<?=$row4->layer_name?>) <br/> <?=$row4->approved_at?></p>
		  <? } else { echo "Not Applicable"; }?>


		  </td>
	 <? } ?>
      </tr>
	   <tr>
          <td class="text-center">---------------------</td>
		  <?
		  While($row2=mysqli_fetch_object($query_approval_member)){ 

		  ?>
              <td class="text-center">---------------------</td>
		  <? } ?>

        </tr>
		
        <tr>
          <td class="text-center"><strong>Prepared By</strong></td>
		  <?
		While($row3=mysqli_fetch_object($query_approval_member2)){ 
			

		  ?>

          <td class="text-center"><strong><?=$row3->signatory_tittle?></strong></td>
		  
		  <? } ?>
         <!--<td class="text-center" ><strong>Production Manager</strong>
          <td class="text-center"><strong>Executive Director</strong></td> -->
        </tr>
    </table></td>
  </tr>
 </tbody>
   <tfoot>

  <tr>
    <td>
	
	

		
			<table width="100%" cellspacing="0" cellpadding="2" border="0" class="teg">
				<tr style="letter-spacing:.3px; line-height: 22px;">
					<td colspan="3">Note: </td>
				</tr>
			  <? $sql = 'select * from print_note_setup where type="PO" order by sl_no';
						$tc_query=db_query($sql);
						while($tc_data= mysqli_fetch_object($tc_query)){
				?>	
							  <tr style="letter-spacing:.3px; line-height: 22px;">
							  	 <td width="2%">&nbsp;</td>
								 <td width="1%"><?=$tc_data->sl_no;?>.</td>
								 <td width="81%"> <?=$tc_data->terms_condition;?></td>
							  </tr>
							 
						   <? }?>
				</table>	
		
  </tr>
  <tr>
			<td colspan="4"><?php //include("../../../../assets/template/report_print_buttom_content.php");?></td>
		</tr>
		  <td colspan="4">  	
				<hr style="color:black;border:1px solid black;" />
				<table width="100%" cellspacing="0" cellpadding="0">
						<tr style=" font-size: 12px; font-weight: 500;">
							<td class="text-left w-33">Printed by: <?=find_a_field('user_activity_management','user_id','user_id='.$_SESSION['user']['id'])?></td>
							<td class="text-center w-33"><?=date("h:i A")?></td>
							<td class="text-right w-33"><?=date("d-m-Y")?></td>
						</tr>
						
						
						<tr>
						<td colspan="4" style="text-align: center;font-size: 11px;color: #444;"> This is an ERP generated report. That is Powered By www.erp.com.bd</td>
						</tr>
				</table>
		  </td>
 
  
  
    </tfoot>
</table>
<?php /*?><?php include("../../../controllers/routing/report_print_buttom_content.php");?><?php */?>
    <script>
        // JavaScript for page number counting
        function updatePageNumber() {
            var pageNumberElement = document.getElementById('footer');
            var totalPages = document.querySelectorAll('.pagedjs_page').length;

            pageNumberElement.textContent = 'Page ' + window.pagedConfig.pageCount + ' of ' + totalPages;
        }

        // Call the updatePageNumber function when the page is loaded and after printing
        window.onload = updatePageNumber;
        window.onafterprint = updatePageNumber;
    </script>
</body>
</html>