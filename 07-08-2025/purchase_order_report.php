<?php


require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
$title='Purchase Basic Reports';

do_calander("#f_date");
do_calander("#t_date");

$tr_type="Show";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// Build the full URL
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


$trimmed_path = str_replace("https://saaserp.ezzy-erp.com/app/views/", "", $url);
$parts = explode('/', $trimmed_path);

 $mod_name = $parts[0]; 
 $folder_name = $parts[1];
 $page_name = $parts[2]; 

//$module_id=find_a_field('user_module_manage','id','id="'.$_SESSION['mod'].'"');
//$feature_id=find_a_field('user_feature_manage','id','module_id="'.$module_id.'" ');
//$page_id=find_a_field('user_page_manage','id','feature_id="'.$feature_id.'"');


	 $res2 ='SELECT  r.folder_name, r.report_no, r.feature_id,r.page_id, p.id AS page_id, f.id AS feature_id, f.feature_name,  m.id AS module_id,  m.module_name
			FROM  user_page_manage p JOIN  user_feature_manage f ON p.feature_id = f.id JOIN  user_module_manage m ON f.module_id = m.id, report_manage r
			WHERE  m.module_file="'.$mod_name.'" and p.folder_name="'.$folder_name.'" and p.page_link="'.$page_name .'" and r.folder_name="'.$folder_name.'" and r.feature_id=f.id and r.page_id=p.id';

								$query=db_query($res2);
								
								While($row=mysqli_fetch_object($query)){
									$page_file[$row->page_no] = $row->page_id;
								}
?>



<div class="d-flex justify-content-center">
    <form class="n-form1 fo-width pt-4" action="master_report.php" autocomplete="off" method="post" name="form1" target="_blank" id="form1">
        <div class="row m-0 p-0">
            <div class="col-sm-5">
                <div align="left">Select Report </div>
				
					<?
					
							echo $res ='select page_id,report_no,status from report_manage where page_id="'.$page_file[$data->page_id].'" and status="Yes"';
									
									$query=db_query($res);
								While($row=mysqli_fetch_object($query)){
								}
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
				
				<? ?>
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




<?
$tr_from="Purchase";
require_once SERVER_CORE."routing/layout.bottom.php";
?>