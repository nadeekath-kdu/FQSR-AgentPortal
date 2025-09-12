<?php
session_start();
include '../../../config/dbcon.php';
$fro_role = 'FRO';
$dr_role = 'DR';
$dvc_role = 'DVC';
$vc_role = 'VC';

$user_role = $_SESSION['user_role'];
$agency_code = $_GET['agency_code'];
$sql_get_agency = "SELECT * FROM agency WHERE agency_code ='$agency_code'    ";
$res_get_agency = mysqli_query($con, $sql_get_agency);
$row_get_agency = mysqli_fetch_array($res_get_agency);
$code = $row_get_agency['agency_code'];
?>
<link rel="stylesheet" href="../../assets/css/register_nav.css" />
<div class="container">
    <div id="register-page" class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Agency Details</h2>
                    <small class="text-muted float-end"></small>
                </div>
                <div class="card-body"><!-- action="../pages/updateagencystatus.php" -->
                    <form class="register-form" method="post" onsubmit="return validateForm()" name="my-form" id="my-form">
                        <input type="text" id="user_role" name="user_role" value="<?php echo $user_role; ?>" style="display: none;" />
                        <input type="text" id="code" name="code" value="<?php echo $code; ?>" style="display: none;" />
                        <br><br>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-fullname">Organization Name
                                    <span class="error" style="color: #FF0000;">&nbsp;*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-fullname2" class="input-group-text"><i class="bx bx-buildings"></i></span>
                                    <input type="text" class="form-control" id="organisation" name="organisation" placeholder="ACME Inc." aria-label="ACME Inc." aria-describedby="basic-icon-default-fullname2" value="<?php echo $row_get_agency['organisation']; ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">Address
                                    <span class="error" style="color: #FF0000;">&nbsp;*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="text" id="addressLine1" name="addressLine1" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['addressLine1']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-email">Town/City</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-detail"></i></span>
                                    <input type="text" id="city" name="city" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-email2" value="<?php echo $row_get_agency['city']; ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">Country</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="text" id="country" name="country" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['country']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">PostCode</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="text" id="postcode" name="postcode" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['postcode']; ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-email">Email</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="john.dev" aria-label="john.dev" aria-describedby="basic-icon-default-email2" value="<?php echo $row_get_agency['email']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-phone">Phone No</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i class="bx bx-phone"></i></span>
                                    <input type="text" id="telephone1" name="telephone1" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" value="<?php echo $row_get_agency['telephone1']; ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-phone">Mobile No</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-phone2" class="input-group-text"><i class="bx bx-phone"></i></span>
                                    <input type="text" id="mobile" name="mobile" class="form-control phone-mask" placeholder="658 799 8941" aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" value="<?php echo $row_get_agency['mobile']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">Fax No</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text"><i class="bx bx-fax"></i></span>
                                    <input type="text" id="fax" name="fax" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['fax']; ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">URL/Web Address</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text"><i class="bx bx-web"></i></span>
                                    <input type="text" id="url" name="url" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['url']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">Full Name</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-name" class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-user" value="<?php echo $row_get_agency['fullname']; ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-nis">NIC/ Passport No</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-nic" class="input-group-text"><i class="bx bx-paasport"></i></span>
                                    <input type="text" id="nic" name="nic" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['owner_nic']; ?>" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="basic-icon-default-company">Owner Address</label>
                                <div class="input-group input-group-merge">
                                    <span id="basic-icon-default-company2" class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="text" id="fax" name="fax" class="form-control" placeholder="" aria-label="" aria-describedby="basic-icon-default-company2" value="<?php echo $row_get_agency['owner_address']; ?>" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 shadow-sm border-0">
                            <div class="card-body">
                                <div class="row g-4">

                                    <?php if (!empty($row_get_agency['status_fro'])) { ?>
                                        <div class="col-md-12">
                                            <div class="rounded border-start border-5 border-primary bg-light p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-primary fw-bold">
                                                        <i class="bi bi-person-badge"></i> Review by FRO :
                                                        <span class="badge bg-primary px-3 py-2"><?php echo $row_get_agency['status_fro']; ?></span>
                                                    </h6>

                                                </div>
                                                <?php if (!empty($row_get_agency['remark_fro'])) { ?>
                                                    <div class="mt-2">
                                                        <label class="fw-semibold text-dark mb-1">Due To:</label>
                                                        <div class="bg-warning bg-opacity-75 text-dark p-2 rounded">
                                                            <i class="bi bi-chat-left-quote-fill me-1 text-dark"></i>
                                                            <?php echo $row_get_agency['remark_fro']; ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($row_get_agency['status_dr'])) { ?>
                                        <div class="col-md-12">
                                            <div class="rounded border-start border-5 border-success bg-light p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-success fw-bold">
                                                        <i class="bi bi-patch-check-fill"></i> Verification by DR:
                                                        <span class="badge bg-success px-3 py-2"><?php echo $row_get_agency['status_dr']; ?></span>
                                                    </h6>

                                                </div>
                                                <?php if (!empty($row_get_agency['remark_dr'])) { ?>
                                                    <div class="mt-2">
                                                        <label class="fw-semibold text-dark mb-1">Remark:</label>
                                                        <div class="bg-warning bg-opacity-75 text-dark p-2 rounded">
                                                            <i class="bi bi-chat-left-quote-fill me-1 text-dark"></i>
                                                            <?php echo $row_get_agency['remark_dr']; ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($row_get_agency['status_dvc'])) { ?>
                                        <div class="col-md-12">
                                            <div class="rounded border-start border-5 border-info bg-light p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-info fw-bold">
                                                        <i class="bi bi-lightbulb-fill"></i> Recommendation by DVC:
                                                        <span class="badge bg-info text-dark px-3 py-2"><?php echo $row_get_agency['status_dvc']; ?></span>
                                                    </h6>

                                                </div>
                                                <?php if (!empty($row_get_agency['remark_dvc'])) { ?>
                                                    <div class="mt-2">
                                                        <label class="fw-semibold text-dark mb-1">Remark:</label>
                                                        <div class="bg-warning bg-opacity-75 text-dark p-2 rounded">
                                                            <i class="bi bi-chat-left-quote-fill me-1 text-dark"></i>
                                                            <?php echo $row_get_agency['remark_dvc']; ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($row_get_agency['status_vc'])) { ?>
                                        <div class="col-md-12">
                                            <div class="rounded border-start border-5 border-danger bg-light p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 text-danger fw-bold">
                                                        <i class="bi bi-award-fill"></i> Approval by VC:
                                                        <span class="badge bg-danger px-3 py-2"><?php echo $row_get_agency['status_vc']; ?></span>
                                                    </h6>

                                                </div>
                                                <?php if (!empty($row_get_agency['remark_vc'])) { ?>
                                                    <div class="mt-2">
                                                        <label class="fw-semibold text-dark mb-1">Remark:</label>
                                                        <div class="bg-warning bg-opacity-75 text-dark p-2 rounded">
                                                            <i class="bi bi-chat-left-quote-fill me-1 text-dark"></i>
                                                            <?php echo $row_get_agency['remark_vc']; ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>



                    </form>
                </div>
            </div>
        </div>
    </div>

    <div style="margin:50px 0px 0px 0px;">
        <!-- <a class="btn btn-default read-more" style="background:#3399ff;color:white" href="http://webdamn.com/create-material-design-login-and-register-form" title="">Back to Tutorial</a>	 -->
    </div>
</div>

<script src="../../assets/js/admin/viewagency.js"></script>