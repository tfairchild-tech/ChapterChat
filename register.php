<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["badpassword"]) == 1) {
    unset($_SESSION['badpassword']);
    $badpassword = 1;
} else {
    $badpassword = 0;
}

if (isset($_POST["login-submit"])) {
    include('db_connect.php');

    $login = $_POST["inputEmail"];
    $pw = $_POST["inputPassword"];

    $call = mysqli_prepare($conn, 'CALL ReadEdu.getAuthentication(?, ?)');
    mysqli_stmt_bind_param($call, 'ss', $login,$pw);
    mysqli_stmt_execute($call);

    $result = $call ->get_result();

    while ($row = $result->fetch_assoc()) {
        $_SESSION["logintype"] = $row["loginType"];
        $loginType= $row["loginType"];
    }
    mysqli_stmt_close($call);
    mysqli_free_result($result);
    //$call->close();

    if (isset($_SESSION["logintype"])) {

        if ($_SESSION["logintype"] == 0) { //student
            $call = mysqli_prepare($conn, 'CALL ReadEdu.getStudentInfo(?)');
            mysqli_stmt_bind_param($call, 's', $login);
            mysqli_stmt_execute($call);

            $result = $call->get_result();

            while ($row = $result->fetch_assoc()) {
                $_SESSION["login"] = $row["StudentFirstName"] . ' ' . $row["StudentLastName"];
                $_SESSION["SCId"] = $row["SCId"];
                $_SESSION["readinglevel"] = $row["ReadingLevel"];

            }
        } else { //teacher
            $call = mysqli_prepare($conn, 'CALL ReadEdu.getTeacherInfo(?)');
            mysqli_stmt_bind_param($call, 's', $login);
            mysqli_stmt_execute($call);

            $result = $call->get_result();



            while ($row = $result->fetch_assoc()) {
                $_SESSION["login"] = $row["TeacherFirstName"] . ' ' . $row["TeacherLastName"];
                $_SESSION["teacherid"] = $row["TeacherId"];
            }

        }

        mysqli_stmt_close($call);
        mysqli_free_result($result);
        //$call->close();
    }

    include 'db_close.php';

    if (isset($_SESSION["logintype"]) == false) {
        $badpassword = 1;
        $_SESSION["badpassword"] = 1;
    }

   // if (isset($_SESSION["logintype"])==false) {
   //     $badpassword = 1;
   // } else if ($loginType==0){// student login
   //     header( 'Location: ./StudentHome.php' );
   // } else if ($loginType==1){	// teacher login
   //     header( 'Location: ./TeacherHome.php' );
   // }


} elseif (isset($_POST["register-submit"])) { //new user
    include('db_connect.php');

    $fname = $_POST["firstname"];
    $lname = $_POST["lastname"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $login = $_POST["username"];
    $pw = $_POST["newpassword"];
    $logintype = $_POST["role"];
    $verified = 1;
    $classid = $_POST["classId"];
    $schoolid = $_POST["schoolId"];
    $gradelevel = $_POST["gradelevel"];

    /*
    echo "<table>";
    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    */

    if ($logintype == 0) {  //student

        $call = mysqli_prepare($conn, 'CALL ReadEdu.AddNewStudent(?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($call, 'ssssssii', $fname, $lname, $gender, $dob, $pw, $email, $verified, $classid);
        mysqli_stmt_execute($call);

        $result = $call->get_result();

        while ($row = $result->fetch_array(MYSQLI_NUM))
        {
            foreach ($row as $r)
            {
                print "$r ";
            }
            print "\n";
        }

        mysqli_stmt_close($call);
        mysqli_free_result($result);

        $call = mysqli_prepare($conn, 'CALL ReadEdu.getStudentInfo(?)');
        mysqli_stmt_bind_param($call, 's', $login);
        mysqli_stmt_execute($call);

        $result = $call->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION["logintype"] = $logintype;
                $_SESSION["login"] = $row["StudentFirstName"] . ' ' . $row["StudentLastName"];
                $_SESSION["SCId"] = $row["SCId"];
            }
        } else {
            $error_msg = '<p class="error">Error:  New user does not exist</p>';
        }

        mysqli_stmt_close($call);
        mysqli_free_result($result);
        //$call->close();

        include 'db_close.php';

        if (isset($_SESSION["logintype"]) == false) {
            $badpassword = 1;
            $_SESSION["badpassword"] = 1;
        }

/*
        if (isset($_SESSION["logintype"]) == false) {
            $badpassword = 1;
        } else {
            // student login
            header('Location: ./StudentHome.php');
        }
*/
    } elseif ($logintype == 1) {  //teacher

        $call = mysqli_prepare($conn, 'CALL ReadEdu.AddNewTeacher(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($call, 'ssssssiii', $fname, $lname, $gender, $dob, $pw, $email, $verified, $schoolid, $gradelevel);
        mysqli_stmt_execute($call);

        $result = $call->get_result();

        mysqli_stmt_close($call);
        mysqli_free_result($result);

        $call = mysqli_prepare($conn, 'CALL ReadEdu.getTeacherInfo(?)');
        mysqli_stmt_bind_param($call, 's', $login);
        mysqli_stmt_execute($call);

        $result = $call->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION["logintype"] = $logintype;
                $_SESSION["login"] = $row["TeacherFirstName"] . ' ' . $row["TeacherLastName"];
                $_SESSION["teacherid"] = $row["TeacherId"];
            }
            mysqli_free_result($result);
            mysqli_stmt_close($call);
            //$call->close();
        } else {
            $error_msg = '<p class="error">ERROR:  New User does not exist</p>';
            mysqli_free_result($result);
            mysqli_stmt_close($call);

        }


        include 'db_close.php';

        if (isset($_SESSION["logintype"]) == false) {
            $badpassword = 1;
            $_SESSION["badpassword"] = 1;
        }
    }

}


?>


<link rel="stylesheet" href="css/register.css" />
<div class="modal fade" id="registerModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Register or sign in</h4><br>
            </div>
            <div class="modal-body">

                <div class="container">
                    <div class="row">
                        <div class="col-md-12 ">
                            <div class="panel panel-login col-md-5">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <a href="#" class="active" id="login-form-link">Sign In</a>
                                        </div>
                                        <div class="col-xs-12">
                                            <a href="#" id="register-form-link">Register</a>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?php if ($badpassword == 1) {echo "<div id='badpassword'><h3 class='text-danger'>Login Failed!</h3></div>";} ?>
                                            <form id="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" role="form" style="display: block;">

                                                <div class="form-group">
                                                    <div class="row">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" name="inputEmail" id="inputEmail" tabindex="1" class="form-control " placeholder="Username" value="" required>
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" name="inputPassword" id="inputPassword" tabindex="2" class="form-control " placeholder="Password" required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-12 ">
                                                            <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>




                                            <form id="register-form" name="register-form" action="" method="post" role="form" style="display: none;">
                                                <div class="form-group">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input name="role" id="role-student" value="0" type="radio" tabindex="10" checked />Student
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input name="role" id="role-teacher" value="1" type="radio" />Teacher
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <select name="classId" id="classId">
                                                        <?php
                                                        include('db_connect.php');
                                                        $call = mysqli_prepare($conn, 'CALL ReadEdu.getClasses()');
                                                        mysqli_stmt_execute($call);

                                                        $result = $call -> get_result();

                                                        while ($row = $result->fetch_assoc()) {
                                                            $val1 = $row["ClassId"];
                                                            $val2 = $row["SchoolName"];
                                                            $val3 = $row["GradeLevel"];
                                                            $val4 = $row["teachername"];
                                                            echo '<option   value='.$val1.'>'.$val2.' (grade '.$val3.'-'.$val4.')'.'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <select name="schoolId" id="schoolId" hidden>
                                                        <?php
                                                        include('db_connect.php');

                                                        $call = mysqli_prepare($conn, 'CALL ReadEdu.getSchools()');
                                                        mysqli_stmt_execute($call);

                                                        $result = $call -> get_result();

                                                        while ($row = $result->fetch_assoc()) {
                                                            $val1 = $row["schoolId"];
                                                            $val2 = $row["schoolname"];
                                                            $val3 = $row["SchoolLocation"];
                                                            echo '<option   value='.$val1.'>'.$val2.' ('.$val3.')'.'</option>';
                                                        }

                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group gradeDiv" hidden>
                                                    <label for="gradelevel">Grade Level</label>
                                                    <select class="form-control" id="gradelevel" name="gradelevel">
                                                        <option val=0>Please Select</option>
                                                        <option val=1>1</option>
                                                        <option val=2>2</option>
                                                        <option val=3>3</option>
                                                        <option val=4>4</option>
                                                        <option val=5>5</option>
                                                        <option val=6>6</option>
                                                    </select>
                                                </div>



                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="firstname">First Name</label>
                                                    <input type="text" name="firstname" id="firstname" tabindex="1" class="form-control" placeholder="First Name" value="" required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="lastname">Last Name</label>
                                                    <input type="text" name="lastname" id="lastname" tabindex="2" class="form-control" placeholder="Last Name" value=""required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="dob">Date of Birth</label>
                                                    <input type="date" name="dob" id="dob" tabindex="3" class="form-control" required readonly>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input name="gender" id="gender-f" value="F" type="radio"  tabindex="4" checked/>Female
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="radio-inline">
                                                            <input name="gender" id="gender-m" value="M" type="radio" tabindex="5" />Male
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="email">Email</label>
                                                    <input type="email" name="email" id="email" tabindex="6" class="form-control" placeholder="Email Address" value="" required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="username">UserName</label>
                                                    <input type="text" name="username" id="username" tabindex="7" class="form-control" placeholder="Username" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="password">Password</label>
                                                    <input type="password" name="newpassword" id="id_newpassword" tabindex="8" class="form-control" placeholder="Password" required>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row"></div>
                                                    <label for="confirm-password">Confirm Password</label>
                                                    <input type="password" name="newpassword_conf" id="id_newpassword_conf" tabindex="9" class="form-control" placeholder="Confirm Password" required>
                                                </div>

                                                <div class="form-group">
                                                    <div class="row">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-6 col-sm-offset-3">
                                                            <input type="submit" name="register-submit" id="register-submit" tabindex="12" class="form-control btn btn-register" value="Register Now">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- container -->
            </div>
        </div>
    </div>
</div>

<script>

    $(function() {

        $('#login-form-link').click(function(e) {
            $("#login-form").delay(100).fadeIn(100);
            $("#register-form").fadeOut(100);
            $('#register-form-link').removeClass('active');
            $(this).addClass('active');
            e.preventDefault();
        });
        $('#register-form-link').click(function(e) {
            $("#badpassword").hide();
            $("#register-form").delay(100).fadeIn(100);
            $("#login-form").fadeOut(100);
            $('#login-form-link').removeClass('active');
            $(this).addClass('active');
            e.preventDefault();
        });

    });

</script>

<script  type="text/javascript">
    $(document).ready(function(){

        $('#role-teacher').click(function(){
           $('#classId').hide();
           $('#schoolId').show();
           $('.gradeDiv').show();
        });
        $('#role-student').click(function(){
            $('#classId').show();
            $('#schoolId').hide();
            $('.gradeDiv').hide();
        });

        $('#register-submit').click(function(){
            $('#register-form').validate({
                rules: {
                    newpassword: {
                        required: true
                    },
                    newpassword_conf: {
                        required: true,
                        equalTo: "#id_newpassword"
                    }
                },
                messages: {
                    newpassword_conf: {
                        required: "Please enter the password."
                    },
                    newpassword_conf: {
                        required: "Please enter the confirm password.",
                        equalTo: "Passwords do not match"
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });

        //Adds a datepicker to any input field with name ending in Date e.g. startDate, endDate.
        $("#dob").datepicker({
               dateFormat: 'yy-mm-dd',
               changeMonth: true,
               changeYear: true,
               yearRange: "-100:+0"
            });


        $("#username").focusin(function(){
            var dob = $("#dob").val();
            var classOf;

            if (dob == '') {
                classOf = new Date().getFullYear()+18;
            } else {
                classOf = new Date(dob).getFullYear()+18;
            }
            var fname = $("#firstname").val().charAt(0);
            var lname = $("#lastname").val();
            $(this).val(fname+lname+classOf);
        });
    });
</script>
