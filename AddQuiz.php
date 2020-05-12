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
		$books= $books . '<div class="col-md-3" ><b>ISBN:</b> '.$row['ISBN'].'</div>';
		//$books= $books . '</div>';//4
		//$books= $books . '</div>';//2
		//$books= $books . '</div>';//3		
	}	
	mysqli_stmt_close($call);
	$bookQuizId = $_GET['bookQuizId'];
	
	$call = mysqli_prepare($conn, 'CALL ReadEdu.AddStudentQuiz(?,?)');
	mysqli_stmt_bind_param($call, 'ii', $bookQuizId,$_SESSION["SCId"]);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();

	while ($row = $result->fetch_assoc()) {
		$studentQuizId=$row["studentquizId"];
		$numOfQuestions=$row["numOfQuestions"];
	}
	mysqli_stmt_close($call);
	if ($studentQuizId<>0){
		// get the quiz
		$call1 = mysqli_prepare($conn, 'CALL ReadEdu.GetBookQuiz(?)');
		mysqli_stmt_bind_param($call1, 'i', $bookQuizId);
		mysqli_stmt_execute($call1);

		$result1 = $call1 ->get_result();
		$qIdOld=0;
		$bol=1;		
		$qIndex=1;
		
		while ($row = $result1->fetch_assoc()) {
			$qId=$row["Qid"];
			if  ($qId <> $qIdOld){
				if ($bol==0){
					echo ("<div><button class='btn btn-sm btn-primary' id='saveAnswer' name='saveAnswer' onclick='saveAnswer(".$qIdOld.")'>Next</button></div>");
					echo ("</div></div><br>");
				}			
				echo ("<div class='hidden' id='".$qIndex."div'><div class='chapterchat-question'>");
				echo ("<label class='radio control-label'>".$qIndex."/".$numOfQuestions.". ".$row["QText"]."</label>");		
				$qIndex=$qIndex+1;				
			}
			echo("<div class='radio'><label><input type='radio' id='".$qId."'  name='".$qId."'  value='".$row["AnswerId"]."'>".$row["AnswerText"]."</label></div>");				
			$bol=0;
					
			$qIdOld= $qId;
			
		}
		echo ("<div><button class='btn btn-sm btn-primary' id='saveAnswer' name='saveAnswer' onclick='saveAnswer(".$qIdOld.")'>Submit</button></div>");
		echo ("</div></div><br></div></div>");
				
		mysqli_free_result($result1);				
		$call1->close();
	}
	include 'db_close.php';
?>
<?php
if ($studentQuizId	<>0){
?>
	<br/><br/>
    <div class="container">
        <div class="jumbotron chapter_chat_jumbotron container container-fluid">
            <div><?php echo ($books) ?><br/><br/><br/></div>
            <div id='questiondiv' class='row'></div>
            <input type="hidden" name="numOfQuestions" id="numOfQuestions" value="<?php echo ($qIndex) ?>" />
            <input type="hidden" name="studentQuizId" id="studentQuizId" value="<?php echo ($studentQuizId) ?>" />
            <input type='hidden' id='quizReady' value='1' />
            <br>
        </div>
    </div>
<?php }else{ ?>
	<br/><br/>
    <div class="container">
        <div class="jumbotron chapter_chat_jumbotron container container-fluid">
            <input type='hidden' id='quizReady' value='0' />
            <div><?php echo ($books) ?></div>
            <div class="row attention">
                <div class="col-md-2">
                    <img src="icons/oops.png" />
                </div>
                <div class="col-md-8 chapterchat-font-plain">
                    You have already taken the quiz for this book!<br>To find another book, please click <a href='bookSearch.php'>here</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
	$(document).ready(function() {
		qIndex=1;
		timestart = new Date();		
		showQuestion(qIndex);
	});
	
	function saveAnswer(qid){		
		answer= $("input[name='"+qid+"']:checked"). val();
		if (answer== undefined){
			alert("You didn't answer this question yet!");
		}else{
			timeend = new Date();				 	
			minutes= ((timeend- timestart)*60)/1000;
			saveAnswerInDb(answer, minutes,qid);
			
			if (qIndex+1 == $("#numOfQuestions").val()){
				submitQuiz();
			}else{				
				timestart = new Date();
				qIndex= qIndex+1;
				showQuestion(qIndex);
			}
		}
	}
	function saveAnswerInDb(answerId, duration,qid){
		studentQuizId = $("#studentQuizId").val();	
		$.ajax({
			url: 'saveAnswer.php?studentQuizId='+studentQuizId+'&answerId='+answerId + '&qid='+ qid +'&duration=' + duration + '&r='+Math.random(),
			type: 'GET',
			dataType: 'text',
			error: function(){
				alert("error");
			},
			success: function(returninfo){
				//calculate the score
				//alert(returninfo);
			}
		});		
	}
	function showQuestion(qIndex){
		//alert(qIndex);
		//alert($("#"+qIndex+"div").html());
		$("#questiondiv").html($("#"+qIndex+"div").html());
		$("#"+qIndex+"div").show();
	}
	function submitQuiz(){
		studentQuizId = $("#studentQuizId").val();	
		$.ajax({
			url: 'submitQuiz.php?studentQuizId='+studentQuizId + '&r='+Math.random(),
			type: 'GET',
			dataType: 'text',
			error: function(){
				alert("error");
			},
			success: function(returninfo){
				//calculate the score
				window.location= 'quizReflection.php?studentQuizId='+studentQuizId+'&viewOnly=0';
			}
		});	
	}
</script>
<?php include 'footer.php';?>