<?PHP include 'header.php'; ?>
<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
			
	include('db_connect.php');
	// get student class id
	$bookId = $_GET['bookId'];
	// get the book information
	$call = mysqli_prepare($conn, 'CALL ReadEdu.getBookInfo(?)');
	mysqli_stmt_bind_param($call, 'i', $bookId);
	mysqli_stmt_execute($call);

	$result = $call ->get_result();
	$books="";
	while ($row = $result->fetch_assoc()) {
		$books= $books . '<div class="row">';
		$books= $books . '<div class="col-md-3" ><img src="bookimages/big'.$row['picPath'].'" /></div>';
		$books= $books . '<div class="col-md-8" >';//2
		$books= $books . '<div class="row">';//1
		$books= $books . '<div class="col-md-8"><h3 class="underline">'.$row['bookTitle'].'</h3> By '.$row['authorname'].'</div>';
		$books= $books . '</div>';//1
		$books= $books . '<div class="row">';//4
		$books= $books . '<div class="col-md-3" ><b>Book Level:</b> '.$row['BookLevel'].'</div>';
		$books= $books . '<div class="col-md-3" ><b>Total Points:</b> '.$row['possiblePoints'].'</div>';
		$books= $books . '<div class="col-md-6" ><b>ISBN:</b> '.$row['ISBN'].'</div>';
		$books= $books . '</div>';//4
		//$books= $books . '</div>';//2
		//$books= $books . '</div>';//3		
	}
	mysqli_stmt_close($call);
	
	$call = mysqli_prepare($conn, 'CALL ReadEdu.GetBookQuizzes(?)');
	mysqli_stmt_bind_param($call, 'i', $bookId);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();

	$bookQuizzes= '<br/><br/><div class="row">';
	while ($row = $result->fetch_assoc()) {
		$BookQuizId=$row["BookQuizId"];
		$numOfQuestions=$row["numOfQuestions"];		
		$bookQuizzes= $bookQuizzes . '<div class="col-md-3" >';
		if ($row["IsPractice"]== 1){
			$bookQuizzes= $bookQuizzes . '<a href="AddQuiz.php?bookQuizId='.$BookQuizId.'&bookId='.$bookId.'"><img type="button" src="icons/practice.png" alt="click to take the quiz" /></a>';			
		}else{			
			$bookQuizzes= $bookQuizzes . '<a href="AddQuiz.php?bookQuizId='.$BookQuizId.'&bookId='.$bookId.'"><img type="button"  src="icons/test.png" alt="click to take the quiz" /></a>';
		}
		$bookQuizzes= $bookQuizzes . '</div>';
	}	
	$bookQuizzes= $bookQuizzes . '</div></div></div></div></div>';
	mysqli_stmt_close($call);
	include 'db_close.php';
	?>
	<br/><br/>
	<div class="container">
        <div class="pull-right">
            <a href="studentHome.php"><br>
                <span class="glyphicon glyphicon-remove">&nbsp;&nbsp;</span>
            </a>
        </div>
        <div class="jumbotron chapter_chat_jumbotron container container-fluid">
            <div>
                <?php echo ($books) ?>
                <?php echo ($bookQuizzes) ?>
            </div>
        </div>
    </div>
	
<?php include 'footer.php';?>
	