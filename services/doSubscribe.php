<?php
error_reporting ( E_ALL );

include (dirname ( __FILE__ ) . "/../libs/core.lib.php");
include (dirname ( __FILE__ ) . "/../dal/SQLite3DataLayer.class.php");

$email = $_POST ['email'];
$name = $_POST ['name'];
$title = $_POST ['title'];
$entityVal = $_POST ['entity'];


if (empty ( $email )) 
{
	echo "ERROR";
	exit ();
}

$dbPath = dirname ( __FILE__ ) . "/../data/databases/main.sqlite";

$sqliteDBObj = new SQLite3DataLayer ();

$sqliteDBObj->openDB ( $dbPath, "rw" );

$sqliteDBObj->execOnewayQuery ( MAILING_LIST_TABLE );

$sqliteDBObj->execOnewayQuery ( "INSERT INTO EmailList " . "(name, title, entity, email) " . "VALUES " . "('$name', '$title','$entityVal','$email')" );

$error = handleDBError ( $sqliteDBObj );

if (empty ( $error )) 
{
	$body = "New subscription<br>Name:$name<br>Title:$title<br>Entity:$entityVal<br>Email:$email<br>";
	include ("sendEmail.inc.php");
	
	echo "DONE";
} 
else 
{
	echo "Error occured! please report it using the contact page";//$error;
}

?>
