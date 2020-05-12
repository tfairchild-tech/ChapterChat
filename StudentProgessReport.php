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
<button type="button" class="btn btn-success btn-xs" onclick="window.open(`StudentProgessReport.php?scId=<?php echo $scId;?>&newwin=2`)"><i class="fa fa-user"></i> Open in New Window</button>
<?php } ?>

<style>
#studentReport {
	min-width: 310px;
	max-width: 710px;
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

	$bookTitle="";	
 	if (isset($_GET["newwin"])) {
 		$year=2017;	
		$call = mysqli_prepare($conn, 'CALL ReadEdu.GetQuizHistory(? , ?)');
		mysqli_stmt_bind_param($call, 'ii', $scId, $year);
		mysqli_stmt_execute($call);
		$result = $call ->get_result();
		$history="<table class='table table-striped table-bordered'>";
		$history= $history . '<tr><th>Book Cover</th>';
		$history= $history . '<th>Book Title</th>';
		$history= $history . '<th>Author Name</th>';
		$history= $history . '<th>Genre</th>';
		$history= $history . '<th>Book Level</th>';
		$history= $history . '<th>Points</th>';
		$history= $history . '<th>Correctness</th>';
		$history= $history . '<th>Date/Time Quiz Taken</th>';
		$history= $history . '</tr>';
		
		while ($row = $result->fetch_assoc()) {

			$history=$history. '<tr>';
			$history= $history . '<td><img src="bookimages/'.$row["picPath"].'" /></td>';
			$history= $history . '<td><a href="quizReflection.php?studentQuizId='.$row['StudentQuizId'].'&viewOnly=1">'.$row['BookTitle'].'</a>';			
			if  ($row['IsPractice']==1)
				$history=$history.'<br/><span class="label label-success">practice</span>';

			$history=$history.'</td>';
			$history= $history . '<td>'.$row['authorname'].'</td>';
			$history= $history . '<td>'.$row['Genre'].'</td>';
			$history= $history . '<td>'.$row['BookLevel'].'</td>';
			$history= $history . '<td>'.$row['earnedPoints'].'/'.$row['Points'].'</td>';
			if  ($row['Passed']==1)
				$history= $history . '<td><font color="green"><b>'.$row['numCorrectQuestions'].'/'.$row['numOfQuestions'].'</b></font></td>';
			else
				$history= $history . '<td><font color="red"><b>'.$row['numCorrectQuestions'].'/'.$row['numOfQuestions'].'</b></font></td>';
			
			$date=date_create($row['DateOfQuiz']);	
			$history= $history . '<td>'.date_format($date,"F j, Y").' at '.$row['TimeOfQuiz'].'</td>';	
			$history=$history. '</tr>';
		}
		$history=$history. '</table>';	
		mysqli_stmt_close($call);
	}
	
	$call = mysqli_prepare($conn, 'CALL ReadEdu.rptStudentMonthlyReading(?)');
	mysqli_stmt_bind_param($call, 'i', $scId);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();

	$passed = array();
	$failed = array();
	$practice = array();
	$categories = array();
	while ($row = $result->fetch_assoc()) {
		$date=date_create($row['DateOfQuiz']);
		$categories[] = date_format($date,"M d");
		$passed[] = $row['countPassed'];
		$failed[] = $row['countFailed'];
		$practice[] = $row['countPractice'];
	}
	$cat= "'". join($categories, "','")."'";
	mysqli_stmt_close($call);

	$call = mysqli_prepare($conn, 'CALL ReadEdu.rptStudentReadingLvl(?)');
	mysqli_stmt_bind_param($call, 'i', $scId);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();
	$readingLvl = array();
	while ($row = $result->fetch_assoc()) {
		$readingLvl[] = $row['weightedavg'];
	}
			
	$call->close();
	include 'db_close.php';
	if (isset($_GET["newwin"]) ) {
		echo '<div class="jumbotron chapter_chat_jumbotron container container-fluid">';
		
	}
	echo '<div>'.$studentInfo;
	?>
		
	<div id="studentReport"></div>
	<?php
	
	if (isset($_GET["newwin"]) ) {
		echo ("<br/><br/><div>");
		echo ($history);
		echo ("</div>");
	}
	?>
	</div>
	 <?php 
	if (count($categories)>0) { 
	?>
<script type="text/javascript">
	$("#studentReport").html("ggg");
	Highcharts.chart('studentReport', {
		chart: {

			marginRight: 80 // like left
		},
		title: {
			text: 'Student Reading Progress Chart'
		},
		xAxis: {
			categories: [<?php echo $cat ?>],
			crosshair: true
		},
		yAxis: [{ // Primary yAxis
			labels: {
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			},
			title: {
				text: 'Number Of Quizzes',
				style: {
					color: Highcharts.getOptions().colors[0]
				}
			}
		}, { // Secondary yAxis
			title: {
				text: 'Reading Level',
				style: {
					color: Highcharts.getOptions().colors[3]
				}
			},
			opposite: true,
			labels: {
				style: {
					color: Highcharts.getOptions().colors[3]
				}
			}
		}],
		legend: {
			reversed: true
		},
		plotOptions: {
			series: {
				stacking: 'normal'
			}
		},
		series: [{
			name: 'Passed Test Quiz',
			type: 'column',
			color: 'lightgreen',
			maxPointWidth: 100,
			data: [<?php echo join(',', $passed) ?>]
		},{
			name: 'Not Passed Test Quiz',
			type: 'column',
			color: 'yellow',
			maxPointWidth: 100,			
			data: [<?php echo join(',', $failed) ?>]
		},{
			name: 'Practice',
			type: 'column',
			color: 'lightblue',
			maxPointWidth: 100,			
			data: [<?php echo join(',', $practice) ?>]
		},{
			name: 'ReadingLevel',
			type: 'spline',
			color: 'orange',
			maxPointWidth: 100,			
			yAxis:1,
			data: [<?php echo join(',', $readingLvl) ?>]
		}

		]
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