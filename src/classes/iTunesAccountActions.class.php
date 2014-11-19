<?php
require_once(__DIR__ . '/iTunes.class.php');
require_once(__DIR__ . '/simple_html_dom.php');
/**
 * iTunes Appstore Protocol class
 *
 * @author andrewzirkel (andrewzirkel [at] gmail [dot] com)
 * 
 * public methods:
 * string	getError()				- gets error thrown by apple
 * bool 	associateMD($MDInvite) 	- Associates MDInvite URL with logged in user
 * string	createAccount($email,$pw,$q1,$a1,$q2,$a2,$q3,$a3,$by,$bd,$bm,$fn,$ln,$address,$city,$state,$zip,$phoneArea,$phone)
 * 									- creates regular (13 and over) account from supplied info
 * string	verifyAccount($verifyUrl,$email,$aidpw)
 * 									- verifys email address
 * bool createAccountEDU($url,$tpw,$q1,$a1,$q2,$a2,$q3,$a3,$phone)
 * 									- creates EDU (12 and under) with parent url and supplied info
 * 									- use set_callback($function_name) to register your password generator function
 */
class iTunesAccountActions extends iTunes
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//array to hold user info (email, bday, aidpw) for 
	protected $userInfo;
	//Callback to get formatted password
	public $pwcallback = null;
	
	private $errormsg;
	
	public function getError() {
		return($this->errormsg);
	}
	
	#Uses logged in user and associates with MD Invite URL
	public function associateMD($MDInvite) {
		$url=$MDInvite;
		$ret = $this->http_get($url);
		if (preg_match('/Your Apple ID is already associated with this VPP account/',$ret)) {
			$this->errormsg='Your Apple ID is already associated with this VPP account';
			print ($this->errormsg);
			return(false);
		}
		if (preg_match('/Unable to Allow Assignment of Content/',$ret)) {
			$this->errormsg='Unable to Allow Assignment of Content';
			print ($this->errormsg);
			return(false);
		}
		$m = array();
		if (!preg_match('#\<form name\="f_2_1_1_1_3_0_7_11_7" method\="post" action\="([a-zA-Z0-9\/.]+)"\>#', $ret, $m)) {
			$this->errormsg='link not found';
			print ($this->errormsg);
			return(false);
		}else {$url='https://buy.itunes.apple.com' . $m[1];}
		if (!preg_match('#\<input id\="pageUUID" class\="optional" name\="mzPageUUID" type\="hidden" value=\'([a-zA-Z0-9\/.-]+)\' \/\>#', $ret, $m)) {
			$this->errormsg='mzPageUUID not found';
			print ($this->errormsg);
			return(false);
		}else {$mzPageUUID=$m[1];}
		$postfields='action=POST&2_1_1_1_3_0_7_11_7=2_1_1_1_3_0_7_11_7&mzPageUUID=' . $mzPageUUID;
		$ret = $this->http_post($url, $postfields);
		if (preg_match('/This organization can now assign apps and books to you/',$ret)) {
			$msg='This organization can now assign apps and books to you';
			print ($msg);
			return(true);
		}
		$this->errormsg='an error occured';
		return(false);
	}
	
	#traslate string month to integer for form
	private function translateMonth($bm) {
		if (gettype($bm) == "string") {
			switch($bm) {
				case "January":
					$bm = 0;
					break;
				case "February":
					$bm = 1;
					break;
				case "March":
					$bm = 2;
					break;
				case "April":
					$bm = 3;
					break;
				case "May":
					$bm = 4;
					break;
				case "June":
					$bm = 5;
					break;
				case "July":
					$bm = 6;
					break;
				case "August":
					$bm = 7;
					break;
				case "September":
					$bm = 8;
					break;
				case "October":
					$bm = 9;
					break;
				case "November":
					$bm = 10;
					break;
				case "December":
					$bm = 11;
					break;
			}
			return($bm);
		}else{
			return($bm);
		}
	}
	
	#translate question string to int for form
	private function translateQuestion($q) {
		if (gettype($q) == "string") {
			switch($q) {
				case "What was the first film you saw in the theater?":
					$q = 3;
					break;
				case "What is your dream job?":
					$q = 0;
					break;
				case "What was the first name of your first boss?":
					$q = 1;
					break;
			}
		}
		return($q);
	}
	
	#translate state to int for form
	private function translateState($state) {
		if (gettype($state) == "string") {
			switch($state) {
				case "PA":
					$state = 38;
					break;
			}
		}
		return($state);
	}
	
	#Creates Account
	public function createAccount($email,$pw,$q1,$a1,$q2,$a2,$q3,$a3,$by,$bd,$bm,$fn,$ln,$address,$city,$state,$zip,$phoneArea,$phone) {
		$bm = $this->translateMonth($bm);
		$q1 = $this->translateQuestion($q1);
		$q2 = $this->translateQuestion($q2);
		$q3 = $this->translateQuestion($q3);
		$state = $this->translateState($state);
		$headers = array(
											'X-Apple-Store-Front' => '143441-1,12',
											'X-Apple-Tz' => '-14400',
											);
		$this->http_add_headers($headers);
		$url='https://itunes.apple.com/us/app/ibooks/id364709193?mt=8';
		$ret = $this->http_get($url);
		#verify page load
		if (!preg_match('/iBooks/',$ret)) {
			$msg='iBooks page not loaded';
			print ($msg);
			return($msg);
		}
		
		
		$urlbase='https://buy.itunes.apple.com';
		$url=$urlbase . '/WebObjects/MZFinance.woa/wa/signupWizard?machineName=LOCALHOST&guid='. $this->guid . '&product=productType%3DC%26price%3D0%26salableAdamId%3D364709193%26pricingParameters%3DSTDQ%26appExtVrsId%3D114692711%26origPage%3DSoftware-US-Apple-iBooks-364709193%26origPageCh%3DSoftware%2520Pages%26origPageLocation%3DBuy%26origPage2%3DSearch-US%26origPageCh2%3DMedia%2520Search%2520Pages%26origPageLocation2%3DTitledBox_iPad%2520Apps%257CLockup_1';
		curl_setopt($this->curl, CURLOPT_REFERER, 'https://itunes.apple.com/us/app/ibooks/id364709193?mt=8');
		#call welcome
		$reta=array();
		$ret = $this->http_get($url,$reta);
		$htmldom = str_get_html($reta['body']);
		#verify page load
		if (!preg_match('/Connections Get Started/',$ret)) {
			$msg='Connections Get Started page not loaded';
			print ($msg);
			return($msg);
		}
		#set referer for next call
		curl_setopt($this->curl, CURLOPT_REFERER, $url);
		#get pod and update url
		$m = array();
		if (!preg_match('#set-cookie: Pod\=([0-9]+)\;#', $ret, $m)) {
			$msg='link not found';
			print ($msg);
			return($msg);
		}else {$urlbase = 'https://p' . $m[1] . '-buy.itunes.apple.com';}
		#get terms url
		$url=null;
		foreach($htmldom->find('a') as $element)
			if(strpos($element->innertext(),'Continue') !== false) {
				$url = $urlbase . $element->href;
				break;
			}
		if (!isset($url)) {
			$msg='terms link not found';
			print ($msg);
			return($msg);
		}
		
		#call terms
		$ret = $this->http_get($url);
		#verify page load
		if (!preg_match('/Connections Get Started/',$ret)) {
			$msg='Connections Get Started page not loaded';
			print ($msg);
			return($msg);
		}
		#set referer for next call
		curl_setopt($this->curl, CURLOPT_REFERER, $url);
		#get mzPageUUID
		if (!preg_match('#\<input id\="pageUUID" class\="optional" name\="mzPageUUID" type\="hidden" value=\'([a-zA-Z0-9\/.-]+)\' \/\>#', $ret, $m)) {
			$msg='mzPageUUID not found';
			print ($msg);
			return($msg);
		}else {$mzPageUUID=$m[1];}
		#get ID Details url
		$m = array();
		if (!preg_match('#\<form name\="f_2_1_1_1_3_0_7_11_3_7" method\="post" action\="([a-zA-Z0-9\/.]+)"\>#', $ret, $m)) {
			$msg='link not found';
			print ($msg);
			return($msg);
		}else {$url=$urlbase . $m[1];}
		
		
		#post terms & get ID Details
		$postfields='2.1.1.1.3.0.7.11.3.7.1=2.1.1.1.3.0.7.11.3.7.1';
		$postfields.='&mzPageUUID=' . $mzPageUUID;
		$ret = $this->http_post($url, $postfields);
		#verify page load
		if (!preg_match('/Edit Account/',$ret)) {
			$msg='Edit Account page not loaded';
			print ($msg);
			return($msg);
		}
		#get mzPageUUID
		if (!preg_match('#\<input id\="pageUUID" class\="optional" name\="mzPageUUID" type\="hidden" value=\'([a-zA-Z0-9\/.-]+)\' \/\>#', $ret, $m)) {
			$msg='mzPageUUID not found';
			print ($msg);
			return($msg);
		}else {$mzPageUUID=$m[1];}
		#get Payment url
		$m = array();
		if (!preg_match('#\<form name\="f_2_0_1_1_3_0_7_11_1" method\="post" action\="([a-zA-Z0-9\/.]+)"\>#', $ret, $m)) {
			$msg='link not found';
			print ($msg);
			return($msg);
		}else {$url=$urlbase . $m[1];}
		
		
		#post ID Details & get payment
		$postfields='2.0.1.1.3.0.7.11.1.3.1.2.5.7.3.4.5.0=' . urlencode($email);
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.7.13=' . urlencode($pw);
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.7.15=' . urlencode($pw);
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.5=' . $q1;
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.7=' . urlencode($a1);
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.9=' . $q2;
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.11=' . urlencode($a2);
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.13=' . $q3;
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.15=' . urlencode($a3);
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.1.17.5='; #rescue email
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.7.1.1=' . $bm;
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.7.1.3=' . $bd;
		$postfields.='&2.0.1.1.3.0.7.11.1.3.1.2.5.11.7.1.5.0=' . $by;
		$postfields.='&2.0.1.1.3.0.7.11.1.3.7=Continue';
		$postfields.='&mzPageUUID=' . $mzPageUUID;
		$postfields.='&machineGUID=';
		$postfields.='&xAppleActionSignature=';
		$ret = $this->http_post($url, $postfields);

		#check for problems:
		if (preg_match('/A more complex password is required/',$ret)) {
			$msg='A more complex password is required';
			print ($msg);
			return($msg);
		}
		if (preg_match('/Your password must not contain more than 3 consecutive identical characters/',$ret)) {
			$msg='Your password must not contain more than 3 consecutive identical characters';
			print ($msg);
			return($msg);
		}
		if (preg_match('/Your password must have at least one capital letter/',$ret)) {
			$msg='Your password must have at least one capital letter';
			print ($msg);
			return($msg);
		}
		if (preg_match('/Your password must have at least one lower case character/',$ret)) {
			$msg='Your password must have at least one lower case character';
			print ($msg);
			return($msg);
		}
		if (preg_match('/The email address you entered is already associated with an Apple ID/',$ret)) {
			$msg='The email address you entered is already associated with an Apple ID';
			print ($msg);
			return($msg);
		}
		#verify page load
		if (!preg_match('/Provide a Payment Method/',$ret)) {
			$msg='Provide a Payment Method page not loaded';
			print ($msg);
			return($msg);
		}
		#get mzPageUUID
		if (!preg_match('#\<input id\="pageUUID" class\="optional" name\="mzPageUUID" type\="hidden" value=\'([a-zA-Z0-9\/.-]+)\' \/\>#', $ret, $m)) {
			$msg='mzPageUUID not found';
			print ($msg);
			return($msg);
		}else {$mzPageUUID=$m[1];}
		#get Payment post url
		$m = array();
		if (!preg_match('#\<form name\="f_2_0_1_1_3_0_7_11_3" method\="post" action\="([a-zA-Z0-9\/.]+)"\>#', $ret, $m)) {
			$msg='link not found';
			print ($msg);
			return($msg);
		}else {$url=$urlbase . $m[1];}
		
		#post payment
		$postfields='2.0.1.1.3.0.7.11.3.1.0.5.11.1.0.5.5=US';
		$postfields.='&credit-card-type=';
		$postfields.='&sp=';
		$postfields.='&res=';
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.21.1.3.1.3.2.3=';
		$postfields.='&prefixName=0';
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.1.5.1=' . urlencode($fn);
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.1.5.3=' . urlencode($ln);
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.5.1=' . urlencode($address);
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.5.3=';
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.5.19.3=' . urlencode($city);
		$postfields.='&state='. $state;
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.5.19.9=' . $zip;
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.5.19.13.0=' . $phoneArea;
		$postfields.='&2.0.1.1.3.0.7.11.3.1.0.5.23.5.5.19.15=' . $phone;
		$postfields.='&2.0.1.1.3.0.7.11.3.9.1=Create+Apple+ID';
		$postfields.='&mzPageUUID=' . $mzPageUUID;
		$postfields.='&machineGUID=';
		$postfields.='&xAppleActionSignature=';
		$ret = $this->http_post($url, $postfields);
		#verify page load
		if (!preg_match('/Verify Your Apple ID/',$ret)) {
			$msg='Verify Your Apple ID page not loaded';
			print ($msg);
			return($msg);
		}
		return("ID Created - Verification email sent");
		
	}
	
	public function verifyAccount($verifyUrl,$email,$aidpw) {
		#create new curl instance instead of using $this becuase we don't want the itunes client junk
		$webRequest = new spCurl;
		$webRequest->set_UserAgent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; .NET CLR 1.0.3705;)");
		$reta = array();
		#call verify url
		$ret = $webRequest->http_get($verifyUrl,$reta);
		if (!preg_match('/To verify the email address/',$ret)) {
			$msg='Verify page not loaded';
			print ($msg);
			return($msg);
		}
		#set referer for next call
		curl_setopt($webRequest->curl, CURLOPT_REFERER, $reta['last_url']);
		#post
		$url = "https://id.apple.com/IDMSEmailVetting/authenticate.html";
		$postfields = 'openiForgotInNewWindow=false&fdDetails=240%7C900x1440x24x874x1440&myAppleIDURL=https%3A%2F%2Fappleid.apple.com%2Fcgi-bin%2FWebObjects%2FMyAppleId.woa%3Flocalang%3Den_US&imagePath=images%2Fvetting%2Fimages%2FUS-EN%2Fmyappleid_title.png&vetting=true&language=US-EN';
		$postfields .= '&appleId=' . urlencode($email);
		$postfields .= '&accountPassword=' . urlencode($aidpw);
		$ret = $webRequest->http_post($url, $postfields);
		if (!preg_match('/Email address verified/',$ret)) {
			$msg='Email not verified';
			print ($msg);
			return($msg);
		}
		return("Email verified");
	}

	/*
	#not working
	#upload data to create 12 and under accounts.
	public function uploadAccountEDU($aid,$aidpw,$data) {
		#create new curl instance instead of using $this becuase we don't want the itunes client junk
		$webRequest = new spCurl;
		$webRequest->set_UserAgent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; .NET CLR 1.0.3705;)");
		$baseurl = 'https://edu-appleid.apple.com';
		$loginbase = 'https://idmsa.apple.com';
		$url = $baseurl . '/admin/studentsearch';
		$reta = array();
		#call login url
		$ret = $webRequest->http_get($url,$reta);
		if (!preg_match('/Apple - Student - Admin/',$ret)) {
			$msg='Login Page not loaded';
			print ($msg);
			return($msg);
		}
		#get appIdKey
		if (!preg_match('#\<input type\="hidden" id\="appIdKey" name\="appIdKey"\n\t\tvalue=\"([a-zA-Z0-9]+)\" \/\>#', $ret, $m)) {
			$msg='appIdKey not found';
			print ($msg);
			return($msg);
		}else {$appIdKey=$m[1];}
		
		if (!preg_match('#\<form id\="command" name\="form1" action\="([a-zA-Z0-9\/;\=.]+)" method\="post"#', $ret, $m)) {
			$msg='link not found';
			print ($msg);
			return($msg);
		}else {$url=$loginbase . $m[1];}
		
		#set referer for next call
		//curl_setopt($webRequest->curl, CURLOPT_REFERER, $reta['last_url']);
		$post = 'openiForgotInNewWindow=false';
		//$post .= '&fdcBrowserData=' . urlencode('{"U":"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36","L":"en-US","Z":"GMT-04:00","V":"1.1","F":"TF1;016;;;;;;;;;;;;;;;;;;;;;;Mozilla;Netscape;5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_9_4%29%20AppleWebKit/537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome/37.0.2062.120%20Safari/537.36;20030107;undefined;true;;true;MacIntel;undefined;Mozilla/5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_9_4%29%20AppleWebKit/537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome/37.0.2062.120%20Safari/537.36;en-US;ISO-8859-1;idmsa.apple.com;undefined;undefined;undefined;undefined;true;true;1414435823329;-5;6/7/2005%209%3A33%3A44%20PM;1440;900;;15.0;7.7.3;;12.1.1;;6;300;240;10/27/2014%202%3A50%3A23%20PM;24;1440;874;0;22;;;;;Shockwave%20for%20Director%7CAdobe%20Shockwave%20for%20Director%20Netscape%20plug-in%2C%20version%2012.1.1;Shockwave%20Flash%7CShockwave%20Flash%2015.0%20r0;;;;QuickTime%20Plug-in%207.7.3%7CThe%20QuickTime%20Plugin%20allows%20you%20to%20view%20a%20wide%20variety%20of%20multimedia%20content%20in%20web%20pages.%20For%20more%20information%2C%20visit%20the%20%3CA%20HREF%3Dhttp%3A//www.apple.com/quicktime%3EQuickTime%3C/A%3E%20Web%20site.;;;RealPlayer%20Plugin.plugin%7CRealPlayer%20Plugin;;Silverlight%20Plug-In%7C5.1.30514.0;Flip4Mac%20Windows%20Media%20Plugin%7CThe%20Flip4Mac%20WMV%20Plugin%20allows%20you%20to%20view%20Windows%20Media%20content%20using%20QuickTime.;;;18;;;;;;;"}');
		$post .= '&appleid=' . urlencode($aid);
		$post .= '&accountPassword=' . urlencode($aidpw);
		$post .= '&appIdKey=' . $appIdKey;
		$post .= '&language=';
		$post .= '&path=' . urlencode('/admin/studentsearch');
		$post .= '&disable2SV=&Env=PROD&captchaType=&captchaToken=';
		#post login, get admin/studentsearch
		$ret = $webRequest->http_post($url, $post);
		if (!preg_match('/Apple - Student - Admin/',$ret)) {
			$msg='Login Page not loaded';
			print ($msg);
			return($msg);
		}
		#get token
		if (!preg_match('#\<input id\="token" name\="token" name\="token" value=\"([a-zA-Z0-9]+)\" type\="hidden"#', $ret, $m)) {
			$msg='token not found';
			print ($msg);
			return($msg);
		}else {$token=$m[1];}
		
		$url = $baseurl . '/admin/batchcreate';
		$post = array(
			'filedata' => $data,
			'filename' => 'appleID.csv',
			'token' => $token,
			'isFDSupported' => 'Y'
		);
		curl_setopt($webRequest->curl,CURLOPT_INFILESIZE,sizeof($data));
		$reta = array();
		#Post file
		$ret = $webRequest->http_post($url, $post, 'multipart/form-data',$reta);
		$json = json_decode($ret['body']);
		if ($json) {
			print_r($json);
		}else{
			$msg='data not uploaded';
			print ($msg);
			return($msg);
		}
		}
		*/

	#translate question string to int for edu form
	private function translateQuestionedu($q) {
		if (gettype($q) == "string") {
			switch($q) {
				case "What is the name of your favorite sports team?":
					$q = 147;
					break;
				case "What is your dream job?":
					$q = 136;
					break;
				case "What was the first name of your first boss?":
					$q = 143;
					break;
			}
		}
		return($q);
	}
	
	// set callback function
	function set_callback($function_name) {
		$this->pwcallback = $function_name;
	}
	
	// remove callback function
	function remove_callback() {
		$this->pwcallback = null;
	}

	public function createAccountEDU($url,$tpw,$q1,$a1,$q2,$a2,$q3,$a3,$phone) {
		/*
		* Use Verify URL to create EDU Apple ID
		* parameter:
		* $url - Verify URL
		* $tpw - Temporary Password for Verify URL
		* $aidpw - Desired Apple ID Password
		* $q1-3 - Security questions exactly as they appear on Register page
		* $a1-3 - Answers to secuirty questions
		* $phone - Phone Number
		*/
		#check that the call back is registered for password
		if ($this->pwcallback==null) {
			$this->errormsg='Callback not registered for password';
			print ($this->errormsg);
			return(false);
		}
		#create new curl instance instead of using $this becuase we don't want the itunes client junk
		$webRequest = new spCurl;
		$webRequest->set_UserAgent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; .NET CLR 1.0.3705;)");
		$reta = array();
		#call verify url
		$ret = $webRequest->http_get($url,$reta);
		if (!preg_match('/Apple - Student/',$ret)) {
			$this->errormsg='Login page not loaded';
			print ($this->errormsg);
			return(false);
		}
		#get default form values
		// Create DOM from string
		$htmldom = str_get_html($reta['body']);
		$formdata = array();
		foreach ($htmldom->find('input') as $element)
		if (array_key_exists('value',$element->getAllAttributes()))
			$formdata[$element->name] = $element->value;
		
		#build post
		unset($formdata['0']);
		print("Form Data returned from $url\n");
		print_r($formdata);
		$formdata['accountPassword']=$tpw;
		$post=$formdata;
		
		#set referer for next call
		curl_setopt($webRequest->curl, CURLOPT_REFERER, $reta['last_url']);
		$url = 'https://idmsa.apple.com/IDMSWebAuth/authenticate';
		
		$reta = array();
		#post temp pw, get Register page
		$ret = $webRequest->http_post($url, http_build_query($post), 'application/x-www-form-urlencoded', $reta);
		if (!preg_match('/Apple - Student/',$ret)) {
			$this->errormsg='Register page not loaded';
			print ($this->errormsg);
			return(false);
		}
		#get default form values
		// Create DOM from string
		$htmldom = str_get_html($reta['body']);
		
		#get apple id from page
		foreach ($htmldom->find('div[class=formrow]') as $element)
			foreach($element->children as $elementp)
				if (array_key_exists('for',$elementp->getAllAttributes()))
					if ($elementp->for == "appleid")
						$this->userinfo['email']=$elementp->nextSibling()->innertext();
		
		#build post array
		$formdata = array();
		foreach ($htmldom->find('input') as $element)
			if (array_key_exists('value',$element->getAllAttributes()))
				$formdata[$element->name] = $element->value;
		foreach ($htmldom->find('select') as $element)
			foreach($element->children as $elementp)
				if (array_key_exists('selected',$elementp->getAllAttributes()))
					$formdata[$element->name] = $elementp->value;
		print("Form Data returned from $url\n");
		print_r($formdata);
			
		#set referer for next call
		curl_setopt($webRequest->curl, CURLOPT_REFERER, $reta['last_url']);
			
		$url='https://edu-appleid.apple.com/parent/update';
		$post=array();
		$post['clientInfo'] = '';
		$post['name.firstName'] = $this->userinfo['firstName'] = $formdata['firstName'];
		$post['name.middleName'] = $this->userinfo['middleName'] = $formdata['middleName'];
		$post['name.lastName'] = $this->userinfo['lastName'] = $formdata['lastName'];
		$post['token'] = $formdata['token'];
		$post['birthDate.birthMonth'] = $this->userinfo['birthMonth'] = $formdata['birthMonth'];
		$post['birthDate.birthDay'] = $this->userinfo['birthDay'] = $formdata['birthDay'];
		$post['birthDate.birthYear'] = $this->userinfo['birthYear'] = $formdata['birthYear'];
		#get password form callback
		$aidpw = call_user_func($this->pwcallback,$this->userinfo);
		$post['password'] = $this->userinfo['password'] = $aidpw;
		$post['confirmPassword'] = $aidpw;
		$post['questionAnswer.questionId1'] = $this->translateQuestionedu($q1);
		$post['questionAnswer.answer1'] = $a1;
		$post['questionAnswer.questionId2'] = $this->translateQuestionedu($q2);
		$post['questionAnswer.answer2'] = $a2;
		$post['questionAnswer.questionId3'] = $this->translateQuestionedu($q3);
		$post['questionAnswer.answer3'] = $a3;
		$post['language'] = $formdata['language'];
		$post['placeholderVal'] = $formdata['placeholderVal'];
		$post['RescueEmail'] = $formdata['rescueEmail'];
		$post['confirmRescueEmail'] = $formdata['rescueEmail'];	
		$post['phoneNumber.phoneNumber'] = $phone;
		$post['termsAndConditionsAccepted'] = 'true';
		$post['parentalConsentAccepted'] = 'true';
		$post['_termsAndConditionsAccepted'] = 'on';
			
		echo http_build_query($post) . PHP_EOL;
		$reta = array();
		#post Register page, get confirmation
		$ret = $webRequest->http_post($url, http_build_query($post), 'application/x-www-form-urlencoded', $reta);
		// Create DOM from string
		$htmldom = str_get_html($reta['body']);
		#see if we have an error:
		foreach ($htmldom->find('span[class=input-msg red show]') as $element) {
			$this->errormsg=$element->innertext();
			print ($this->errormsg);
			return(false);
		}
		#verify page load
		if (!preg_match('/Apple ID Created/',$ret)) {
			$this->errormsg='Confirmation page not loaded';
			print ($this->errormsg);
			return(false);
		}
		return(true);
	}
}

# EOF
