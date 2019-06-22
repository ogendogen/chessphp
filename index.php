<?php
	$before = microtime(true);// Performance Tests
	
	session_start();
	require_once("inc/constants.php");
	require_once("inc/utility.php");
	require_once("inc/header.php");

	$is_logged_in = isset($_SESSION['userid']);
	echo "<script>var is_logged_in = ".($is_logged_in ? "true" : "false").";</script>";

	// Main Area
	require_once("inc/modal.html"); // html modal window

    if (isset($_GET["tab"]))
    {
        switch($_GET["tab"])
        {
            case "register":
                include("inc/register.php");
                break;
            case "login":
                include("inc/login.php");
                break;
            case "browse":
                include("inc/browse.php");
                break;
            case "play":
                include("inc/play.php");
                break;
            case "help":
                include("inc/help.php");
                break;
            default:
                include("inc/main.php");
                break;
        }
    }
    else
    {
        include("inc/main.php");
    }

	// End of main area
	require_once("inc/footer.php");
?>