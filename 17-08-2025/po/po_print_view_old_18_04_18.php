<?php

session_start();

//====================== EOF ===================


require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

require "../../../engine/tools/class.numbertoword.php";





$po_no		= $_REQUEST['po_no'];



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

<link href="../../css/invoice.css" type="text/css" rel="stylesheet"/>

<script type="text/javascript">

function hide()

{

    document.getElementById("pr").style.display="none";

}

</script>

<style type="text/css">

<!--

.style4 {

	font-size: 12px;

	font-weight: bold;

}
.style5 {font-weight: bold}
.style6 {font-weight: bold}
.style7 {font-weight: bold}
.style8 {font-weight: bold}
.style9 {font-weight: bold}

-->

</style></head>

<body>

<form action="" method="post">

<table width="700" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>

    <td align="center"><strong style="font-size:24px"><?

if($_SESSION['user']['group']>1)

echo find_a_field('user_group','group_name',"id=".$_SESSION['user']['group']);

else

echo $_SESSION['proj_name'];

				?><br /></strong>

    Head Office: Dargamohalla, Sylhet. Phone: +880-821-716552, 718815, Fax: +880-821-715200<br />

    <strong><font style="font-size:20px">Purchase (Packing Materials) </font></strong></td>

  </tr>

  <tr>

    

	<td>	<div align="center"><span class="style4">VAT REG NO: 000377966 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><br />

	  </div></td>

  </tr>

  <tr>

    <td><div class="line">

      <div align="center">

      </div>

    </div></td>

  </tr>

  <tr>

    <td><p><span style="font-size:10px">

    	</span></p>

      <table width="100%" border="0" cellspacing="0" cellpadding="0">

        <tr>

          <td valign="top"><span style="font-size:10px"><span style="font-size:20px; font-style:italic"><strong>

            <?=$vendor->vendor_name;?>

          </strong></span><br />



<?=$vendor->address;?>

<br />

Atten:

<b><?=$vendor->contact_person_name;?></b>

<br />

<?=$vendor->contact_person_designation;?>, Contact No:<?=$vendor->contact_person_mobile;?>,  Email:<?=$vendor->email;?><br />
<b><h3><u>Sub: <?=$data->po_details?></u></h3></b>



          </span></td>

          <td valign="top"><span style="font-size:10px">&nbsp;&nbsp;&nbsp;PO No.#

              <span class="style5">
              <?=$_GET['po_no']?>
              </span>
              <br />

              &nbsp;&nbsp;&nbsp;PO Date:

               <span class="style6">
               <?= date("d-m-Y",strtotime($data->po_date));?>
               </span>
              <br />

&nbsp;&nbsp;&nbsp;PO Requisition:

<span class="style7">
<?=$data->req_no?>
</span><br />


&nbsp;&nbsp;&nbsp;Ref. No:

<span class="style8">
<?=$data->quotation_no?>
</span><br />


&nbsp;&nbsp;&nbsp;Ref. Date:

 <span class="style9">
 <?= date("d-m-Y",strtotime($data->quotation_date));?>
 </span><br />


&nbsp;&nbsp;&nbsp;PO Status:

<?=$data->status?><br />


 </span></td>

        </tr>

      </table>   
	  <p style="margin:0; padding:0; font-size:14px;">Dear Sir, <br />
	  Please arrange to supply us the following quantities of <?=$data->goods_title?> for our Magnolia Tea Packaging Unit on urgent basis: </p>
	  
      </td>

  </tr>

  <tr>

    <td><div id="pr">

      <div align="left">

        

          <table width="60%" border="0" cellspacing="0" cellpadding="0">

        <tr>

          <td><input name="button" type="button" onclick="hide();window.print();" value="Print" /></td>

          <!--<td><span class="style3">Special Cash Discount: </span></td>

          <td><label>

            <input name="cash_discount" type="text" id="cash_discount" value="<?=$cash_discount?>" />

            </label>

            <input type="hidden" name="po_no" id="po_no" value="<?=$po_no?>" /></td>

          <td><label>

            <input type="submit" name="Update" value="Update" />

          </label></td>-->

        </tr>

      </table>

        

      </div>

    </div>

<table width="100%" class="tabledesign" border="1" bordercolor="#000000" cellspacing="0" cellpadding="0">

       <tr>

        <td width="8%"><strong>SL/No</strong></td>

        <td width="24%"><strong> Item Name </strong></td>

        <td width="25%"><strong>Description</strong></td>
        <td width="14%"><strong>Qty.</strong></td>

        <td width="15%"><strong>Unit Price </strong></td>

        <td width="14%"><strong>Total Price </strong></td>
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

$sl=$pi;

//$item=find_a_field('item_info','concat(item_name," ; ",	item_description)','item_id='.$info->item_id);

$item=find_a_field('item_info','concat(item_name)','item_id='.$info->item_id);

$item_description=find_a_field('item_info','concat(item_description)','item_id='.$info->item_id);

$qty=$info->qty;

$unit_name=$info->unit_name;

$rate=$info->rate;

$disc=$info->disc;

?>

<tr>

        <td valign="top"><?=$sl?></td>

        <td align="left" valign="top"><?=$item?></td>

        <td align="left" valign="top"><?=$item_description?></td>
        <td valign="top"><?=$qty.' '.$unit_name?></td>

        <td align="right" valign="top"><?=number_format($rate,2)?></td>

        <td align="right" valign="top"><?=number_format($amount,2)?></td>
      </tr>

<? }?>

      <tr>

        <td colspan="4"></td>

        <td align="right">Total:</td>

        <td align="right"><strong><?php echo number_format($total,2);?></strong></td>
      </tr>
    </table>

      <table width="100%" border="0" bordercolor="#000000" cellspacing="3" cellpadding="3" class="tabledesign1" style="width:700px">

        <tr>

		<td>In Word: Taka <?

		$tax_total=(($total*$data->tax)/100);

		$scs =  $aatotal = ($total+$tax_total+$data->transport_bill+$data->labor_bill-$data->cash_discount);

			 $credit_amt = explode('.',$scs);

	 if($credit_amt[0]>0){

	 

	 echo convertNumberToWordsForIndia($credit_amt[0]);}

	 if($credit_amt[1]>0){

	 if($credit_amt[1]<10) $credit_amt[1] = $credit_amt[1]*10;

	 echo  ' & '.convertNumberToWordsForIndia($credit_amt[1]).' paisa ';}

	 echo ' Only';

		?></td>

          <td align="right">&nbsp;</td>

          <td align="right">&nbsp;</td>
        </tr>

<? if($tax_total>0){?>

        <tr>

		<td align="right">&nbsp;</td>

          <td align="right">Tax/Vat(<?=$data->tax?>%):</td>

          <td align="right"><strong><?  echo number_format($tax_total,2);?></strong></td>
        </tr>
		
<? }?>

<? if($data->transport_bill>0){?>

        <tr>

          <td align="right">&nbsp;</td>

          <td align="right">Transport Bill: </td>

          <td align="right"><strong>

            <? echo number_format(($data->transport_bill),2);?>

          </strong></td>
        </tr>

<? }?>

<? if($data->labor_bill>0){?>

        <tr>

          <td align="right">&nbsp;</td>

          <td align="right">Labor Bill: </td>

          <td align="right"><strong>

           <? echo number_format(($data->labor_bill),2);?>

          </strong></td>
        </tr>

<? }?>

        <tr>

          <td align="right">&nbsp;</td>

          <td align="right">Grand Total:</td>

          <td align="right"><strong>

            <? echo number_format(($total+$data->transport_bill+$tax_total+$data->labor_bill-$data->cash_discount),2);?>

          </strong></td>
        </tr>

        <tr>

          <td colspan="3" align="left"><p><strong>Terms &amp; Conditions: </strong></p>

            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:9px">

              <tr>

                <td width="20%" align="left" valign="top">

                  <li>Delivery Spot</li>                </td>

                <td width="3%" align="right" valign="top">:</td>

                <td align="left" valign="top"> <strong><?=$whouse->address?></strong></td>
              </tr>

			  <? if($data->payment_terms!=''){?>

              <tr>

                <td align="left" valign="top">

                  <li>Payment</li>                </td>

                <td align="right" valign="top">:</td>

                <td align="left" valign="top"><?=$data->payment_terms?></td>
              </tr>

			  <? }?>
			  
			  
			  
			  <tr>

                <td align="left" valign="top">

                  <li>Tax and Vat</li>                </td>

                <td align="right" valign="top">:</td>

                <td align="left" valign="top">Tax and Vat impose as per Govt rules and regulation</td>
              </tr>
			  
			  
			  

              <tr>

                <td align="left" valign="top">

                  <li>Bill Submit</li>                </td>

                <td align="right" valign="top">:</td>

                <td align="left" valign="top">Head Office: Dargamohalla, Sylhet. Phone: +880-821-716552, 718815 <strong> (Copy of Work-Order must be attached with original bill)</strong></td>
              </tr>

              

              

              <tr>

                <td align="left" valign="top">

                  <li>Contact Person</li>                </td>

                <td align="right" valign="top">:</td>

                <td align="left" valign="top"><strong>Md. Abdun Noor, Mobile No: +880-1713-485380</strong></td>
              </tr>
            </table>

            <p>Your early action in this regards will be highly appreciated.</p></td>
        </tr>

        <tr>

          <td colspan="3" align="left" style="font-size:10px" >

            <table width="100%" border="0" cellspacing="0" cellpadding="0">

              <tr>

                <td width="25%" valign="top"><p>Thanking You.<br /> <br />
				
				Yours Faithtully,

                </p>

                  <p><br /> <br /> <br />

                      <br />

                    -----------------------<br />

                Director Marketing&nbsp;</p></td>

                <td width="25%" valign="top"><p><br /> <br />

                </p>

                  <p><br /> <br /> <br />

                      <br />

                   <br />

                </p></td>

                <td width="25%" valign="top"><p><br /> <br /><br />

                </p>

                  <p><br /> <br /><br />
                  <br />

                    -----------------------<br />

                  Marketing Manager</p></td>
                </tr>
            </table></td>
        </tr>

        <tr>

          <td colspan="3" align="left" style="font-size:10px"><p><br />
            -This Purchasedr prepared by <em>

              <b>
              <?=find_a_field('user_activity_management','fname','user_id='.$data->entry_by);?>
              </b>

            </em>
            </p>

            </td>
        </tr>

        
      </table></td>

  </tr>



</table>

</form>

</body>

</html>

