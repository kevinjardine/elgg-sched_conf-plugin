This plugin adds web conferencing integration to the event calendar.

This version supports Big Blue Button (BBB) version 0.8.

To configure, set the URL for your BBB server in the plugin settings 
(including the closing slash).

You will also need to set the BBB securitySalt value in the plugin 
settings. This can be found in

/var/lib/tomcat6/webapps/bigbluebutton/WEB-INF/classes/bigbluebutton.properties

for a typical BBB install.

The plugin adds an additional button "Add conference".

Adding a BBB conference automatically adds a conference on your BBB server and
a corresponding event to the Elgg event calendar.

The duration of the conference is set so that it closes 24 hours after the
event start time.

Conference creators and site admins are granted moderator rights over the conference
and can enter it at any time after the conference is created (even before the
conference start time) until the conference is closed.

Any other user with the conference event on his/her personal calendar can join
the conference beginning 15 minutes before the conference start time.

To enter the conference, visit the conference event page in the Elgg event calendar
and click on the "Join conference" button. This sends the participant to the BBB
server using their Elgg display name.

The "Join conference" button only appears if and when the user is allowed to
participate in the conference.
