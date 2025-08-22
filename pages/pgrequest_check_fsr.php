<?php

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

session_start();
require_once 'config/dbcon.php';
require_once 'config/iv_key.php';
require_once 'config/global.php';
require_once 'config/mystore_func.php'; 

$conn = mysqli_connect(DB_HOST,DB_USERNAME,DB_PWD,DB_TBL);

$dec_nic_no = "";
//print_r($_POST);
	if( (isset($_GET['idn'])) && ($_GET['idn'] != NULL) && ($_GET['idn'] != "") && ($_GET['idn'] != " ") ){
	    $enc_nic_no = $_GET['idn'];
	    $dec_nic_no = decryptStr($enc_nic_no,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV); //local
		//dec_nic_no=$enc_nic_no; //local
	}
	// get personal details
	$sql_get_personal = "SELECT * FROM mst_personal_details WHERE nic_no ='$dec_nic_no'";
	$res_get_personal = mysqli_query($conn,$sql_get_personal);
	$row_get_personal = mysqli_fetch_array($res_get_personal);
	$name=$row_get_personal['stu_name_initials'];
	//$def_amount = $row_get_personal['amount'];
	$paymentDescription='Registration Fee - Foreign';
	$amount='100';
	$_SESSION['pay_amount']=$amount;
	$currencyType = 'USD';
	$_SESSION['my_cur_typ']=$currencyType;
	$email= $row_get_personal['stu_email'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		<title>KDU - Payment Gateway Verification </title>
		<link rel="stylesheet" href="style.css">
		<link href="dist/css/styles.css" rel="stylesheet" />
	    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
<script>
    // disable right click
    document.addEventListener('contextmenu', event => event.preventDefault());
 
    document.onkeydown = function (e) {
 
        // disable F12 key
        if(e.keyCode == 123) {
            return false;
        }
 
        // disable I key
        if(e.ctrlKey && e.shiftKey && e.keyCode == 73){
            return false;
        }
 
        // disable J key
        if(e.ctrlKey && e.shiftKey && e.keyCode == 74) {
            return false;
        }
 
        // disable U key
        if(e.ctrlKey && e.keyCode == 85) {
            return false;
        }
    }
 
</script>
<body oncontextmenu="return false;"> 
	   
	 <center>
     <div>
	 <div class="container">
	 <center><img border="0" src="logo.png" ></center>
		<form name="myform" id="ff" action="pgrequest.php" method="post" enctype="multipart/form-data">
			<div class="row justify-content-center">
				<div class="card">					
                    
                        <div class="card-body">
                        	<div class="row justify-content-center">
                        		<h4>Payment Verification </h4>
                        	</div>
                        	<div class="frame">
								<br><br>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>Name : </label>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control form-control-sm" name="payeename" value="<?php echo $name; ?>" readonly>
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
									<div class="col-md-4"  style="text-align:left !important">
											<label>NIC/Passport No : </label>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control form-control-sm" name="merchant_reference_no" value="<?php echo $dec_nic_no; ?>" readonly>
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>Payment Description : </label>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control form-control-sm" name="order_desc" value="<?php echo $paymentDescription; ?>" readonly>
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>Amount : </label>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control form-control-sm" name="amount" value="<?php echo $amount; ?>" readonly>
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>Currency Type : </label>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control form-control-sm" name="my_cur_typ" value="<?php echo $currencyType; ?>" readonly>
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>Contact Number with Contry code : </label>
										</div>
										<div class="col-md-6">
											<input type="text" class="form-control form-control-sm" name="fstu_mobile" maxlength = "20" value="<?php echo $contact_no; ?>" required>
											
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>Payer Relationship : </label>
										</div>
										<div class="col-md-6">											
											<td><select class="form-control" name="payer_relationship" editable="false" required="true" ><option value="">Please Select</option><option value="AGENT">AGENT</option><option value="FRIEND">FRIEND</option><option value="GUARDIAN">GUARDIAN</option><option value="PARENT">PARENT</option><option value="SELF">SELF</option></select></td>
										</div>
									</div>
									<div class="form-group row">
									<div class="col-md-2"></div>
										<div class="col-md-4"  style="text-align:left !important">
											<label>E-mail : </label>
										</div>
										<div class="col-md-6">
											<input type="email" class="form-control form-control-sm" name="fstu_email" value="<?php echo $email; ?>" readonly>
											
										</div>
									</div>
									<div class="form-group row">
										
										<!-- <div class="col-md-1">
											<input type="checkbox" name="agree_kdupolicy" id="agree_kdupolicy" required />
										</div> -->
										<div class="col-md-9">
											<input type="checkbox" name="agree_kdupolicy" id="agree_kdupolicy" required />
											<label>
												I agree with the <a href="http://enlistment.kdu.ac.lk/pg_sampath/KDU - IRC.html" target="_blank">return policy</a>
											</label>
										</div>
										<div class="col-md-3">
											<button type="submit" name="submit" id="submit" class="btn btn-info">
			                                    Submit
			                                </button>
										</div>
									</div>
									
									<!-- <center><input type="submit" value="Submit"></input></center>
 -->
							<!-- <table width="550" class="table">
								<tr>
									<td width="160">Name </td>
									<td><input name="payeename" value="<?php echo $name; ?>" readonly ></input></td>			    	
								</tr>
				                <tr>
				                	<td>NIC/Passport No</td>
									<td><input name="nic_no" value="<?php echo $dec_nic_no; ?>" readonly></input></td>					                    
								</tr>				
								
								<tr>
									<td>Payment Description</td>
									<td><input name="order_desc" value="<?php echo $paymentDescription; ?>" readonly ></input>							    	
								</tr>				
												
								<tr>
									<td><div id="amt_name">Amount</div></td>
									<td><input name="amount" id="amount" value="<?php echo $amount; ?>" readonly  ></input></div></td>
				                    <td><input name="my_cur_typ"  value="<?php echo $currencyType; ?>" readonly  ></input></div></td>						
								</tr>
								<tr>
									<td>Contact Number</td>
									<td><input name="fstu_mobile" value="<?php echo $contact_no; ?>" readonly ></input></td>
								</tr>
								<tr>
									<td>Payer Relationship</td>
				                    <td><input name="payer_relationship" value="<?php echo $payer_relationship; ?>" readonly ></input></td>	
								</tr>
								<tr>
									<td>E-mail</td>
									<td><input type="email" name="fstu_email" value="<?php echo $client_email; ?>" readonly></input></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><input type="checkbox" name="agree_kdupolicy" id="agree_kdupolicy" required /> I agree with the <a href="http://enlistment.kdu.ac.lk/pg_sampath/KDU - IRC.html" target="_blank">return policy</a></td>			    	
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>			    	
								</tr>
								<tr>
									<td></td>
									<td><center><input type="submit" value="Submit"></input></center></td>
								</tr>
						</table> -->
					</div>
				</div>
			</div>
		</form>
	
    </div>
	</center>	
</body>
</html>	

