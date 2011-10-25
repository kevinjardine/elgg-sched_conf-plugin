<?php
elgg_load_library('elgg:sched_conf');

$conf_guid = get_input('guid',0);
$conf = get_entity($conf_guid);
if (elgg_instanceof($conf,'object','conference') && $conf->canEdit()) {
	if (get_input('cancel','')) {
		system_message(elgg_echo('sched_conf:delete_cancel_response'));
	} else {
		$container = get_entity($conf->container_guid);
		// delete associated event
		$event = sched_conf_get_event_for_conference($conf_guid);
		if ($event) {
			$event->delete();
		}
		$conf->delete();
		system_message(elgg_echo('sched_conf:delete_response'));
		if (elgg_instanceof($container,'group')) {
			forward('event_calendar/group/'.$container->guid);
		} else {
			forward('event_calendar/list');
		}
	}
} else {
	register_error(elgg_echo('sched_conf:error_delete'));
}

forward(REFERER);