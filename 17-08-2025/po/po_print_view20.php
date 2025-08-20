<?php
session_start();
//====================== EOF ===================

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
require "../../../engine/tools/class.numbertoword.php";


$po_no		= $_REQUEST['po_no'];

if($_GET['update']=='Update')
{
	$pur_status = $_GET['pur_status'];
	$ssql='update purchase_master set status="'.$_GET['pur_status'].'" where po_no="'.$po_no.'"';
	db_query($ssql);
}

echo $sql1="select * from purchase_master where po_no='$po_no'";
$data=mysqli_fetch_object(db_query($sql1));
$vendor=find_all_field('vendor','','vendor_id='.$data->vendor_id );
$whouse=find_all_field('warehouse','','warehouse_id='.$data->warehouse_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Cash Memo :.</title>
<link href="../../css/invoice.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript">
function hide()
{
    document.getElementById("pr").style.display="none";
}
</script>
</head>
<body>
<table width="700" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center"><strong style="font-size:24px"><?=$whouse->warehouse_name?><br /></strong>
    Shezan point, 2 Indira Road, Farmgate, Dhaka-1215. Phone: 02-8150237-8, Fax: 02-8124839<br />
    <strong><font style="font-size:20px">Purchase Order</font></strong></td>
  </tr>
  <tr>
    
	<td>	</td>
  </tr>
  <tr>
    <td><div class="line"></div></td>
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
Attn:
<?=$vendor->contact_person_name;?>
<br />
<?=$vendor->contact_person_designation;?>, Contact No:<?=$vendor->contact_no;?>, Fax No:<?=$vendor->fax_no;?>, Email:<?=$vendor->email;?><br />
          </span></td>
          <td valign="top"><span style="font-size:10px">&nbsp;&nbsp;&nbsp;P O No.#
              <?=$_GET['po_no']?>
              <br />
&nbsp;&nbsp;&nbsp;P. Requisition:
<?=$data->req_no?>
<br />
&nbsp;&nbsp;&nbsp;Date:
<?=$data->po_date?>
          </span></td>
        </tr>
      </table>   <br />
      <?=$vendor->contact_person_name;?>, <br />
        In reference to your quotation ref no: <? if($data->quotation_no=='') echo 'NIL'; else echo $data->quotation_no;?> Dated at : <? if($data->quotation_date=='') echo 'NIL'; else echo $data->quotation_date;?>.<br />
</span>
        <span class="debit_box">
        </div>
    </span></td>
  </tr>
  <tr>
    <td><div id="pr">
      <div align="left">
        <form action="" method="get">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="1"><input name="button" type="button" onclick="hide();window.print();" value="Print" /></td>
              <td width="100" align="right">Present Status:</td>
              <td width="1"><?=$data->pur_status;?><select name="pur_status">
                <option>
                  <?=$data->pur_status;?>
                  </option>
                <option>PENDING</option>
                <option>STOPPED</option>
                <option>CANCELED</option>
                <option>COMPLETE</option>
              </select></td>
              <td><input name="update" type="submit" value="Update" />
                <input type="hidden" name="v_no" id="v_no" value="<?=$v_no?>" /></td>
            </tr>
          </table>
        </form>
      </div>
    </div>
<table width="100%" class="tabledesign" border="1" bordercolor="#000000" cellspacing="0" cellpadding="0">
       <tr>
        <td width="8%"><strong>SL/No</strong></td>
        <td width="54%"><div align="center"><strong>Description of the Goods </strong></div></td>
        <td width="9%"><strong>Qty.</strong></td>
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
$item=find_a_field('item_info','concat(item_name," : ",	item_description)','item_id='.$info->item_id);
$qty=$info->qty;
$unit_name=$info->unit_name;
$rate=$info->rate;
$disc=$info->disc;
?>
<tr>
        <td valign="top"><?=$sl?></td>
        <td align="left" valign="top"><?=$item?></td>
        <td valign="top"><?=$qty.' '.$unit_name?></td>
        <td align="right" valign="top"><?=number_format($rate,2)?></td>
        <td align="right" valign="top"><?=number_format($amount,2)?></td>
      </tr>
<? }?>
      <tr>
        <td colspan="3"></td>
        <td align="right">Total:</td>
        <td align="right"><strong><?php echo number_format($total,2);?></strong></td>
      </tr>
    </table>
      <table width="100%" border="0" bordercolor="#000000" cellspacing="3" cellpadding="3" class="tabledesign1" style="width:700px">
        <tr>
		<td>In Word: Taka <?=CLASS_Numbertoword::convert(((int)($total)),'en')?> Only</td>
          <td align="right">Discount:</td>
          <td align="right"><strong><? if($data->total_disc>0) echo number_format(((($data->total_disc)/100)*$total),2); else echo '0.00';?></strong></td>
        </tr>
        <tr>
		<td align="right">&nbsp;</td>
          <td align="right">Tax/Vat(15%):</td>
          <td align="right"><strong>0.00</strong></td>
        </tr>
        <tr>
          <td align="right">&nbsp;</td>
          <td align="right">Grand Total:</td>
          <td align="right"><strong>
            <?=number_format($total,2);?>
          </strong></td>
        </tr>
        <tr>
          <td colspan="3" align="left"><p><strong>Terms &amp; Conditions: </strong></p>
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:9px">
              <tr>
                <td width="20%" align="left" valign="top">
                  <li>Delivery</li>
                </td>
                <td width="3%" align="right" valign="top">: </td>
                <td align="left" valign="top"> Within <? if($data->delivery_within>0) echo $data->delivery_within.' ('.CLASS_Numbertoword::convert(((int)$data->delivery_within),'en').') '; else echo ' 90 (Ninty)'?>  Days from the date of Work-Order<strong> (Delivery Period is strictly defined)</strong></td>
              </tr>
              <tr>
                <td align="left" valign="top">
                  <li>Delivery Spot</li>
                </td>
                <td align="right" valign="top">:</td>
                <td align="left" valign="top"> <strong><?=$whouse->address?></strong></td>
              </tr>
              <tr>
                <td align="left" valign="top">
                  <li>Payment</li>
                </td>
                <td align="right" valign="top">:</td>
                <td align="left" valign="top">After 30 (Thirty) Days from the date of receiving all goods at our factory.</td>
              </tr>
              <tr>
                <td align="left" valign="top">
                  <li>Bill Submit</li>
                </td>
                <td align="right" valign="top">:</td>
                <td align="left" valign="top">Shezan Point(5th Floor) 2, Indira Road Farmgate, Dhaka-1215.<strong>(Copy of Work-Order must be attached with original bill)</strong></td>
              </tr>
              <tr>
                <td align="left" valign="top">
                  <li>Defined</li>
                </td>
                <td align="right" valign="top">:</td>
                <td align="left" valign="top">Supplied goods will be same as approved sample, otherwise the goods must be replaced &amp; you will bare all expenses.</td>
              </tr>
              <tr>
                <td align="left" valign="top">
                  <li>Contact Person</li>
                </td>
                <td align="right" valign="top">:</td>
                <td align="left" valign="top"><strong><?=$whouse->warehouse_company?>. Mobile No: <?=$whouse->contact_no?></strong></td>
              </tr>
            </table>
            <p><strong></strong></p></td>
        </tr>
        <tr>
          <td colspan="3" align="left" style="font-size:10px" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top"><p>Thanking You,<br />
                </p>
                  <p><br />
                    <br />--------------------<br />
                    Authorized Person&nbsp;</p></td>
                <td><em><strong>Prepared By </strong>: <?=find_a_field('user_activity_management','fname','user_id='.$data->entry_by);?></em></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td colspan="3" align="left" style="font-size:10px">
          <ul>
            <li>The Copy of Work Order must be shown at the factory premises during the delivery.</li>
            <li>Company protects the right to reconsider or cancel the Work-Order every nowby any administrational dictation.</li>
            <li>Any inefficiency in maintanence must be informed(Officially) before the execution to avoid the compensation.</li>
        </ul></td>
        </tr>
        <tr>
          <td colspan="3" align="left">&nbsp;</td>
        </tr>
      </table></td>
  </tr>

</table>
</body>
</html>
