<?php
    // Start the session
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- <script src="js/jquery-ui-1.12.1/external/jquery/jquery.js"></script>
    <script src="js/jquery-ui-1.12.1/jquery-ui.min.js"></script> -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script> -->
    <script src="js/bootstrap.min.js"></script>
    <!-- <script src="js/bootstrap-datepicker.min.js"></script> -->
    <script src="js/jquery-validation/jquery.validate.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="js/jquery.peity.min.js"></script>
    <script src="js/datatableall/js/jquery.dataTables.min.js"></script>
    <script src="js/datatableall/js/dataTables.bootstrap.js"></script>

    <link rel="icon" href="favicon.ico">
    <link rel="stylesheet" href="css/ChapterChat.css" />

    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Baloo+Bhaina' type='text/css'>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.min.css" />


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
	-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand chapter-chat-nav-link" href="#ChapterChatMain">
                <img src="assets/chapterChat.png">
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                <li class="hidden">
                    <a class="page-scroll" href="#ChapterChatMain"></a>
                </li>
                <li>
                    <a class="page-scroll chapterchat-font-top" id="home" href="index.php">&nbsp;&nbsp;&nbsp;Home&nbsp;&nbsp;&nbsp;</a>
                </li>
                <li>
                    <a class="page-scroll chapterchat-font-top chapter-chat-nav-link" href="about.php" id="aboutLink">&nbsp;&nbsp;&nbsp;About&nbsp;&nbsp;&nbsp;</a>
                </li>
                <?php
                if (isset($_SESSION['login'])) {
                    echo '<li role="presentation"><a class="page-scroll chapterchat-font-top" href="#" data-toggle="modal" data-target="#signoutModal">&nbsp;&nbsp;&nbsp;Sign Out&nbsp;&nbsp;&nbsp;</a></li>';
                } else {
                    echo '<li role="presentation"><a class="page-scroll chapterchat-font-top" href="#" data-toggle="modal" data-target="#registerModal">&nbsp;&nbsp;&nbsp;Sign In/Register&nbsp;&nbsp;&nbsp;</a></li>';
                }
                ?>
                <?php
                if (isset($_SESSION['login'])) {
                    echo '<li><a class="chapterchat-font-top-welcome" href="#">&nbsp;&nbsp;&nbsp;Welcome, ' . $_SESSION['login'] . '</a></li>';
                }
                ?>

           </ul>
        </div>

    </div>
</nav>

<?php include 'register.php';?>
<?php include 'admin.php'; ?>
<?php include 'signout.php';?>
