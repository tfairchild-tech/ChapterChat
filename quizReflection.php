<?PHP 
	include 'requireStudent.php';
	include 'header.php'; 
	
	include('db_connect.php');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$viewOnly=$_GET["viewOnly"];
	
	$studentQuizId = $_GET["studentQuizId"];

	$call = mysqli_prepare($conn, 'CALL ReadEdu.GetStudentQuizReflection(?)');
	mysqli_stmt_bind_param($call, 'i', $studentQuizId);
	mysqli_stmt_execute($call);
	$result4 = $call ->get_result();	
	
	mysqli_stmt_close($call);
	
	$call3 = mysqli_prepare($conn, 'CALL ReadEdu.getReflection()');	
	mysqli_stmt_execute($call3);

	$result3 = $call3 ->get_result();
	$reflections='';	
	mysqli_stmt_close($call3);

	$call = mysqli_prepare($conn, 'CALL ReadEdu.GetStudentQuizResult(?)');
	mysqli_stmt_bind_param($call, 'i', $studentQuizId);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();		
	// display the result
	$summary='';
	while ($row = $result->fetch_assoc()) {
		$numOfQuestions= $row['numOfQuestions'];
		$summary= $summary . '<div class="row">';
		$summary= $summary . '<div class="col-md-3" ><img src="bookimages/big'.$row['picPath'].'" /></div>';
		$summary= $summary . '<div class="col-md-8" >';//2
		$summary= $summary . '<div class="row">';//1
		$summary= $summary . '<div class="col-md-8"><h3 class="underline">'.$row['BookTitle'].'</h3> By '.$row['authorname'].'</div>';
		$summary= $summary . '</div>';//1
		$summary= $summary . '<div class="row">';//4
		$summary= $summary . '<div class="col-md-3" ><b>Total Points:</b> '.$row['possiblePoints'].'</div>';
	
		if ($row['Passed']==1){
			$summary= $summary.'<div class="goodjob"><img src="icons/welldone.png" /><h4>Congratulations, You have passed the Quiz, please review your answers below!</h4></div>';
		}else{
			$summary= $summary."<div class='warning'><img src='icons/f3.png' /><h4>You didn't pass the quiz<br>please review your answers below!</h4></div>";
		}
		$num_total = $row['numOfQuestions'];
		$num_correct = $row['numCorrectQuestions'];
		if ($num_correct==0) {
            $summary= $summary."<h4>You did not answer any questions correctly</h4>";
        }elseif ($num_correct == 1) {
            $summary= $summary."<h4>You answered one question correctly, out of ".$num_total." questions</h4>";
        }elseif ($num_correct < $num_total) {
            $summary= $summary."<h4>You answered ".$num_correct." questions correctly, out of ".$num_total." questions</h4>";
        }else {
            $summary= $summary."<h4>You answered all questions correctly!</h4>";
        }
	}
	// now we're at the end of our first result set.
	mysqli_free_result($result);
	mysqli_stmt_close($call);
	//move to next result set
	//mysqli_next_result($call);	
	$call1 = mysqli_prepare($conn, 'CALL ReadEdu.GetStudentQuizResultAnswers(?)');
	mysqli_stmt_bind_param($call1, 'i', $studentQuizId);
	mysqli_stmt_execute($call1);

	$result2 = $call1 ->get_result();		
	
	$summary2= '';
	$qIdOld=0;
	$bol=1;		
	$qIndex=1;
	$qpass=0;	
	$qId =0;
	while ($row = $result2->fetch_assoc()) {
			$qId=$row["QId"];
			if  ($qId <> $qIdOld){
				if ($bol==0){	
					if ($qpass==0){
						$summary2= $summary2."<div class='notice'><select id='ref".$qId."'";
						if ($viewOnly== 0){
							$summary2= $summary2." onChange='changeRef(".$qId.")'";
						}else{
							$summary2= $summary2." disabled";						
						}
						$summary2= $summary2."><option value=''>Why did you choose that answer?</option>";
						while ($row3 = $result3 -> fetch_assoc()) {
							$summary2= $summary2 . '<option value="'.$row3['ReflectionId'].'"';
							while ($row2 = $result4->fetch_assoc()) {	//looping on the reflection
								if ($row2['QId'] == $qId & $row2['ReflectionId'] == $row3['ReflectionId']  ){
									$summary2= $summary2 . ' selected';										
								}
							}
							mysqli_data_seek($result4, 0); 
							$summary2= $summary2 . '>'.$row3['ReflectionText'].'</option>';
						}
						mysqli_data_seek($result3, 0); 
												
						$summary2= $summary2 ."</select>";						
						if ($viewOnly== 1){
							$summary2= $summary2 ."<br/><a style='cursor:pointer' onclick='changeRef(".$qIdOld.")' >Show Related Paragraph</a>";						
						}
						$summary2= $summary2 ."</div>";
					}
					
					
					$summary2= $summary2."</div><br>";
					$summary2= $summary2."</div><br>";
				}			
				$summary2= $summary2."<div id='".$qIndex."div'><div id='".$qIndex."colordiv'><input type='hidden' id='RelatedParagraph".$qId."' value='".$row["RelatedParagraph"]."'/> ";
				
				$summary2= $summary2."<label class='radio control-label'>".$qIndex."/".$numOfQuestions.". ".$row["QText"]."</label>";
				$summary2= $summary2."<input type='hidden' id='passed".$qIndex."' value='".$qpass."'/>";
				$qIndex=$qIndex+1;				
				$qpass=0;
			}
			
			if ($row["Correct"] == 1)
				$summary2= $summary2."<div class='radio'><label><input type='radio' id='".$qId."'  name='".$qId."'  value='".$row["AnswerId"]."'";
			else
				$summary2= $summary2."<div class='radio'><label><input type='radio' id='".$qId."'  name='".$qId."'  value='".$row["AnswerId"]."'";
				
			if  ($row["AnswerId"]== $row["studentAnswerId"])
				$summary2= $summary2." checked" ;
			$summary2= $summary2." disabled>".$row["AnswerText"]."</label>";
				
			if ($row["Correct"] == 1){
				$summary2= $summary2." <img src='assets/checkmark.png' />";
			}
			$summary2= $summary2." </div>";
			
			if ($row["AnswerId"] == $row["studentAnswerId"] && $row["Correct"] == 1){
				$qpass=1;				 
			}				
			$bol=0;					
			$qIdOld= $qId;			
	}
	$summary2= $summary2."<input type='hidden' id='passed".$qIndex."' value='".$qpass."'/>";		


	if ($qpass==0){
		$summary2= $summary2."<div class='notice'><select id='ref".$qId."'";
		if ($viewOnly== 0){
			$summary2= $summary2." onChange='changeRef(".$qIdOld.")'";
		}else{
			$summary2= $summary2." disabled";						
		}
		$summary2= $summary2."><option value=''>Why you chose that answer?</option>";
		while ($row3 = $result3 -> fetch_assoc()) {
			$summary2= $summary2 . '<option value="'.$row3['ReflectionId'].'"';
			while ($row2 = $result4->fetch_assoc()) {
				if ($row2['QId'] == $qId ){
					$summary2= $summary2 . ' selected';										
				}
			}
			
			$summary2= $summary2 . '>'.$row3['ReflectionText'].'</option>';
		}
		mysqli_data_seek($result3, 0); 
		mysqli_data_seek($result4, 0); 
		
		$summary2= $summary2 ."</select>";						
		if ($viewOnly== 1){
			$summary2= $summary2 ."<br/><a style='cursor:pointer' onclick='changeRef(".$qId.")' >Show Related Paragraph</a>";						
		}		
		$summary2= $summary2 ."</div>";
	}
	
	
	$summary2= $summary2."</div></div><br>";
					
	///mysqli_free_result($result2);
	// close statement
	//mysqli_stmt_close($call);	
?>
	<br/><br/>
<div class="container">
    <div class="pull-right">
        <a href="studentHome.php"><br>
            <span class="glyphicon glyphicon-remove">&nbsp;&nbsp;</span>
        </a>
    </div>
	<div class="jumbotron chapter_chat_jumbotron_tight container container-fluid">
	<input type="hidden" name="studentQuizId" id="studentQuizId" value="<?php echo ($studentQuizId) ?>" />
	<input type="hidden" name="viewOnly" id="viewOnly" value="<?php echo ($viewOnly) ?>" />
	<?php echo $summary; ?>
	<br/>
	<br/><input type="hidden" name="numOfQuestions" id="numOfQuestions" value="<?php echo ($qIndex) ?>" />
	<?php echo $summary2; ?>
	</div>
</div>
	<script type="text/javascript">
	$(document).ready(function() {		
		numOfQuestions= $("#numOfQuestions").val();
		
		for (i=1 ; i< numOfQuestions; i++){
			i1=i+1;
			if ($('#passed'+i1).val() ==1){				
				$('#'+i+"colordiv").addClass('chapterchat-question-passed');
			}else
			{			
				$('#'+i+"colordiv").addClass('chapterchat-question-failed');
			}
		
		}
	});
	
	function changeRef(qId){
		
		studentQuizId = $("#studentQuizId").val();
		if ($("#viewOnly").val()==0){
			//document.write('AddReflection.php?studentQuizId='+studentQuizId+'&qid='+ qId +'&refId=' + $('#ref'+qId).val() + '&r='+Math.random());
			$.ajax({
				url: 'AddReflection.php?studentQuizId='+studentQuizId+'&qid='+ qId +'&refId=' + $('#ref'+qId).val() + '&r='+Math.random(),
				type: 'GET',
				dataType: 'text',
				error: function(){
					alert("error");
				},
				success: function(returninfo){
					refId= $('#ref'+qId).val();
					if (refId != 2){
						//open little window and pass the pargraph pic to it	
						var RelatedParagraph= $('#RelatedParagraph'+qId).val();
						if (RelatedParagraph.indexOf('png')>0 || RelatedParagraph.indexOf('jpg')>0 || RelatedParagraph.indexOf('JPG')>0){
							$('#refContent').html ("<img src='bookimages/"+RelatedParagraph+"' />")
						}else{$('#refContent').html (RelatedParagraph+"<br/><br/><br/>");}
						$('#reflectionModal').modal('show');
					}				
				}
			});		
		}else{
			//open little window and pass the pargraph pic to it	
			var RelatedParagraph= $('#RelatedParagraph'+qId).val();
			if (RelatedParagraph.indexOf('png')>0 || RelatedParagraph.indexOf('jpg')>0 || RelatedParagraph.indexOf('JPG')>0){
				$('#refContent').html ("<img src='bookimages/"+RelatedParagraph+"' />")
			}else{$('#refContent').html (RelatedParagraph+"<br/><br/><br/>");}
			$('#reflectionModal').modal('show');
		}
		// add more logic to show the paragraph
	}
	</script>
	
	
	
	<div class="modal fade" id="reflectionModal" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Reflection Page</h4><br>
				</div>
				<div class="modal-body" id='refContent'>

				</div>
			</div>
		</div>
	</div>