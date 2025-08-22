<?php
session_start();
require_once '../config/dbcon.php';
require_once '../config/iv_key.php';
require_once '../config/global.php';
//require_once 'config/mystore_func.php'; local

$conn = $con;

$dec_nic_no = "";
//print_r($_POST);
	if( (isset($_GET['idn'])) && ($_GET['idn'] != NULL) && ($_GET['idn'] != "") && ($_GET['idn'] != " ") ){
	    $enc_nic_no = $_GET['idn'];
	    //$dec_nic_no = decryptStr($enc_nic_no,ENCRYPT_METHOD,WSECRET_KEY,WSECRET_IV); //local
	    $dec_nic_no=$enc_nic_no; //local
	}
	// get personal details
	$sql_get_personal = "SELECT * FROM mst_personal_details WHERE nic_no ='$dec_nic_no'";
	$res_get_personal = mysqli_query($conn,$sql_get_personal);
	$row_get_personal = mysqli_fetch_array($res_get_personal);
	$name=$row_get_personal['stu_name_initials'];
	//$def_amount = $row_get_personal['amount'];
	$paymentDescription='Registration Fee';
	$amount='$100';
	$currencyType = 'USD';
	$email= $row_get_personal['stu_email'];

?>

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
<!-- <body oncontextmenu="return false;"> -->

	
	<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="payment-form card">
				<div class="card-header d-flex align-items-center">
                    <i class="bi bi-cash-stack" ></i> <!-- Example icon, replace with your preferred icon -->
                    <h4>Payment Verification</h4>
                </div>
                <div class="card-body">
                    <form name="myform" id="ff" action="pgrequest.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="payeename" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="payeename" name="payeename" value="<?php echo htmlspecialchars($name);?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="nic_no" class="form-label">NIC/Passport No:</label>
                            <input type="text" class="form-control" id="nic_no" name="nic_no" value="<?php echo htmlspecialchars($dec_nic_no);?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="order_desc" class="form-label">Payment Description:</label>
                            <input type="text" class="form-control" id="order_desc" name="order_desc" value="<?php echo htmlspecialchars($paymentDescription);?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount:</label>
                            <input type="text" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($amount);?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="currencyType" class="form-label">Currency Type:</label>
                            <input type="text" class="form-control" id="currencyType" name="currencyType" value="<?php echo htmlspecialchars($currencyType);?>" readonly>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="agree_kdupolicy" id="agree_kdupolicy" required>
                                <label class="form-check-label" for="agree_kdupolicy">
                                    I agree with the <a href="http://enlistment.kdu.ac.lk/pg_sampath/KDU - IRC.html" target="_blank">return policy</a>
                                </label>
                            </div>
                        </div>
                        <button type="submit" name="submit" id="submit" class="btn btn-primary w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


