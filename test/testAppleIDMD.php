<?php
require(__DIR__ . '/../src/classes/iTunesAccountActions.class.php');

$itunes = new iTunesAccountActions;
$itunes->apple_id = 'myappleid@domain.net';
$itunes->password = 'MyPassword';
$itunes->guid = '368AB5B8.1E4FBBA5.00000000.69DDCAEA.14DF9387.83A1EBA9.662208C0';
$MDInvite='https://buy.itunes.apple.com/WebObjects/MZFinance.woa/wa/associateVPPUserWithITSAccount?cc=us&inviteCode=c4705138afad44d9bfc5c641fcc53652&mt=8';
	
if(!$MDInviteURL) {
	$msg=$LSMDM->geterror();
	print($msg . "\n");
} else {
	if(!$itunes->login()) {
		$msg=$itunes->geterror();
		print($msg . "\n");
	} else
	if(!$itunes->associateMD($MDInviteURL)) {
		$msg=$itunes->geterror();
		print($msg . "\n");
	}
}

