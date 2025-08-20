<?php
session_start();
//====================== EOF ===================
//var_dump($_SESSION);

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";


$pr_no 		=url_decode(str_replace(' ', '+', $_REQUEST['v_no']));

$group_data = find_all_field('user_group','group_name','id='.$_SESSION['user']['group']);

$datas=find_all_field('purchase_receive','s','pr_no='.$pr_no);
$module_name = find_a_field('user_module_manage','module_file','id='.$_SESSION["mod"]);


$sql1="select b.* from purchase_receive b where b.pr_no = '".$pr_no."'";

$data1=db_query($sql1);

$pi=0;

$total=0;

while($info=mysqli_fetch_object($data1)){ 

$pi++;

$rec_date=$info->rec_date;

$rec_no=$info->rec_no;

$po_no=$info->po_no;

$sale_no=$info->sale_no;

$order_no[]=$info->order_no;

$truck_no=$info->truck_no;

$ch_no=$info->ch_no;

$qc_by=$info->qc_by;

$remarks=$info->remarks;

$entry_at=$info->entry_at;

$entry_by=$info->entry_by;

$item_id[] = $info->item_id;

$rate[] = $info->rate;

$amount[] = $info->amount;
$rec_no=$info->rec_no;

$garden[]=find_a_field('tea_garden','garden_name','garden_id='.$info->garden_id);

$shed[]=find_a_field('tea_warehouse','warehouse_nickname','warehouse_id='.$info->shed_id);

$lot_no[]=$info->lot_no;

$invoice_no[]=$info->invoice_no;

$liquor_mark[]=$info->quality;

$pkgs[]=$info->pkgs;

$tpkgs+=$info->pkgs;

$sam_pay[]=$info->sam_pay;

$sam_qty[]=$info->sam_qty;

$secondary_carton=find_a_field('item_info','carton_qty','item_id='.$info->item_id);
         if ($secondary_carton > 0)
		 $ctnyy[]=$info->qty/$secondary_carton;

$unit_qty[] = $info->qty;

$tot_unit_qty+= $info->qty;

$unit_name[] = $info->unit_name;

}

$ssql = 'select a.* from vendor a, purchase_master b where a.vendor_id=b.vendor_id and b.po_no='.$po_no;

$dealer = find_all_field_sql($ssql);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Cash Memo :.</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
<script type="text/javascript">
	function hide()
	{
		document.getElementById("pr").style.display="none";
		document.getElementById("pr1").style.display="none";
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
			<!--<span><h5 style="letter-spacing:1px;">Quality product at affordable cost</h5></span>-->
			
			<?=$group_data->address?><br>
			Cell: <?=$group_data->mobile?>. Email: <?=$group_data->email?> <br> <?=$group_data->vat_reg?>
		</div>
		<div class="col-2"></div>
	</div>
	
	   <div class="text-center" >
              <button class="btn btn-default outline border rounded-pill border border-dark  text-black "><h4 style="font-size:15px;font-weight:bold; margin:0 auto;">GOODS RECEIVE NOTE</h4></button>
            </div><br />
	<div class="row">
      <div class="col-6">
		    
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">GRN No: </span>
			  </div>
			  <input type="text" class="form-control" readonly="readonly" value="<?php echo $pr_no;?>">
			</div>
			 
			 		<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">GRN Date</span>
			  </div>
			  <input type="text" class="form-control" readonly="readonly" value="<?=$rec_date?>">
			</div>
				
			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">Requisition No:</span>
			  </div>
			
			   <input type="text" disabled class="form-control" id="no" value=" <?php echo $req_all->req_no;?>">
			</div>
			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;"> Supplier :</span>
			  </div>
			   <input type="text" disabled class="form-control" id="no" value="  <?php echo $dealer->vendor_name;?>">
			</div>
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;"> Address :</span>
			  </div>
			   <input type="text" disabled class="form-control" id="no" value="  <?php echo $dealer->address;?>">
			</div>
					<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">Truck No: </span>
			  </div>
			  <input type="text" disabled class="form-control" id="no" value=" <?php echo $truck_no;?>">
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
			  <?php 
			  $po_all=find_all_field('purchase_master','','po_no="'.$po_no.'"');
			  $req_all=find_all_field('requisition_master','','req_no="'.$po_all->req_no.'"');
			  ?>
			   <input type="text" disabled class="form-control" id="no" value=" <?php echo $po_no;?>">
			</div>
			
	<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">PO Date</span>
			  </div>
			  <input type="text" class="form-control" readonly="readonly" value="<?=$po_all->po_date?>">
			</div>
			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">Requisition Date</span>
			  </div>
			  <input type="text" class="form-control" readonly="readonly" value="<?=$req_all->req_date?>">
			</div>
			
				<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">Manual Rec No: </span>
			  </div>
			  <input type="text" disabled class="form-control" id="no" value=" <?php echo $rec_no;?>">
			</div>
			
			
				<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">QC By: </span>
			  </div>
			  <input type="text" disabled class="form-control" id="no" value="<?php echo $qc_by;?>">
			</div>
			
			
			<div class="input-group mb-3 input-group-sm">
			  <div class="input-group-prepend">
				<span class="input-group-text"  style="font-weight:bold;">Supplier Challan No: </span>
			  </div>
			  <input type="text" disabled class="form-control" id="ch_no" value="<?php echo $ch_no;?>">
			</div>
			
		
		<table id="pr1" cellpadding="3" >
			


<td>Chalan View :</td>
<td><?php if ($datas->upload_chalan_1!=''){?>
<a href="<?=SERVER_CORE?>core/upload_view.php?name=<?=$datas->upload_chalan_1?>&folder=mrr_chalan_1&proj_id=<?=$_SESSION['proj_id']?>&mod=<?=$module_name?>" target="_blank">Chalan 1 </a>
<?php } ?>

</br>

<?php if ($datas->upload_chalan_2!=''){?>
<a href="<?=SERVER_CORE?>core/upload_view.php?name=<?=$datas->upload_chalan_2?>&folder=mrr_chalan_2&proj_id=<?=$_SESSION['proj_id']?>&mod=<?=$module_name?>" target="_blank">Chalan 2 </a>
<?php } ?>
</br>
<?php if ($datas->upload_chalan_3!=''){?>
<a href="<?=SERVER_CORE?>core/upload_view.php?name=<?=$datas->upload_chalan_3?>&folder=mrr_chalan_3&proj_id=<?=$_SESSION['proj_id']?>&mod=<?=$module_name?>" target="_blank">Chalan 3  </a>
<?php } ?></br>
</td>
</tr>

<tr>
<td align="right">Purchase Order: </td>
<td><a href="../../../../app/views/purchase_mod/po/po_print_view.php?po_no=<?=rawurlencode(url_encode($po_no));?>" target="_blank"><?=$po_no; ?></a></td>
</tr>
			
			
			</table>
			
		  </div>
              
            </div>

  

      

<table class="table table-bordered table-condensed">
<thead>

       <tr>

        <th>SL</th>
        <th>Item Code</th>
        <th >Item Name</th>
        <th >UOM</th>
		<th>Rate</th>
        <th>Received Qty</th>
		<th>Amount</th>

<!-- 		<th>UOM-2</th>
		 <th>UOM-2 Qty</th>
        <th>Remarks</th>-->
        </tr>
</thead>
       
<tbody>
<? for($i=0;$i<$pi;$i++){?>

     
      <tr style="text-align:center;">

        <td><?=$i+1?></td>
        <td><?=find_a_field('item_info','finish_goods_code','item_id='.$item_id[$i]);?></td>
        <td><?=find_a_field('item_info','item_name','item_id='.$item_id[$i]);?></td>
        <td><?=find_a_field('item_info','unit_name','item_id='.$item_id[$i]);?></td>
		<td><?=number_format($rate[$i],2)?></td>
    
        <td><?php 
		echo $unit=number_format($unit_qty[$i],2)."<br>"; 
		 ?></td>

        <td><?=number_format($amount[$i],2); $t_amount = $t_amount + $amount[$i];?></td>
		
		
<!--		<td><?=find_a_field('item_info','pack_unit','item_id='.$item_id[$i]); ?></td>
		      <td><?php echo number_format($ctnyy[$i],2);?></td>
		<td><?=$remarks ;?></td>-->
	
        </tr>

<? }?>

  <tr><td colspan="6"><div align="right"><strong>Total Amount: </strong></div></td>

       
 
<!--        <td align="center" valign="top">
		<span class="style1">
          <?=number_format($tot_unit_qty,2)?>
        </span></td>-->
		
        <td align="center" valign="top">&nbsp; <?=number_format($t_amount,2);?></td>
			
			</tr>
			
			
			<? if($po_ms->tax>0) {?>
      <tr  align="right">
        <td colspan="6">VAT (<?=number_format($po_ms->tax,2)?>%):</td>
        <td align="right">          <?  echo number_format($tax_total=(($t_amount*$po_ms->tax)/100),2);?>        </td>
      </tr>
	  <? }?>
	  
	  <? if($po_ms->ait>0) {?>
      <tr  align="right">
        <td colspan="6">AIT (<?=number_format($po_ms->ait,0)?>%):</td>
        <td align="right">          <?  echo number_format($ait_total=(($t_amount*$po_ms->ait)/100),2);?>        </td>
      </tr>
	  <? }?>
	  
	  
	  <? if($datas->transport_charge>0) {?>
      <tr  align="right">
        <td colspan="6">Transport Charge:</td>
        <td align="right">          <?  echo number_format($transport_charge=$datas->transport_charge,2);?>        </td>
      </tr>
	  <? }?>
	  
	  <? if($datas->other_charge>0) {?>
      <tr  align="right">
        <td colspan="6">Other Charge:</td>
        <td align="right">          <?  echo number_format($other_charge=$datas->other_charge,2);?>        </td>
      </tr>
	  <? }?>
	  
	  <tr>
        <td align="right" colspan="6"><strong>Net Amount: </strong></td>
        <td align="right"><strong><? echo number_format($payable_amount=($t_amount+$tax_total+$ait_total+$transport_charge+$other_charge),2);?></strong></td>
      </tr>
	  
	  
	  
		</tbody>
		
    </table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>
    <td colspan="2" style="font-size:12px"><em>All goods are received in a good condition as per Terms</em></td>
    </tr>
    </table>
	
	<br /><br />
	<div class="row">
 			<div class="col-1"></div>
              <div class="col-2 text-center">
               <b><?php 
			   $qc_all=find_a_field('qc_receive_purchase','qc_by','qc_no="'.$datas->qc_no.'"');
			  echo find_a_field('user_activity_management','fname','user_id="'.$datas->entry_by.'"');?></b>
                <br>
               <p style="border-top:1px solid"> Received By </p>
                
              </div>
			  
			  <div class="col-1"></div>
              <div class="col-2 text-center">
               <b><?php 
			   	  echo find_a_field('user_activity_management','fname','user_id="'.$qc_all.'"');
			   ?></b>
                <br>
               <p style="border-top:1px solid"> Quality Controller </p>
                
              </div>
			  
			  	<div class="col-1"></div>
              <div class="col-2 text-center">
               <b><?php echo find_a_field('user_activity_management','fname','user_id="'.$datas->post_pur_verify.'"');?></b>
                <br>
               <p style="border-top:1px solid"> Store Manager </p>
                
              </div>
			  	<div class="col-1"></div>
              <div class="col-2 text-center">
               <b>&nbsp;</b>
                <br>
               <p style="border-top:1px solid"> Authorized By </p>
                
              </div>
	
			  <?php include("../../../assets/template/report_print_buttom_content.php");?>
            </div>

</div>
</body>

</html>

