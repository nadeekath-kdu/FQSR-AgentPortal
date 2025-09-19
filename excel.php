<?php
require_once 'config/dbcon.php';
require_once 'config/iv_key.php';
require_once 'config/global.php';
//require_once 'config/mystore_func.php';
// Note: FPDF is not used in this file; remove to avoid fatal if path not present
// require_once('fpdf/fpdf.php');

// Sync payment status from payment_gateway DB if available (non-fatal if missing)
$conn = @mysqli_connect('127.0.0.1', 'root', 'L0n3w_lkRmPw', 'payment_gateway');
if ($conn) {
    $sql = "SELECT trans_ref FROM tbl_transaction_details WHERE (payment_desc='Registration Fee - Foreign' OR payment_desc='Foriegn Application Fee') AND actual_payment_status='YES'";
    $result1 = mysqli_query($conn, $sql);
    if ($result1) {
        if (mysqli_num_rows($result1) > 0) {
            while ($row = mysqli_fetch_assoc($result1)) {
                $NIC = $row["trans_ref"];
                $conn1 = @mysqli_connect('127.0.0.1', 'root', 'L0n3w_lkRmPw', 'foreign_students_registration');
                if ($conn1) {
                    $sql_ol_sub_updatec = "UPDATE mst_personal_details SET payment_status='Yes' WHERE mst_personal_details.nic_no='" . mysqli_real_escape_string($conn1, $NIC) . "'";
                    if (!mysqli_query($conn1, $sql_ol_sub_updatec)) {
                        error_log('excel.php: Update payment_status failed: ' . mysqli_error($conn1));
                    }
                    mysqli_close($conn1); // fqsr
                } else {
                    error_log('excel.php: foreign_students_registration connection failed: ' . mysqli_connect_error());
                }
            }
        }
        mysqli_free_result($result1);
    } else {
        error_log('excel.php: payment_gateway query failed: ' . mysqli_error($conn));
    }
    mysqli_close($conn); // pg
} else {
    // Log and continue; do not break page rendering
    error_log('excel.php: payment_gateway connection failed: ' . mysqli_connect_error());
}

$con = $con_fqsr; //mysqli_connect(DB_HOST,DB_USERNAME,DB_PWD,DB_TBL);
// $row_academicYear may not be set in all environments; avoid undefined var
// $current_intake = $row_academicYear['intake'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql_get_personal = "SELECT * FROM mst_personal_details ORDER BY applicant_id";
    $start_date = isset($_POST["start_date"]) ? $_POST["start_date"] : null;
    $end_date = isset($_POST["end_date"]) ? $_POST["end_date"] : null;

    if (!empty($start_date) && !empty($end_date)) {
        $start_date .= ' 00:00:00';
        $end_date .= ' 23:59:59';
        $sql_get_personal = "SELECT * FROM mst_personal_details WHERE application_submit_dt BETWEEN '$start_date' AND '$end_date' ORDER BY applicant_id";
    } else {

        $sql_get_personal = "SELECT * FROM mst_personal_details ORDER BY applicant_id";
    }
} else {

    $sql_get_personal = "SELECT * FROM mst_personal_details ORDER BY applicant_id";
}

//$sql_get_personal = "SELECT * FROM mst_personal_details where intake = '$current_intake';";
$res_get_personal = mysqli_query($con, $sql_get_personal);

$sql_get_closing_date = "SELECT * FROM intake ORDER BY ID DESC LIMIT 1;";
$res_get_closing_date = mysqli_query($con, $sql_get_closing_date);
$row_get_closing_date = $res_get_closing_date ? mysqli_fetch_array($res_get_closing_date) : null;
$closing_date_display = ($row_get_closing_date && isset($row_get_closing_date[3]) && $row_get_closing_date[3]) ? $row_get_closing_date[3] : date('Y-m-d');
$personal_row_cnt = mysqli_num_rows($res_get_personal);
// --------------------


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>KDU-Foreign Student List</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <link rel="shortcut icon" type="image/x-icon" href="img/Kdu.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <!-- Google Fonts ============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- Bootstrap CSS ============================================ -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS ============================================ -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css">



    <style>
        #table th {

            background-color: #87CEEB;
            color: white;
        }

        .daterange-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button.clear-btn {
            background-color: #f44336;
        }
    </style>

</head>

<body>

    <!-- Static Table Start -->

    <div class="data-table-area mg-b-15">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="sparkline13-list">
                        <div class="sparkline13-hd">
                            <div class="main-sparkline13-hd">
                                <h1>Student Details - Foreign Qualification </h1>
                            </div>
                        </div>
                        <br><br>
                        <form id="dateRangeForm" method="POST">
                            <div class="daterange-container">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date">

                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date">

                                <button type="submit">Filter</button>
                                <button type="button" class="clear-btn" onclick="clearDateRange()">Clear</button>
                            </div>
                        </form>
                        <br>
                        <div class="sparkline13-graph">
                            <div class="datatable-dashv1-list custom-datatable-overright">

                                <table id="table" class="table table-striped table-hover table-sm nowrap" style="width:100%" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="true" data-key-events="true" data-resizable="true" data-cookie="true"
                                    data-cookie-id-table="saveId" data-click-to-select="true" data-toolbar="#toolbar">
                                    <thead>
                                        <!-- <tr><p>Student Details - Foreign Qualification </p></tr> -->
                                        <tr>
                                            <th data-field="id">Name & Address</th>
                                            <!-- <th>Given Name </th> -->
                                            <th>Passport No </th>
                                            <th>Citizenship/Country</th>
                                            <th>DoB</th>
                                            <th>Age(As at: <?php echo htmlspecialchars($closing_date_display); ?> )</th>
                                            <th>Civil Status </th>
                                            <th>Address </th>
                                            <!--  <th>Contact No </th>
                                              <th>Mobile No</th> -->
                                            <th>Email</th>
                                            <th>Applied Course </th>
                                            <th>Advanced Level</th>
                                            <th>Ordinary Level</th>


                                            <th>Refree</th>
                                            <th>Application Submit Date</th>
                                            <th>Document Upload Link</th>
                                            <th>Payment Status</th>
                                            <th>Actions</th>


                                        </tr>
                                    </thead>


                                    <tbody>
                                        <?php

                                        while ($row = $res_get_personal ? mysqli_fetch_row($res_get_personal) : null) {
                                            if (!$row) {
                                                break;
                                            }
                                            $ID = '"' . $row[1] . '"';
                                            $stu_id = '"' . $row[0] . '"';
                                            $dateOfBirth = isset($row[8]) ? $row[8] : null;
                                            $closing_date = $closing_date_display;
                                            $ageYears = '';
                                            if ($dateOfBirth) {
                                                try {
                                                    $diff = date_diff(date_create($dateOfBirth), date_create($closing_date));
                                                    $ageYears = $diff ? $diff->format('%y') : '';
                                                } catch (Exception $e) {
                                                    $ageYears = '';
                                                }
                                            }

                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row[6]) . "<br>" . htmlspecialchars($row[12]); ?></td>
                                                <!-- <td><?php echo $row[7]; ?></td> -->
                                                <td><?php echo htmlspecialchars($row[1]); ?></td>
                                                <td><?php echo htmlspecialchars($row[23]) . "<br>" . htmlspecialchars($row[24]) . "<br>" . htmlspecialchars($row[25]) . "<br>" . htmlspecialchars($row[10]); ?></td>
                                                <td><?php echo htmlspecialchars($row[8]); ?></td>
                                                <td><?php echo htmlspecialchars($ageYears); ?></td>
                                                <td><?php echo htmlspecialchars($row[11]); ?></td>
                                                <td><?php echo htmlspecialchars($row[12]); ?></td>
                                                <!-- <td><?php echo $row[23]; ?></td>
                                                <td><?php echo $row[24]; ?></td> -->
                                                <td>
                                                    <?php $email = isset($row[13]) ? trim($row[13]) : ''; ?>
                                                    <?php if ($email): ?>
                                                        <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php
                                                    // Fetch applied degrees with preferences from appliedDegrees table
                                                    $applied_degrees = "";
                                                    $sql_applied_degrees = "SELECT degree_name, preference FROM appliedDegrees WHERE student_id = $stu_id OR nic = $ID ORDER BY preference ASC";
                                                    
                                                    if ($result_degrees = mysqli_query($con, $sql_applied_degrees)) {
                                                        $degree_count = 0;
                                                        while ($row_degree = mysqli_fetch_row($result_degrees)) {
                                                            $degree_count++;
                                                            $degree_name = isset($row_degree[0]) ? $row_degree[0] : '';
                                                            $preference = isset($row_degree[1]) ? $row_degree[1] : $degree_count;
                                                            
                                                            if ($degree_name != "") {
                                                                // Format preference display
                                                                $pref_suffix = '';
                                                                switch ($preference) {
                                                                    case 1: $pref_suffix = 'st Choice'; break;
                                                                    case 2: $pref_suffix = 'nd Choice'; break;
                                                                    case 3: $pref_suffix = 'rd Choice'; break;
                                                                    default: $pref_suffix = 'th Choice'; break;
                                                                }
                                                                
                                                                echo "<strong>" . $preference . $pref_suffix . ":</strong> " . htmlspecialchars($degree_name) . "<br>";
                                                            }
                                                        }
                                                        mysqli_free_result($result_degrees);
                                                        
                                                        // Fallback to original field if no degrees found in appliedDegrees table
                                                        if ($degree_count == 0 && isset($row[2]) && $row[2] != "") {
                                                            echo htmlspecialchars($row[2]);
                                                        }
                                                    } else {
                                                        // Fallback to original field if query fails
                                                        if (isset($row[2]) && $row[2] != "") {
                                                            echo htmlspecialchars($row[2]);
                                                        }
                                                    }
                                                    ?></td>
                                                <td><?php
                                                    $edu = "";
                                                    $sql_edu_qual = "SELECT * FROM mst_educational_qualifications where stu_nic = $ID AND exm_type = 'A/L'";

                                                    if ($result = mysqli_query($con, $sql_edu_qual)) {
                                                        $result2 = mysqli_query($con, $sql_edu_qual);
                                                        $result_1 = $result->fetch_row();
                                                        if ($result_1 && isset($result_1[2]) && $result_1[2] != "") {
                                                            echo "Year: " . htmlspecialchars($result_1[2]) . "<br>";
                                                        }
                                                        while ($row_edu = mysqli_fetch_row($result2)) {
                                                            $edu = "";

                                                            $edu = (isset($row_edu[4]) ? $row_edu[4] : '') . ' - ' . (isset($row_edu[5]) ? $row_edu[5] : '');
                                                            echo htmlspecialchars($edu);
                                                            echo "\t";
                                                            echo "<br>";
                                                        }
                                                        mysqli_free_result($result);
                                                    }
                                                    ?></td>
                                                <td><?php
                                                    $edu = "";
                                                    $sql_edu_qual = "SELECT * FROM mst_educational_qualifications where stu_nic = $ID AND exm_type = 'O/L'";

                                                    if ($result = mysqli_query($con, $sql_edu_qual)) {
                                                        $result2 = mysqli_query($con, $sql_edu_qual);
                                                        $result_1 = $result->fetch_row();
                                                        if ($result_1 && isset($result_1[2]) && $result_1[2] != "") {
                                                            echo "Year: " . htmlspecialchars($result_1[2]) . "<br>";
                                                        }
                                                        while ($row_edu = mysqli_fetch_row($result2)) {
                                                            $edu = "";
                                                            $edu = (isset($row_edu[4]) ? $row_edu[4] : '') . ' - ' . (isset($row_edu[5]) ? $row_edu[5] : '');
                                                            echo htmlspecialchars($edu);
                                                            echo "<br>";
                                                        }
                                                        mysqli_free_result($result);
                                                    }
                                                    ?></td>



                                                <td><?php
                                                    $edu = "";
                                                    $sql_refree = "SELECT * FROM refree where stu_id = $stu_id;";
                                                    if ($res_refree = mysqli_query($con, $sql_refree)) {
                                                        while ($row_ref = mysqli_fetch_row($res_refree)) {
                                                            if (isset($row_ref[3]) && $row_ref[3] != "") {
                                                                echo "Refree: " . htmlspecialchars($row_ref[3]) . "<br>";
                                                            }
                                                            if (isset($row_ref[4]) && $row_ref[4] != "") {
                                                                echo "Contact No: " . htmlspecialchars($row_ref[4]) . "<br>";
                                                            }
                                                            echo "<br><br>";
                                                        }
                                                        mysqli_free_result($res_refree);
                                                    }
                                                    ?></td>
                                                <td><?php echo htmlspecialchars($row[15]); ?></td>
                                                <td>
                                                    <?php $docLink = isset($row[17]) ? trim($row[17]) : ''; ?>
                                                    <?php if ($docLink && preg_match('/^https?:\/\//i', $docLink)): ?>
                                                        <a href="<?php echo htmlspecialchars($docLink); ?>" target="_blank" rel="noopener noreferrer">Open Link</a>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($docLink); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php $pay = isset($row[28]) ? strtolower(trim($row[28])) : ''; ?>
                                                    <?php if ($pay === 'yes' || $pay === 'paid'): ?>
                                                        <span class="badge badge-success">Paid</span>
                                                    <?php elseif ($pay === 'no' || $pay === 'unpaid'): ?>
                                                        <span class="badge badge-danger">Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($row[28]); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php $nicSan = isset($row[1]) ? urlencode($row[1]) : ''; ?>
                                                    <a class="btn btn-sm btn-primary" target="_blank" href="pages/view_documents.php?nic=<?php echo $nicSan; ?>">
                                                        <i class="fa fa-folder-open"></i> View Docs
                                                    </a>
                                                </td>
                                            <?PHP
                                        }

                                            ?>

                                            </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

    <script src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

</body>

</html>

<script>
    function clearDateRange() {
        document.getElementById("start_date").value = "";
        document.getElementById("end_date").value = "";
        document.getElementById("dateRangeForm").submit();
    }

    $(document).ready(function() {

        var buttonCommon = {
            exportOptions: {
                format: {
                    body: function(data, row, column, node) {
                        // Handle columns with <br> tags and HTML formatting
                        if (column === 0 || column === 2 || column === 8 || column === 9 || column === 10 || column === 11) {
                            return data.replace(/<br ?\/?>/g, "\n").replace(/<[^>]*>/g, "");
                        }
                        return data.replace(/<[^>]*>/g, "");
                    }
                }
            }
        };

        $('#table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', $.extend(true, {}, buttonCommon, {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }), 'pdf', 'print'
            ],
            columnDefs: [{
                    targets: 12,
                    type: 'date',
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return moment(data, 'YYYY-MM-DD').format('YYYY-MM-DD');
                        }
                        return data;
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: true,
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
        });

    });
</script>