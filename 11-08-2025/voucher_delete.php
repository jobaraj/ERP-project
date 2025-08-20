<?php

session_start();

ob_start();



require_once "../../../assets/template/layout.top.php";

// ::::: Start Edit Section ::::: 

$title='Voucher Delete';			// Page Name and Page Title







$chalan_no = $_REQUEST['chalan_no'];


$type = $_REQUEST['type'];


if($chalan_no>0)

{

	$insert_sql = "INSERT INTO journal_del select * from journal where tr_from ='".$type."' and  tr_no=".$chalan_no."";
	db_query($insert_sql);




	$sql3 = "DELETE FROM `journal` WHERE tr_from ='".$type."' and  tr_no=".$chalan_no."";

	db_query($sql3);


	$sql3 = "DELETE FROM `secondary_journal` WHERE tr_from ='".$type."' and  tr_no=".$chalan_no."";

	db_query($sql3);



}





	?>

<style type="text/css">

<!--

.style1 {

	color: #FF0000;

	font-weight: bold;

}

.style2 {

	color: #006600;

	font-weight: bold;

}

-->

</style>

<? 

if($found>0){

?>

		<div class="alert alert-danger" role="alert">
		  Sorry Journal Exists!
		</div>


		<!--<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FF0000">

      <tr>

        <td><div align="center" class="style2">Sorry Journal Exists! </div></td>

      </tr>

    </table>-->

<? 

}

elseif($chalan_no>0)

{



?>

<div class="alert alert-success p-2" role="alert">
  Deleted Successfull
</div>


		<!--<table width="50%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#99FF66">

      <tr>

        <td><div align="center" class="style2">  </div></td>

      </tr>

    </table>-->

<? }?>








<div class="form-container_large">
   
    <form action="" method="post">
           
        <div class="container-fluid bg-form-titel">
            <div class="row">
                <div class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
                    <div class="form-group row m-0">
                        <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Voucher No</label>
                        <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">
                            <input name="chalan_no" type="text" id="chalan_no" maxlength="16" value="<?=$chalan_no?>" required />
                        </div>
                    </div>

                </div>
                <div class="col-sm-5 col-md-5 col-lg-5 col-xl-5">
                    <div class="form-group row m-0">
                        <label class="col-sm-4 col-md-4 col-lg-4 col-xl-4 m-0 p-0 d-flex justify-content-end align-items-center pr-1 bg-form-titel-text">Voucher Type</label>
                        <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 p-0">
                           <select name="type" id="type" required>
							<option></option>
								<option value="Receipt" >Receipt</option>
								<option value="Payment" >Payment</option>
								<option  value="Journal">Journal</option>
								<option value="coutra" >Contra</option>
							</select>

                        </div>
                    </div>
                </div>

                <div class="col-sm-2 col-md-2 col-lg-2 col-xl-2">
							<input name="search" type="submit" class="btn1 btn1-submit-input" id="search" value="Delete" />
                  
                   
                </div>

            </div>
        </div>

    </form>
</div>




<?php /*?><form action="" method="post">

<div class="oe_view_manager oe_view_manager_current">
        <div class="oe_view_manager_body">
                <div  class="oe_view_manager_view_list"></div>

            

                <div class="oe_view_manager_view_form"><div style="opacity: 1;" class="oe_formview oe_view oe_form_editable">

        <div class="oe_form_buttons"></div>

        <div class="oe_form_sidebar"></div>

        <div class="oe_form_pager"></div>

        <div class="oe_form_container"><div class="oe_form">

          <div class="">

<div class="oe_form_sheetbg" style="min-height:10px;">

        <div class="oe_form_sheet oe_form_sheet_width">



          <div  class="oe_view_manager_view_list"><div  class="oe_list oe_view">

           	 <table width="85%" border="0" align="center" cellpadding="5" cellspacing="0">

  <tr>

    <td height="35" bgcolor="#33CCFF"><strong>Voucher No: </strong></td>

    <td bgcolor="#33CCFF"><input name="chalan_no" type="text" id="chalan_no" maxlength="16" value="<?=$chalan_no?>" required /></td>

    <td align="center" valign="middle" bgcolor="#33CCCC">Voucher Type </td>
    <td align="center" valign="middle" bgcolor="#33CCCC">
		<select name="type" id="type" required>
		<option></option>
			<option value="Receipt" >Receipt</option>
			<option value="Payment" >Payment</option>
			<option  value="Journal">Journal</option>
			<option value="coutra" >Contra</option>
		</select>
	
	</td>
    <td align="center" valign="middle"><input name="search" type="submit" class="btn1 btn1-submit-input" id="search" value="Delete" /></td>
  </tr>
</table>



		

		  

          </div></div>

          </div>

    </div>

    <div class="oe_chatter"><div class="oe_followers oe_form_invisible">

      <div class="oe_follower_list"></div>

    </div></div></div></div></div>

    </div></div>

            

        </div>

  </div>

</form><?php */?>

<?

require_once "../../../assets/template/layout.bottom.php";

?>