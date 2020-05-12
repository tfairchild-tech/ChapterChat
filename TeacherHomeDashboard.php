<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
 $teacherid = $_SESSION["teacherid"];

    include('db_connect.php');
    $call = mysqli_prepare($conn, 'CALL ReadEdu.getTeacherClasses(?)');
    mysqli_stmt_bind_param($call, 'i', $teacherid);
    mysqli_stmt_execute($call);

    $result = $call -> get_result();
    $row_count = $result->num_rows;
    echo '<br/><br/>
        <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped">
            <thead>
            <tr>
                <th>School Name</th>
                <th>School Location</th>
                <th>Class ID</th>
                <th>Grade</th>
                <th>School Year</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            '
    ;

    while ($row = $result->fetch_assoc()) {
         $schoolname = $row["SchoolName"];
         $schoollocation = $row["SchoolLocation"];
         $grade = $row["GradeLevel"];
         $schoolyear = $row["SchoolYear"];
         $classid = $row["ClassId"];
         // <button type="button" class="btn btn-info btn-xs" disabled><i class="fa fa-pencil-square"></i> update</button>
         // <button type="button" class="btn btn-xs btn-danger" disabled><i class="fa fa-trash-o"></i> delete</button>
         echo '
            <tr><td>'.$schoolname.'</td><td>'.$schoollocation.'</td><td>'.$grade.'</td><td>'.$schoolyear.'</td><td>'.$classid.'</td>
            <td>
                <button type="button" class="btn btn-success btn-xs" onclick="getReport(`rptTeacherClass.php?classId='.$classid.'`, `teacher-home`)"><i class="fa fa-user"></i> Class Progress</button>
                <button type="button" class="btn btn-success btn-xs" onclick="getReport(`rptStatistic.php?classId='.$classid.'`, `teacher-home`)"><i class="fa fa-user"></i> Statistics</button>
            </td>
            </tr>
            <tr>
            </tr>
         ';
    }
    echo '
        </tbody>
        </table>
        </div>
    ';

?>



