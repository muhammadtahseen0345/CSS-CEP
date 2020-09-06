<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated', 'phpids' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ]   = 'DVWA Security' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'security';

$securityHtml = '';
if( isset( $_POST['seclev_submit'] ) ) {
	// Anti-CSRF
	checkToken( $_REQUEST[ 'user_token' ], $_SESSION[ 'session_token' ], 'security.php' );

	$securityLevel = '';
	switch( $_POST[ 'security' ] ) {
		case 'low':
			$securityLevel = 'low';
			break;
		case 'medium':
			$securityLevel = 'medium';
			break;
		case 'high':
			$securityLevel = 'high';
			break;
		default:
			$securityLevel = 'impossible';
			break;
	}

	dvwaSecurityLevelSet( $securityLevel );
	dvwaMessagePush( "Security level set to {$securityLevel}" );
	dvwaPageReload();
}

if( isset( $_GET['phpids'] ) ) {
	switch( $_GET[ 'phpids' ] ) {
		case 'on':
			dvwaPhpIdsEnabledSet( true );
			dvwaMessagePush( "PHPIDS is now enabled" );
			break;
		case 'off':
			dvwaPhpIdsEnabledSet( false );
			dvwaMessagePush( "PHPIDS is now disabled" );
			break;
	}

	dvwaPageReload();
}

$securityOptionsHtml = '';
$securityLevelHtml   = '';
foreach( array( 'low', 'medium', 'high', 'impossible' ) as $securityLevel ) {
	$selected = '';
	if( $securityLevel == dvwaSecurityLevelGet() ) {
		$selected = ' selected="selected"';
		$securityLevelHtml = "<p>Security level is currently: <em>$securityLevel</em>.<p>";
	}
	$securityOptionsHtml .= "<option value=\"{$securityLevel}\"{$selected}>" . ucfirst($securityLevel) . "</option>";
}

$phpIdsHtml = 'PHPIDS is currently: ';

// Able to write to the PHPIDS log file?
$WarningHtml = '';

if( dvwaPhpIdsIsEnabled() ) {
	$phpIdsHtml .= '<em>enabled</em>. [<a href="?phpids=off">Disable PHPIDS</a>]';

	# Only check if PHPIDS is enabled
	if( !is_writable( $PHPIDSPath ) ) {
		$WarningHtml .= "<div class=\"warning\"><em>Cannot write to the PHPIDS log file</em>: ${PHPIDSPath}</div>";
	}
}
else {
	$phpIdsHtml .= '<em>disabled</em>. [<a href="?phpids=on">Enable PHPIDS</a>]';
}

// Anti-CSRF
generateSessionToken();

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>DVWA Security <img src=\"" . DVWA_WEB_PAGE_TO_ROOT . "dvwa/images/lock.png\" /></h1>
	<br />

	<h2>Security Level</h2>

	{$securityHtml}

	<form action=\"#\" method=\"POST\">
		{$securityLevelHtml}
		<p>You can set the security level to low, medium, high or impossible. The security level changes the vulnerability level of this web app</p>
		
		<select name=\"security\">
			{$securityOptionsHtml}
		</select>
		<input type=\"submit\" value=\"Submit\" name=\"seclev_submit\">
		" . tokenField() . "
	</form>

</div>";

dvwaHtmlEcho( $page );

?>
