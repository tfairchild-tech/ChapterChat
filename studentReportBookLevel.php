
	<?php
    // Start the session
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
    }
	include 'requirestudent.php';
	
	include('db_connect.php');
	ini_set('display_errors', 1);
	
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	echo ($_SESSION["SCId"]);
	$call = mysqli_prepare($conn, 'CALL ReadEdu.rptStudentBookLevel(?)');
	mysqli_stmt_bind_param($call, 'i', $_SESSION["SCId"]);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();
	$json = array();
	while ($row = $result->fetch_assoc()) {
		$json[] = $row;
	}

	echo json_encode($json);

?>