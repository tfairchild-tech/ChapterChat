<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	// Start the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $badpassword = 0;

    //if (isset($_SESSION["loginCount"])) {
    //    $login_count = $_SESSION["loginCount"];
    //    $_SESSION["loginCount"] = $login_count+1;
    //} else {
    //    $_SESSION["loginCount"] = 1;
    // }

    if (isset($_POST["submit"])) {
		include('db_connect.php');

		$login = $_POST["inputEmail"];
		$pw = $_POST["inputPassword"];
		//$loginType= -1;

		$call = mysqli_prepare($conn, 'CALL ReadEdu.getAuthentication(?, ?)');
		mysqli_stmt_bind_param($call, 'ss', $login,$pw);
		mysqli_stmt_execute($call);

		$result = $call ->get_result();

		while ($row = $result->fetch_assoc()) {
			//if ($row["loginType"] == 1) {
            $_SESSION["logintype"] = $row["loginType"];
            $loginType= $row["loginType"];
			//}
		}
		mysqli_stmt_close($call);
        mysqli_free_result($result);
        //$call->close();

		if (isset($_SESSION["logintype"])) {

			$call = mysqli_prepare($conn, 'CALL ReadEdu.getStudentInfo(?)');
			mysqli_stmt_bind_param($call, 's', $login);
			mysqli_stmt_execute($call);

			$result = $call -> get_result();

			while ($row = $result->fetch_assoc()) {
				$_SESSION["login"] = $row["StudentFirstName"].' '.$row["StudentLastName"];
				$_SESSION["SCId"]= $row["SCId"];

			}

            mysqli_stmt_close($call);
            mysqli_free_result($result);
            //$call->close();
		}

		include 'db_close.php';

        if (isset($_SESSION["logintype"])==false) {
            $badpassword = 1;
        } else if ($loginType==0){// student login
            header( 'Location: ./StudentHome.php' ) ;
        } else if ($loginType==1){	// teacher login
            header( 'Location: ./TeacherHome.php' ) ;
        }
	}
	
?>


<div class="modal fade" id="signinModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Please sign in</h4><br>
            </div>
            <div class="modal-body">

                    <div class="container">

                        <form class="form-signin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="inputEmail" class="sr-only">User Name</label>
                                        <input type="text" id="inputEmail" name="inputEmail" class="form-control col-sm-4" placeholder="Login" required autofocus>
                                    </div>
                                    <div class="col-md-8"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="inputPassword" class="sr-only">Password</label>
                                        <input type="password" id="inputPassword" name="inputPassword" class="btn-sm form-control col-sm-4" placeholder="Password" required>
                                    </div>
                                    <div class="col-md-8"></div>
                                </div>
                                <div class="row">


                                </div>
                                <br>
                                    <?php
                                         if ($badpassword==1){
                                         echo "<br><div class='alert alert-danger col-sm-4' role='alert'>Invalid Login or Password</div><br>";
                                         }
                                       ?>
                                    <div>
                                        <button class="btn btn-sm btn-primary" id="submit" name="submit" type="submit">Sign in</button>
                                    </div>
                            </div>
                        </form>
                    </div> <!-- /container -->
            </div>
        </div>
    </div>
</div>
