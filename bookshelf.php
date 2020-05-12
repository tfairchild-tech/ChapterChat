<?php
include 'requireStudent.php';
include 'header.php';

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<link rel="stylesheet" href="css/bookshelf.css" />

<div class="container">
    <div class="pull-right">
        <a href="studentHome.php"><br>
            <span class="glyphicon glyphicon-remove">&nbsp;&nbsp;</span>
        </a>
    </div>
    <div class="jumbotron chapter_chat_jumbotron">

        <div class="jumbotron chapter_chat_bookshelf_jumbotron center-block">

                <?php

                    include('db_connect.php');
                    $readinglevel = $_SESSION["readinglevel"];
                    $studentID = $_SESSION["SCId"];
                    $quizyear = date("Y");

                    if (!isset($_GET['rlevel'])) {  //default is get all books the student has taken quizzes on
                        $call = mysqli_prepare($conn, 'CALL ReadEdu.getQuizHistory(?,?)');
                        mysqli_stmt_bind_param($call, 'ss', $studentID,$quizyear);
                        mysqli_stmt_execute($call);

                        $result = $call->get_result();
                        $rowcount = mysqli_num_rows($result);

                        if ($rowcount > 0) {
                            echo '<div class="row bookrow">';
                            $bookcounter = 0;
                            while ($row = $result->fetch_assoc()) {
                                $bookcounter++;
                                $picPath = $row["picPath"];
                                $bookTitle = $row["BookTitle"];
                                if ($bookcounter > 6) {
                                    echo '</div><div class="row bookrow">';
                                    $bookcounter = 0;
                                }
                                echo '<a href="bookSearch.php?bookTitle=' . $bookTitle . '" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/' . $picPath . '\');"></div></a>';
                            }
                            echo '</div>';
                        }

                        mysqli_stmt_close($call);
                        mysqli_free_result($result);


                    } else {
                        if ($_GET['rlevel'] = 'gte') {  //display all books greater than or equal to the students current reading level
                            $op = 'gte';
                            $call = mysqli_prepare($conn, 'CALL ReadEdu.getBooksByLevel(?,?,?)');
                            mysqli_stmt_bind_param($call, 'dsi', $readinglevel, $op, $_SESSION["SCId"]);  //get books >= this students reading level
                            mysqli_stmt_execute($call);

                            $result = $call->get_result();
                            $rowcount = mysqli_num_rows($result);
                            if ($rowcount > 0) {
                                echo '<div class="row bookrow">';
                                $bookcounter = 0;
                                while ($row = $result->fetch_assoc()) {
                                    $bookcounter++;
                                    $picPath = $row["picPath"];
                                    $bookTitle = $row["bookTitle"];
                                    if ($bookcounter > 6) {
                                        echo '</div><div class="row bookrow">';
                                        $bookcounter = 0;
                                    }
                                    echo '<a href="bookSearch.php?bookTitle=' . $bookTitle . '" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/' . $picPath . '\');"></div></a>';
                                }
                                echo '</div>';
                            }

                            mysqli_stmt_close($call);
                            mysqli_free_result($result);
                        }
                    }

                    if (isset($_GET['disptestbooks'])) {
                        echo '
                        <div class="row bookrow">
                            <a href="bookSearch.php?bookTitle=Grumpy Groundhog" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Grumpy_Groundhog.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=Charlie Bone and the Hidden King" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Charlie_Bone_and_the_Hidden_King.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=The Green Ember" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Green_Ember.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=The Wasington Monument" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Washington_Monument.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=Grumpy Groundhog" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Grumpy_Groundhog.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=Charlie Bone and the Hidden King" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Charlie_Bone_and_the_Hidden_King.jpg\');"></div></a>
                        </div>
                        <div class="row bookrow">
            
                            <a href="bookSearch.php?bookTitle=The Green Ember" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Green_Ember.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=The Wasington Monument" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Washington_Monument.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=Grumpy Groundhog" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Grumpy_Groundhog.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=Charlie Bone and the Hidden King" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Charlie_Bone_and_the_Hidden_King.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=The Green Ember" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Green_Ember.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=The Wasington Monument" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Washington_Monument.jpg\');"></div></a>
                        </div>
                        <div class="row bookrow">
                            <a href="bookSearch.php?bookTitle=Grumpy Groundhog" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Grumpy_Groundhog.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=Charlie Bone and the Hidden King" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/Charlie_Bone_and_the_Hidden_King.jpg\');"></div></a>
                            <a href="bookSearch.php?bookTitle=The Green Ember" class="book-link"><div class="col-md-3 img_container" style="background-image:url(\'bookimages/The_Green_Ember.jpg\');"></div></a>
                        </div>
                        ';
                    } else {
                        echo '
                            <div class="row bookrow"><div class="col-md-3 img_container_no_shadow"></div></div>
                            <div class="row bookrow"><div class="col-md-3 img_container_no_shadow"></div></div>    
                            <div class="row bookrow"><div class="col-md-3 img_container_no_shadow"></div></div>    
                        ';
                    }

                ?>





        </div>
</div>


<?php include 'footer.php';?>
