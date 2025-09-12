<?php
include 'config/dbcon.php';
include('header.php');
$sql_last_rec = "SELECT * FROM agency ORDER BY rec_id DESC LIMIT 1";
$res_last_rec = mysqli_query($con, $sql_last_rec);
$row_get_agency = mysqli_fetch_array($res_last_rec);
$lastRec = $row_get_agency['rec_id'];
$addNo = 1;
$nextRec = $lastRec + $addNo;
$code = date('Y') . 'AGNT' . $nextRec;
//echo date('Y');
?>
<title>KDU</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/css/materialize.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.1/js/materialize.min.js"></script>
<script src="js/register.js"></script>
<?php
include('container.php');

?>

<div class="container">
	<h4>Agency Registration Form</h4>

	<div id="register-page" class="row">
		<div class="col s12 z-depth-6 card-panel">
			<form name="my-form" method="post" onsubmit="return validateForm()" action="../pages/agencySave.php" enctype="multipart/form-data">

				<div class="row margin" style="display: none;">
					<div class="input-field col s12">
						<i class="mdi mdi-blur prefix"></i>
						<input id="agency_code" name="agency_code" type="text" class="validate" value="<?php echo $code; ?>">
						<label for="agency_code" class="center-align">Agency Code&nbsp;<span class="error" style="color: #FF0000;">*</span></label>
					</div>
				</div>

				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-domain prefix"></i>
						<input id="organisation" name="organisation" type="text" class="validate">
						<label for="organisation" class="center-align">Organization Name<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-communication-email prefix"></i>
						<input id="addressLine1" name="addressLine1" type="text" class="validate">
						<label for="addressLine1" class="center-align">Address Line1<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-communication-email prefix"></i>
						<input id="addressLine2" name="addressLine2" type="text" class="validate">
						<label for="addressLine2" class="center-align">Address Line2</label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-communication-email prefix"></i>
						<input id="addressLine3" name="addressLine3" type="text" class="validate">
						<label for="addressLine3" class="center-align">Address Line3</label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-city prefix"></i>
						<input id="city" name="city" type="text" class="validate">
						<label for="city" class="center-align">Town/CIty<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-flag-checkered prefix"></i>
						<input id="country" name="country" type="text" class="validate">
						<label for="country" class="center-align">Country<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-home-modern prefix"></i>
						<input id="postcode" name="postcode" type="text" class="validate">
						<label for="postcode" class="center-align">PostCode</label>
					</div>
				</div>
				<!-- <div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-flag-triangle prefix"></i>
						<input id="office_country" name="office_country" type="text" class="validate">
						<label for="office_country" class="center-align">Country of Office</label>
					</div>
				</div> -->
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-gmail prefix"></i>
						<input id="email" name="email" type="email" class="validate">
						<label for="email" class="center-align">Email Address<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-communication-email prefix"></i>
						<input id="alt_email" name="alt_email" type="email" class="validate">
						<label for="alt_email" class="center-align">Alternative Email Address</label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-phone prefix"></i>
						<input id="telephone1" name="telephone1" type="text" class="validate">
						<label for="telephone1" class="center-align">Telephone 1 (ex: +94123456789)<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-phone prefix"></i>
						<input id="telephone2" name="telephone2" type="text" class="validate">
						<label for="telephone2" class="center-align">Telephone 2</label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-cellphone prefix"></i>
						<input id="mobile" name="mobile" type="text" class="validate">
						<label for="mobile" class="center-align">Mobile No</label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-deskphone prefix"></i>
						<input id="fax" name="fax" type="text" class="validate">
						<label for="fax" class="center-align">Fax No</label>
					</div>
				</div>
				<!-- <div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-account-box-outline prefix"></i>
						<input id="agency_group_code" name="agency_group_code" type="text" class="validate">
						<label for="agency_group_code" class="center-align">Agency Group Code</label>
					</div>
				</div> -->
				<!-- <div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-information-outline prefix"></i>
						<input id="description" name="description" type="text" class="validate">
						<label for="description" class="center-align">Description</label>
					</div>
				</div> -->
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-web prefix"></i>
						<input id="url" name="url" type="text" class="validate">
						<label for="url" class="center-align">URL/Web Address</label>
					</div>
				</div>
				Details of Company Owner/ Head of Company
				<hr>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-social-person-outline prefix"></i>
						<input id="fullname" name="fullname" type="text" class="validate">
						<label for="fullname" class="center-align">Full Name<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-blur prefix"></i>
						<input id="nic" name="nic" type="text" class="validate">
						<label for="nic" class="center-align">NIC/ Passport No<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-communication-email prefix"></i>
						<input id="ownerAddress" name="ownerAddress" type="text" class="validate">
						<label for="ownerAddress" class="center-align">Address(if different)</label>
					</div>
				</div>
				<hr>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi mdi-web prefix"></i>
						<label for="document" class="center-align">Upload Documents<span class="error" style="color: #FF0000;">&nbsp;*</span></label>
						<br><br>
						<p>You can upload multiple files here. Only PDF, DOCX, JPG, GIF and PNG types are accepted. Total size of files must be less than 2 megabytes.</p><br><br>
						<input id="document" name="document[]" type="file" class="validate" multiple>

					</div>
				</div>
				<!-- <div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-action-lock-outline prefix"></i>
						<input id="user_passw" type="password" class="validate">
						<label for="user_passw">Password</label>
					</div>
				</div>
				<div class="row margin">
					<div class="input-field col s12">
						<i class="mdi-action-lock-outline prefix"></i>
						<input id="confirm_pass" type="password">
						<label for="confirm_pass">Re-type password</label>
					</div>
				</div> -->
				<div class="row">
					<div class="input-field col s12">
						<button type="submit" class="btn waves-effect waves-light col s12">Register Now</button>
					</div>
					<div class="input-field col s12">
						<p class="margin center medium-small sign-up">Already have an account? <a href="login.php?code=0"">Login</a></p>
					</div>
				</div>
			</form>
		</div>
	</div>
		
	<div style=" margin:50px 0px 0px 0px;">
								<!-- <a class="btn btn-default read-more" style="background:#3399ff;color:white" href="http://webdamn.com/create-material-design-login-and-register-form" title="">Back to Tutorial</a>	 -->
					</div>
				</div>
				<!-- <?php include('footer.php'); ?>  -->