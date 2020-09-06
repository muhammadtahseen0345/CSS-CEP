<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated', 'phpids' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: Command Injection' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'exec';
//$page[ 'help_button' ]   = 'exec';
//$page[ 'source_button' ] = 'exec';

dvwaDatabaseConnect();

$vulnerabilityFile = '';
switch( $_COOKIE[ 'security' ] ) {
	case 'low':
		$vulnerabilityFile = 'low.php';
		break;
	case 'medium':
		$vulnerabilityFile = 'medium.php';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	default:
		$vulnerabilityFile = 'impossible.php';
		break;
}

require_once DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/exec/source/{$vulnerabilityFile}";

$page[ 'body' ] .= "
<div >
	<h1>Command Injection</h1>

	<div class=\"vulnerable_code_area\">
		<h2>Ping a device</h2>

		<form name=\"ping\" action=\"#\" method=\"post\">
			<p>
				Enter an IP address:
				<input type=\"text\" name=\"ip\" size=\"30\">
				<input type=\"submit\" name=\"Submit\" value=\"Submit\">
			</p>\n";

if( $vulnerabilityFile == 'impossible.php' )
	$page[ 'body' ] .= "			" . tokenField();

$page[ 'body' ] .= "
		</form>
		{$html}
	</div>

	
</div>\n";

dvwaHtmlEcho( $page );

?>
