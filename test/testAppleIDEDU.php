<?php
require(__DIR__ . '/../src/classes/iTunesAccountActions.class.php');

//parent url from email.
$url="";
//temporary password from email.
$tpw="";
//Password for new Apple ID
$GLOBALS['aidpw']='MyPassword';

#questions for form - these are the only supported questions right now.
$q1="What is your dream job?";
$a1="";
$q2="What was the first name of your first boss?";
$a2="";
$q3="What is the name of your favorite sports team?";
$a3="";
$phone="5555550000";

function generatepw(){
	return $GLOBALS['aidpw'];
}

$itunes->set_callback('generatepw');

$itunes = new iTunesAccountActions;
if (!$itunes->createAccountEDU($url,$tpw,$q1,$a1,$q2,$a2,$q3,$a3,$phone))
	print($itunes->getError() . PHP_EOL);
