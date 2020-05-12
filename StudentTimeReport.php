<?php
include 'requireTeacher.php';
include('db_connect.php');
$scId= $_GET["scId"];

if (isset($_GET['newwin'])) {

?>
    <head>
    <title>Most Popular Books in Each Level</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>
    <script src="../ReadAndQuiz/bootstrap/js/bootstrap-datepicker.min.js"></script>
    <script src="js/jquery-validation/jquery.validate.min.js"></script>
    <script src="js/datatableall/js/jquery.dataTables.min.js"></script>
    <script src="js/datatableall/js/dataTables.bootstrap.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Baloo+Bhaina' type='text/css'>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
	-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<?php } else {?>
<br/><br/>
<button type="button" class="btn btn-success btn-xs" onclick="window.open(`StudentTimeReport.php?scId=<?php echo $scId;?>&newwin=2`)"><i class="fa fa-user"></i> Open in New Window</button>
<?php } ?>


<style>
#studentReport {
	min-width: 310px;
	height: 400px;
	margin: 0 auto;
    z-index: 500;
}
</style>

<?php
	$call = mysqli_prepare($conn, 'CALL ReadEdu.GetStudentClassInfo(?)');
	mysqli_stmt_bind_param($call, 'i', $scId);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();
	
	$studentInfo="<table class='table table-striped table-bordered'>";
	$studentInfo= $studentInfo. '<tr><th>Student Name</th>';
	$studentInfo= $studentInfo. '<th>Class Name</th>';
	$studentInfo= $studentInfo. '<th>Reading Level</th>';
	$studentInfo= $studentInfo. '</tr>';
	
	while ($row = $result->fetch_assoc()) {

		$studentInfo= $studentInfo. '<tr>';
		$studentInfo= $studentInfo. '<td>'.$row['StudentFirstName'].' '.$row['StudentLastName'].'</td>';
		$studentInfo= $studentInfo. '<td>'.$row['className'].'</td>';
		$studentInfo= $studentInfo. '<td>'.$row['ReadingLevel'].'</td>';
		$studentInfo= $studentInfo. '</tr>';
	}
	$studentInfo=$studentInfo. '</table>';
	mysqli_stmt_close($call);

	$call = mysqli_prepare($conn, 'CALL ReadEdu.rptStudentAnswerDuration(?)');
	mysqli_stmt_bind_param($call, 'i', $scId);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();

	$avgDuration = array();
	$reflections = array();
	while ($row = $result->fetch_assoc()) {		
		$passedtime = $row['durationOfSuccess'];
		$passedcount = $row['countOfSuccess'];
		$failedtime = $row['durationOfFailed'];
		$failedcount = $row['countOfFailed'];	
		if ($passedcount>0){
			$avgDuration []= number_format(($passedtime/60) /$passedcount);
		}else{$avgDuration []= 0;}
		if ($failedcount >0){
			$avgDuration []= number_format(($failedtime/60) /$failedcount) ;
		}else{$avgDuration []=0; }
		$reflections  []= 'Passed';
		$reflections  []= 'Not Passed';						
	}
	$ref = "'". join($reflections , "','")."'";
		
	$call->close();
	include 'db_close.php';
	if (isset($_GET["newwin"]) ) {
		echo '<div class="jumbotron chapter_chat_jumbotron container container-fluid">';
		
	}
	echo '<div>'.$studentInfo;
	?>
	<div id="studentReport"></div>
	</div>
	
	 <?php 
	
	if (count($reflections )>0) { 
?>
<script type="text/javascript">
	$("#studentReport").html("ggg");
	Highcharts.chart('studentReport', {
		chart: {

			marginRight: 80 // like left
		},
		title: {
			text: 'Student Reading Time Analysis Chart'
		},
		xAxis: {
			categories: [<?php echo $ref ?>],
			crosshair: true
		},
		yAxis: [{ // Primary yAxis
			labels: {
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			},
			title: {
				text: 'Average Time to Answer Question (Minutes)',
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			}
		}],
		legend: {
			reversed: true
		},
		series: [{
			name: 'Quizzes',
			type: 'bar',
			data: [<?php echo join(',', $avgDuration ) ?>]
		}]
	});
	
</script>
<?php } 
else{
?>
<script type="text/javascript">
	$("#studentReport").html("<div class='attention'>This student has no reading history in this system!</div>");
</script>
<?php
}
?>