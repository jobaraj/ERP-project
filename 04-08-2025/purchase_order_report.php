<?php



require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
$title='Purchase Basic Reports';

do_calander("#f_date");
do_calander("#t_date");

$tr_type="Show";



?>



<div class="d-flex justify-content-center">
    <form class="n-form1 fo-width pt-4" action="master_report.php" autocomplete="off" method="post" name="form1" target="_blank" id="form1">
        <div class="row m-0 p-0">
            <div class="col-sm-5">
                <div align="left">Select Report </div>
				
					<?
                            $last_two_months = date('Y-m-d', strtotime('-2 months'));

                            $con .= ' and a.po_date between "'.$last_two_months.'" and "'.date('Y-m-d').'"';
                            
							if(isset($_POST['fdate'])){
								if($_POST['status']!=''&&$_POST['status']!='ALL')
								$con .= 'and a.status="'.$_POST['status'].'"';
								
								if($_POST['fdate']!=''&&$_POST['tdate']!='')
								$con .= 'and a.po_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';
								
								if($_POST['group_for']!='')
								$con .= 'and b.group_for = "'.$_POST['group_for'].'"';
								
								if($_POST['warehouse_id']!='')
								$con .= 'and b.warehouse_id = "'.$_POST['warehouse_id'].'"';
                            }
								$res='select  a.po_no,a.po_no, a.po_date,a.current_approval_level, a.req_no,v.vendor_name, b.warehouse_name as warehouse, a.quotation_no as note, c.fname as "by", a.entry_at,a.status from purchase_master a,warehouse b,user_activity_management c, vendor v where   a.warehouse_id=b.warehouse_id and  a.group_for="'.$_SESSION['user']['group'].'" and a.entry_by=c.user_id and a.vendor_id=v.vendor_id '.$con.' order by a.po_no desc';
						

								
								$query=db_query($res);
								
								While($row=mysqli_fetch_object($query)){
								
								
								?>
								
								
								
                <div class="form-check">
                    <input name="report" type="radio" class="radio1" id="report1-btn" value="36" checked="checked" tabindex="1"/>
                    <label class="form-check-label p-0" for="report1-btn">
                       Purchase Order Details (36)
                    </label>
                </div>
                <div class="form-check">
                    <input name="report" type="radio" class="radio1" id="report1-btn" value="38"  tabindex="1"/>
                    <label class="form-check-label p-0" for="report1-btn">
                      Purchase Requisition Details (38)
                    </label>
                </div>
                <div class="form-check">
                    <input name="report" type="radio" class="radio1" id="report1-btn" value="39"  tabindex="1"/>
                    <label class="form-check-label p-0" for="report1-btn">
                      Purchase Receive Details (39)
                    </label>
                </div>
				        <div class="form-check">
                    <input name="report" type="radio" class="radio1" id="report1-btn" value="40"  tabindex="1"/>
                    <label class="form-check-label p-0" for="report1-btn">
                       Purchase Receive Pending (40)
                    </label>
                </div>
				
				<? 
							}
							?>
                            <!--<div class="form-check">
                                        <input name="report" type="radio" class="radio1" id="report1-btn1" value="1005"  tabindex="1"/>
                                        <label class="form-check-label p-0" for="report1-btn1">
                                            Present Stock Summary (1005)
                                        </label>
                                    </div>-->
                    <!--				<div class="form-check">
                                        <input name="report" type="radio" class="radio1" id="report1-btn2" value="5"  tabindex="1"/>
                                        <label class="form-check-label p-0" for="report1-btn2">
                                            Purchase Receive Report(PO Wise) (5) 
                                        </label>
                                    </div>-->
                            
                            <!--<div class="form-check">
                                        <input name="report" type="radio" class="radio1" id="report1-btn3" value="1007"  tabindex="1"/>
                                        <label class="form-check-label p-0" for="report1-btn3">
                                          Requisition vs Purchase vs Receive Report (1007)
                                        </label>
                                    </div>-->
              </div>

            <div class="col-sm-7">
				      <div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">Prepare By</label>
                    <div class="col-sm-8 p-0">
                      <select name="by" id="by" class="form-control">
                        <option></option>
                        <? 
                        $sql="SELECT a.user_id,a.fname FROM `user_activity_management` a WHERE 1";
                        advance_foreign_relation($sql,$by);	  
                        ?>
                      </select>
                    </div>
                </div>
				<div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">Product Sub Category</label>
                    <div class="col-sm-8 p-0">
					<input type="text" list="sub_group" name="sub_group_id" id="sub_group_id" class="form-control" />
                      <datalist id="sub_group">
					 		 <option></option>
							<? foreign_relation('item_sub_group','sub_group_id','sub_group_name',$data->sub_group_id,'group_for="'.$_SESSION['user']['group'].'"');?>
					</datalist>
                    </div>
                </div>
                <div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">Product Name</label>
                    <div class="col-sm-8 p-0">
					<input type="text" list="item" name="item_id" id="item_id" class="form-control" />
                      <datalist id="item">
                        <option></option>
                      
						<? foreign_relation('item_info','item_id','item_name',$item_id,'group_for="'.$_SESSION['user']['group'].'"');?>
                    </datalist>
                    </div>
                </div>  

                <div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">From Date</label>
                    <div class="col-sm-8 p-0">
                     <input  name="f_date" type="text" id="f_date" value="<?=date('Y-m-01')?>" required autocomplete="off" / class="form-control">
                    </div>
                </div>


                <div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">To Date</label>
                    <div class="col-sm-8 p-0">
                      <span class="oe_form_group_cell">
                     <input  name="t_date" type="text" id="t_date" value="<?=date('Y-m-d')?>" required autocomplete="off" / class="form-control">
                      </span>

                    </div>
                </div>

                <div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">Vendor Name</label>
                    <div class="col-sm-8 p-0">

                        <span class="oe_form_group_cell">
						<input type="text" list="vendor" name="vendor_id" id="vendor_id" class="form-control" />
                            <datalist id="vendor">
                       		 <option></option>
								<? 
								$sql = "select v.vendor_id,v.vendor_name from vendor v where v.group_for='".$_SESSION['user']['group']."' order by v.vendor_name";
								foreign_relation_sql($sql);?>
                   			 </datalist>

                        </span>


                    </div>
                </div>
				<div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">Purchase Status</label>
                    <div class="col-sm-8 p-0">
                      <select name="status" id="status" class="form-control">
					    <option value="">All</option>
						<option value="UNCHECKED">UNCHECKED</option>
                        <option value="CHECKED">CHECKED</option>   
					    <option value="COMPLETED">COMPLETED</option>
			        </select>
                    </div>
                </div>
                 <div class="form-group row m-0 mb-1 pl-3 pr-3">
                    <label for="group_for" class="col-sm-4 m-0 p-0 d-flex align-items-center">Inventory Name:</label>
                    <div class="col-sm-8 p-0">
                            
                            <select name="warehouse_id" id="warehouse_id">
							<option></option>
							  <? foreign_relation('warehouse','warehouse_id','warehouse_name','','1');?>
                   			 </select>

                    </div>
                </div>

            </div>

        </div>
        <div class="n-form-btn-class">
            <input name="submit" type="submit" class="btn1 btn1-bg-submit" value="Report" />
        </div>
    </form>
</div>




<!--<form action="master_report.php" method="post" name="form1" target="_blank" id="form1">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><div class="box4">
          <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
              <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td colspan="2" class="title1"><div align="left">Select Report </div></td>
                              </tr>
                              <tr>
                                <td width="6%"><input name="report" type="radio" class="radio" value="1" /></td>
                                <td width="94%"><div align="left">Purchase Order Summary</div></td>
                              </tr>
							  
							  <tr>
                                <td><input name="report" type="radio" class="radio" value="1005" /></td>
                                <td><div align="left">Present Stock Summary</div></td>
                              </tr>-->
                              
                            <!-- <tr>
                                <td><input name="report" type="radio" class="radio" value="2" /></td>
                                <td><div align="left">Purchase Receive Report(PO Wise)</div></td>
                              </tr>
                               <tr>
                                <td><input name="report" type="radio" class="radio" value="3" /></td>
                                <td><div align="left">Purchase Receive Report(Date Wise)</div></td>
                              </tr>-->
                            <!--  <tr>
                                <td><input name="report" type="radio" class="radio" value="5" /></td>
                                <td><div align="left">Purchase Receive Report (PO Wise)</div></td>
                              </tr>-->
                              <!--<tr>
                                <td><input name="report" type="radio" class="radio" value="6" /></td>
                                <td><div align="left">Purchase Receive Report</div></td>
                              </tr>-->
                       <!--       <tr>
                                <td><input name="report" type="radio" class="radio" value="4" /></td>
                                <td><div align="left">View Purchase Order(Single)</div></td>
                              </tr>-->
                   <!--       </table></td>
                        </tr>
                    </table></td>
                  </tr>
              </table></td>
              <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>Prepared By:</td>
                    <td><select name="by" id="by" class="form-control">
					  <option></option>-->

<?
$tr_from="Purchase";
require_once SERVER_CORE."routing/layout.bottom.php";
?>