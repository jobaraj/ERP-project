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
	<?php include("../../../../public/assets/css/theme_responsib_new_table_report.php");?>
</head>

<body>
  
<div class="body">
	<div class="header">
		<table class="table1">
		<tr>
		<td class="logo">
			<img src="<?=SERVER_ROOT?>public/uploads/logo/<?=$_SESSION['proj_id']?>.png" class="logo-img"/>
		</td>
		
		<td class="titel">
				<h2 class="text-titel"> <?=$group_data->group_name?> </h2>			
				<p class="text"><?=$group_data->address?></p>
				<p class="text">Cell: <?=$group_data->mobile?>. Email: <?=$group_data->email?> <br> <?=$group_data->vat_reg?></p>
		</td>
		
		
		<td class="Qrl_code">
					<?='<img class="barcode Qrl_code_barcode" alt="'.$barcodeText.'" src="barcode.php?text='.$barcodeText.'&codetype='.$barcodeType.'&orientation='.$barcodeDisplay.'&size='.$barcodeSize.'&print='.$printText.'"/>' ?>
			<p class="qrl-text"><?php echo $master->po_no;?></p>
		</td>
		
		</tr>
		 
		</table>
	</div>
	
	<div class="header-one">
	<hr class="hr"/>
		<h5 class="report-titel">PURCHASE ORDER</h5>
	<br>


	<div class="row">

		<div class="col-md-6 col-sm-6 col-lg-6 left">
			
			<p class="left-text">Purchase Order No: <span><?php echo $master->po_no;?></span></p>
			<p class="left-text">Purchase Order Date: <span><?php echo $master->po_date;?></span></p>
			<p class="left-text">Purchase Requisition No: <span> <?php echo $master->req_no;?></span></p>
			
			<div class="new-div">
				<p class="titel-text">Business Partner</p>
				<p class="text1">Vendor Code: <span> <?php echo find_a_field('vendor','vendor_id','vendor_id="'.$master->vendor_id.'"');?></span></p>
				<p class="text1">Vendor Name: <span><?php echo find_a_field('vendor','vendor_name','vendor_id="'.$master->vendor_id.'"');?></span></p>
				<p class="text1">Vendor Address: <span><?php echo find_a_field('vendor','address','vendor_id="'.$master->vendor_id.'"');?></span></p>
				<p class="text1">Vendor Contact No: <span><?php echo find_a_field('vendor','contact_no','vendor_id="'.$master->vendor_id.'"');?> </span></p>
				<p class="text1">Vendor Email: <span><?php echo find_a_field('vendor','email','vendor_id="'.$master->vendor_id.'"');?> </span></p>
			</div>

		</div>

		<div class="col-md-6 col-sm-6 col-lg-6 right">

			<p class="right-text">RFQ No: <span><?php echo $master->quotation_no;?>  </span></p>
			<p class="right-text">Purchaser Code: <span>[<?php echo $master->entry_by;?>] 
			<?=find_a_field('user_activity_management','fname','user_id='.$master->entry_by)?>  </span></p>
			<p class="right-text">Purchaser Email ID: <span> <?=find_a_field('user_activity_management','email','user_id='.$master->entry_by)?>  </span></p>
			
			<div class="new-div">
				<p class="titel-text">Bill To</p>

				<p class="text1">
					</br>
					<span class="bold">
						<?=$group_data->group_name?>
					</span><br/>

					<span >
						 Address: <?=$group_data->address?>

					</span>
					<br/><br/><br/>

				</p>

			</div>
		</div>
	</div>


	<p class="p-text">
		Dear Sir/Madam,
			<br/>
		We are pleased to issue Purchase Order for the following goods/services as per below mentioned terms & conditions:
			<br />
	</p>	

</div>


<div class="main-content">
	
	
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
          </nobr>	    
		  </div>
      </div>
	  
	  
	  
	  
	  
	  
	<table class="table1">
		<thead>
			<tr>
				<th>SL</th>
				<th class="w-8">Item Code</th>
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
				<td style="text-align:center;"><?=$sl?></td>
				<td style="text-align:center;"><?=$item_code?></td>
				<td><?=$item?></td>
				<td style="text-align:center;"><?=$unit_name?></td>
				<td style="text-align:right;"><?=$qty?></td>
				<td style="text-align:right;"><?=number_format($rate,2)?></td>
				<td style="text-align:right;"><?=number_format($amount,2)?></td>
			  </tr>
		<? }?>
		
		
		
			  <tr>
				<td align="right" colspan="6" style="text-align:right;"><strong>Sub Total:</strong></td>
				<td align="right" style="text-align:right;"><strong><?php echo number_format($total,2);?></strong></td>
			  </tr>
			  <? if($master->tax>0) {?>
			  <tr  align="right">
				<td colspan="6" style="text-align:right;"><strong>VAT (<?=number_format($master->tax,2)?>%):</strong></td>
				<td align="right" style="text-align:right;"><strong>
				  <?  echo number_format($tax_total=(($total*$master->tax)/100),2);?>
				</strong></td>
			  </tr>
			  <? }?>
			  
			  <? if($data->ait>0) {?>
			  <tr  align="right">
				<td colspan="6" style=" text-align:right;"><strong>AIT (<?=number_format($data->ait,0)?>%):</strong></td>
				<td align="right" style="text-align:right;"><strong>
				  <?  echo number_format($ait_total=(($total*$data->ait)/100),2);?>
				</strong></td>
			  </tr>
			  <? }?>
			  
			  <tr >
				<td   align="right" colspan="6" style="text-align:right;"><strong>Net Amount: </strong></td>
				<td align="right" style="text-align:right;"><strong><? echo number_format($payable_amount=($total+$tax_total+$ait_total),2);?></strong></td>
			  </tr>
			  
		
			</tbody>
		
    </table>
	

	<p class="p bold">In Words : 
		<? $scs =  $payable_amount;
					$credit_amt = explode('.',$scs);
	
		 if($credit_amt[0]>0){
		  echo convertNumberToWordsForIndia($credit_amt[0]);}
	
			 if($credit_amt[1]>0){
			 if($credit_amt[1]<10) $credit_amt[1] = $credit_amt[1]*10;
			 echo  ' & '.convertNumberToWordsForIndia($credit_amt[1]).' paisa ';
								 }
		  echo ' Only.';

		?> 
		</p>
	
	<table>
	<tr <?=($sl%20==0)? 'class="page-break"' : '' ?> >
	</tr>
	</table>
	
	
	

<p class="p-text"> The Vendor has agreed to provide the above goods/services to <?=$group_data->group_name?> agreed to accept the merchandise from the Vendor in the quantities, at the prices, at the time and subject to the terms, provisions and conditions stated below: <p>


	<table class="terms-table">
			<tr>
			  <td colspan="4" class="terms-titel">Terms &amp; Condition : </td>
			</tr>
			
			 <? 		
			$sql = 'select * from terms_condition_setup where type="PO" group by id order by sl_no';
				$tc_query=db_query($sql);
				while($tc_data= mysqli_fetch_object($tc_query)){
				?>		
			<tr style="letter-spacing:.3px; line-height: 16px;">
			  <td class="text-center w-5" ><?=++$id;?>.</td>
			  <td class="text-left w-95"><?=$tc_data->terms_condition;?></td>
			  </tr> 
			 <? }?>
	</table>
	
	<p class="p-text">	For <?=$group_data->group_name?>. </p>
	
</div>




	 
<div class="footer"  id="footer">
	<table class="footer-table">
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>

        <tr>
          <td class="text-center w-25">
		  <p style="font-weight:600; margin: 0;"> <?=find_a_field('user_activity_management','fname','user_id='.$master->entry_by);?> </p>
		  <p style="font-size:11px; margin: 0;">(<?=find_a_field('user_activity_management','designation','user_id='.$master->entry_by);?>) <br/> <?=$master->entry_at?></p>
		  </td>
          <td class="text-center w-25">&nbsp;</td>
          <td class="text-center w-25">&nbsp;</td>
          <td class="text-center w-25">&nbsp;</td>
        </tr>
        <tr>
          <td class="text-center">-------------------------------</td>
          <td class="text-center">-------------------------------</td>
          <td class="text-center">-------------------------------</td>
          <td class="text-center">-------------------------------</td>
        </tr>
        <tr>
          <td class="text-center"><strong>Prepared By</strong></td>
          <td class="text-center"><strong>Store Incharge</strong></td>
          <td class="text-center"><strong>Production Manager</strong></td>
          <td class="text-center"><strong>Executive Director</strong></td>
        </tr>
	
	
	
		<tr>
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
		  </tr>
	</table>

	  </div>
	  
	  
	  
</div>
</body>
</html>