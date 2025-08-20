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
<link href="../../../assets/css/invoice.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript">
function hide()
{
    document.getElementById("pr").style.display="none";
}
</script>

<style type="text/css">
<!--
@font-face {
  font-family: 'MYRIADPRO-REGULAR';
  src: url('MYRIADPRO-REGULAR.OTF'); /* IE9 Compat Modes */

}

@font-face {
  font-family: 'TradeGothicLTStd-Extended';
  src: url('TradeGothicLTStd-Extended.otf'); /* IE9 Compat Modes */

}


@font-face {
  font-family: 'Humaira demo';
  src: url('Humaira demo.otf'); /* IE9 Compat Modes */

}

.style4 {
	font-size: 12px;
	font-weight: bold;
}

.style5 {font-weight: bold}

.style6 {font-weight: bold}

.style7 {font-weight: bold}

.style9 {font-weight: bold}

.style10 {font-weight: bold}
-->

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

<body>
<form action="" method="post">
    <div id="pr" align="center">
        <h5><input name="button" type="button" onclick="hide();window.print();" value="Print" /></h5>
    </div>


<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="center"><div class="header" style="margin-top:0;">
       <table width="100%" border="0" cellspacing="0" cellpadding="0">
    
		  <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="30%">
                       		<table  width="100%" border="0" cellspacing="0" cellpadding="0"  style="font-size:15px">
								<tr>
					    <td width="60%" align="left" style="padding-bottom:0px;"><img src="<?=SERVER_ROOT?>public/uploads/logo/<?=$_SESSION['proj_id']?>.png"  width="100%" /></td>
					    <td width="40%" align="left">&nbsp;</td>
							  </tr>
						
						<!--<tr>
					    <td align="left" width="57%">&nbsp;</td>
					    <td align="left" width="43%">&nbsp;</td>
						</tr>-->
						  </table>
                        </td>
                        <td width="40%" align="center">
							<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:15px; margin:0; padding:0;">
									<tr>
                                        <h3 style="margin: 0px;font-size: 19px;font-weight: 800;">
                                            <?=find_a_field('user_group','group_name','id='.$_SESSION['user']['group'])?>.
                                        </h3>
							        </tr>
							  
							  
									<tr align="center"><td style="font-size:12px; line-height:20px;"><?=$group_data->address?></td>
									</tr>
									
									<!--<tr><td style="font-size:12px; line-height:20px;">Mobile No: <?=$group_data->mobile?></td>
									</tr>
									<tr><td style="font-size:12px; line-height:20px;">Email: <?=$group_data->email?></td>
									</tr>
							        <tr><td style=" font-size:12px; line-height:20px;">BIN/VAT Reg. No: <?=$group_data->vat_reg?></td>
							        </tr>-->
						  </table>
                        </td>
                        <td width="30%"></td>

                      </tr>
                    </table>
                  </td>
                </tr>
              </table></td>
          </tr>
        </table>
       </div></td>
  </tr>



  <tr>
    <td colspan="0" align="center"><hr /></td>
  </tr>

  <tr>
   <td colspan="2" align="center"><h4 style="font-size:18px; padding:10px 0; margin:0; font-family:  'MYRIADPRO-REGULAR'; letter-spacing:1px;text-decoration:underline; text-transform:uppercase;">Purchase Order</h4></td>
  </tr>
 
  
  
  <tr>
      <td>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">

		  <tr>
		    <td width="68%" valign="top">
		      <table width="96%" border="0" cellspacing="0" cellpadding="3"  style="font-size:12px">
		        <tr style="font-size: 14px;line-height:20px;font-weight: 900;" >
		          <td width="28%" align="left" valign="middle"><strong>Supplier Name </strong></td>
		          <td width="3%" align="left" valign="middle"><strong>: </strong></td>
		          <td width="69%" ><strong><?= $vendor->vendor_name;?></strong></td>
	            </tr>

		        <tr style=" line-height:15px;">
		          <td align="left" valign="middle"><strong>Contract Person</strong></td>
		          <td align="left" valign="middle"><strong>:</strong></td>
		          <td><?= $vendor->contact_person_name;?></td>
	            </tr>
		        <tr style=" line-height:15px;">
		          <td align="left" valign="middle"><strong>Mobile No</strong></td>
		          <td align="left" valign="middle"><strong>:</strong></td>
		          <td><?= $vendor->contact_no;?></td>
	            </tr>
		        <tr style=" line-height:15px;">
		          <td align="left" valign="middle"><strong> Address</strong></td>
		          <td align="left" valign="middle"><strong>:</strong></td>
		          <td><?= $vendor->address;?></td>
	            </tr>
		        </table>		      </td>


			<td width="32%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2"  style="font-size:11px">
			
			
			
			<tr>


                <td width="58%" align="right" valign="middle"><strong> PO No : </strong></td>


			    <td width="42%"><table width="100%" border="1" cellspacing="0" cellpadding="1">


                    <tr height="20">


                      <td>&nbsp; <?=$_GET['po_no']?></td>
                    </tr>


                </table></td> </tr>




			  <tr>


                <td width="58%" align="right" valign="middle"><strong> PO Date : </strong></td>


			    <td width="42%">
                    <table width="100%" border="1" cellspacing="0" cellpadding="1">
                  <tr height="20">
                    <td>&nbsp; <?= date("d-m-Y",strtotime($data->po_date));?></td>
                  </tr>
                </table>
                </td>
              </tr>
				



		    </table>
            </td>
		  </tr>

		</table>
      </td>
  </tr>



    <tr>
		<td>&nbsp;</td>
	</tr>



  <tr>



    <td>
        <div id="pr">
      <div align="left">
          <table width="60%" border="0" cellspacing="0" cellpadding="0">
        <tr>
<!--          <td><input name="button" type="button" onclick="hide();window.print();" value="Print" /></td>-->



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



    </div></td>
</tr>

<tr>

<td>


<table  class="tabledesign" cellspacing="0" cellpadding="0">

       <tr bgcolor="#ffec62">
        <td width="4%"><strong>SL</strong></td>
        <td width="29%"><strong> Item Description </strong></td>
        <td width="10%"><strong>Unit</strong></td>
        <td width="10%"><strong>Quantity</strong></td>
        <td width="14%"><strong>Unit Price </strong></td>
        <td width="16%"><strong> Amount </strong></td>
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
$fg_code=find_a_field('item_info','concat(finish_goods_code)','item_id='.$info->item_id);
$sku_code=find_a_field('item_info','concat(sku_code)','item_id='.$info->item_id);
$qty=$info->qty;
$unit_name=find_a_field('item_info','concat(unit_name)','item_id='.$info->item_id);
$rate=$info->rate;
$disc=$info->disc;
?>

<tr>
        <td valign="top"><?=$sl?></td>
        <td align="left" valign="top"><?=$item?></td>
        <td valign="top"><?=$unit_name?></td>
        <td valign="top"><?=$qty?></td>
        <td align="left" valign="top"><?=number_format($rate,2)?></td>
        <td align="right" valign="top"><?=number_format($amount,2)?></td>
      </tr>
<? }?>


      <tr>
        <td align="right" colspan="5"><strong>Sub Total:</strong></td>
        <td align="right"><strong><?php echo number_format($total,2);?></strong></td>
      </tr>
	  <? if($data->tax>0) {?>
      <tr  align="right">
        <td colspan="5"><strong>VAT (<?=number_format($data->tax,2)?>%):</strong></td>
        <td align="right"><strong>
          <?  echo number_format($tax_total=(($total*$data->tax)/100),2);?>
        </strong></td>
      </tr>
	  <? }?>
	  
	  <? if($data->ait>0) {?>
      <tr  align="right">
        <td colspan="5"><strong>AIT (<?=number_format($data->ait,0)?>%):</strong></td>
        <td align="right"><strong>
          <?  echo number_format($ait_total=(($total*$data->ait)/100),2);?>
        </strong></td>
      </tr>
	  <? }?>
      <tr>
        <td   align="right" colspan="5"><strong>Net Amount: </strong></td>
        <td align="right"><strong><? echo number_format($payable_amount=($total+$tax_total+$ait_total),2);?></strong></td>
      </tr>
    </table>	</td>
	</tr>

<tr>

<td>

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
</td>

  </tr>
</table>



</form>



</body>



</html>



