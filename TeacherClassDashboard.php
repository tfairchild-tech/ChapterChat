<?php
$teacherid = $_SESSION["teacherid"];
$classid = $_GET["classid"];
$grade = $_GET["grade"];

include('db_connect.php');

$call = mysqli_prepare($conn, 'CALL `ReadEdu`.`getClassInfo`(?);');
mysqli_stmt_bind_param($call, 'i', $classid);
mysqli_stmt_execute($call);
$result = $call -> get_result();
while ($row = $result->fetch_assoc()) {
    $classname = $row["ClassName"];
}
mysqli_stmt_close($call);

$call = mysqli_prepare($conn, 'CALL `ReadEdu`.`getClassStudentInfo`(?);');
mysqli_stmt_bind_param($call, 'i', $classid);
mysqli_stmt_execute($call);

$result = $call -> get_result();
$row_count = $result->num_rows;
if ($classid == 'all') {
    $report_title = "<h2>All Students</h2>";
} else {
    $report_title = "<h2>".$classname."</h2>";
}
echo $report_title . '  
                <table id="studentDatatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%;">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>LoginID</th>
                    <th>Grade</th>
                    <th>Reading<br>Level</th>
                    <th>Quiz<br>Accuracy</th>
                    <th>Total<br>Quizzes<br>Taken</th>
                    <th>Last<br>Quiz<br>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
     ';

                $testpeitycount = 28;
                while ($row = $result->fetch_assoc()) {
                    $testpeitycount = $testpeitycount + 15;
                    if ($testpeitycount > 100) { $testpeitycount = 99; }
                    $studentid = $row["StudentId"];
                    $studentfname = $row["StudentFirstName"];
                    $studentlname = $row["StudentLastName"];
                    $gender = $row["Gender"];
                    $dob = $row["DateOfBirth"];
                    $studentloginid = $row["studentLoginId"];
                    $readinglevel = $row["ReadingLevel"];
                    $correctnesslevel = $row["CorrectnessLevel"];
                    $grade = $row["GradeLevel"];
                    $schoolyear = $row["SchoolYear"];
                    $totalquizzes = $row["TotalQuizzesTaken"];
                    $lastquizdate = $row["LastQuizDate"];
                    //<span class="sparkpie" data-peity="{ &quot;fill&quot;: [&quot;#6EBB1F&quot;, &quot;#eee&quot;]}">'.$correctnesslevel.'/100</span>  '.$correctnesslevel.'%

                    echo '
                            <tr>
                            <td>'.$studentfname.' '.$studentlname.'</td>
                            <td>'.$studentloginid.'</td>
                            <td>'.$grade.'</td>
                            <td>'.$readinglevel.'</td>
                            <td>
                                <span class="sparkpie" data-peity="{ &quot;fill&quot;: [&quot;#6EBB1F&quot;, &quot;#eee&quot;]}">'.$testpeitycount.'/100</span> '.$testpeitycount.'%
                            </td>
                            <td><strong>'.$totalquizzes.'</strong></td>
                            <td>'.$lastquizdate.'</td>
                            <td nowrap>
                            <button type="button" class="btn btn-success btn-xs" onclick="getReport(`StudentProgessReport.php?scId='.$row["SCId"].'`, `teacher-class`)"><i class="fa fa-user"></i> Progress</button>
                            <button type="button" class="btn btn-success btn-xs" onclick="getReport(`StudentAnalysisReport.php?scId='.$row["SCId"].'`, `teacher-class`)"><i class="fa fa-pencil-square"></i> Reflection</button>
			                <button type="button" class="btn btn-success btn-xs" onclick="getReport(`StudentTimeReport.php?scId='.$row["SCId"].'`, `teacher-class`)"><i class="fa fa-trash-o"></i> Quiz Times</button>
                           <!--- <button type="button" class="btn btn-info btn-xs"><i class="fa fa-pencil-square"></i> update</button>
                            <button type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> delete</button>--->
                            </td>
                            </tr>
                       ';
                }

 echo '
            </tbody>
        </table>
    ';

 ?>

<script>

    //$(".sparkpie").peity("pie", {
    //    fill: ["green", "white"]
    //})

    $(".sparkpie").peity("pie", {
        delimeter: "/",
        radius: 8,
        fill: function(value, i, all) {
            var colors
            if (value >= 80) {
                colours = ["green", "white"]
            } else if (value <= 60) {
                colours = ["orange", "white"]
            } else {
                colours = ["red", "white"]
            }
            return colours[i]
        }
    })
    //$('.sparkpie').peity("pie");


    $('#studentDatatable').dataTable( {
        "dom": '<"pull-right"f><"pull-left"l>tip',
        "paging": true
    } );

</script>