<?PHP 
	include 'requireStudent.php';
	include 'header.php'; 
	include('db_connect.php');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$bookTitle="";	
    if (isset($_POST["viewQuizzes"])) {
	
		$call = mysqli_prepare($conn, 'CALL ReadEdu.GetQuizHistory(? , ?)');
		mysqli_stmt_bind_param($call, 'ii', $_SESSION["SCId"], $_POST["year"]);
		mysqli_stmt_execute($call);
		$result = $call ->get_result();
		$history='<ul>';
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
			else {
			    if ($row['numCorrectQuestions'] == null) {
                    $num_correct = 0;
                } else {
                    $num_correct = $row['numCorrectQuestions'];
                }
                $history = $history . '<td><font color="red"><b>' . $num_correct . '/' . $row['numOfQuestions'] . '</b></font></td>';
            }
			$date=date_create($row['DateOfQuiz']);	
			$history= $history . '<td>'.date_format($date,"F j, Y").' at '.$row['TimeOfQuiz'].'</td>';	
			$history=$history. '</tr>';
		}
		$history=$history. '</table>';
		
				
		$call->close();	
		include 'db_close.php';
	}
	if (isset($_POST["year"]) ){
		$passedyear=$_POST["year"];
	}else{
		$passedyear=date("Y");
	 }
?>

<div class="container">
    <div class="pull-right">
        <a href="studentHome.php"><br>
            <span class="glyphicon glyphicon-remove">&nbsp;&nbsp;</span>
        </a>
    </div>
    <div class="jumbotron chapter_chat_jumbotron">
        <div class="row center-block">
            <div >
                <div class="chapterchat-font-plain-lg">View Your Taken Quizzes</div>
                <form class="form-signin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group col-lg-6 text-center">
                        <div class="input-group">
							
							<select class="form-control" id="year" name="year"  required autofocus>								
								<option value='<?php echo(date("Y")) ?>' <?php if ($passedyear ==date("Y")){ echo("selected");} ?> ><?php echo date("Y") ?></option>
								<option value='<?php echo(date("Y")-1) ?>' <?php if ($passedyear ==date("Y")-1){ echo("selected");} ?> ><?php echo date("Y")-1 ?></option>								
							</select>
                            <span class="input-group-btn">
                                <button type="submit" name="viewQuizzes" class="btn btn-primary">View Quizzes</button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>



<?php
	if (isset($_POST["viewQuizzes"]) ) {
		echo ("<br/><br/><div>");
		echo ($history);
		echo ("</div>");
	}
?>
    </div>
</div>
<?php include 'footer.php';?>