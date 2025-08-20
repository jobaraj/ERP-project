<?php 
require_once "../../../assets/template/layout.top.php";
require_once ('../../../acc_mod/common/class.numbertoword.php');
$po_no=$_GET['po_no'];
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
  <!--<style>
  table .table-bordered  thead  tr  th{
  border:2px solid black;
}
 table.table-bordered  tbody  tr  td{
  border:2px solid black;
}
body{
	font-size:16px;
}


@media print {
	thead {display: table-header-group;}
	table tr.page-break{
	  page-break-after:always
	} 
	
	#pr{
	display:none;
	
	}

    .footer {
        position: fixed;
        bottom: 0;
		width: 100%;
		page-break-after: always;
    }
	
	#footer:after {
    counter-increment: page;
    content: counter(page);
}

#footer:after {
    counter-increment: page;
    content:"Page " counter(page);
    left: 0; 
    top: 100%;
	font-size: 12px;
    white-space: nowrap; 
    z-index: 20;
    -moz-border-radius: 5px; 
    -moz-box-shadow: 0px 0px 4px #222;  
    background-image: -moz-linear-gradient(top, #eeeeee, #cccccc);  
  }
  
}

  </style>-->
  

</head>
<script type="text/javascript">
function hide()
{
//  document.getElementById("pr").style.display="none";
//	document.getElementById('footer').style.position="absolute";
//	document.getElementById('footer').style.width="100%";
//	document.getElementById('footer').style.bottom="0px";
}
</script>

<body>
  
<div style="width:1186px;margin:0 auto;">

	<table width="100%" style="margin-top:6%">
	<tr>
	<td style="width:20%;">
		<img src="../../../logo/<?=$_SESSION['user']['group']?>.png" style=" width:80%" />
	</td>
	
	<td width="60%">
		<h2 style="padding:0px; margin: 0px; text-align:center; font-weight:bold;">RADIANT CARE LIMITED</h2>
		<p style="padding:0px; margin: 0px; text-align:center;"> <strong>Factory Address: </strong> Dorikandi, Bar Para, PS-Sonargaon, Dist-Narayanganj </p>
		<p style="padding:0px; margin: 0px; text-align:center;"> <strong>CHQ Address:</strong> Office No.03, Level-08, SKS Tower, 7 VIP Road, Mohakhali, Dhaka-1206, Bangladesh</p>
	
	</td>
	<td width="20%"> </td>
	</tr>
	 
	</table>
	<hr style="color:black;border:2px solid black;" />
	<br>


	<div class="row">

		<div class="col-md-6">
			
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

		<div class="col-md-6">

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
						Radiant care Limited
					</span><br/>

					<span style="padding:2px;">
						CHQ Address: Office No.03,Level-08,SKS Tower,7 VIP Road,Mohakhali,Dhaka-1206, Bangladesh

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
			<tr style="border: 2px solid black;">
				<th style="text-align:center; border: 2px solid black !important; ">SL No</th>
				<th style="text-align:center; border: 2px solid black !important;">Code</th>
				<th style="text-align:center; border: 2px solid black !important;">Item</th>
				<th style="text-align:center; border: 2px solid black !important;">Discription</th>
				<th style="text-align:center; border: 2px solid black !important;">Type</th>
				<th style="text-align:center; border: 2px solid black !important;">Specification</th>
				<th style="text-align:center; border: 2px solid black !important;">UOM</th>
				<th style="text-align:center; border: 2px solid black !important;">Quantity</th>
				<th style="text-align:center; border: 2px solid black !important;">Rate@TK</th>
				<th style="text-align:center; border: 2px solid black !important;">VAT(%)</th>
				<th style="text-align:center; border: 2px solid black !important;">Value With VAT in BDT</th>
				<th style="text-align:center; border: 2px solid black !important;">Special Note</th>
				<th style="text-align:center; border: 2px solid black !important;">Delivery Schedule</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		$sql='select * from purchase_invoice where po_no="'.$po_no.'"';
		$query=mysql_query($sql);
		while($row=mysql_fetch_object($query)){
			$item_all=find_all_field('item_info','*','item_id="'.$row->item_id.'"');
			//$specification = find_a_field('requisition_order','specification','req_no="'.$row->req_no.'" and item_id="'.$row->item_id.'"');
			$SL = ++$i;
		?>
			<tr style="border: 2px solid black;">
				<td style="border-right: 2px solid black !important;"><?php echo $SL;?></td>
				<td style="border: 2px solid black !important;"><?php echo $item_all->finish_goods_code;?></td>
				<td style="border: 2px solid black !important;"><?php echo $item_all->item_name;?></td>
				<td style="border: 2px solid black !important; width: 20%;"><?php echo $row->discription;?></td>
				<td style="border: 2px solid black !important;"><?php echo find_a_field('item_sub_group','sub_group_name','sub_group_id='.$item_all->sub_group_id);?></td>
				<td style="border: 2px solid black !important; width: 20%;"><?php echo $item_all->item_description;?></td>
				<td style="border: 2px solid black !important;"><?php echo $item_all->unit_name;?></td>
				<td style="border: 2px solid black !important;"><?php echo $row->qty;?></td>
				<td style="border: 2px solid black !important;"><?php echo $row->rate;?></td>
				<td style="border: 2px solid black !important;"><?php echo $master->tax;?></td>
				<td style="border: 2px solid black !important;"><?php echo number_format($amount=($row->amount+(($row->amount*$master->tax)/100)),4);?></td>
				<td style="border: 2px solid black !important; width: 20%;"><?php echo $row->special;?></td>
				<td style="border: 2px solid black !important;"><?php echo $row->delivery_schedule;?></td>
			</tr>
			<?php 
			$tot_amount+=$amount;
			$t_quty+=$row->qty;
			} ?>
			<tr>
				<td colspan="10" style="height:40px; border: 2px solid black !important;">NOTE:</td>
			</tr>
			
			<tr style="font-weight:bold; border: 2px solid black !important;">
				<td colspan="9" style="text-align:right !important;">PO Wise Total Value in BDT :</td>
				
				<!--<td style="font-weight:bold; border: 2px solid black !important;"><?=number_format($t_quty,4)?></td>-->
				
				<td style="font-weight:bold; border: 2px solid black !important;"><?=number_format($tot_amount,4)?></td>
				<td style="font-weight:bold; border: 2px solid black !important;"></td>
			</tr>
		</tbody>
	</table>

	<span>
		<p style="font-weight:bold;">In Words in TK : <? $scs =  $tot_amount;
				$credit_amt = explode('.',$scs);

	 if($credit_amt[0]>0){
	  echo convertNumberToWordsForIndia($credit_amt[0]);}

		 if($credit_amt[1]>0){
		 if($credit_amt[1]<10) $credit_amt[1] = $credit_amt[1]*10;
		 echo  ' & '.convertNumberToWordsForIndia($credit_amt[1]).' paisa ';
							 }
	  echo ' Only.';

		?> </p>
	</span>
	<span>
		<p>
	The Vendor has agreed to provide the above goods/services to RADIANT CARE LIMITED agreed to accept the merchandise from the Vendor in the quantities, at the prices, at the time and subject to the terms, provisions and conditions stated below:<br>
	1)  Delivery Location: <?php echo find_a_field('purchase_master','deli_point','po_no="'.$master->po_no.'"');?><br>
	2)  Carrying Instruction:<br>
	3)  Please send all delivery Challan/Legal Documents/COA/MSDS/TDS along with goods during delivery.<br>
	4)  Payment will be made within 30 days for Raw Materials & 45 days for Packaging Materials after receiving the original Commercial Invoices.<br>
	5)  The Machine Proof of all Packaging Materials must be approved in written by the assigned Authority of RADIANT CARE LIMITED before execution of work order.<br>
	6)  The Supply can be totally rejected or the unit price can be reduced in case of failure to deliver the materials in due time.<br>
	7) 	Statutory (VAT & AIT) deduction will be made at source as per Govt. Law.<br>
	8) 	Radiant Care Limited authority reserves the right to revise/modify/cancel any terms and conditions of the purchase order or can cancel a part or entire purchase order with prior notice otherwise consultation with supplier.<br>
	9) BIN : 004931924-0304. Darikandi, Bar Para, Sonargaon PS, Narayanganj-1440, Bangladesh.<br><br>
	For Radiant Care Limited.
		</p>
	</span>



<?php 
if(5>=$SL){
?>
<style>
@media print {
	table tr.page-break{
	  page-break-after:always
	} 
	
	#pr{
	display:none;
	
	}

    .footer {
/*        position: fixed;
        bottom: 0;
		width: 100%;
		page-break-after: always;*/

        position: relative;
    	width: 1186px;
		page-break-after: always;
    }  
}
</style>
<?php
}
else{
 ?>
<style>
@media print {
	table tr.page-break{
	  page-break-after:always
	} 
	
	#pr{
	display:none;
	}
	
    .footer {
        position: absolute;
    	width: 1186px;
		page-break-after: always;
    }
}
</style>
 <? } ?>


	 
<div class="footer" id="footer" width="100%">
	
	<table width="100%" cellspacing="0" cellpadding="0">
	
	<?php
		   $amount_range=find_a_field('purchase_invoice','sum(amount)','po_no="'.$po_no.'"');
		   ?>
		<tr>
		  <td align="center" >&nbsp;</td>
		  <td align="center">&nbsp;</td>
		  <?php 
		  if($amount_range>50000){
		  ?>
		  <td align="center">&nbsp;</td>
		  <?php 
		  if($amount_range>=200001){
		  ?>
		  <td align="center">&nbsp;</td>
		  <?php } } ?>
		  </tr>
		<tr>
		  <td align="center" >&nbsp;</td>
		  <td align="center">&nbsp;</td>
		<?php 
		  if($amount_range>50000){
		  ?>
		  <td align="center">&nbsp;</td>
		    <?php 
		  if($amount_range>=200001){
		  ?>
		  <td align="center">&nbsp;</td>
		  <?php } } ?>
		  </tr>
		<tr>
		  <td align="center" >&nbsp;</td>
		  <td align="center">&nbsp;</td>
		 <?php 
		  if($amount_range>50000){
		  ?>
		  <td align="center">&nbsp;</td>
		    <?php 
		  if($amount_range>=200001){
		  ?>
		  <td align="center">&nbsp;</td>
		  <?php } } ?>
		  </tr>
		   <tr>
		 	  <td align="center" >
			  <p style="border-bottom: 1px solid #333;margin: 0px 15%;" align="center"> &nbsp;
			  <?php
		 $ucid=find_a_field('purchase_master','entry_by','po_no="'.$po_no.'"');
		   echo find_a_field('user_activity_management','fname','user_id="'.$ucid.'"')?>
		   </p>

		   </td>
		   
		   
		     <td align="center">
			 			  <p style="border-bottom: 1px solid #333;margin: 0px 15%;" align="center"> &nbsp;
		  <?php
		  
		 $lm_id=find_a_field('purchase_master','lm_approve','po_no="'.$po_no.'"');
		   echo find_a_field('user_activity_management','fname','user_id="'.$lm_id.'"'); ?>		
		   </p>
		     </td>
		  <td align="center">
		  	
		   
		   <?php
		   $signecher = find_a_field('purchase_master','buh_approve','po_no="'.$po_no.'"');
		    if($signecher > 0){?>
			
		  	<p style="border-bottom: 1px solid #333;margin: 0px 15%;" align="center">&nbsp;
				<?php
				 
				 
				 $buh_id=find_a_field('purchase_master','buh_approve','po_no="'.$po_no.'"');
		  		 echo find_a_field('user_activity_management','fname','user_id="'.$buh_id.'"')
		   		?>	
		   	</p>
			
		  <? } else{ ?>
			<p style="border-bottom: 1px solid #333;margin: 0px 15%;" align="center">&nbsp;
				<?php
				 $gm_id=find_a_field('purchase_master','gm_approver','po_no="'.$po_no.'"');
		  		 echo find_a_field('user_activity_management','fname','user_id="'.$gm_id.'"')
		   		?>	
		   </p>
		  <? }?>	 
		   
		    </td>
		 <?php 
		  if($amount_range>50000){
		  ?>
		    <?php 
		  if($amount_range>=200001){
		  ?>
		  <td align="center">
		  			  <p style="border-bottom: 1px solid #333;margin: 0px 15%;" align="center"> &nbsp;
		  <?php
		  
		 $md_id=find_a_field('purchase_master','md_approve','po_no="'.$po_no.'"');
		   echo find_a_field('user_activity_management','fname','user_id="'.$md_id.'"')?>
		   </p>
		   </td>
		   <?php } } ?>
		   
		  </tr>
		<!--<tr>
		  <td align="center">_______________________________</td>
		  <td align="center">_______________________________</td>
		   <?php 
		  if($amount_range>50000){
		  ?>
		  <td align="center">_______________________________</td>
		    <?php 
		  if($amount_range>=200001){
		  ?>
		  <td align="center">_______________________________</td>
		  <?php }} ?>
		 
		  </tr>-->
<!--		<tr>
		  <td align="center"></td>
		  <td align="center"></td>
		  <td align="center"></td>
		  <td align="center"></td>
		 
		  </tr>-->
		<tr style="font-size:12px">
            <td align="center" width="25%"><strong>Desk Officer</strong></td>
		    
			<td  align="center" width="25%"><strong>Line Manager</strong></td>
			 <?php 
			 $ed_gm = find_a_field('purchase_master','buh_approve','po_no="'.$po_no.'"');
		
		  ?>
		  <?php if($ed_gm > 0){?>
		  	<td  align="center" width="25%"><strong>CEO</strong></td>
		  <? } else{ ?>

			<td  align="center" width="25%"><strong>GM</strong></td>
		  <? }?>

			  <?php 
		  if($amount_range>=200001){
		  ?>
		    <td  align="center" width="25%"><strong>Managing Director</strong></td>
			<?php  }?>
		   
		    </tr>

		  
		  <tr>
		  <td colspan="4">

	  	
	<hr style="color:black;border:1px solid black;" />
	<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
				<td style="width:33%; text-align:left">Printed by: <?=find_a_field('user_activity_management','user_id','user_id='.$_SESSION['user']['id'])?></td>
				<td style="width:33%; text-align:center"><?=date("h:i A")?></td>
				<td style="width:33%; text-align:right"><?=date("d-m-Y")?></td>
				
			
			</tr>
	</table>

		  
		  </td>
		  
		  </tr>
		

	</table>

	  </div>
	  
	  
	  
</div>
</body>
</html>