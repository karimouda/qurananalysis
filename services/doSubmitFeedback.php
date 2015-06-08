<?php
error_reporting ( E_ALL );

include (dirname ( __FILE__ ) . "/../libs/core.lib.php");
include (dirname ( __FILE__ ) . "/../dal/SQLite3DataLayer.class.php");

$email = $_POST ['email'];
$name = $_POST ['name'];
$feedbackType = $_POST ['feedbackType'];
$feedbackText = $_POST ['feedbackText'];

if (empty ( $email )) 
{
	echo "ERROR";
	exit ();
}



$dbPath = dirname ( __FILE__ ) . "/../data/databases/main.sqlite";

$sqliteDBObj = new SQLite3DataLayer ();

$sqliteDBObj->openDB ( $dbPath, "rw" );

$sqliteDBObj->execOnewayQuery ( FEEDBACK_TABLE );

$sqliteDBObj->execOnewayQuery ( "INSERT INTO Feedback " . "(name, email, type, feedback_text) " . "VALUES " . "('$name', '$email', '$feedbackType', '$feedbackText')" );

$error = handleDBError ( $sqliteDBObj );

if (empty ( $error )) 
{
	$body = "New Feedback<br>Name:$name<br>Email:$email<br>Type:$feedbackType<br>Text:$feedbackText<br>";
	include ("sendEmail.inc.php");
	
	echo "DONE";
} 
else 
{
	echo $error;
}

?>
