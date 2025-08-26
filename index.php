<?php
// Debug: Check for agent_code
error_log('Agent code in URL: ' . (isset($_GET['agent_code']) ? $_GET['agent_code'] : 'not set'));

if (isset($_GET['agent_code'])) {
    $redirect_url = "includes/dashboard.php?agent_code=" . urlencode($_GET['agent_code']);
} else {
    $redirect_url = "includes/dashboard.php";
}

header("Location: " . $redirect_url);
exit();
