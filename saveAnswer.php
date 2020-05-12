<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	include('db_connect.php');
	
	$studentQuizId = $_GET["studentQuizId"];
	$answerId = $_GET["answerId"];
	$duration= $_GET["duration"];		
	$qid= $_GET["qid"];

	$call = mysqli_prepare($conn, 'CALL ReadEdu.saveAnswer(?, ?, ?,?)');
	mysqli_stmt_bind_param($call, 'iiii', $studentQuizId,$answerId,$qid,$duration);
	mysqli_stmt_execute($call);

	$result = $call ->get_result();		
	if (isset($result)==FALSE) {
		echo ("error");
	}else{
		echo ("success");
	}
	mysqli_stmt_close($call);
?>		