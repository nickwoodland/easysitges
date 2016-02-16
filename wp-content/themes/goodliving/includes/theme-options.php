<?php

//Enable CoLabsSEO on these custom Post types
//$seo_post_types = array('post','page');
//define("SEOPOSTTYPES", serialize($seo_post_types));

//Global options setup
add_action('init','colabs_global_options');
function colabs_global_options(){
	// Populate CoLabsThemes option in array for use in theme
	global $colabs_options;
	$colabs_options = get_option('colabs_options');
}

add_action('admin_head','colabs_options');  
if (!function_exists('colabs_options')) {
function colabs_options(){
	
// VARIABLES
$themename = "GoodLiving";
$manualurl = 'http://colorlabsproject.com';
$shortname = "colabs";

//Access the WordPress Categories via an Array
$colabs_categories = array();  
$colabs_categories_obj = get_categories('hide_empty=0');
foreach ($colabs_categories_obj as $colabs_cat) {
    $colabs_categories[$colabs_cat->cat_ID] = $colabs_cat->cat_name;}
//$categories_tmp = array_unshift($colabs_categories, "Select a category:");

//Access the WordPress Pages via an Array
$colabs_pages = array();
$colabs_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($colabs_pages_obj as $colabs_page) {
    $colabs_pages[$colabs_page->ID] = $colabs_page->post_title; }
//$colabs_pages_tmp = array_unshift($colabs_pages, "Select a page:");

//Access the Property Features via an Array
$colabs_properties = array();  
$colabs_properties_obj = get_categories('hide_empty=0&taxonomy=property_features');
foreach ($colabs_properties_obj as $colabs_tax) {
    $colabs_properties[$colabs_tax->cat_ID] = $colabs_tax->cat_name;}

$options_features_amount = array("0","1","2","3","4","5","6","7","8","9","10+");
$options_matching_method = array("exact" => "Exact Match","minimum" => "Minimum Value"); 

//Get Agent via an Array
global $wpdb;
$colabs_agents = array();
$agentarray = $wpdb->get_results( "
SELECT id,post_title
FROM $wpdb->posts AS p
WHERE p.post_type = 'agent' AND p.post_status = 'publish'
" );
$i = 0;
foreach ($agentarray as $item) {
	//$colabs_agent[$i] = $item -> id;
	$colabs_agents[$item->id] = $item->post_title;
	//$i = $i + 1;
}
//array_unshift($colabs_agent, "");

$images_dir =  get_template_directory_uri() . '/functions/images/';

//More Options
$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");

$other_entries_10 = array("Select a number:","1","2","3","4","5","6","7","8","9","10");

$other_entries_4 = array("Select a number:","1","2","3","4");

$other_status = array();
$other_status['true'] = "Available";

$zoom = array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$colors = array('blue'=>'Blue','red'=>'Red','green'=>'Green','yellow'=>'Yellow','pink'=>'Pink','purple'=>'Purple','teal'=>'Teal','white'=>'White','black'=>'Black');

// THIS IS THE DIFFERENT FIELDS
$options = array();

// General Settings
$options[] = array( "name" => __("General Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "general");

$options[] = array( "name" => __( "Use for blog title/logo", "colabsthemes" ),
					"desc" => __( "Select title or logo for your blog.", "colabsthemes" ),
					"id" => $shortname."_logotitle",
					"std" => "logo",
					"type" => "select2",
					"options" => array( "logo" => __( "Logo", "colabsthemes" ), "title" => __( "Title", "colabsthemes" ) ) );
                    
$options[] = array( "name" => __("Header Custom Logo","colabsthemes"),
					"desc" => __("Upload a logo for your theme, or specify an image URL directly. Best image size in 219x48 px","colabsthemes"),
					"id" => $shortname."_logo",
					"std" => trailingslashit( get_template_directory_uri() ) . "images/logo.png",
					"type" => "upload");					

$options[] = array( "name" => __("Custom Favicon","colabsthemes"),
					"desc" => __("Upload a 16x16px ico image that will represent your website's favicon. Favicon/bookmark icon will be shown at the left of your blog's address in visitor's internet browsers.","colabsthemes"),
					"id" => $shortname."_custom_favicon",
					"std" => trailingslashit( get_template_directory_uri() ) . "images/favicon.png",
					"type" => "upload"); 
					
$options[] = array( "name" => "Disable Responsive",
          "desc" => "You can disable responsive module for your site.",
          "id" => $shortname."_disable_mobile",
          "std" => "false",
          "type" => "checkbox");

$options[] = array( "name" => "Allow Registration Password",
          "desc" => "You can enable to allow registration password for your site.",
          "id" => $shortname."_allow_registration_password",
          "std" => "false",
          "type" => "checkbox");
					
$options[] = array( "name" => __('Enable / Disable reCaptcha', 'colabsthemes'),
					"desc" => sprintf(__('%2$s. reCaptcha is a free anti-spam service provided by Google. Learn more about <a target="_new" href="%1$s">reCaptcha</a>.', 'colabsthemes'), 'http://code.google.com/apis/recaptcha/', __('Set this option to yes to enable the reCaptcha service that will protect your site against spam registrations. It will show a verification box on your registration page that requires a human to read and enter the words','colabsthemes') ),
					"id" => $shortname."_captcha_enable",
                    'class' => 'collapsed',
					"std" => "true",
					"type" => "checkbox");

$options[] = array( "name" => __('reCaptcha Public Key', 'colabsthemes'),
					"desc" => sprintf( '%3$s. %1$s' . __('Sign up for a free <a target="_new" href="%2$s">Google reCaptcha</a> account.','colabsthemes'), '<div class="captchaico"></div>', 'https://www.google.com/recaptcha/admin/create', __('Enter your public key here to enable an anti-spam service on your new user registration page (requires a free Google reCaptcha account). Leave it blank if you do not wish to use this anti-spam feature','colabsthemes') ),
					"id" => $shortname."_captcha_public_key",
					"std" => "",
                    'class' => 'hidden',
					"type" => "text");

$options[] = array( "name" => __('reCaptcha Private Key', 'colabsthemes'),
					"desc" => sprintf( '%3$s. %1$s' . __('Sign up for a free <a target="_new" href="%2$s">Google reCaptcha</a> account.','colabsthemes'), '<div class="captchaico"></div>', 'https://www.google.com/recaptcha/admin/create', __('Enter your private key here to enable an anti-spam service on your new user registration page (requires a free Google reCaptcha account). Leave it blank if you do not wish to use this anti-spam feature','colabsthemes') ),
					"id" => $shortname."_captcha_private_key",
					"std" => "",
                    'class' => 'hidden',
					"type" => "text");

$options[] = array( "name" => __('Choose Theme for reCaptcha', 'colabsthemes'),
					"desc" => __('Select the color scheme you wish to use for reCaptcha.', 'colabsthemes'),
					"id" => $shortname."_captcha_theme",
					"std" => "",
                    'class' => 'hidden last',
					"type" => "select2",
					"options" => array( 'light' => __('Light', 'colabsthemes'), 'dark' => __('Dark', 'colabsthemes') )
                    );

$options[] = array( "name" => __( 'Single Main Layout', 'colabsthemes' ),
                    "desc" => __( 'Select main content and sidebar alignment. Choose between left or right sidebar layout.', 'colabsthemes' ),
                    "id" => $shortname . "_layout", //colabs_layout
                    "std" => "colabs-two-col-left",
                    "type" => "images",
                    "options" => array(    
								"colabs-one-col" => $images_dir . "1c.png",
                                "colabs-two-col-left" => $images_dir . "2cl.png",
                                "colabs-two-col-right" => $images_dir . "2cr.png")
                    );
										
// FrontPage Options
$options[] = array( "name" => __("Announcement Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "home");

$options[] = array( "name" => __("Announcement Enable", "colabsthemes"),
										"desc" => __("Display announcement on frontpage", "colabsthemes"),
										"id" => $shortname."_announcement",
										"std" => "false",
										"class" => "collapsed",
										"type" => "checkbox" );
					
$options[] = array( "name" => __("Announcement Title","colabsthemes"),
                    "desc" => __("Enter the announcement title","colabsthemes"),
                    "id" => $shortname."_announcement_title",
                    "std" => "",
										"class" => "hidden",
                    "type" => "text");  
					
$options[] = array( "name" => __("Announcement Content","colabsthemes"),
                    "desc" => __("Enter the announcement content","colabsthemes"),
                    "id" => $shortname."_announcement_text",
                    "std" => "",
										"class" => "hidden last",
                    "type" => "textarea"); 

// Global Page
$options[] = array( "name" => __("Page Settings", "colabsthemes" ),
					"type" => "heading",
					"icon" => "home");

$options[] = array( "name" => __("Dashboard Page", "colabsthemes" ),
					"desc" => sprintf( __("This is the page to your customers dashboard page. Do not change unless you know what you are doing.",'colabsthemes') ),
					"id" => $shortname."_dashboard_url",
					"std" => "",
					"options" => $colabs_pages,
					"type" => "select2");                    

$options[] = array( "name" => __("Profile Page", "colabsthemes" ),
					"desc" => sprintf( __("This is the page to your customers profile page. Do not change unless you know what you are doing.",'colabsthemes') ),
					"id" => $shortname."_profile_url",
					"std" => "",
					"options" => $colabs_pages,
					"type" => "select2");		

$options[] = array( "name" => __("Edit Property Page", "colabsthemes" ),
					"desc" => sprintf( __("This is the page to your customers edit property page. Do not change unless you know what you are doing.",'colabsthemes') ),
					"id" => $shortname."_edit_url",
					"std" => "",
					"options" => $colabs_pages,
					"type" => "select2");		

$options[] = array( "name" => __("Submit Property Page", "colabsthemes" ),
					"desc" => sprintf( __("This is the page to your customers submit property page. Do not change unless you know what you are doing.",'colabsthemes') ),
					"id" => $shortname."_submit_url",
					"std" => "",
					"options" => $colabs_pages,
					"type" => "select2");		

$options[] = array( "name" => __("Bookmark Property Page", "colabsthemes" ),
					"desc" => sprintf( __("This is the page to your customers bookmark property page. Do not change unless you know what you are doing.",'colabsthemes') ),
					"id" => $shortname."_bookmark_property",
					"std" => "",
					"options" => $colabs_pages,
					"type" => "select2");

$options[] = array( "name" => __("Property by Agent Page", "colabsthemes" ),
					"desc" => sprintf( __("This is the page to your property list by agent page. Do not change unless you know what you are doing.",'colabsthemes') ),
					"id" => $shortname."_agent_page",
					"std" => "",
					"options" => $colabs_pages,
					"type" => "select2");
					
//Property Labels Options
$options[] = array( "name" => __("Property Settings","colabsthemes"),
					"icon" => "misc",
					"type" => "heading");

$options[] = array( "name" => __('Property Requires Approval', 'colabsthemes'),
					"desc" => __('This options allows you to define whether or not you want to moderate submit property. The property will be marked as \'pending\' and admin will be notified via email.','colabsthemes'),
					"id" => $shortname."_property_require_moderation",
					"std" => "true",
					"type" => "checkbox");

$options[] = array( "name" => __('Allow Property Editing', 'colabsthemes'),
					"desc" => __('This options allows you to control if property listings can be edited by the user.','colabsthemes'),
					"id" => $shortname."_allow_editing",
					"std" => "true",
					"type" => "checkbox",
                    );
					
$options[] = array( "name" => __("Currency Symbol","colabsthemes"),
                    "desc" => __("Specify the currency that your properties price will be shown in.","colabsthemes"),
                    "id" => $shortname."_currency_symbol",
                    "std" => "$",
                    "type" => "text"); 		

$options[] = array( "name" => __("Unit of Measure Label","colabsthemes"),
                    "desc" => __("Specify the text that will be displayed on the frontend for the Unit of Measure.","colabsthemes"),
                    "id" => $shortname."_unit_measure",
                    "std" => "sq ft",
                    "type" => "text");	
	
					
$options[] = array( "name" => __('Property Listing Fee', 'colabsthemes'),
					"desc" => sprintf( '%2$s. %1$s', __('Enter a numeric value, do not include currency symbols. Leave blank to enable free listings.','colabsthemes'), __('Default property listing fee.','colabsthemes') ),
					"id" => $shortname."_property_listing_cost",
					"std" => "",
					"type" => "text");				

$options[] = array( "name" => __('Featured Property Price', 'colabsthemes'),
					"desc" => sprintf( '%2$s. %1$s', __('Only enter numeric values or decimal points. Do not include a currency symbol or commas.','colabsthemes'), __('This is the additional amount you will charge visitors to post a featured property on your site. A featured property appears at the top of the homepage and at the bottom on other page. Leave this blank if you do not want to offer featured.','colabsthemes') ),
					"id" => $shortname."_cost_to_feature",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => __('Allow Property Relisting', 'colabsthemes'),
					"desc" => __('This enables an option for your customers to relist their property posting when it has expired.','colabsthemes'),
					"id" => $shortname."_allow_relist",
					"std" => "true",
					"type" => "checkbox");
                    
$options[] = array( "name" => __('Re-Listing Fee', 'colabsthemes'),
					"desc" => sprintf( '%2$s. %1$s', __('Enter a numeric value, do not include currency symbols. Leave blank to enable free re-listings.','colabsthemes'), __('Default re-listing fee.','colabsthemes') ),
					"id" => $shortname."_property_relisting_cost",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => __("Property Listing Period", "colabsthemes" ),
					"desc" => __("Number of days each property will be listed on your site. This option is overridden by property if you are charging for property and using the Fixed Price Per property option. ", "colabsthemes" ),
					"id" => $shortname."_prun_period",
					"std" => "30",
					"type" => "text");

$options[] = array( "name" => __('Enable Reminder Emails', 'colabsthemes'),
					"desc" => __('Send the property owner an email 5/1 days before their property expires, and another email once their property has expired (post status changes from published to draft).', 'colabsthemes'),
					"id" => $shortname."_expired_property_email_owner",
					"std" => "false",
					"type" => "checkbox");

$options[] = array( "name" => __('Expired Property Action', 'colabsthemes'),
					"desc" => __('Choose what to do with expired property. Selecting \'display message\' will keep the property visible and display a \'property not available\' notice on it. Selecting \'hide\' will change the property post to private so only the property poster may view it..', 'colabsthemes'),
					"id" => $shortname."_expired_action",
					"std" => "hide",
					'options' => array(  
						'display_message' => __('Display Message', 'colabsthemes'),
						'hide'  => __('Hide', 'colabsthemes')
					),
					"type" => "select2");

$options[] = array( "name" => __('Rich Text Editor', 'colabsthemes'),
          "desc" => __('Use Rich text editor for property submission','colabsthemes'),
          "id" => $shortname."_enable_rich_text_editor",
          "std" => "true",
          "type" => "checkbox",
          );

					
//Property Search Options
$options[] = array( "name" => __("Property Search","colabsthemes"),
					"icon" => "misc",
					"type" => "heading");

$options[] = array( "name" => __("Advance Search", "colabsthemes"),
                    "desc" => __("Show advance search on header", "colabsthemes"),
                    "id" => $shortname."_show_advance_search",
                    "std" => "true",
                    "type" => "checkbox");

$options[] = array( "name" => "Search Results",
                    "desc" => "Select the number of entries that should appear on the search results page.",
                    "id" => $shortname."_property_search_results",
                    "std" => "3",
                    "type" => "select",
                    "options" => $other_entries);

$options[] = array( "name" => "Search by Features Matching Method",
										"desc" => "Choose the matching method for Search Results. <br /><strong>Exact Match</strong> means only properties with the same number of baths, beds, garages searched for will be returned while <strong>Minimum Value</strong> means that all properties with at least the amount of baths, beds, garages searched for will be returned.",
										"id" => $shortname."_feature_matching_method",
										"std" => "exact",
										"type" => "radio",
										"options" => $options_matching_method); 

$options[] = array( "name" => "Search box Title",
                    "desc" => "Include a short title for the search box on the home page, e.g. Search Our Properties.",
                    "id" => $shortname."_search_header",
                    "std" => "Search Our Properties",
                    "type" => "text");

$options[] = array( "name" => "Search Keyword Text",
                    "desc" => "Default text that is displayed in the search textbox.",
                    "id" => $shortname."_search_keyword_text",
                    "std" => "Your Keywords",
                    "type" => "text"); 
                    
$options[] = array( "name" => "Locations Dropdown View All Label",
                    "desc" => "Specify the text that will be displayed on the header search Locations dropdown View All option.",
                    "id" => $shortname."_label_locations_dropdown_view_all",
                    "std" => "View all Locations",
                    "type" => "text"); 

$options[] = array( "name" => "Property Type Dropdown View All Label",
                    "desc" => "Specify the text that will be displayed on the header search Property Type dropdown View All option.",
                    "id" => $shortname."_label_property_type_dropdown_view_all",
                    "std" => "View all Property Types",
                    "type" => "text"); 

$options[] = array( "name" => "Property Status Dropdown View All Label",
                    "desc" => "Specify the text that will be displayed on the header search Property Status dropdown View All option.",
                    "id" => $shortname."_label_property_status_dropdown_view_all",
                    "std" => "View all Property Status",
                    "type" => "text"); 
                    
$options[] = array( "name" => "Price Label",
                    "desc" => "Specify the text that will be displayed on the header search for the Price label.",
                    "id" => $shortname."_label_price",
                    "std" => "Min Price",
                    "type" => "text"); 

$options[] = array( "name" => "Advanced Search Button Label",
                    "desc" => "Specify the text that will be displayed on the header search Advanced Search Button.",
                    "id" => $shortname."_label_advanced_search",
                    "std" => "Advanced Search",
                    "type" => "text"); 

$options[] = array( "name" => "Hide Advanced Search Button Label",
                    "desc" => "Specify the text that will be displayed on the header search Hide Advanced Search Button.",
                    "id" => $shortname."_label_hide_advanced_search",
                    "std" => "Hide Advanced Search",
                    "type" => "text");
                    
$options[] = array( "name" => "Size Label",
                    "desc" => "Specify the text that will be displayed on the header search for the Min Size label.",
                    "id" => $shortname."_label_size",
                    "std" => "Min Size",
                    "type" => "text");  		

$options[] = array( "name" => "Beds Label",
                    "desc" => "Specify the text that will be displayed on the header for more than one Bed.",
                    "id" => $shortname."_label_beds",
                    "std" => "Beds",
                    "type" => "text");                                       

$options[] = array( "name" => "Bathrooms Label",
                    "desc" => "Specify the text that will be displayed on the header search and backend for more than one Bathroom.",
                    "id" => $shortname."_label_baths_long",
                    "std" => "Bathrooms",
                    "type" => "text"); 

$options[] = array( "name" => "Garages Label",
                    "desc" => "Specify the text that will be displayed on the header for more than one Garage.",
                    "id" => $shortname."_label_garages",
                    "std" => "Garages",
                    "type" => "text");                    
										
/*-----------------------------------------------------------------------------------*/
/* dsIDXpress Plugin Settings */
/*-----------------------------------------------------------------------------------*/                   
if (defined('DSIDXPRESS_OPTION_NAME')) {
	$idx_options = get_option(DSIDXPRESS_OPTION_NAME);
	$pluginUrl = DSIDXPRESS_PLUGIN_URL;
} else {
	$idx_options = array('Activated' => false);
}
if ($idx_options['Activated']) {

	$options[] = array( "name" => __("dsIDXpress Plugin Integration","colabsthemes"),
            "icon" => "general",
						"type" => "heading");
	
	$options[] = array( "name" => __("Enable the IDX Plugin Search","colabsthemes"),
						"desc" => __("Enable if you want the searchbox to show the option to search the MLS database.","colabsthemes"),
						"id" => $shortname."_idx_plugin_search",
						"std" => "false",
						"type" => "checkbox");

	$options[] = array( "name" => __("Search box Title","colabsthemes"),
                    	"desc" => __("Include a short title for the search box on the home page, e.g. Search the MLS.","colabsthemes"),
                    	"id" => $shortname."_search_mls_header",
                    	"std" => "MLS Search",
                    	"type" => "text");
                    	
	$options[] = array( "name" => __("Cities","colabsthemes"),
						"desc" => sprintf( __("Add the cities that you want to show up in the search options. <strong>ONE city per line</strong>.<br /><a target='_blank' href='%s/locations.php?type=city'>Click here for a list of cities</a>.","colabsthemes"), $pluginUrl ),
						"id" => $shortname."_idx_search_cities",
						"std" => "",
						"type" => "textarea"); 
	
	$options[] = array( "name" => __("Communities","colabsthemes"),
						"desc" => sprintf( __("Add the communities that you want to show up in the search options. <strong>ONE community per line</strong>.<br /><a target='_blank' href='%s/locations.php?type=community'>Click here for a list of communities</a>.","colabsthemes"), $pluginUrl ),
						"id" => $shortname."_idx_search_communities",
						"std" => "",
						"type" => "textarea"); 
	
	$options[] = array( "name" => __("Tracts","colabsthemes"),
						"desc" => sprintf( __("Add the tracts that you want to show up in the search options. <strong>ONE tract per line</strong>.<br /><a target='_blank' href='%s/locations.php?type=tract'>Click here for a list of tracts</a>.","colabsthemes"), $pluginUrl ),
						"id" => $shortname."_idx_search_tracts",
						"std" => "",
						"type" => "textarea"); 
	
	$options[] = array( "name" => __("Zips","colabsthemes"),
						"desc" => sprintf( __("Add the zips that you want to show up in the search options. <strong>ONE zip per line</strong>.<br /><a target='_blank' href='%s/locations.php?type=zip'>Click here for a list of zips</a>.","colabsthemes"), $pluginUrl ),
						"id" => $shortname."_idx_search_zips",
						"std" => "",
						"type" => "textarea");
						 				
}
					
//Single Property Options
/*$options[] = array( "name" => __("Single Property","colabsthemes"),
					"icon" => "misc",
					"type" => "heading");*/

//Google Maps Options
$options[] = array( "name" => __("Maps","colabsthemes"),
					"icon" => "home",
				    "type" => "heading");    

$options[] = array( "name" => __("Google Maps API Key","colabsthemes"),
					"desc" => __("Enter your Google Maps API key before using any of Postcard's mapping functionality. <a href='http://code.google.com/apis/maps/signup.html'>Signup for an API key here</a>.","colabsthemes"),
					"id" => $shortname."_maps_apikey",
					"std" => "",
					"class" => "hidden",
					"type" => "text"); 
					
$options[] = array( "name" => __("Disable Mousescroll","colabsthemes"),
					"desc" => __("Turn off the mouse scroll action for all the Google Maps on the site. This could improve usability on your site.","colabsthemes"),
					"id" => $shortname."_maps_scroll",
					"std" => "",
					"type" => "checkbox");

$options[] = array( "name" => __("Single Page Map Height","colabsthemes"),
					"desc" => __("Height in pixels for the maps displayed on Single property pages.","colabsthemes"),
					"id" => $shortname."_maps_single_height",
					"std" => "235",
					"type" => "text");
					
$options[] = array( "name" => __("Default Map Zoom Level","colabsthemes"),
					"desc" => __("Set this to adjust the default in the post & page edit backend.","colabsthemes"),
					"id" => $shortname."_maps_default_mapzoom",
					"std" => "9",
					"type" => "select2",
					"options" => $zoom);

$options[] = array( "name" => __("Default Map Type","colabsthemes"),
					"desc" => __("Set this to the default rendered in the post backend.","colabsthemes"),
					"id" => $shortname."_maps_default_maptype",
					"std" => "Normal",
					"type" => "select2",
					"options" => array('G_NORMAL_MAP' => 'Normal','G_SATELLITE_MAP' => 'Satellite','G_HYBRID_MAP' => 'Hybrid','G_PHYSICAL_MAP' => 'Terrain'));

$options[] = array(    "name" =>  "Marker Pin Color",
                        "desc" => __("Choose from a preset colored pin.","colabsthemes"),
                        "id" => $shortname."_google_pin",
                        "std" => "red",
                        "type" => "select2",
                        "options" => $colors);					

/* //Social Settings	 */				
$options[] = array( "name" => __("Social Networking","colabsthemes"),
					"icon" => "misc",
					"type" => "heading");
					
$options[] = array( "name" => __("Enable/Disable Social Share Button","colabsthemes" ),
					"desc" => __("Select which social share button you would like to enable.","colabsthemes" ),
					"id" => $shortname."_share",
					"std" => array("fblike","twitter","google_plusone"),
					"type" => "multicheck2",
                    "class" => "",
					"options" => array(
                            "fblike" => "Facebook Like Button",
                            "twitter" => "Twitter Share Button",
                            "google_plusone" => "Google +1 Button",
														"pinterest" => "Pinterest",
														"linkedin" => "Linked In"
                                )
                    );
                    

// Open Graph Settings
$options[] = array( "name" => __("Open Graph Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "graph");

$options[] = array( "name" => __("Open Graph","colabsthemes"),
					"desc" => __("Enable or disable Open Graph Meta tags.","colabsthemes"),
					"id" => $shortname."_og_enable",
					"type" => "select2",
                    "std" => "",
                    "class" => "collapsed",
					"options" => array("" => "Enable", "disable" => "Disable") );

$options[] = array( "name" => __("Site Name","colabsthemes"),
					"desc" => __("Open Graph Site Name ( og:site_name ).","colabsthemes"),
					"id" => $shortname."_og_sitename",
					"std" => "",
                    "class" => "hidden",
					"type" => "text");

$options[] = array( "name" => __("Admin","colabsthemes"),
					"desc" => __("Open Graph Admin ( fb:admins ).","colabsthemes"),
					"id" => $shortname."_og_admins",
					"std" => "",
                    "class" => "hidden",
					"type" => "text");

$options[] = array( "name" => __("Image","colabsthemes"),
					"desc" => __("You can put the url for your Open Graph Image ( og:image ).","colabsthemes"),
					"id" => $shortname."_og_img",
					"std" => "",
                    "class" => "hidden last",
					"type" => "text");

//Dynamic Images 					                   
$options[] = array( "name" => __("Thumbnail Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "image");
                    
$options[] = array( "name" => __("WordPress Featured Image","colabsthemes"),
					"desc" => __("Use WordPress Featured Image for post thumbnail.","colabsthemes"),
					"id" => $shortname."_post_image_support",
					"std" => "true",
					"class" => "collapsed",
					"type" => "checkbox");

$options[] = array( "name" => __("WordPress Featured Image - Dynamic Resize","colabsthemes"),
					"desc" => __("Resize post thumbnail dynamically using WordPress native functions (requires PHP 5.2+).","colabsthemes"),
					"id" => $shortname."_pis_resize",
					"std" => "true",
					"class" => "hidden",
					"type" => "checkbox");
                    
$options[] = array( "name" => __("WordPress Featured Image - Hard Crop","colabsthemes"),
					"desc" => __("Original image will be cropped to match the target aspect ratio.","colabsthemes"),
					"id" => $shortname."_pis_hard_crop",
					"std" => "true",
					"class" => "hidden last",
					"type" => "checkbox");
                    
$options[] = array( "name" => __("TimThumb Image Resizer","colabsthemes"),
					"desc" => __("Enable timthumb.php script which dynamically resizes images added thorugh post custom field.","colabsthemes"),
					"id" => $shortname."_resize",
					"std" => "true",
					"type" => "checkbox");
                    
$options[] = array( "name" => __("Automatic Thumbnail","colabsthemes"),
					"desc" => __("Generate post thumbnail from the first image uploaded in post (if there is no image specified through post custom field or WordPress Featured Image feature).","colabsthemes"),
					"id" => $shortname."_auto_img",
					"std" => "true",
					"type" => "checkbox");
                    
$options[] = array( "name" => __("Thumbnail Image in RSS Feed","colabsthemes"),
					"desc" => __("Add post thumbnail to RSS feed article.","colabsthemes"),
					"id" => $shortname."_rss_thumb",
					"std" => "false",
					"type" => "checkbox");

$options[] = array( "name" => __("Thumbnail Image Dimensions","colabsthemes"),
					"desc" => __("Enter an integer value i.e. 250 for the desired size which will be used when dynamically creating the images.","colabsthemes"),
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"type" => array( 
									array(  'id' => $shortname. '_thumb_w',
											'type' => 'text',
											'std' => 100,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_thumb_h',
											'type' => 'text',
											'std' => 100,
											'meta' => 'Height')
								  ));

$options[] = array( "name" => __("Custom Field Image","colabsthemes"),
					"desc" => __("Enter your custom field image name to change the default name (default name: image).","colabsthemes"),
					"id" => $shortname."_custom_field_image",
					"std" => "",
					"type" => "text");
								  
// Analytics ID, RSS feed
$options[] = array( "name" => __("Analytics ID, RSS feed","colabsthemes"),
					"type" => "heading",
					"icon" => "statistics");

$options[] = array( "name" => __("GoSquared Token","colabsthemes"),
					"desc" => __("You can use <a href='http://www.gosquared.com/livestats/?ref=11674'>GoSquared</a> real-time web analytics. Enter your <strong>GoSquared Token</strong> here (ex. GSN-893821-D).","colabsthemes"),
					"id" => $shortname."_gosquared_id",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => __("Google Analytics","colabsthemes"),
					"desc" => __("Manage your website statistics with Google Analytics, put your Analytics Code here. ","colabsthemes"),
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea");

$options[] = array( "name" => __("Feedburner URL","colabsthemes"),
					"desc" => __("Feedburner URL. This will replace RSS feed link. Start with http://.","colabsthemes"),
					"id" => $shortname."_feedlinkurl",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => __("Feedburner Comments URL","colabsthemes"),
					"desc" => __("Feedburner URL. This will replace RSS comment feed link. Start with http://.","colabsthemes"),
					"id" => $shortname."_feedlinkcomments",
					"std" => "",
					"type" => "text");
				
$options[] = array( "name" => __("Enable PressTrends Tracking","colabsthemes"),
					"desc" => __("PressTrends is a simple usage tracker that allows us to see how our customers are using our themes, so that we can help improve them for you. <strong>None</strong> of your personal data is sent to PressTrends.","colabsthemes"),
					"id" => $shortname."_pt_enable",
					"std" => "true",
					"type" => "checkbox");
// Footer Settings
$options[] = array( "name" => __("Footer Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "footer");    	

$options[] = array( "name" => __("Enable / Disable Custom Footer Credit","colabsthemes"),
					"desc" => __("Activate to add custom credit on footer area.","colabsthemes"),
					"id" => $shortname."_footer_credit",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => __("Footer Credit","colabsthemes"),
                    "desc" => __("You can customize footer credit on footer area here.","colabsthemes"),
                    "id" => $shortname."_footer_credit_txt",
                    "std" => "",
					"class" => "hidden last",                    
                    "type" => "textarea"); 					
					
/* //Contact Form */
$options[] = array( "name" => __("Contact Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "general");
					
$options[] = array( "name" => __("Contact Form Email Address","colabsthemes"),
					"desc" => __("All inquiries made by your visitors through the Contact Form page will be sent to this email address.","colabsthemes"),
					"id" => $shortname."_contactform_email",
					"std" => "",
					"type" => "text");

/* //Subscribe Form */					
$options[] = array( "name" => __("Subscribe Settings","colabsthemes"),
					"type" => "heading",
					"icon" => "general");

$options[] = array( "name" => __("Enable / Disable Subscribe Form","colabsthemes"),
					"desc" => __("Activate to add subscribe form on footer area.","colabsthemes"),
					"id" => $shortname."_subscribe_form",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");

$options[] = array( "name" => __("Subscribe Form Action","colabsthemes"),
					"desc" => __("Enter your subscribe action on footer area. To get MailChimp action you can read this <a href='http://colorlabsproject.com/documentations/goodliving/'>documentation</a>","colabsthemes"),
					"id" => $shortname."_subscribe_action",
					"std" => "",
					"class" => "hidden",
					"type" => "text");
					
$options[] = array( "name" => __("Subscribe Form Title","colabsthemes"),
					"desc" => __("Enter your subscribe form on footer area.","colabsthemes"),
					"id" => $shortname."_subscribe_title",
					"std" => "",
					"class" => "hidden",
					"type" => "text");

$options[] = array( "name" => __("Subscribe Button Title","colabsthemes"),
					"desc" => __("Enter your subscribe button title on footer area.","colabsthemes"),
					"id" => $shortname."_subscribe_button",
					"std" => "Subscribe",
					"class" => "hidden",
					"type" => "text");
					
$options[] = array( "name" => __("Subscribe Form Description","colabsthemes"),
					"desc" => __("Enter your subscribe form description on footer area.","colabsthemes"),
					"id" => $shortname."_subscribe_desc",
					"std" => "",
					"class" => "hidden last",
					"type" => "textarea");					
					
// Add extra options through function
if ( function_exists("colabs_options_add") )
	$options = colabs_options_add($options);

if ( get_option('colabs_template') != $options) update_option('colabs_template',$options);      
if ( get_option('colabs_themename') != $themename) update_option('colabs_themename',$themename);   
if ( get_option('colabs_shortname') != $shortname) update_option('colabs_shortname',$shortname);
if ( get_option('colabs_manual') != $manualurl) update_option('colabs_manual',$manualurl);

//PressTrends
$colabs_pt_auth = "2nq81rt8k9p1u0qp5nsix6mnsn2y7ycmd"; 
update_option('colabs_pt_auth',$colabs_pt_auth);

// CoLabs Metabox Options
// Start name with underscore to hide custom key from the user
$colabs_metaboxes = array();
$colabs_metabox_settings = array();
global $post;
$url =  get_template_directory_uri() . "/functions/images/";
    //Metabox Settings
    $colabs_metabox_settings['post'] = array(
                                'id' => 'colabsthemes-settings',
								'title' => 'ColorLabs' . __( ' Image/Video Settings', 'colabsthemes' ),
								'callback' => 'colabsthemes_metabox_create',
								'page' => 'post',
								'context' => 'normal',
								'priority' => 'high',
                                'callback_args' => ''
								);
                                    
    $colabs_metabox_settings['page'] = array(
                                'id' => 'colabsthemes-settings',
								'title' => 'ColorLabs' . __( 'Settings', 'colabsthemes' ),
								'callback' => 'colabsthemes_metabox_create',
								'page' => 'page',
								'context' => 'normal',
								'priority' => 'high',
                                'callback_args' => ''
								);

    $colabs_metabox_settings['property'] = array(
                                'id' => 'colabsthemes-settings',
								'title' => 'ColorLabs' . __( ' Property Detail Settings', 'colabsthemes' ),
								'callback' => 'colabsthemes_metabox_create',
								'page' => 'property',
								'context' => 'normal',
								'priority' => 'high',
                                'callback_args' => ''
								);	
								
	$colabs_metabox_settings['agent'] = array(
                                'id' => 'colabsthemes-settings',
								'title' => 'ColorLabs' . __( ' Agent Detail Settings', 'colabsthemes' ),
								'callback' => 'colabsthemes_metabox_create',
								'page' => 'agent',
								'context' => 'normal',
								'priority' => 'high',
                                'callback_args' => ''
								);							

if ( ( get_post_type() == 'post') || ( !get_post_type() ) ) {
	$colabs_metaboxes[] = array (  "name"  => $shortname."_single_top",
					            "std"  => "Image",
					            "label" => __("Item to Show","colabsthemes"),
					            "type" => "radio",
					            "desc" => __("Choose Image/Embed Code to appear at the single top.","colabsthemes"),
								"options" => array(	"none" => "None",
													"single_image" => "Image",
													"single_video" => "Embed" ));
	$colabs_metaboxes[] = array (	"name" => "image",
								"label" => __("Post Custom Image","colabsthemes"),
								"type" => "upload",
                                "class" => "single_image",
								"desc" => __("Upload an image or enter an URL.","colabsthemes"));
	
	$colabs_metaboxes[] = array (  "name"  => $shortname."_embed",
					            "std"  => "",
					            "label" => __("Video Embed Code","colabsthemes"),
					            "type" => "textarea",
                                "class" => "single_video",
					            "desc" => __("Enter the video embed code for your video (YouTube, Vimeo or similar)","colabsthemes"));
	
	
	$colabs_metaboxes[] = array (	"name" => "layout",
											"label" => __( "Layout", "colabsthemes" ),
											"type" => "images",
											"class" => '',
											"desc" => __( "Select a specific layout for this post/page. Overrides default site layout.", "colabsthemes" ),
											"options" => array(	"" => $url . "layout-off.png",
												"colabs-one-col" => $url . "1c.png",
												"colabs-two-col-left" => $url . "2cl.png",
												"colabs-two-col-right" => $url . "2cr.png")
											);
} // End post

if ( ( get_post_type() == 'page') || ( !get_post_type() ) ) {
	$colabs_metaboxes[] = array (	"name" => "layout",
											"label" => __( "Layout", "colabsthemes" ),
											"type" => "images",
											"class" => '',
											"desc" => __( "Select a specific layout for this post/page. Overrides default site layout.", "colabsthemes" ),
											"options" => array(	"" => $url . "layout-off.png",
												"colabs-one-col" => $url . "1c.png",
												"colabs-two-col-left" => $url . "2cl.png",
												"colabs-two-col-right" => $url . "2cr.png")
											);
}
if ( ( get_post_type() == 'property') || ( !get_post_type() ) ) {
$post_type='property';

$colabs_metaboxes[] = array (	"name" => $post_type."_image",
								"label" => "Custom Image",
								"type" => "upload",
								"desc" => __("Upload an image or enter an URL.","colabsthemes"));

$colabs_metaboxes[] = array (  "name"  => "colabs_embed",
					            "std"  => "",
					            "label" => __("Video Embed Code","colabsthemes"),
					            "type" => "textarea",
					            "desc" => __("Enter the video embed code for your video (YouTube, Vimeo or similar)","colabsthemes"));

$colabs_metaboxes[] = array (	"name" => "layout",
										"label" => __( "Layout", "colabsthemes" ),
										"type" => "images",
										"class" => '',
										"desc" => __( "Select a specific layout for this post/page. Overrides default site layout.", "colabsthemes" ),
										"options" => array(	"" => $url . "layout-off.png",
											"colabs-one-col" => $url . "1c.png",
											"colabs-two-col-left" => $url . "2cl.png",
											"colabs-two-col-right" => $url . "2cr.png")
										);											
} // End property

if ( ( get_post_type() == 'agent') || ( !get_post_type() ) ) {

$colabs_metaboxes[] = array (  "name"  => "colabs_email_agent",
					            "std"  => "",
					            "label" => __("Email Agent","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter your email agent.","colabsthemes"));

$colabs_metaboxes[] = array (  "name"  => "colabs_number_agent",
					            "std"  => "",
					            "label" => __("Number Agent","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter your number agent.","colabsthemes"));

$colabs_metaboxes[] = array (  "name"  => "colabs_address_agent",
					            "std"  => "",
					            "label" => __("Address Agent","colabsthemes"),
					            "type" => "text",
					            "desc" => __("Enter your address agent.","colabsthemes"));											
} //End agent
// Add extra metaboxes through function
if ( function_exists("colabs_metaboxes_add") ){
	$colabs_metaboxes = colabs_metaboxes_add($colabs_metaboxes);
    }
if ( get_option('colabs_custom_template') != $colabs_metaboxes){
    update_option('colabs_custom_template',$colabs_metaboxes);
    }
if ( get_option('colabs_metabox_settings') != $colabs_metabox_settings){
    update_option('colabs_metabox_settings',$colabs_metabox_settings);
    }
     
}
}



?>