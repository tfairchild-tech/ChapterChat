<?php
include 'requireTeacher.php';
include 'header.php';

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$teacherid = $_SESSION["teacherid"];

?>

    <div class="container">
        <div class="chapter_chat_teacher_jumbotron">
                <ul class="nav nav-tabs nav-justified">
                    <li class="homeTab active chapterchat-tab-font-md"><a data-toggle="tab" href="#teacher-home">Teacher Home</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle parentmenu chapterchat-tab-font-md" data-toggle="dropdown" href="#">My Students
                            <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <?php
                                    include('db_connect.php');
                                    $call = mysqli_prepare($conn, 'CALL ReadEdu.getTeacherClasses(?)');
                                    mysqli_stmt_bind_param($call, 'i', $teacherid);
                                    mysqli_stmt_execute($call);

                                    $result = $call -> get_result();

                                    while ($row = $result->fetch_assoc()) {
                                        $grade = $row["GradeLevel"];
                                        $schoolyear = $row["SchoolYear"];
                                        $classid = $row["ClassId"];
                                        $classname = $row["ClassName"];
                                        echo '<li><a class="submenu chapterchat-tab-font-md" data-toggle="tab" href="#teacher-class" grade="'.$grade.'" classid="'.$classid.'">'.$row["ClassName"].'</a></li>';
                                    }
                                    ?>
                                    <li role="separator" class="divider"></li>
                                    <li><a class="submenu chapterchat-tab-font-md" data-toggle="tab" href="#teacher-class" classid="all">All Students</a></li>
                                </ul>
                    </li>
                    <li class="adminTab chapterchat-tab-font-md"><a data-toggle="tab" href="#teacher-admin">Quiz Admin</a></li>
                </ul>

            <div class="tab-content">
                <div id="teacher-home" class="tab-pane fade in active">
                    <?php include 'TeacherHomeDashboard.php'; ?>
                </div>
                <div id="teacher-class" class="tab-pane fade">

                </div>
                <div id="teacher-admin" class="tab-pane fade">
                    <?php include 'TeacherAdminDashboard.php'; ?>
                </div>
            </div>


        </div>
    </div>

<?php include 'footer.php';?>

<script>
    $(document).ready(function() {


        $('.homeTab a').addClass('active');
        $('.homeTab a').css("background-color", "#0396d7");

        $('.submenu').on('click',function(){
            $('.parentmenu').css("background-color", "#fc6");
            $('.homeTab a').css("background-color", "rgba(255, 255, 255, .90)");
            $('.adminTab a').css("background-color", "rgba(255, 255, 255, .90)");
            var classid = $(this).attr('classid');
            var grade = $(this).attr('grade');

            var url = "TeacherClassDashboard.php?classid="+classid+"&grade="+grade;
            if (typeof url !== "undefined") {
                var pane = $(this), href = this.hash;
                // ajax load from data-url
                $(href).load(url,function(result){
                    pane.tab('show');
                });
            } else {
                $(this).tab('show');
            }
        });

        $('.adminTab').on('click',function(){
            $('.adminTab a').css("background-color", "#85CA4D");
            $('.homeTab a').css("background-color", "rgba(255, 255, 255, .90)");
            $('.parentmenu').css("background-color", "rgba(255, 255, 255, .90)");
            
        });

        $('.homeTab').on('click',function(){
        	$('.homeTab a').css("background-color", "#0396d7");
		$('.parentmenu').css("background-color", "rgba(255, 255, 255, .90)");
		$('.adminTab a').css("background-color", "rgba(255, 255, 255, .90)");
		$.ajax({
		  url: 'TeacherHomeDashboard.php',
		  method: "GET",
		  success: function(result){
		  	$('#teacher-home').html(result);
		  },
		  error: function(e){alert("error"+e);}
		});            
        });

        $(function () {
            $('.submenu').click(function (e) {
                e.preventDefault();
                $('a[classid="' + $(this).attr('classid') + '"]').tab('show');
            })
        });
    });
    function getReport(url, tabname){
	$.ajax({
	  url: url,
	  method: "GET",
	  async:false,
	  success: function(result){
	  	$('#'+tabname).html(result);
	  },
	  error: function(e){alert("error"+e);}
	});

	    
    }
</script>

