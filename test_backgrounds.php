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
    <link rel="icon" href="../../favicon.ico">
    <link rel="stylesheet" href="css/ChapterChat.css" />
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Baloo+Bhaina' type='text/css'>
    <link rel="shortcut icon" href="http://barbados.lno.att.com/ReadAndQuiz/favicon.ico" type="image/x-icon"/>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>


    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

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
                <img src="assets/logaster_logo/Small_size_370x63_pixels/1_Primary_logo_on_transparent_370x63.png">
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
                    <a class="page-scroll chapterchat-font-top" id="home" href="index.php">Home</a>
                </li>
                <li>
                    <a class="page-scroll chapterchat-font-top chapter-chat-nav-link" href="about.php" id="aboutLink">About</a>
                </li>
                <?php
                if (isset($_SESSION['login'])) {
                    echo '<li role="presentation"><a class="page-scroll chapterchat-font-top" href="#" data-toggle="modal" data-target="#signoutModal">Sign Out</a></li>';
                } else {
                    echo '<li role="presentation"><a class="page-scroll chapterchat-font-top" href="#" data-toggle="modal" data-target="#signinModal">Sign In</a></li>';
                }
                ?>
                <li role="presentation">
                    <a class="page-scroll chapterchat-font-top" href="#" data-toggle="modal" data-target="#adminModal" >Admin</a>
                </li>

                <?php
                if (isset($_SESSION['login'])) {
                    echo '<li><a class="chapterchat-font-top-welcome" href="#">Welcome, ' . $_SESSION['login'] . '</a></li>';
                }
                ?>
            </ul>
        </div>


<div class="bs-example" data-example-id="simple-carousel">
    <div class="carousel slide" id="carousel-example-generic2" data-ride="carousel">

        <ol class="carousel-indicators">
            <li class="active" data-slide-to="0" data-target="#carousel-example-generic2"></li>
            <li data-slide-to="1" data-target="#carousel-example-generic2"></li>
            <li data-slide-to="2" data-target="#carousel-example-generic2"></li>
        </ol>
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <img title="image1" alt="First slide [900x500]" src="assets/stock-vector-many-children-reading-books-in-the-park-illustration-475561780.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img title="image2" alt="Second slide [900x500]" src="assets/stock-vector-illustration-of-a-popup-story-book-with-many-animals-236571706.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img  title="image3" alt="Third slide [900x500]" src="assets/stock-vector-imagination-concept-open-book-with-air-balloon-rocket-and-airplane-flying-out-213950314.jpg" data-holder-rendered="true">
            </div>
             <div class="item">
                <img  title="image4" alt="Third slide [900x500]" src="assets/stock-vector-a-garden-where-they-grow-books-224509702.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img  title="image5"  alt="Third slide [900x500]" src="assets/stock-photo-a-young-boy-is-reading-a-book-with-school-icons-such-as-math-formulas-animals-and-nature-objects-137694665.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img title="image6"  alt="Third slide [900x500]" src="assets/stock-vector-seascape-with-hot-air-balloons-vector-139229783.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img  title="image7" alt="Third slide [900x500]" src="assets/stock-vector-empty-blackboard-with-backpack-and-books-on-the-sides-264084749.jpg" data-holder-rendered="true">
            </div>

            <div class="item">
                <img  title="image8" alt="Third slide [900x500]" src="assets/stock-vector-children-playing-in-the-playground-illustration-312350645.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img title="image9"  alt="Third slide [900x500]" src="assets/stock-vector-many-children-reading-in-room-illustration-471815687.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img title="image10"  alt="Third slide [900x500]" src="assets/stock-vector-many-children-reading-books-in-the-park-illustration-475561780.jpg" data-holder-rendered="true">
            </div>
            <div class="item">
                <img  title="image11" alt="Third slide [900x500]" src="assets/stock-vector-illustration-of-a-dragon-and-a-knight-in-the-storybook-249378946.jpg" data-holder-rendered="true">
            </div>


        </div>
        <a class="left carousel-control" role="button" href="#carousel-example-generic2" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" role="button" href="#carousel-example-generic2" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <script>
        $("#carousel-example-generic2").carousel({
            interval : 8000,
            pause: false
        });
    </script>
</div>


<?php include 'footer.php';?>

    </div>
</nav>
</body>
</html>

