<?PHP 
include 'requireStudent.php';
include 'header.php'; 
	$bookTitle="";	
    if (isset($_POST["search"]) or isset($_GET["bookTitle"])) {

        if (isset($_POST["search"])) {
                $bookTitle = $_POST["bookTitle"];
        } else {
                $bookTitle = $_GET["bookTitle"];
        }
		
		include('db_connect.php');

		$call = mysqli_prepare($conn, 'CALL ReadEdu.lookupBooks(?)');
		mysqli_stmt_bind_param($call, 's', $bookTitle);
		mysqli_stmt_execute($call);

		$result = $call ->get_result();
		$books="<table class='table table-striped table-bordered'>";
		$books= $books . '<tr><th>Book Cover</th>';
		$books= $books . '<th>Book Title</th>';
		$books= $books . '<th>Author Name</th>';
		$books= $books . '<th>Book Level</th>';
		$books= $books . '<th>Book Points</th>';
		$books= $books . '<th>ISBN</th>';
		$books= $books . '<th></th></tr>';
		
		while ($row = $result->fetch_assoc()) {
			$books= $books . '<tr>';
			$books= $books . '<td><img src="../bookimages/'.$row["picPath"].'" /></td>';
			$books= $books . '<td>'.$row['bookTitle'].'</td>';
			$books= $books . '<td>'.$row['authorname'].'</td>';
			$books= $books . '<td>'.$row['BookLevel'].'</td>';
			$books= $books . '<td>'.$row['possiblePoints'].'</td>';
			$books= $books . '<td>'.$row['ISBN'].'</td>';
			$books= $books . '<td class="chapterchat-font-md"><a href="takeQuiz.php?bookId='.$row["bookId"].'">Take Quiz</a></td>';
			$books= $books . '</tr>';
		}
		$books= $books . '</table>';
		/* free result set */
		mysqli_free_result($result);		
		$call->close();	
		include 'db_close.php';
	}	
?>
<br/><br/>

<div class="container">
    <div class="pull-right">
        <a href="studentHome.php"><br>
            <span class="glyphicon glyphicon-remove">&nbsp;&nbsp;</span>
        </a>
    </div>
    <div class="jumbotron chapter_chat_jumbotron">
        <div class="row center-block">
            <div >
                <div class="chapterchat-font-plain-lg">Find a Book</div>
                <form class="form-signin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group col-lg-6 text-center">
                        <div class="input-group">
                            <input type="text" id="bookTitle" name="bookTitle" class="form-control col-sm-4" placeholder="Book title" value="<?php echo $bookTitle ?>" required autofocus>
                            <span class="input-group-btn">
                                <button type="submit" name="search" class="btn btn-primary">Search</button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>



<?php
	if (isset($_POST["search"]) or isset($_GET["bookTitle"])) {
		echo ("<br/><br/><div>");
		echo ($books);
		echo ("</div>");
	}
?>
    </div>
</div>