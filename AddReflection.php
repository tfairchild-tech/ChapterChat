<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	include('db_connect.php');
	
	
	$refId = $_GET["refId"];			
	$qid= $_GET["qid"];
	$studentQuizId= $_GET["studentQuizId"];

	$call = mysqli_prepare($conn, 'CALL ReadEdu.AddUpdateStudnetRef(?, ?, ?)');
	mysqli_stmt_bind_param($call, 'iii', $studentQuizId,$qid,$refId);
	mysqli_stmt_execute($call);

	$result = $call ->get_result();		
	if (isset($result)==FALSE) {
		echo ("error");
	}else{
		echo ("success");
	}
	mysqli_stmt_close($call);
?>		