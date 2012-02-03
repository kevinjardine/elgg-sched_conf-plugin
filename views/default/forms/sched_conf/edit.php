<?php
/**
 * Edit sched_conf form
 *
 */

$conf = get_entity($vars['guid']);
$vars['entity'] = $conf;
$fd = $vars['form_data'];

if(isset($fd['start_time_h'])){
	$fd['start_time'] = 60*($fd['start_time_h']-1)+($fd['start_time_m']-1);
}

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/sched_conf/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete elgg-state-disabled float-alt'
	));
}

$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button . $delete_link;

$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'id' => 'sched-conf-title',
	'value' => $fd['title']
));

$description_label = elgg_echo('sched_conf:description');
$description_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'id' => 'sched-conf-description',
	'value' => $fd['description']
));

$immediate_label = elgg_echo('sched_conf:immediate_label');
$immediate_input = elgg_view('input/checkboxes', array(
	'name' => 'immediate',
	'id' => 'sched-conf-immediate',
	'value' => $fd['immediate'],
	'options' => array(
		'1' => elgg_echo('sched_conf:immediate_yes'),
	),
));

$start_time_label = elgg_echo("event_calendar:start_time_label");
$start_time_input = elgg_view("input/timepicker",array(
	'name' => 'start_time',
	'id' => 'sched-conf-start-time',
	'value'=>$fd['start_time'],
));

$start_date_label = elgg_echo("event_calendar:start_date_label");
$start_date_input = elgg_view("event_calendar/input/date_local",array(
	'timestamp'=>TRUE, 
	'autocomplete'=>'off',
	'name' => 'start_date',
	'value'=>$fd['start_date'],
));

/*$application_label = elgg_echo('sched_conf:application_label');
$application_input = elgg_view('input/dropdown', array(
	'name' => 'application',
	'id' => 'sched-conf-application',
	'options_values' => array(
		'open_meetings'=>elgg_echo('sched_conf:open_meetings'),
		'bbb'=>elgg_echo('sched_conf:big_blue_button'),
		'unity' => elgg_echo('sched_conf:unity')),
	'value' => $fd['application']
));
*/

/*$application_code_label = elgg_echo('sched_conf:application_code_label');
$application_code_input = elgg_view('input/text', array(
	'name' => 'application_code',
	'id' => 'sched-conf-application-code',
	'value' => $fd['application_code']
));*/

$tags_label = elgg_echo('tags');
$tags_input = elgg_view('input/tags', array(
	'name' => 'tags',
	'id' => 'sched-conf-tags',
	'value' => $fd['tags']
));

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'sched-conf-access-id',
	'value' => $fd['access_id']
));

$event_calendar_personal_manage = elgg_get_plugin_setting('personal_manage', 'event_calendar');
if ($event_calendar_personal_manage == 'by_event') {
	$personal_manage_options = array(
		elgg_echo('event_calendar:personal_manage:open') => 'open',
		elgg_echo('event_calendar:personal_manage:closed') => 'closed',
		elgg_echo('event_calendar:personal_manage:private') => 'private',
	);
	$personal_manage_label = elgg_echo('event_calendar:personal_manage:label');
	$personal_manage_input = elgg_view("input/radio",array('name' => 'personal_manage','value'=>$fd['personal_manage'],'options'=>$personal_manage_options));
	$personal_manage_description = '<p class="description">'.elgg_echo('event_calendar:personal_manage:description').'</p>';
}

// hidden inputs
$group_guid_input = elgg_view('input/hidden', array('name' => 'group_guid', 'value' => $vars['group_guid']));
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $fd['guid']));
// hard coded to BBB for now
$application_input = elgg_view('input/hidden', array('name' => 'application', 'value' => 'bbb'));

echo <<<__HTML
<div>
	<label for="sched-conf-title">$title_label</label>
	$title_input
</div>

<label for="sched-conf-description">$description_label</label>
$description_input
<br />

<div>
	<label for="sched-conf-start-time">$immediate_label</label>
	$immediate_input
</div>

<div>
	<label for="sched-conf-start-time">$start_time_label</label>
	$start_time_input
</div>

<div>
	<label for="sched-conf-start-date">$start_date_label</label>
	$start_date_input
</div>
__HTML;

if ($event_calendar_personal_manage == 'by_event') {
echo <<<__HTML2
<div>
	<label for="sched-conf-personal-manage">$personal_manage_label</label>
	$personal_manage_input
	$personal_manage_description
</div>
__HTML2;
}

echo <<<__HTML3
<div>
	<label for="sched-conf-tags">$tags_label</label>
	$tags_input
</div>

<div>
	<label for="sched-conf-access-id">$access_label</label>
	$access_input
</div>

<div class="elgg-foot">

	$guid_input
	$group_guid_input
	$application_input

	$action_buttons
</div>

__HTML3;
