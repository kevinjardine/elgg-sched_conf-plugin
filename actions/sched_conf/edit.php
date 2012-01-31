<?php

/**
 * Edit action
 * 
 * @package sched_conf
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Kevin Jardine <kevin@radagast.biz>
 * @copyright Radagast Solutions 2011
 * @link http://radagast.biz/
 * 
 */

elgg_load_library('elgg:sched_conf');

// start a new sticky form session in case of failure
elgg_make_sticky_form('sched_conf');
      
$conf_guid = get_input('guid',0);
$group_guid = get_input('group_guid',0);
$conf = sched_conf_set_event_from_form($conf_guid,$group_guid);
if ($conf) {
	// remove sticky form entries
	elgg_clear_sticky_form('sched_conf');
	$user_guid = elgg_get_logged_in_user_guid();
	if ($conf_guid) {
		system_message(elgg_echo('sched_conf:edit_conf_response'));
	} else {
		add_to_river('river/object/sched_conf/create','create',$user_guid,$event_guid);
		system_message(elgg_echo('sched_conf:add_conf_response'));
	}
	
	if (elgg_instanceof(get_entity($conf->container_guid),'group')) {
		forward('event_calendar/group/'.$group_guid);
	} else {
		forward('event_calendar/list');
	}
	 
} else {
	// redisplay form with error message
	if ($conf_guid) {
		register_error(elgg_echo('sched_conf:edit_conf_error'));
		forward('sched_conf/edit/'.$conf_guid);
	} else {
		register_error(elgg_echo('sched_conf:add_conf_error'));
		if ($group_guid) {
			forward('sched_conf/add/'.$group_guid);
		} else {
			forward('sched_conf/add/');
		}
	}
}
