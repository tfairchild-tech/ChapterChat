<?php

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php');
$teacherid = $_SESSION["teacherid"];

$classId= $_GET['classId'];

//$call = mysqli_prepare($conn, 'CALL ReadEdu.rptTeacherClass(?)');
$call = mysqli_prepare($conn, 'CALL ReadEdu.getClassTeacherInfo(?)');
mysqli_stmt_bind_param($call, 'i', $classId);
mysqli_stmt_execute($call);


if (isset($_GET['newwin'])) {

?>
    <head>
    <title>Class Reading Progress Report</title>
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="js/jquery.peity.min.js"></script>
    <script src="js/datatableall/js/jquery.dataTables.min.js"></script>
    <script src="js/datatableall/js/dataTables.bootstrap.js"></script>

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
<?php } ?>
         <?php
		$result = $call -> get_result();
		$row_count = $result->num_rows;
		echo '
		<br/><br/>';

		if (!isset($_GET['newwin'])) {
			echo '
			<button type="button" class="btn btn-success btn-xs" onclick="window.open(`rptTeacherClass.php?classId='.$classId.'&newwin=2`)"><i class="fa fa-user"></i> Open in New Window</button>';
               
		}
		echo '

		 
		<div class="table-responsive">
		<h4>Class Reading Progress Report</h4>
		<table class="table table-info">
		    <thead>
		    <tr>
		        <th>Teacher Name</th>
		        <th>Class ID</th>
		        <th>Class Name</th>
		        <th>Grade</th>
		        <th>School Year</th>
		    </tr>
		    </thead>
		    <tbody>
		    '
		;
		
		while ($row = $result->fetch_assoc()) {
		 $teachername = $row["teacherFirstName"].' '.$row["teacherLastName"];
		 $grade = $row["GradeLevel"];
		 $schoolyear = $row["SchoolYear"];
		 $className = $row["className"];
		 echo '
		    <tr class=`bg-info`><th>'.$teachername.'</td><td>'.$classId.'</td><td>'.$className.'</td><td>'.$grade.'</td><td>'.$schoolyear.'</th>
		    </tr>
		 ';
		}
		echo '
		</tbody>
		</table>
		</div>
		';
		mysqli_stmt_close($call);
		
		$call = mysqli_prepare($conn, 'CALL ReadEdu.rptTeacherClass(?)');
		mysqli_stmt_bind_param($call, 'i', $classId);
		mysqli_stmt_execute($call);
		$result2 = $call -> get_result();
		echo '
		<div class="table-responsive">
		<table class="table table-hover table-bordered table-striped">
		    <thead>
		    <tr>
		        <th>Student Name</th>
		        <th>Student Total Points</th>
		    </tr>
		    </thead>
		    <tbody>
		';
		$pnts = array();
		$categories = array();
		$goal = array();
		while ($row = $result2->fetch_assoc()) {
			$studentname = $row["StudentFirstName"].' '.$row["StudentLastName"];
			$points= $row["totalpoints"];
			echo '<tr><td>'.$studentname .'</td><td>'.$points.'</td></tr>';
			$pnts[] = $points;
			$categories[] = $studentname;
			$goal []= 10;

		}
		$students = "'". join($categories, "','")."'";

		echo '
		</tbody>
		</table>
		</div>
		';
		
       	  ?>
	       	<div id="studentReport"></div>


<style>
#studentReport {
	min-width: 310px;
	height: 400px;
	margin: 0 auto;
    z-index: 500;
}
</style>
<script type="text/javascript">

		Highcharts.chart('studentReport', {
			chart: {
				backgroundColor: '#eefdec',
				marginRight: 80 // like left
			},
			title: {
				text: '<?php  echo '<b>'.$className.' ('.$teachername.')</b> Progress Report';   ?>'
			},
			xAxis: {
				categories: [<?php echo $students ?>],
				crosshair: true
			},
			yAxis: [{ // Primary yAxis
				labels: {
					style: {
						color: Highcharts.getOptions().colors[0]
					}
				},
				title: {
					text: 'Points',
					style: {
						color: Highcharts.getOptions().colors[0]
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
				data: [<?php echo join(',', $pnts) ?>]				
			},{
				name: 'Target',
				type: 'line',
				color:'red',
				data: [<?php echo join(',', $goal) ?>]				
			}
			]
		});
</script>
