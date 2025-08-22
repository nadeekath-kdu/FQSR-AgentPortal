<?php session_start();
include("pgconfig.php"); 
include("conn.php");
$def_amount = $_POST["amount"];
$amount = $def_amount*100;
?>
<html>
<?php

//$perform=$_POST["perform"];
//$currencyCode=$_POST["currency_code"];

//$amount=$_POST["amount"]*100;
$merchantReferenceNo=$_POST["merchant_reference_no"];
$merchantReferenceNo=mysql_real_escape_string($merchantReferenceNo);
//$orderDesc=$_POST["order_desc"];
$orderDesc = $_POST["order_desc"];
$orderDesc=mysql_real_escape_string($orderDesc);
$payer_relationship=$_POST["payer_relationship"];
$contact_no = $_POST["fstu_mobile"];
$client_email= $_POST["fstu_email"];
$payee_name = $_POST["payeename"];
$kdu_cur = $_POST["my_cur_typ"];
$kdu_client_id = '';

if($kdu_cur == 'LKR'){
	$kdu_client_id = '14000317';
}
if($kdu_cur == 'USD'){
	$kdu_client_id = '14000701';
}

//$sql_add = "INSERT INTO tbl_transaction_details (payee_name,amount,trans_ref,payment_desc,payer_relationship,contact_no,customer_email,currency) VALUES('$payee_name',$def_amount,'$merchantReferenceNo','$orderDesc','$payer_relationship','$contact_no','$client_email','$kdu_cur')";
//$res = mysql_query($sql_add);

//$messageHash = $pgInstanceId."|".$merchantId."|".$perform."|".$currencyCode."|".$amount."|".$merchantReferenceNo."|".$hashKey."|";
//$message_hash = "CURRENCY:7:".base64_encode(sha1($messageHash, true));
?>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<title>Processing..</title>
<script language="javascript">
function onLoadSubmit() {
	document.merchantForm.submit();
}
</script>
</head>

<body onLoad="onLoadSubmit();">
	<br />&nbsp;<br />
	<center><font size="5" color="#3b4455">Transaction is being processed,<br/>Please wait ...</font></center>
	<form name="merchantForm" method="post" action="<?php echo $pgdomain;?>">		
	<input type="hidden" name="amount" value="<?php echo $amount;?>" />
	<input type="hidden" name="merchant_reference_no" value="<?php echo $merchantReferenceNo;?>" />
	<input type="hidden" name="order_desc" value="<?php echo $orderDesc;?>" />
	<input type="hidden" name="currency" value="<?php echo $kdu_cur;?>" />
	<input type="hidden" name="kdumyclientid" value="<?php echo $kdu_client_id;?>" />
	

	<noscript>
		<br />&nbsp;<br />
		<center>
		<font size="3" color="#3b4455">
		JavaScript is currently disabled or is not supported by your browser.<br />
		Please click Submit to continue the processing of your transaction.<br />&nbsp;<br />
		<input type="submit" />
		</font>
		</center>
	</noscript>
	</form>
</body>
</html>
