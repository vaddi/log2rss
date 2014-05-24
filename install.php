<?php

// Sanity check, install should only be checked from index.php
defined('PATH') or exit('Install tests must be loaded from within index.php!');

if (version_compare(PHP_VERSION, '5.3', '<'))
{
	// Clear out the cache to prevent errors. This typically happens on Windows/FastCGI.
	clearstatcache();
}
else
{
	// Clearing the realpath() cache is only possible PHP 5.3+
	clearstatcache(TRUE);
}

function urlExists($url=NULL)  
{  
    if($url == NULL) return false;  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $data = curl_exec($ch);  
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch);  
    if($httpcode>=200 && $httpcode<300){  
        return true;  
    } else {  
        return false;  
    }  
}

?>
<!DOCTYPE html>

<html lang="<?= APPLANG ?>">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= APPNAME ?> Installation</title>

	<style type="text/css">
	body { width: 42em; margin: 0 auto; font-family: sans-serif; background: #fff; font-size: 1em; }
	h1 { letter-spacing: -0.04em; }
	h1 + p { margin: 0 0 2em; color: #333; font-size: 90%; font-style: italic; }
	code { font-family: monaco, monospace; }
	table { border-collapse: collapse; width: 100%; }
		table th,
		table td { padding: 0.4em; text-align: left; vertical-align: top; }
		table th { width: 12em; font-weight: normal; }
		table tr:nth-child(odd) { background: #eee; }
		table td.pass { color: #191; }
		table td.fail { color: #911; }
	@-webkit-keyframes reset { 0% { opacity: 0; } 100% { opacity: 0; } }
	@-webkit-keyframes fade-in { 0% { opacity: 0; } 60% { opacity: 0; } 100% { opacity: 1; } }
	@-moz-keyframes reset { 0% { opacity: 0; } 100% { opacity: 0; } }
	@-moz-keyframes fade-in { 0% { opacity: 0; } 60% { opacity: 0; } 100% { opacity: 1; } }
	@keyframes reset { 0% { opacity: 0; } 100% { opacity: 0; } }
	@keyframes fade-in { 0% { opacity: 0; } 60% { opacity: 0; } 100% { opacity: 1; } }
	.fade-in {
		-webkit-animation-name: reset, fade-in;
		-webkit-animation-duration: 1s;
		-webkit-animation-timing-function: ease-in;
		-webkit-animation-iteration-count: 1;
		-moz-animation-name: reset, fade-in;
		-moz-animation-duration: 1s;
		-moz-animation-timing-function: ease-in;
		-moz-animation-iteration-count: 1;    
		animation-name: reset, fade-in;
		animation-duration: 1s;
		animation-timing-function: ease-in;
		animation-iteration-count: 1;
	}
	#mainh {text-align:center;}
	#results { padding: 0.8em; color: #fff; font-size: 1.4em; }
	#results.pass { background: #191; }
	#results.fail { background: #911; }
	</style>

</head>
<body>

	<h1 id="mainh">Environment Tests</h1>

	<p>
		The following tests have been run to determine if <a href=""><?= APPNAME ?></a> will work in your environment.
		If any of the tests have failed, consult the <a href="">documentation</a>
		for more information on how to correct the problem.
	</p>

	<?php $failed = FALSE ?>

	<table cellspacing="0">
		<tr>
			<th>PHP Version</th>
			<?php if (version_compare(PHP_VERSION, '5.3.3', '>=')): ?>
				<td class="pass"><?php echo PHP_VERSION ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><?= APPNAME ?> requires PHP 5.3.3 or newer, this version is <?php echo PHP_VERSION ?>.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>URL validation</th>
			<?php if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', URL)): ?>
				<?php if (urlExists( URL . '/install.php')): ?>
				  <td class="pass"><?php echo URL ?></td>
				<?php else: $failed = TRUE ?>
					<td class="fail">The configured system <code>url</code> <br><b><?php echo URL ?></b><br> couldn't get resolved by curl.</td>
				<?php endif ?>
			<?php else: $failed = TRUE ?>
				<td class="fail">The configured system <code>url</code> <br><b><?php echo URL ?></b><br> does not match to valid URL encoding.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Application Directory</th>
			<?php if (is_file('install.php')): ?>
				<td class="pass"><?php echo PATH ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">The configured <code>system</code> directory <br><b><?php echo PATH ?></b><br> does not exist or does not contain required files.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Log File</th>
			<?php if (is_file( 'tmp.log')): ?>
				<td class="pass"><?php echo 'tmp.log' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">The <code><?php echo 'tmp.log' ?></code> doesn't exist!</td>
			<?php endif ?>
		</tr>
		
		<tr>
			<th>PHP URI Determination</th>
			<?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])): ?>
				<td class="pass">Pass</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Neither <code>$_SERVER['REQUEST_URI']</code>, <code>$_SERVER['PHP_SELF']</code>, or <code>$_SERVER['PATH_INFO']</code> is available.</td>
			<?php endif ?>
		</tr>
	</table>

	<?php if ($failed === TRUE): ?>
		<p id="results" class="fail fade-in">✘ <?= APPNAME ?> will not work correctly with your environment!</p>
	<?php else: ?>
		<?php $realpath = str_replace("index.php/", "", $_SERVER['REQUEST_URI']); ?>
		<?php $abs_path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) ?>
		<p id="results" class="pass fade-in">✔ Your environment passed all requirements.</p>
		<p>Remove or rename the <code>install.php</code> file.<br />
		<br />
		Edit the file <b>log2rss</b>: <br /><br />
		
		# for Apache error grep<br />
		#<br />
		APPPATH="<?= $abs_path ?>" <br />
		LOGFILE="error.log" <br />
		LOGGREP="error|crit|notice" # error|crit|notice<br />

		<br />
		<b>OR</b>
		<br />
		<br />
		
		# for Fail2ban grep banned<br />
		#<br />
		APPPATH="<?= $abs_path ?>" <br />
		LOGFILE="fail2ban.log" <br />
		LOGGREP="Ban" <br />

		<br />
		<br />
		Then move <b>log2rss</b> to you cron (e.g. /etc/cron.hourly) folder.<br /><br />
		
		</p>
		––––––––––––––––––––––––––––––––––––– HINT –––––––––––––––––––––––––––––––––––––<br /><br />
		<p>If you want to check minutly, you'll have to settup a new cronjob in your crontab by adding the following line:<br /><br />
		* * * * *     root    cd / && run-parts --report /etc/cron.minutly<br /><br />
		Create the neccessary Folder <b>sudo mkdir /etc/cron.minutly</b> and move <b>log2rss</b> into it.
		</p>
	<?php endif ?>
	
	<br />
	
</body>
</html>
