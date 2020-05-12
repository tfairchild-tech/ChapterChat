<?PHP include 'requireStudent.php'; ?>
<?php include 'header.php';

	include('db_connect.php');
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$thisyear= date("Y");
	$call = mysqli_prepare($conn, 'CALL ReadEdu.GetQuizHistory(?,?)');
	mysqli_stmt_bind_param($call, 'ii', $_SESSION["SCId"],$thisyear);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();
	/*
	$history='<ul>';
	$counter=1;
	while ($row = $result->fetch_assoc() or $counter<=4) {
		//$history=$history. '<li>';
		//$history=$history. $row['DateOfQuiz'] ." <a href='quizReflection.php?studentQuizId=".$row['StudentQuizId']."'>".$row['BookTitle'] .'</a> '. $row['numCorrectQuestions'].'/'.$row['numOfQuestions'];
		//$history=$history. '</li>';

		$history=$history. '<li>';
		$history=$history.'<b style="font-size:10pt"><a href="quizReflection.php?studentQuizId='.$row['StudentQuizId'].'&viewOnly=1">'.$row['BookTitle'].'</a> ';
		$history=$history.'by '.$row['authorFirstName'].' '.$row['authorLastName'].'</b> ';
		$date=date_create($row['DateOfQuiz']);
		$history=$history.'on '. date_format($date,"F j, Y").' ';
		if  ($row['Passed']==1)
			$history=$history."<font color='green'><b>";
		else
			$history=$history."<font color='red'><b>";
		$history=$history.$row['numCorrectQuestions'].'/'.$row['numOfQuestions'].'</b></font>';
		//$history=$history."  <font size='1pt'><a href='quizReflection.php?studentQuizId=".$row['StudentQuizId']."'>Access Quiz</a></font>";
		$history=$history. '</li><br/>';
		$counter= $counter+1;
	}
	$history=$history. '</ul>';
	*/

	$history='';
	$counter=1;
	while ($row = $result->fetch_assoc() and $counter<=3) {
		$history=$history. '<a href="#" class="list-group-item">';
		$history=$history.'<b style="font-size:10pt"><span  style="color:blue" onclick=window.location="quizReflection.php?studentQuizId='.$row['StudentQuizId'].'&viewOnly=1">'.$row['BookTitle'].'</span> ';
		$history=$history.'by '.$row['authorFirstName'].' '.$row['authorLastName'].'</b> ';
		$date=date_create($row['DateOfQuiz']);
		$history=$history.'on '. date_format($date,"F j, Y").' ';
		if  ($row['Passed']==1)
			$history=$history."<font color='green'><b>";
		else
			$history=$history."<font color='red'><b>";
        if ($row['numCorrectQuestions'] == null) {
            $num_correct = 0;
        } else {
            $num_correct = $row['numCorrectQuestions'];
        }
		$history=$history.$num_correct.'/'.$row['numOfQuestions'].'</b></font>';
		//$history=$history."  <font size='1pt'><a href='quizReflection.php?studentQuizId=".$row['StudentQuizId']."'>Access Quiz</a></font>";
		if  ($row['IsPractice']==1)
			$history=$history.'  <span class="label label-success">practice</span>';
		$history=$history. '</a>';
		$counter= $counter+1;
	}
	if ($history == '')
		$history= '<a href="#" class="list-group-item">No Quiz Found</a>';


	mysqli_stmt_close($call);

	$call = mysqli_prepare($conn, 'CALL ReadEdu.GetStudentSummary(?)');
	mysqli_stmt_bind_param($call, 'i', $_SESSION["SCId"]);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();

	$studentSummary='';
	$schoolyear='';
	while ($row = $result->fetch_assoc()) {
		$schoolyear= $row['SchoolYear'];
        $_SESSION["readinglevel"] = $row["ReadingLevel"];
        $studentSummary=$studentSummary.' <a href="#" class="list-group-item">School Year: '.$row['SchoolYear'].'</a>';
		$studentSummary=$studentSummary.'<a href="#" class="list-group-item">Student Reading Level: '.$row['ReadingLevel'].'</a>';

		$studentSummary=$studentSummary. '<a href="#" class="list-group-item">Student Accuracy: <span alt="'.number_format($row['CorrectnessLevel']).'"  class="sparkpie" data-peity="{ &quot;fill&quot;: [&quot;#6EBB1F&quot;, &quot;#eee&quot;]}">'. number_format($row['CorrectnessLevel']).'/100</span> '. number_format($row['CorrectnessLevel']).'%</a>';
		$studentSummary=$studentSummary. '<a href="#" class="list-group-item">Student Total Points: <span alt="'.number_format($row['EarnedPointsTotal']).'"  class="donutpie" data-peity="{ &quot;fill&quot;: [&quot;#6EBB1F&quot;, &quot;#eee&quot;]}">'. $row['EarnedPointsTotal'].'/10</span> '.$row['EarnedPointsTotal'].' Points.</a>';
		
	
	}	
	mysqli_stmt_close($call);
	/*
	$call = mysqli_prepare($conn, 'CALL ReadEdu.rptStudentBookLevel(?)');
	mysqli_stmt_bind_param($call, 'i', $_SESSION["SCId"]);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();

	$data = array();
	$categories = array();
	while ($row = $result->fetch_assoc()) {
		$categories[] = str_replace("'","",$row['BookTitle']);
		$data[] = $row['BookLevel'];
	}
	$cat= "'". join($categories, "','")."'";
	*/

	$call = mysqli_prepare($conn, 'CALL ReadEdu.rptStudentMonthlyReading(?)');
	mysqli_stmt_bind_param($call, 'i', $_SESSION["SCId"]);
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
	mysqli_stmt_bind_param($call, 'i', $_SESSION["SCId"]);
	mysqli_stmt_execute($call);
	$result = $call ->get_result();
	$readingLvl = array();
	while ($row = $result->fetch_assoc()) {
		$readingLvl[] = number_format($row['weightedavg'], 2, '.', '');
	}

	$call->close();
	include 'db_close.php';
?>

<style>
#studentReport {
	min-width: 310px;
	height: 400px;
	margin: 0 auto;
    z-index: 500;
}
</style>

<div class="container">
    <div class="jumbotron chapter_chat_jumbotron_test">
            <div class="row center-block">

                    <div class="panel" style="width:32%">
                        <div class="panel-heading chapterchat-font-hdr text-center" style="background-image:none;background-color: #2E7B32; color:#7AB142;">Student Reading Summary</div>
                        <div class="list-group" id='StudentSummary'><?php echo($studentSummary) ?></div>
                    </div>
                    <div class="panel panel-info" style="width:32%">
                        <div class="panel-heading chapterchat-font-hdr text-center" style="background-image:none; background-color: #FFCD07; color:#FF8E00;">Quiz History</div>
                        <div class="list-group"><?php echo($history) ?></div>
                        <div class="panel-footer">
							<div class="text-center chapterchat-font chapter-chat-nav-link" data-target="#quiz">
								<a href="bookSearch.php">Take Quiz</a>   /    <a href="StudentQuizSearch.php">View All Quizzes</a>
							</div>
						</div>

                    </div>
                    <div class="panel panel-success" style="width:22%">
                        <div class="panel-heading chapterchat-font-hdr text-center" style="background-image:none; background-color: #1565C0; color:#62B3F5;">Book Shelf</div>
                        <div class="list-group"></div>
                        <a href="bookshelf.php"><div class="panel-footer page-scroll"><div class="text-center chapterchat-font chapter-chat-nav-link"  href="bookshelf_summary.php" data-target="#bookshelf" id="bookshelfsummaryLink">View My Books</div></div></a>
                        <a href="bookshelf.php?rlevel=gte"><div class="panel-footer page-scroll"><div class="text-center chapterchat-font chapter-chat-nav-link"  href="bookshelf_summary.php" data-target="#bookshelf" id="bookshelfsummaryLink" title="View books greater than your current reading level">Book Recommendations</div></div></a>
                        <a href="bookSearch.php"><div class="panel-footer page-scroll"><div class="text-center chapterchat-font chapter-chat-nav-link"  href="bookSearch.php" data-target="#booksearch" id="bookshelfsearchLink" title="View books greater than your current reading level">Book Search</div></div></a>
                    </div>

            </div>
<!---
            <div>
                <?php
                    echo '
                        <table>
                            <tr><th>$cat val:</th><td>', print $cat,'</td></tr>
                            <tr><th>$passed val:</th><td>', var_dump($passed), '</td></tr>
                            <tr><th>$failed val:</th><td>', var_dump($failed), '</td></tr>
                            <tr><th>$practice val:</th><td>', var_dump($practice), '</td></tr>
                            <tr><th>$readingLvl val:</th><td>', var_dump($readingLvl), '</td></tr>
                        </table>'
                    ?>
            </div>
---->
			<div id="studentReport"></div>
    </div>
</div>

<?php include 'footer.php';?>

<?php if (count($categories)>0) {
?>

<script type="text/javascript">

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
				maxPointWidth: 50,
				data: [<?php echo join(',', $passed) ?>]
			}, {
				name: 'Not Passed Test Quiz',
				color: 'yellow',
				maxPointWidth: 50,
				type: 'column',
				data: [<?php echo join(',', $failed) ?>]
			}, {
				name: 'Practice',
				color: 'lightblue',
				maxPointWidth: 50,
				type: 'column',
				data: [<?php echo join(',', $practice) ?>]
			},{
				name: 'ReadingLevel',
				color: 'orange',
				maxPointWidth: 50,
				type: 'spline',
				yAxis:1,
				data: [<?php echo join(',', $readingLvl) ?>]
			}

			]
		});
</script>

<?php }
?>

<script>
$(".sparkpie").peity("pie", {
	delimeter: "/",
	radius: 12,
	fill: function(value, i, all) {
		var colors
		if (value >= 80) {
			colours = ["green", "#f5f5f5"]
		} else if (value <= 60) {
			colours = ["orange", "#f5f5f5"]
		} else {
			colours = ["red", "#f5f5f5"]
		}
		return colours[i]
	}
});
$(".donutpie").peity("donut", {
	delimeter: "/",
	radius: 12,
	fill: function(value, i, all) {
		var colors
		if (value >= 80) {
			colours = ["green", "#f5f5f5"]
		} else if (value <= 60) {
			colours = ["orange", "#f5f5f5"]
		} else {
			colours = ["red", "#f5f5f5"]
		}
		return colours[i]
	}
});

</script>