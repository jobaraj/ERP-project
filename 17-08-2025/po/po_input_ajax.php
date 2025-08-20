<?php

session_start();


require_once "../../../controllers/routing/default_values.php";
require_once SERVER_CORE."routing/layout.top.php";

@ini_set('error_reporting', E_ALL);

@ini_set('display_errors', 'Off');

//--========Table information==========-----------//

$table_master='purchase_master';

$table_details='purchase_invoice';

$unique='po_no';



$unique_detail='id';

//--========Table information==========-----------//

$unique = $_POST[$unique];

$crud   = new crud($table_details);

		$iii=explode('#>',$_POST['item_id']);

		$_POST['item_id']=$iii[1];

		$_POST['entry_by']=$_SESSION['user']['id'];

		$_POST['entry_at']=date('Y-m-d h:s:i');

		$_POST['edit_by']=$_SESSION['user']['id'];

		$_POST['edit_at']=date('Y-m-d h:s:i');

		$crud->insert();
		
		

 $res='select a.id,b.item_id as Item_code, concat(b.item_name) as item_description , a.unit_name as Unit,a.qty as Quantity , a.rate as unit_price,a.amount,"x" from purchase_invoice a,item_info b where c.id=a.colour and b.item_id=a.item_id and a.po_no='.$unique;


$all_dealer[]=link_report_add_del_auto($res,'',5,7);
echo json_encode($all_dealer);

?>



