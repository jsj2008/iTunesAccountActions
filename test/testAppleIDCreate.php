<?php
require(__DIR__ . '/../src/classes/iTunesAccountActions.class.php');

	$itunes = new iTunesAccountActions;
	#User a fake username and passoword here.
	$itunes->apple_id = 'username';
	$itunes->password = 'Password';

	/*
	 *Questions are numeric based on this:
	 *Q1:
	 *<option value="0">What is the first name of your best friend in high school?</option>
	 <option value="1">What was the name of your first pet?</option>
	 <option value="2">What was the first thing you learned to cook?</option>
	 <option value="3">What was the first film you saw in the theater?</option>
	 <option value="4">Where did you go the first time you flew on a plane?</option>
	 <option value="5">What is the last name of your favorite elementary school teacher?</option></select></div>
	 *
	 *Q2:
	 <option value="0">What is your dream job?</option>
	 <option value="1">What is your favorite children’s book?</option>
	 <option value="2">What was the model of your first car?</option>
	 <option value="3">What was your childhood nickname?</option>
	 <option value="4">Who was your favorite film star or character in school?</option>
	 <option value="5">Who was your favorite singer or band in high school?</option></select></div>
	 *
	 *Q3:
	 <option value="0">In what city did your parents meet?</option>
	 <option value="1">What was the first name of your first boss?</option>
	 <option value="2">What is the name of the street where you grew up?</option>
	 <option value="3">What is the name of the first beach you visited?</option>
	 <option value="4">What was the first album that you purchased?</option>
	 <option value="5">What is the name of your favorite sports team?</option></select></div>
	 */
	
	$q1=;
	$a1='answer1';
	$q2=;
	$a2='answer2';
	$q3=;
	$a3='answer3';
	
	$fn='';
	$ln='';
	$email='';
	$aidpw='';
	#Birth Month - Numeric - 1
	$bm=0;
	#Birth Day - Numeric - 1
	$bd=0;
	$by=2001;
	$address='123 street';
	$city='';
	/*
	 *State is numeric as follows:
	 <option value="0">AL - Alabama</option>
	 <option value="1">AK - Alaska</option>
	 <option value="2">AZ - Arizona</option>
	 <option value="3">AR - Arkansas</option>
	 <option value="4">CA - California</option>
	 <option value="5">CO - Colorado</option>
	 <option value="6">CT - Connecticut</option>
	 <option value="7">DE - Delaware</option>
	 <option value="8">DC - District of Columbia</option>
	 <option value="9">FL - Florida</option>
	 <option value="10">GA - Georgia</option>
	 <option value="11">HI - Hawaii</option>
	 <option value="12">ID - Idaho</option>
	 <option value="13">IL - Illinois</option>
	 <option value="14">IN - Indiana</option>
	 <option value="15">IA - Iowa</option>
	 <option value="16">KS - Kansas</option>
	 <option value="17">KY - Kentucky</option>
	 <option value="18">LA - Louisiana</option>
	 <option value="19">ME - Maine</option>
	 <option value="20">MD - Maryland</option>
	 <option value="21">MA - Massachusetts</option>
	 <option value="22">MI - Michigan</option>
	 <option value="23">MN - Minnesota</option>
	 <option value="24">MS - Mississippi</option>
	 <option value="25">MO - Missouri</option>
	 <option value="26">MT - Montana</option>
	 <option value="27">NE - Nebraska</option>
	 <option value="28">NV - Nevada</option>
	 <option value="29">NH - New Hampshire</option>
	 <option value="30">NJ - New Jersey</option>
	 <option value="31">NM - New Mexico</option>
	 <option value="32">NY - New York</option>
	 <option value="33">NC - North Carolina</option>
	 <option value="34">ND - North Dakota</option>
	 <option value="35">OH - Ohio</option>
	 <option value="36">OK - Oklahoma</option>
	 <option value="37">OR - Oregon</option>
	 <option value="38">PA - Pennsylvania</option>
	 <option value="39">RI - Rhode Island</option>
	 <option value="40">SC - South Carolina</option>
	 <option value="41">SD - South Dakota</option>
	 <option value="42">TN - Tennessee</option>
	 <option value="43">TX - Texas</option>
	 <option value="44">UT - Utah</option>
	 <option value="45">VT - Vermont</option>
	 <option value="46">VA - Virginia</option>
	 <option value="47">WA - Washington</option>
	 <option value="48">WV - West Virginia</option>
	 <option value="49">WI - Wisconsin</option>
	 <option value="50">WY - Wyoming</option>
	 <option value="51">VI - Virgin Islands</option>
	 <option value="52">PW - Palau</option>
	 <option value="53">AA - Armed Forces Americas</option>
	 <option value="54">AE - Armed Forces Europe</option>
	 <option value="55">AP - Armed Forces Pacific</option>
	 <option value="56">AS - AMERICA SAMOA</option>
	 <option value="57">GU - GUAM</option>
	 <option value="58">PI - PACIFIC ISLANDS</option>
	 <option value="59">PR - PUERTO RICO</option></select></div>
	 */
	$state=38; #'PA';
	$zip='12345';
	$phoneArea='555';
	$phone='5550000';
	
	
	
	$itunes->login();
	$output = $itunes->createAccount($email,$aidpw,$q1,$a1,$q2,$a2,$q3,$a3,$by,$bd,$bm,$fn,$ln,$address,$city,$state,$zip,$phoneArea,$phone);
	print($output);

