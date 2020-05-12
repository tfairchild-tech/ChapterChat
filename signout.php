<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

	// Start the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_POST["submitlogout"])) {

		//$login = $_POST["login"];
        //$loginType= $_POST["logintype"];
        //$scid = $_POST["scid"];

		if (isset($_SESSION["login"])) {
            unset($_SESSION['login']);
        }

        if (isset($_SESSION["logintype"])) {
            unset($_SESSION['logintype']);
        }

        //cleanup
        if (isset($_SESSION["loginType"])) {
            unset($_SESSION['loginType']);
        }

        if (isset($_SESSION["SCId"])) {
            unset($_SESSION['SCId']);
        }

        if (isset($_SESSION["readinglevel"])) {
            unset($_SESSION['readinglevel']);
        }

        if (isset($_SESSION["teacherid"])) {
            unset($_SESSION['teacherid']);
        }


        if (isset($_SESSION["badpassword"])) {
            unset($_SESSION['badpassword']);
        }

        //header( 'Location: ./index.php' ) ;
		//die();
        echo "<script> window.location.replace('index.php') </script>";
	}
	
?>




<link rel="stylesheet" href="css/register.css" />
<div class="modal fade" id="signoutModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Sign Out</h4><br>
            </div>
            <div class="modal-body">
                <div class="container">

                    <div class="row">
                        <div class="col-md-12 ">
                            <div class="panel panel-login col-md-5">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <a href="#" class="active" id="login-form-link">Sign Out</a>
                                        </div>
                                    </div>
                                    <hr>
                                </div>


                                <div class="panel-body center-block">
                                    <div class="row center-block">
                                        <div class="col-lg-12 text-center">
											<form class="form-signout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                <input type="hidden" id="login" name="login" class="form-control col-sm-4" value="<?php echo $_SESSION['login']; ?>">
                                                <input type="hidden" id="scid" name="scid" class="form-control col-sm-4" value="<?php echo $_SESSION['SCId']; ?>">
                                                <input type="hidden" id="loginType" name="loginType" class="form-control col-sm-4" value="<?php echo $_SESSION['loginType']; ?>">

                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-12 ">
                                                            <input type="submit" name="submitlogout" id="submitlogout" tabindex="4" class="form-control btn btn-login" value="Sign Out">
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
