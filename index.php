<?php include 'header.php';?>

     <section id="ChapterChatMain" name="ChapterChatMain">
        <div class="container">
            <div class="row">
                <div >
                    <?php
                    if (isset($_SESSION["logintype"])) {
                        if ($_SESSION["logintype"] == 0) {
                            echo "<script> window.location.replace('studentHome.php') </script>";
                        } else {
                            echo "<script> window.location.replace('TeacherHome.php') </script>";
                        }
                    }
                    if (isset($_SESSION["badpassword"]) == 1) {
                        unset($_SESSION['badpassword']);
                        echo "<script>$('#registerModal').modal('show');</script>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

<?php include 'header.php';?>

<div class="container">
    <div class="jumbotron chapter_chat_about_jumbotron">
        <div class="row center-block" style="width:100%;height:25%;color:#7AB142;">
            <div class="text-center" style="color: #1565C0;font-size:x-small;">
                <div class="hpanel hgreen contact-panel">
                    <div class="panel-body">
                        <h4><a href=""> Welcome to Team 1's project for cs6460 Educational Technology </a></h4>
                        <div class="text-danger font-bold"><h5>designed by Lina Haddad and Traci Fairchild</h5></div>
                        <hr>
                        <h4>
                            At Chapter Chat our goal is to broaden the students reading horizon and create enjoyment through motivation and a love of reading!
                        </h4>
                        <hr>
                        <img alt="logo" src="assets/favicon-32x32.png" data-portal="spastatic">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';?>
