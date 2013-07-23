ee-checkin
==========

A custom function/shortcode for Event Espresso.

It provides front end functionality to manually check users in and out.

It is not endorsed or supported by Event Espresso - use at your own risk!


==========

To use:

You NEED the Custom Files add on installed.

Copy the eecheckin_css.css and eecheckin.js to the wp-content/uploads/espresso folder.

Copy the contents of the custom_functions.php and custom_shortcodes.php from here to the relevant files in your espresso folder. 

Note these DON'T replace the contents but are tacked onto the end.

In your site, create a page and add the [EE_CHECKIN] shortcode, it will list all the events and attendees for each event.

You should be able to use the standard LISTATTENDEE attributes as the shortcode is based on that shortcode.
