<?php
// A non-admin / non-event-creator only sees the button if they have the event on his/her personal calendar 
// and it is at most 15 minutes before the conference starts.

// The button is removed for everyone (even admins) one day after the conference ends.

$conf = $vars['conf'];

if ($conf && $conf->application == 'bbb') {
	elgg_load_library('elgg:sched_conf');
	elgg_load_library('elgg:event_calendar');
	$user_guid = elgg_get_logged_in_user_guid();
	$event = sched_conf_get_event_for_conference($conf->guid);
	$termination_time = $event->real_end_time + 60*60*24;
	if ($termination_time < time()) {
		$in_time_window = FALSE;
	} else if ($conf->canEdit()) {
		$in_time_window = TRUE;
	} else if (event_calendar_has_personal_event($event->guid, $user_guid) && ($event->start_date - 15*60) >= time()) {
		$in_time_window = TRUE;
	} else {
		$in_time_window = FALSE;
	}
	if ( $in_time_window ) {
		$button = elgg_view('output/url', array(
			'href' => sched_conf_get_join_bbb_url($conf),
			'text' => elgg_echo('sched_conf:join_conf_button'),
			'class' => 'elgg-button elgg-button-action',
			'target' => '_blank',
		));
	
		echo '<div class="sched-conf-join-button">'.$button.'</div>';
	}
}
