<?php
require_once 'config/dbcon.php';
require_once 'config/iv_key.php';
require_once 'config/global.php';
//require_once 'config/mystore_func.php';
require_once('fpdf/fpdf.php');

$conn = mysqli_connect('127.0.0.1', 'root', 'L0n3w_lkRmPw', 'payment_gateway');
$sql = "SELECT trans_ref FROM tbl_transaction_details where (payment_desc='Registration Fee - Foreign' or payment_desc='Foriegn Application Fee')  and actual_payment_status='YES'";

$result1 = mysqli_query($conn, $sql);

if (mysqli_num_rows($result1) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result1)) {
        $NIC = $row["trans_ref"];
        $conn1 = mysqli_connect('127.0.0.1', 'root', 'L0n3w_lkRmPw', 'foreign_students_registration');
        $sql_ol_sub_updatec = " UPDATE mst_personal_details  
         SET  payment_status ='Yes'
         WHERE ( mst_personal_details.nic_no ='$NIC' )";

        if (mysqli_multi_query($conn1, $sql_ol_sub_updatec)) {
        } else {
            echo "Error: " . $sql_ol_sub_updatec . "<br>" . mysqli_error($conn1);
        } // multi
        mysqli_close($conn1); //fqsr
    } //while
} //rows>0    
mysqli_close($conn); //pg

$con = $con_fqsr; //mysqli_connect(DB_HOST,DB_USERNAME,DB_PWD,DB_TBL);	

$current_intake = $row_academicYear['intake'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql_get_personal = "SELECT * FROM mst_personal_details;";
    $start_date = isset($_POST["start_date"]) ? $_POST["start_date"] : null;
    $end_date = isset($_POST["end_date"]) ? $_POST["end_date"] : null;

    if (!empty($start_date) && !empty($end_date)) {
        $start_date .= ' 00:00:00';
        $end_date .= ' 23:59:59';
        $sql_get_personal = "SELECT * FROM mst_personal_details WHERE application_submit_dt BETWEEN '$start_date' AND '$end_date';";
    } else {

        $sql_get_personal = "SELECT * FROM mst_personal_details;";
    }
} else {

    $sql_get_personal = "SELECT * FROM mst_personal_details;";
}

//$sql_get_personal = "SELECT * FROM mst_personal_details where intake = '$current_intake';";
$res_get_personal = mysqli_query($con, $sql_get_personal);

$sql_get_closing_date = "SELECT * FROM intake ORDER BY ID DESC LIMIT 1;";
$res_get_closing_date = mysqli_query($con, $sql_get_closing_date);
$row_get_closing_date = mysqli_fetch_array($res_get_closing_date);
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

                                <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="true" data-key-events="true" data-resizable="true" data-cookie="true"
                                    data-cookie-id-table="saveId" data-click-to-select="true" data-toolbar="#toolbar">
                                    <thead>
                                        <!-- <tr><p>Student Details - Foreign Qualification </p></tr> -->
                                        <tr>
                                            <th data-field="id">Name & Address</th>
                                            <!-- <th>Given Name </th> -->
                                            <th>Passport No </th>
                                            <th>Citizenship/Country</th>
                                            <th>DoB</th>
                                            <th>Age(As at: <?php echo $row_get_closing_date[3] ?> )</th>
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


                                        </tr>
                                    </thead>


                                    <tbody>
                                        <?php

                                        while ($row = mysqli_fetch_row($res_get_personal)) {
                                            $ID = '"' . $row[1] . '"';
                                            $stu_id = '"' . $row[0] . '"';
                                            $dateOfBirth = $row[8];
                                            $closing_date = $row_get_closing_date[3];
                                            $diff = date_diff(date_create($dateOfBirth), date_create($closing_date));

                                        ?>
                                            <tr>
                                                <td><?php echo $row[6] . "<br>" . $row[12]; ?></td>
                                                <!-- <td><?php echo $row[7]; ?></td> -->
                                                <td><?php echo $row[1]; ?></td>
                                                <td><?php echo $row[23] . "<br>" . $row[24] . "<br>" . $row[25] . "<br>" . $row[10]; ?></td>
                                                <td><?php echo $row[8]; ?></td>
                                                <td><?php echo $diff->format('%y'); ?></td>
                                                <td><?php echo $row[11]; ?></td>
                                                <td><?php echo $row[12]; ?></td>
                                                <!-- <td><?php echo $row[23]; ?></td>
                                                <td><?php echo $row[24]; ?></td> -->
                                                <td><?php echo $row[13]; ?></td>
                                                <td><?php echo $row[2]; ?></td>
                                                <td><?php
                                                    $edu = "";
                                                    $sql_edu_qual = "SELECT * FROM mst_educational_qualifications where stu_nic = $ID AND exm_type = 'A/L'";

                                                    if ($result = mysqli_query($con, $sql_edu_qual)) {
                                                        $result2 = mysqli_query($con, $sql_edu_qual);
                                                        $result_1 = $result->fetch_row();
                                                        if ($result_1[2] != "") {
                                                            echo "Year: " . $result_1[2] . "<br>";
                                                        }
                                                        while ($row_edu = mysqli_fetch_row($result2)) {
                                                            $edu = "";

                                                            $edu = $row_edu[4] . ' - ' . $row_edu[5];
                                                            echo $edu;
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
                                                        if ($result_1[2] != "") {
                                                            echo "Year: " . $result_1[2] . "<br>";
                                                        }
                                                        while ($row_edu = mysqli_fetch_row($result2)) {
                                                            $edu = "";
                                                            $edu = $row_edu[4] . ' - ' . $row_edu[5];
                                                            echo $edu;
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
                                                            if ($row_ref[3] != "") {
                                                                echo "Refree: " . $row_ref[3] . "<br>";
                                                            }
                                                            if ($row_ref[4] != "") {
                                                                echo "Contact No: " . $row_ref[4] . "<br>";
                                                            }
                                                            echo "<br><br>";
                                                        }
                                                        mysqli_free_result($res_refree);
                                                    }
                                                    ?></td>
                                                <td><?php echo $row[15]; ?></td>
                                                <td><?php echo $row[17]; ?></td>
                                                <td><?php echo $row[28]; ?></td>
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
                        return column === 0 || column === 2 || column === 8 || column === 9 || column === 10 || column === 11 ?
                            data.replace(/<br ?\/?>/g, "\n") :
                            data;
                    }
                }
            }
        };

        $('#table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', $.extend(true, {}, buttonCommon, {
                    extend: 'excel'
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
            }],
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
        });

    });
</script>