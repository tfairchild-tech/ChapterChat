<?PHP

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['logintype'])){
    if ($_SESSION['logintype'] != 1) //if the login person is a student
    {
        header( 'Location: ./logout.php' ) ;
    } 
}
else
{
    header( 'Location: ./login.php' ) ;
}
?>