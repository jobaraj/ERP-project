<?php
 

require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";
$title='Purchase Order Status';

do_calander('#fdate');
do_calander('#tdate');

$table = 'purchase_master';
$unique = 'po_no';
$status = 'UNCHECKED';
$target_url = '../po/chalan_view2.php';
$tr_type="Show";
?>
<script language="javascript">
function custom(theUrl)
{
	window.open('<?=$target_url?>?v_no='+encodeURIComponent(theUrl));
}
function custom2(theUrl)
{
	window.open('<?=$target_url2?>?<?=$unique?>='+encodeURIComponent(theUrl));
}
function custom3(theUrl)
{
	window.open('<?=$target_url2?>?<?=$unique?>='+encodeURIComponent(theUrl));
}

</script>



<div class="form-container_large">
    
    <form action="" method="post" name="codz" id="codz">
            
  
      <div class="container-fluid pt-5 p-0 ">
		
		<table class="table1  table-striped table-bordered table-hover table-sm">
<thead class="thead1">
<tr class="bgc-info" style="color:black!important;">
<th rowspan="2" bgcolor="#00FF66">SL</th>
<th rowspan="2" bgcolor="#00FF66">Item Code</th>
<th rowspan="2" bgcolor="#00FF66">Item Name</th>
<th rowspan="2" bgcolor="#00FF66">Stock Unit</th>
<th rowspan="2" bgcolor="#00FF66">Order Unit</th>
<th colspan="1" bgcolor="#00FF66">Ordered</th>
<th colspan="1" bgcolor="#00FF66">Received</th>
<th colspan="2" bgcolor="#00FF66">UnReceived</th>
</tr>
<tr class="bgc-info" style="color:black!important;">
  <th bgcolor="#00FF66">QTY</th>
  <th bgcolor="#00FF66">Qty</th>
  <th bgcolor="#00FF66">Qty</th>
</tr>
</thead>
<tbody class="tbody1">
<?php
//////purchase receive qty////////
$sql='select sum(qty) as rec_qty,po_no,item_id from purchase_receive where 1 group by po_no,item_id';
$query=db_query($sql);
while($srow=mysqli_fetch_object($query)){
$tot_rec_qty[$srow->po_no][$srow->item_id]=$srow->rec_qty;
}


 $sql='select * from purchase_invoice a,item_info b where b.item_id=a.item_id and a.po_no='.$_GET['po_no'].'';
$res=db_query($sql);
while($row=mysqli_fetch_object($res)){
 
 ?>
<tr>
	<td><?php echo ++$i;?></td>
	<td><?php echo $row->item_id;?></td>
	<td><?php echo $row->item_name;?></td>
	<td><?php echo $row->unit_name;?></td>
	<td><?php if($row->vunit_name!=''){echo $row->vunit_name;} else { echo $row->unit_name;}?></td>
	
	<td><?php echo $row->qty;?></td>
	
	<td><?php echo $tot_rec_qty[$row->po_no][$row->item_id];?></td>
	
    <td><?php echo $row->qty-$tot_rec_qty[$row->po_no][$row->item_id];?></td>
</tr>
<?php } ?>
</tbody>
</table>
<br /><br />


<h2 style="text-align:center;">GRN List</h2>
                <table class="table1  table-striped table-bordered table-hover table-sm">
                    <thead class="thead1">
                    <tr class="bgc-info">
                        <th>SL</th>
                        <th>PO No</th>
						<th>MRR No</th>
                        <th>Vendor Name</th>

                        <th>Received By</th>
                        <th>Received At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody class="tbody1">

                    <?

                    //if(isset($_POST['submitit'])){





               //         if($_POST['fdate']!=''&&$_POST['tdate']!='')
//
//                            $con .= 'and pr.rec_date between "'.$_POST['fdate'].'" and "'.$_POST['tdate'].'"';
//
//                        if($_POST['vendor_id']>0)
//
//                            $con .= 'and pr.vendor_id="'.$_POST['vendor_id'].'"';
                        $si=0;

                          $sql = 'select pr.pr_no,pr.po_no,pr.warehouse_id,pr.entry_at,pr.status,v.vendor_name,u.fname from purchase_receive pr left join vendor v on v.vendor_id=pr.vendor_id left join user_activity_management u on u.user_id=pr.entry_by where 1  and po_no="'.$_GET['po_no'].'" group by pr.pr_no  DESC';

                        $sqlq = db_query($sql);

                        while($info=mysqli_fetch_object($sqlq)){

                            ?>

                            <tr>

                                <td><?=++$si;?></td>

                                <td><?=find_a_field('purchase_master','po_no','po_no='.$info->po_no);?></td>
								 <td><?=$info->pr_no?></td>

                                <td><?=$info->vendor_name?></td>

                                <td><?=$info->fname?></td>

                                <td><?=$info->entry_at?></td>
                                <td><?=$info->status?></td>
                                <td >
								<button type="submit" name="submitit" id="submitit" value="View" onclick="custom('<?=url_encode($info->pr_no);?>')" class="btn2 btn1-bg-submit">
									<i class="fa-solid fa-eye"></i>
								</button>
								</td>
                            </tr>


                        <? }
						//}
						?>



                    </tbody>
                </table>





        </div>
    </form>
</div>





<?
$tr_from="Purchase";
require_once SERVER_CORE."routing/layout.bottom.php";

?>