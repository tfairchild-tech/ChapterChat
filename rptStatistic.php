<?php

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php');
$teacherid = $_SESSION["teacherid"];

$classId= $_GET['classId'];

$call = mysqli_prepare($conn, 'CALL ReadEdu.rptBookStatistic()');
mysqli_stmt_execute($call);


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
			<button type="button" class="btn btn-success btn-xs" onclick="window.open(`rptStatistic.php?newwin=2`)"><i class="fa fa-user"></i> Open in New Window</button>';
		}
		echo '
		<div class="table-responsive">
		<h4>Most Popular Books in Each Level</h4>
		<table class="table table-info table-striped">
		    <thead>
		    <tr>
		        <th>Book Title</th>
		        <th>Genre</th>
		        <th>Author Name</th>
		        <th>Book Level</th>
		    </tr>
		    </thead>
		    <tbody>
		    '
		;
		
		while ($row = $result->fetch_assoc()) {
		 $Authorname= $row["authorFirstName"].' '.$row["authorLastName"];
		 $genre= $row["Genre"];
		 $booktitle= $row["bookTitle"];
		 $booklvl= $row["bookLevel"];
		 echo '
		    <tr class=`bg-info`><th>'.$booktitle.'</td><td>'.$genre.'</td><td>'.$Authorname.'</td><td>'.$booklvl.'</th>
		    </tr>
		 ';
		}
		echo '
		</tbody>
		</table>
		</div>
		';
		
       	  ?>


