<?PHP
//if (session_status() == PHP_SESSION_NONE) {
//    session_start();
//}
if (isset($_SESSION['logintype'])){
    if ($_SESSION['logintype'] != 0) //if the login person is a teacher
    {
        header( 'Location: ./TeacherHome.php' ) ;
    } 
}
?>