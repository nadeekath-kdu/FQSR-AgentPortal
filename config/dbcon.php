<?php
    $userdb="root";
    $pass="";//L0n3w_lkRmPw

    $db="agent_portal";
    $con=mysqli_connect("localhost",$userdb,$pass);
    if(!$con)
    die("could not connect to mysql databass");
    mysqli_select_db($con,$db)
    or die ("could not open $db".mysqli_error($con));

    $db1="foreign_students_registration";
    $con_fqsr=mysqli_connect("localhost",$userdb,$pass);
    if(!$con_fqsr)
    die("could not connect to mysql databass");
    mysqli_select_db($con_fqsr,$db1)
    or die ("could not open $db1".mysqli_error($con_fqsr));
?>