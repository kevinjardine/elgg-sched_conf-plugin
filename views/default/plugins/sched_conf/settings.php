<?php
$bbb_server_url_string = elgg_echo('sched_conf:bbb_server_url');
$bbb_server_url_view = elgg_view('input/text', array(
	'name' => 'params[bbb_server_url]',
	'value' => $vars['entity']->bbb_server_url,
	'class' => 'text_input',
));

echo "<div><label>$bbb_server_url_string</label><br /> $bbb_server_url_view</div>";

$bbb_security_salt_string = elgg_echo('sched_conf:bbb_security_salt');
$bbb_security_salt_view = elgg_view('input/text', array(
	'name' => 'params[bbb_security_salt]',
	'value' => $vars['entity']->bbb_security_salt,
	'class' => 'text_input',
));

echo "<div><label>$bbb_security_salt_string</label><br /> $bbb_security_salt_view</div>";
