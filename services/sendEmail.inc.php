<?php 

$headers="";
$eol="\r\n";
$mime_boundary=md5(time());
$fromaddress = "no-reply@qurananalysis.com";

# Common Headers
$headers .= 'From: QA <'.$fromaddress.'>'.$eol;
$headers .= 'Reply-To: QA<'.$fromaddress.'>'.$eol;
$headers .= 'Return-Path: QA <'.$fromaddress.'>'.$eol;    // these two to set reply address
$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
$headers .= 'MIME-Version: 1.0'.$eol;

$headers .= 'Content-type: text/html; charset=UTF-8' . $eol;
//////////////////////////////////////////////////



$to  = "karim@qurananalysis.com";



$mail_result = mail($to,"QA Event",$body,$headers,"-f$fromaddress");

?>