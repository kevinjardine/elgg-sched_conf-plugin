<?php

elgg_register_event_handler('init', 'system', 'sched_conf_init');
elgg_register_event_handler('init', 'system', 'sched_conf_pagesetup');

/**
 * Init plugin.
 */
function sched_conf_init() {

	elgg_register_library('elgg:sched_conf', elgg_get_plugins_path() . 'sched_conf/models/model.php');

	// add a site navigation item
	$item = new ElggMenuItem('sched_conf', elgg_echo('sched_conf:site_menu_title'), 'sched_conf/add');
	elgg_register_menu_item('site', $item);

	// add to the main css
	//elgg_extend_view('css/elgg', 'sched_conf/css');

	// routing of urls
	elgg_register_page_handler('sched_conf', 'sched_conf_page_handler');

	// override the default url to view a conference object
	elgg_register_entity_url_handler('object', 'conference', 'sched_conf_url_handler');
	
	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'sched_conf_entity_menu_setup');

	// Add group option
	add_group_tool_option('conference', elgg_echo('sched_conf:enableconference'), true);
	elgg_extend_view('groups/tool_latest', 'sched_conf/group_module');

	// register actions
	$action_path = elgg_get_plugins_path() . 'sched_conf/actions/sched_conf';
	elgg_register_action('sched_conf/edit', "$action_path/edit.php");
	elgg_register_action('sched_conf/delete', "$action_path/delete.php");

}

function sched_conf_pagesetup() {
	// a bit of a kludge to check if we are on a listing page
	$url = current_page_url();
	if ((strpos($url,'event_calendar/list') !== FALSE)) {
		elgg_register_menu_item('title', array(
			'name' => 'add_conference',
			'href' => 'sched_conf/add/',
			'text' => elgg_echo('sched_conf:add_conf_title'),
			'class' => 'elgg-button elgg-button-action',
		));
	} else if (strpos($url,'event_calendar/group') !== FALSE) {
		$group_guid = elgg_get_page_owner_guid();
		elgg_register_menu_item('title', array(
			'name' => 'add_conference',
			'href' => "sched_conf/add/".$group_guid,
			'text' => elgg_echo('sched_conf:add_conf_title'),
			'class' => 'elgg-button elgg-button-action',
		));
	}
}

/**
 * Dispatches sched_conf pages.
 * 
 * URLs take the form of
 *  Schedule a conference:			sched_conf/add
 *  Schedule a group conference:	sched_conf/add/<group_guid>
 *  Edit a conference:				sched_conf/edit/<conf_guid>
 *  List site wide conferences: 	sched_conf/list/all
 *  List a user's conferences:   	sched_conf/list/owner/<username>
 *  List a group's conferences:		sched_conf/list/group/<group_guid>
 *  View a conference:				sched_conf/view/<conf_guid>/<title>
 *
 * Title is ignored
 *
 * @param array $page
 * @return NULL
 */
function sched_conf_page_handler($page) {

	elgg_load_library('elgg:sched_conf');

	$page_type = $page[0];
	switch ($page_type) {
		case 'list':
			echo sched_conf_get_page_content_list($page[1],$page[2]);
			break;
		case 'view':
			echo sched_conf_get_page_content_view($page[1]);
			break;
		case 'add':
		case 'edit':
			gatekeeper();
			echo sched_conf_get_page_content_edit($page_type, $page[1]);
			break;
	}
}

function sched_conf_url_handler($entity) {
	elgg_load_library('elgg:sched_conf');
	$friendly_title = elgg_get_friendly_title($entity->title);	
	$event = sched_conf_get_event_for_conference($entity->guid);

	return "event_calendar/view/{$event->guid}/$friendly_title";
}

// The current API appears to require that I repeat this although the only change is the handler
function sched_conf_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}
	
	elgg_load_library('elgg:sched_conf');

	$entity = $params['entity'];
	$entity = sched_conf_get_event_for_conference($entity->guid);
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'sched_conf') {
		return $return;
	}
	$user_guid = elgg_get_logged_in_user_guid();
	if (event_calendar_personal_can_manage($entity,$user_guid)) {
		if (event_calendar_has_personal_event($entity->guid,$user_guid)) {
			$options = array(
				'name' => 'personal_calendar',
				'text' => elgg_echo('event_calendar:remove_from_the_calendar'),
				'title' => elgg_echo('event_calendar:remove_from_my_calendar'),
				'href' => elgg_add_action_tokens_to_url("action/event_calendar/remove_personal?guid={$entity->guid}"),
				'priority' => 150,
			);
			$return[] = ElggMenuItem::factory($options);
		} else {
			if (!event_calendar_is_full($entity->guid) && !event_calendar_has_collision($entity->guid,$user_guid)) {
				$options = array(
					'name' => 'personal_calendar',
					'text' => elgg_echo('event_calendar:add_to_the_calendar'),
					'title' => elgg_echo('event_calendar:add_to_my_calendar'),
					'href' => elgg_add_action_tokens_to_url("action/event_calendar/add_personal?guid={$entity->guid}"),
					'priority' => 150,
				);
				$return[] = ElggMenuItem::factory($options);			}
		}
	} else {
		if (!check_entity_relationship($user_guid, 'event_calendar_request', $entity->guid)) {
			$options = array(
				'name' => 'personal_calendar',
				'text' => elgg_echo('event_calendar:make_request_title'),
				'title' => elgg_echo('event_calendar:make_request_title'),
				'href' => elgg_add_action_tokens_to_url("action/event_calendar/request_personal_calendar?guid={$entity->guid}"),
				'priority' => 150,
			);
			$return[] = ElggMenuItem::factory($options);
		}		
	}

	return $return;
}