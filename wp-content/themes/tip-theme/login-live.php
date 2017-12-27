<?php /* Template Name: Login Members (Clever-IDM-LIVE) */ 
class CleverIDM
{
	private static $idm_username = 'virtual-ui';
	// private static $idm_password = 'SzSGYf3uBeCKu7Q6'; // dev
	private static $idm_password = 'zhe3XXbZmN3D45Jk'; // production
	
	// dev
	/*
	private static $auth_url = 'https://dev.iam.telecominfraproject.com/sso/json/tip/authenticate';
	private static $validate_url = 'https://dev.iam.telecominfraproject.com/sso/json/tip/sessions/[TOKEN]?_action=validate';
	private static $profile_url = 'https://dev.iam.telecominfraproject.com:8443/openidm/IUX/profile?_action=retrieveProfile';
	private static $reset_password_send_url = 'https://dev.iam.telecominfraproject.com:8443/openidm/IUX/anonymous/forgotpassword?_action=requestPasswordReset';
	private static $validate_password_url = 'https://dev.iam.telecominfraproject.com:8443/openidm/IUX/password/validatePassword?_action=validatePassword';
	private static $reset_password_url = 'https://dev.iam.telecominfraproject.com:8443/openidm/IUX/anonymous/forgotpassword?_action=resetPassword';
	*/
	
	// prod
	public static $auth_url = 'https://prod.iam.telecominfraproject.com/sso/json/tip/authenticate';
	private static $validate_url = 'https://prod.iam.telecominfraproject.com/sso/json/tip/sessions/[TOKEN]?_action=validate';
	public static $profile_url = 'https://prod.iam.telecominfraproject.com:8443/openidm/IUX/profile?_action=retrieveProfile';
	private static $reset_password_send_url = 'https://prod.iam.telecominfraproject.com:8443/openidm/IUX/anonymous/forgotpassword?_action=requestPasswordReset';
	private static $validate_password_url = 'https://prod.iam.telecominfraproject.com:8443/openidm/IUX/password/validatePassword?_action=validatePassword';
	private static $reset_password_url = 'https://prod.iam.telecominfraproject.com:8443/openidm/IUX/anonymous/forgotpassword?_action=resetPassword';	
	
	private static $sso_domain = '.telecominfraproject.com';

	public static function curl_set_options(&$ch, $url, $method = 'GET', $data = '', $headers = array(), $curl_options = array())
	{
 		$return_curl_header = false;

 		if($method == 'HEAD')
 			$return_curl_header = true;

		$default_curl_options = array(
			CURLOPT_URL			   => $url,
			CURLOPT_CUSTOMREQUEST  => $method ,        //set request type post or get
			CURLOPT_COOKIESESSION  => false,		// set cookies
			CURLOPT_POST           => false,        //set to GET
			CURLOPT_RETURNTRANSFER => true,     // tell CURL to return the result rather than to load it to the browser
			CURLOPT_HEADER         => $return_curl_header,    // return HTTP headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			CURLOPT_AUTOREFERER    => false,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
			CURLOPT_TIMEOUT        => 60,      // timeout on response
			CURLOPT_MAXREDIRS      => 5,       // stop after 5 redirects
			CURLOPT_FAILONERROR	   => true,		// fail verbosely if the HTTP code returned is greater than or equal to 400
			CURLOPT_SSL_VERIFYPEER => false,	// verify ssl
			CURLOPT_SSL_VERIFYHOST => false,	// verify ssl host
			CURLOPT_VERBOSE		   => false,
			CURLOPT_FAILONERROR	   => false,
			CURLOPT_FORBID_REUSE	=> true,
			CURLOPT_FRESH_CONNECT	=> true,
		);
		
		// set default options
		curl_setopt_array($ch, $default_curl_options);
		
		// set custom options
		curl_setopt_array($ch, $curl_options);

		// POST request
		if(($method == 'POST') && ($data != ''))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		
		// set headers
		if(sizeof($headers) > 0)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);		
		}
	}

	// create a single curl request and return response
	public static function curl($url, $data = '', $method = 'POST', $headers = array(), $curl_options = array())
	{
		$ch = curl_init();

		// set curl options
		self::curl_set_options($ch, $url, $method, $data, $headers, $curl_options);		

		$response = curl_exec($ch);
			
		if(isset($_GET['debug']))
		{
			if($response === false)
			{
				$response = curl_error($ch);
			}	
			else
			{
				$response = 'OK';
			}
		}

		curl_close($ch);
		
		return $response;
	}
	
	private function set_login($token, $username)
	{
		session_start();
		$_SESSION['clever_token'] = $token;
		$_SESSION['clever_username'] = $username;
				
		self::set_sso_cookie($token);
	}
	
	private function set_sso_cookie($token)
	{
		//The value of this token should be persisted in a cookie named virtualSSO and set to include all subdomains (i.e. .telecominfraproject.com)		
		setrawcookie("virtualSSO",$token,time()+3600*24,"/",self::$sso_domain);	
	}

	public static function logout()
	{
		session_start();
		$_SESSION['clever_token'] = '';
		$_SESSION['clever_username'] = '';	
		setcookie("virtualSSO",'',time()-3600,"/",self::$sso_domain);
	}	
	
	private function get_token()
	{
		session_start();
		return trim($_COOKIE['virtualSSO']);
	}
	
	private function get_username()
	{
		session_start();
		return trim($_SESSION['clever_username']);
	}		
	
	public static function has_token()
	{
		$token = self::get_token();
	
		return !empty($token);
	}	
		
	public static function login($username, $password)
	{
		$username = trim($username);
		$password = trim($password);
		
		if(($username == '') || ($password == ''))
		{
			return false;
		}
	
		$headers = array(
			'Content-Type: application/json',
		);

		// authorize request
		$response = self::curl(self::$auth_url, '', 'POST', $headers);
		$auth_response = json_decode($response); 

		//	debug
		//if($_SERVER['HTTP_X_FORWARDED_FOR'] == '81.149.134.175')
		//{
			//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/uploads/clever-idm-logs/clever-idm.log', time().':'.self::$auth_url.':'.$response."\n\n", FILE_APPEND | LOCK_EX);
		//}

		// set username and password
		$auth_response->callbacks[0]->input[0]->value = $username;
		$auth_response->callbacks[1]->input[0]->value = $password;

		$data = json_encode($auth_response);

		$response = self::curl(self::$auth_url, $data, 'POST', $headers);
		$auth_response = json_decode($response); 
		
		//	debug
		//if($_SERVER['HTTP_X_FORWARDED_FOR'] == '81.149.134.175')
		//{
		//	print_r(json_encode($auth_response));
			//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/uploads/clever-idm-logs/clever-idm.log', time().':'.self::$auth_url.':'.$data.':'.$response."\n\n", FILE_APPEND | LOCK_EX);
		//	exit;
		//}
			
		if(isset($auth_response->tokenId))
		{	
			// set session token
			self::set_login($auth_response->tokenId, $username);
			return true;
		}
		
		return false;
	}
	
	public static function revalidate()
	{
		$token = self::get_token();
	
		if($token == '')
			return false;
			
		$headers = array(
			'Content-Type: application/json',
		);
		
		$auth_url = str_replace('[TOKEN]', $token, self::$validate_url);

		// authorize request
		$auth_response = json_decode(self::curl($auth_url, '', 'POST', $headers)); 		

		if(isset($auth_response->valid))
		{	
			if($auth_response->valid)
			{
				return true;
			}
		}
		
		return false;
	}
	
	public static function get_sso_icons()
	{
		$headers = array(
			'Content-Type: application/json',
			'X-OpenIDM-Username: '.self::$idm_username,
			'X-OpenIDM-Password: '.self::$idm_password,		
			'X-OpenIDM-NoSession: true',	
		);
		
		$data = json_encode(
			array(
				'userName' => self::get_username(),
			)
		);

		$auth_response = json_decode(CleverIDM::curl(self::$profile_url, $data, 'POST', $headers)); 

		if(isset($auth_response->userApplications))
		{
			return $auth_response->userApplications;
		}
		
		return false;
	}
	
	public static function reset_password_send($username)
	{
		if($username == '')
			return false;	
	
		$headers = array(
			'Content-Type: application/json',
			'X-OpenIDM-Username: anonymous',
			'X-OpenIDM-Password: anonymous',	
			'X-OpenIDM-NoSession: true',	
		);
		
		$data = json_encode(
			array(
				'userName' => $username,
			)
		);	
		
		$response = json_decode(CleverIDM::curl(self::$reset_password_send_url, $data, 'POST', $headers)); 

		if(isset($response->code))
		{	
			if($response->code == '200')
			{
				return true;
			}
		}
		
		return false;
	}
	
	public static function reset_validate_password($password)
	{
		$headers = array(
			'Content-Type: application/json',
			'X-OpenIDM-Username: anonymous',
			'X-OpenIDM-Password: anonymous',	
			'X-OpenIDM-NoSession: true',	
		);
		
		$data = json_encode(
			array(
				'password' => $password,
			)
		);	
		
		$response = json_decode(CleverIDM::curl(self::$validate_password_url, $data, 'POST', $headers)); 
		
		if(isset($response->result))
		{	
			if($response->result == '1')
			{
				return true;
			}
			else
			{
				return $response->failedPolicyRequirements;
			}
		}
		
		/*
			{"result":false,"failedPolicyRequirements":[ 
			{"policyRequirements":[{"params":{"minLength":8},"policyRequirement":"MIN_LENGTH"}],"property":"password"}, {"policyRequirements":[{"params":{"numCaps":1},"policyRequirement":"AT_LEAST_X_CAPITAL_LETTERS"}],"property":"password"}, {"policyRequirements":[{"params":{"numNums":1},"policyRequirement":"AT_LEAST_X_NUMBERS"}],"property":"password"}, {"policyRequirements":[{"params":{"numSpecials":1},"policyRequirement":"AT_LEAST_X_SPECIALS"}],"property":"password"}]}		
		*/

		return false;
	}	
	
	public static function reset_password($username, $password, $token)
	{
		if($username == '')
			return false;	
			
		if($token == '')
			return false;	
	
		if($password == '')
			return false;		
	
		$headers = array(
			'Content-Type: application/json',
			'X-OpenIDM-Username: anonymous',
			'X-OpenIDM-Password: anonymous',	
			'X-OpenIDM-NoSession: true',	
		);
		
		$data = json_encode(
			array(
				'userName' => $username,
				'resetToken' => $token,
				'password' => $password,
			)
		);	
		
		$response = json_decode(CleverIDM::curl(self::$reset_password_url, $data, 'POST', $headers)); 

		if(isset($response->code))
		{	
			return false;
		}
		
		return true;
	}	
}

$clever_action = '';
$x_username = '';
$x_token = '';
$x_goto = '';

function clever_idm()
{
	global $clever_action, $x_username, $x_token, $x_goto;
	
	$action = $_POST['x_action'];
	
	if(isset($_GET['debug']))
	{
		$headers = array(
			'Content-Type: application/json',
			'X-OpenIDM-Username: anonymous',
			'X-OpenIDM-Password: anonymous',	
			'X-OpenIDM-NoSession: true',	
		);	
	
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';			
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$profile_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$profile_url, '', 'POST', $headers);	
		echo '<hr/>';		
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';	
		
		echo CleverIDM::$auth_url;
		echo '<br/>';
		echo CleverIDM::curl(CleverIDM::$auth_url, '', 'POST', $headers);	
		echo '<hr/>';			
		
		exit;
	}
	
	// try query string
	if($action == '')
	{
		// check for forgot password reset
		if(isset($_GET['username']) && isset($_GET['key']))
		{
			$action = 'forgot_password_reset';
		}
		else
		{
			$action = $_GET['x_action'];

			if(($action == '') || ($action == 'login'))
			{
				// check session token 
				$has_token = CleverIDM::has_token();

				if($has_token)
					$action = 'revalidate';	
			}
		}
	}
	
	// goto
	if(isset($_GET['goto']))
		$x_goto = $_GET['goto'];
		
	if(isset($_POST['x_goto']))
		$x_goto = $_POST['x_goto'];

	switch($action)
	{
		case 'revalidate':
			
			$login_result = CleverIDM::revalidate();
			
			if($login_result)
				$clever_action = 'dashboard';
			else
				$clever_action = 'login';			
			
			break;
	
		case 'logout':
		
			CleverIDM::logout();
			$clever_action = 'login';
		
			break;
		
		case 'login':
		
			$result = CleverIDM::login($_POST['x_username'], $_POST['x_password']);
			
			if($result)
				$clever_action = 'dashboard';
			else
				$clever_action = 'login_error';

			break;
			
		case 'forgot_password_send':
		
			$result = CleverIDM::reset_password_send($_POST['x_username']);
			
			if($result)
				$clever_action = 'forgot_password_sent';
			else
				$clever_action = 'forgot_password_error';			
	
			break;
		
		case 'forgotpassword':
			
			$clever_action = 'forgot_password';
			
			break;
			
		case 'forgot_password_reset':
			
			$clever_action = 'forgot_password_reset';
			
			$x_username = $_GET['username'];
			$x_token = $_GET['key'];			
			
			break;
			
		case 'forgot_password_reset_set':
		
			$password = $_POST['x_password'];
			$x_username = $_POST['x_username'];
			$x_token = $_POST['x_token'];			
			
			// check password is valid
			$result = CleverIDM::reset_validate_password($password);
			
			if($result === true)
			{				
				$result = CleverIDM::reset_password($x_username, $password, $x_token);
			
				if($result === true)
					$clever_action = 'forgot_password_reset_success';
				else
					$clever_action = 'forgot_password_reset_token';
			}
			else
				$clever_action = 'forgot_password_reset_error';			
		
			break;
			
		default:
			
			$clever_action = 'login_page';
			
			break;
	}
	
	// dashboard redirect?
	if(($x_goto != '') && ($clever_action == 'dashboard'))
	{
		header('Location: '.$x_goto);
		exit;
	}
}

clever_idm();        		

get_header();
?>
        <!-- members page -->
		<div id="page">
			<div class="section-container">
        		<div class="component--page--main-content">
<?php
switch($clever_action)
{
	case 'dashboard':
	
		$profile_icons = CleverIDM::get_sso_icons();
?>
		<h1 class="h1 text-align-center component--page--page-title">Member Login</h1>
		<div class="component--page--main-content--body component--wysiwyg">
			<p style="text-align: center;">Please select an option below:</p>
			<p>
<?php
		foreach($profile_icons as $profile_icon)
		{
			// must be approved
			if($profile_icon->status != 'approved')
				continue;

			echo '<a class="component--button--large" href="'.$profile_icon->url.'" target="_blank">'.$profile_icon->displayName.'</a><br />';
		}
		
		echo '<a class="component--button--large" href="?x_action=logout">Log Out</a><br />';
?>			
		</p>
		</div> 
<?php
		break;

	case 'forgot_password_sent':
?>
		<div class="component--page--main-content">
		<h1 class="h1 text-align-center component--page--page-title">Forgot Password</h1>
		<div class="component--page--main-content--body component--wysiwyg">			
			<p>Thank you, we have sent an email to your account with details to reset your password.
			<br/><br/>
			Click <a href="?action=login" style="text-decoration:underline;">here</a> to login.</p>
		</div>
<?php	
		break;
	
	case 'forgot_password_error':
	case 'forgot_password':		
?>
	<div class="component--page--main-content">
		<h1 class="h1 text-align-center component--page--page-title">Forgot Password</h1>
		<div class="component--page--main-content--body component--wysiwyg">
			<form method="POST" id="x_login_form" action="?x_action=forgot_password_send">
				<?
				if($clever_action == 'forgot_password_error')
				{
					echo '<p style="color:red; font-weight:bold;">Sorry, we cannot find an account with those details.</p>';
				}
				?>			
				<p>Please enter your username and click submit and we will send an email with details on how to reset your password.</p>			
				<div class="row">
					<div>Username:</div>
					<div>
						<input name="x_username" type="text" required="true" value="">
					</div>
				</div>
				<div class="row">
					<div></div>
					<div>
						<input type="hidden" name="x_action" value="forgot_password_send">
						<input id="submit" type="submit" value="Submit">
					</div>
				</div>			
				<div class="row">
					<div class="forgot-pw">
						<a href="?">Login Here</a>
					</div>
				</div>
     			<div class="row">
					<p id="label1"></p>
					<p id="reValue1"></p>

					<p id="label2"></p>
					<p id="reValue2"></p>
				</div>					
			</form>	
		</div>
<?php		

		break;

	case 'forgot_password_reset_error':
	case 'forgot_password_reset_token':
	case 'forgot_password_reset':		
?>
	<div class="component--page--main-content">
		<h1 class="h1 text-align-center component--page--page-title">Forgot Password: Reset</h1>
		<div class="component--page--main-content--body component--wysiwyg">
			<form method="POST" id="x_login_form" action="?x_action=forgot_password_reset_set">	
				<?
				if($clever_action == 'forgot_password_reset_error')
				{
					echo '<p style="color:red; font-weight:bold;">Sorry, there was an error with your password.<br/>Please make sure your password meets the requirements below and you enter the confirmation password correctly.</p>';
				}
				
				if($clever_action == 'forgot_password_reset_token')
				{
					echo '<p style="color:red; font-weight:bold;">Sorry, there was an error resetting your password.<br/>Please try the forgot password page again <a href="?x_action=forgotpassword" style="text-decoration:underline;">here</a>.</p>';
				}				
				
				if($clever_action == 'forgot_password_reset_success')
				{
					echo '<p style="color:#008000; font-weight:bold;">Thank you, we have set your password.<br/>Please login with your new password.</p>';
				}				
				?>					
				<p>Please enter your your new password below.</p>			
				<p>The password requirements are: <br/>&bull; Minimum of eight characters.<br/>&bull; At least one upper case character.<br/>&bull; At least one number.<br/>&bull; At least one special character: -!@#$%^&*{}[]()</p>
				<div class="row">
					<div>New Password:</div>
					<div>
						<input name="x_password" type="password" required="true" value="">
					</div>
				</div>
				<div class="row">
					<div>Confirm New Password:</div>
					<div>
						<input name="x_confirm_password" type="password" required="true" value="">
					</div>
				</div>				
				<div class="row">
					<div></div>
					<div>
						<input type="hidden" name="x_action" value="forgot_password_reset_set">
						<input type="hidden" name="x_username" value="<?php echo htmlentities($x_username,ENT_QUOTES); ?>">
						<input type="hidden" name="x_token" value="<?php echo htmlentities($x_token,ENT_QUOTES); ?>">
						<input id="submit" type="submit" value="Submit">
					</div>
				</div>	
     			<div class="row">
					<p id="label1"></p>
					<p id="reValue1"></p>

					<p id="label2"></p>
					<p id="reValue2"></p>
				</div>					
			</form>	
		</div>
<?php		

		break;		

	default:

?>
	<div class="component--page--main-content">
		<h1 class="h1 text-align-center component--page--page-title">Login</h1>
		<div class="component--page--main-content--body component--wysiwyg">
			<div>
				<div style="width:45%; margin:0; float:left; min-width:300px;">
					<form method="POST" id="x_login_form" action="?x_action=login">
						<?
						if($clever_action == 'login_error')
						{
							echo '<p style="color:red; font-weight:bold;">Sorry, the login details you entered were invalid.</p>';
						}

						if($clever_action == 'forgot_password_reset_success')
						{
							echo '<p style="color:#008000; font-weight:bold;">Thank you, we have set your password.<br/>Please login with your new password.</p>';
						}				
						?>
						<div class="row">
							<div>Username:</div>
							<div>
								<input name="x_username" type="text" required="true" value="<?php echo htmlentities($_POST['x_username'],ENT_QUOTES); ?>" placeholder="firstname.lastname@telecominfraproject.com">
							</div>
						</div>
						<div class="row">
							<div>Password:</div>
							<div>
								<input name="x_password" type="password" required="true" value="">
							</div>
						</div>
						<div class="row">
							<div></div>
							<div>
								<input type="hidden" name="x_action" value="login">
								<input type="hidden" name="x_goto" value="<?php echo htmlentities($x_goto,ENT_QUOTES); ?>">
								<input id="submit" type="submit" value="Log In" style="width:100%;">
							</div>
						</div>			
						<div class="row">
							<div class="forgot-pw">
								<a href="?x_action=forgotpassword">Forgot password?</a>
							</div>
						</div>
						<div class="row">
							<p id="label1"></p>
							<p id="reValue1"></p>

							<p id="label2"></p>
							<p id="reValue2"></p>
						</div>					
					</form>	
				</div>
				<div class="video-login-vm" >
					<video poster="https://telecominfraproject.com/wp-content/uploads/workplace-video-placeholder.png" controls>
  <source src="https://brandfolder-res.cloudinary.com/video/upload/v1498571319/Workplace%20User%20Buzz%20Video.mp4.ogv">
       <source src="https://brandfolder-res.cloudinary.com/video/upload/v1498571319/Workplace%20User%20Buzz%20Video.mp4.webm">
       <source src="https://brandfolder-res.cloudinary.com/video/upload/v1498571319/Workplace%20User%20Buzz%20Video.mp4.mp4">
  Your browser does not support the video tag.
</video>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
<?php		
		break;
}
?>        		                           
        		</div>
    		</div>
        </div>
        <!-- end members page -->
<?php
get_footer();
?>