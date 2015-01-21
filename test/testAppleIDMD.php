<?php
require(__DIR__ . '/../src/classes/iTunesAccountActions.class.php');

$itunes = new iTunesAccountActions;
$itunes->apple_id = 'myappleid@domain.net';
$itunes->password = 'MyPassword';
$itunes->guid = '368AB5B8.1E4FBBA5.00000000.69DDCAEA.14DF9387.83A1EBA9.662208C0';
$MDInviteURL='https://buy.itunes.apple.com/WebObjects/MZFinance.woa/wa/associateVPPUserWithITSAccount?cc=us&inviteCode=67d2d8c52b434149b1411aa96dc5aa23&mt=8';
	

	if(!$itunes->login()) {
		$msg=$itunes->geterror();
		print($msg . "\n");
	} else
	if(!$itunes->associateMD($MDInviteURL)) {
		$msg=$itunes->geterror();
		print($msg . "\n");
	}


