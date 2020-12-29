<?php
/**
 * Functions.php file for GV Advocacy Child Theme
 *
 * Assumes parent is gv-project-theme.
 * This code will run before the functions.php in that theme.
 */

if (is_object($gv)) :

	/**
	 * Define an image to show in the header.
	 * Project theme generic has none, so it will use site title
	 */
	$gv->settings['header_img'] = 'https://advox.globalvoices.org/wp-content/uploads/2016/01/advox-theme-header-6002.png';

	/**
	 * Enable Featured posts - Tells GV Query Manipulation to prefetch featured posts before main loop and exclude their ids.
	 * @see gv_load_featured_posts()
	 */
	$gv->use_featured_posts = true;	
	
	/**
	 * Hide tags interface completely to avoid people using them
	 * @see gv_hide_tags_ui()
	 */
	add_filter('gv_hide_tags_ui', '__return_true');
	
	/**
	 * Set site colors for use in PHP-driven CSS (AMP templates)
	 * 
	 * Currently specifically intended for AMP plugin 
	 * 
	 * @see gv_get_site_colors()
	 * @return type
	 */
	function gvadvocacy_gv_site_colors() {
		return array(
			'solid_bg' => 'D10006',
			'link_dark' => '9d0106',
			'link_light' => 'c80005',
		);
	}
	add_filter('gv_site_colors', 'gvadvocacy_gv_site_colors');
	
	/**
	 * Filter the favicon directory used by gv_display_head_icons()
	 * 
	 * @param string $dir Default directory (no trailing /) to find favicons in
	 * @return string desired directory (no trailing /)
	 */
	function gvadvocacy_theme_gv_favicon_dir($dir) {
		return 'https://globalvoices.org/wp-content/gv-static/img/tmpl/favicon-advox';
	}
	add_filter('gv_favicon_dir', 'gvadvocacy_theme_gv_favicon_dir');
	
	/**
	 * Filter the apple touch icon to be an Advocacy logo
	 * 
	 * @param string $icon Default icon
	 * @return string desired icon
	 */
	function gvadvocacy_theme_gv_apple_touch_icon($icon) {
		return gv_get_dir('theme_images') ."gv-advocacy-apple-touch-icon-precomposed-300.png";
	}
	add_filter('gv_apple_touch_icon', 'gvadvocacy_theme_gv_apple_touch_icon');

	/**
	 * Filter the og:image (facebook/g+) default icon to be an Advocacy logo
	 * 
	 * @param string $icon Default icon
	 * @return string desired icon
	 */
	function gvadvocacy_theme_gv_og_image_default($icon) {
		return gv_get_dir('theme_images') ."gv-advox-white-fb-1200x631.png";
	}
	add_filter('gv_og_image_default', 'gvadvocacy_theme_gv_og_image_default');
	
	/**
	 * Filter ALL CASES OF og:image (facebook/g+) icon to be an Advocacy logo
	 * 
	 * @param string $icon Default icon
	 * @return string desired icon
	 */
	function gvadvocacy_theme_gv_og_image($icon) {
		return gv_get_dir('theme_images') ."advocacy-logo-square-365.png";
	}
//	add_filter('gv_og_image', 'gvadvocacy_theme_gv_og_image');

	/**
	 * Filter Google Structured Data "logo" for AMP
	 * 
	 * @see gv_get_sd_logo() Which uses this
	 * @param string $icon Default icon
	 * @return string desired icon
	 */
	function gvadvocacy_theme_gv_sd_logo($icon) {
		return array(
			'url' => 'http://s3.amazonaws.com/static.globalvoices/img/tmpl/advox-redbg-structureddata-60x219.png',
			'height' => 60,
			'width' => 219,
		);
	}
	add_filter('gv_sd_logo', 'gvadvocacy_theme_gv_sd_logo');

	/**
	 * Define Categories to be inserted into post data before returning content for translation during fetch
	 * @see gv_lingua::reply_to_ping()
	 */
	$gv->lingua_site_categories[] = 'gv-advocacy';

	/**
	 * Register "public taxonomies" for gv_taxonomies system to display automatically on posts
	 * 
	 * Should completely replace old backbone/taxonomy_priority system eventually
	 */
	function gv_advox_register_taxonomies($param) {
		
		/**
		 * TEMPORARY: Exit if world doesn't exist to avoid choking initially.
		 */
		if (!gv_slug2cat('world', false, false)) 
			return;

		// Unregister defaults as they aren't useful for this site
		gv_unregister_public_taxonomy('category');
		
		/**
		 * ADVOX IS ALLOWED TAGS
		 * Don't unregister tags AND filter gv_hide_tags_ui as false so the metabox isn't CSS-hidden
		 */
//		gv_unregister_public_taxonomy('post_tag');
		add_filter('gv_hide_tags_ui', '__return_false');

		$world_category_id = gv_slug2cat('world');
	
		// Register TOPICS as terms with parent TOPICS
		$topics_category_id = gv_slug2cat('topics');
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'topics',
			'taxonomy' => 'category',
			'parent' => $topics_category_id,
			'labels' => array(
				'name' => _lingua('topics'),
				'siblings_label' => _lingua('other_topics'),				
			),	
		));
		
		// Register COUNTRIES as terms with grandparent WORLD
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'countries',
			'taxonomy' => 'category',
			'grandparent' => $world_category_id,
			'labels' => array(
				'name' => _lingua('countries'),
				'siblings_label' => _lingua('countries_in_category_name'),
			),			
		));

		// Register REGIONS as terms with parent WORLD
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'regions',
			'taxonomy' => 'category',
			'parent' => $world_category_id,
			'labels' => array(
				'name' => _lingua('regions'),
				'siblings_label' => _lingua('other_regions'),
				'children_label' => _lingua('countries_in_category_name'),
			),
			'show_siblings' => false,
		));

		// Register SPECIAL as terms with parent SPECIAL
		$special_category_id = gv_slug2cat('special');
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'special',
			'taxonomy' => 'category',
			'parent' => $special_category_id,
			'labels' => array(
				'name' => _lingua('special_topics'), 
				'siblings_label' => _lingua('other_special_topics'), 				
			),
//			'public' => true,
		));
		
		// Register TYPE as terms with parent TYPE
		$type_category_id = gv_slug2cat('type');
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'type',
			'taxonomy' => 'category',
			'parent' => $type_category_id,
			'labels' => array(
				'name' => _lingua('type'), 
			),
			'public' => false,
		));
	}
	add_action('init', 'gv_advox_register_taxonomies');

	/**
	 * Filter gv_display_post_terms limit so we only ever show 1 term
	 * @param type $limit
	 * @param type $args
	 * @return int
	 */
	function gv_news_filter_display_post_terms_limit($limit, $args) {
		global $post;
		
		// Don't limit terms for a single post on it's own single screen
		if (is_single() AND ($post->ID == get_queried_object_id()))
			return;
		
		// Only set limit if we're on inline format
		if ('inline' == $args['format'])
			return 1;
		
		return $limit;
	}
	add_filter('gv_display_post_terms_limit', 'gv_news_filter_display_post_terms_limit', 10, 2);

	/**
	 * Filter gv_post_archive_hide_dates to hide them on hoempage
	 * @param type $limit
	 * @param type $args
	 * @return int
	 */
	function gv_advox_gv_post_archive_hide_dates($hide_dates) {
		if (is_home() AND !is_paged())
			return true;
		
		return $hide_dates;
	}
	add_filter('gv_post_archive_hide_dates', 'gv_advox_gv_post_archive_hide_dates', 10);
	
	/**
	 * Filter how recently you must have posted to be considered active
	 */
	function gv_advox_filter_active_days_ago($days_ago) {
		return 365;
	}
	add_filter('gv_active_days_ago', 'gv_advox_filter_active_days_ago');
		
	
	/**
	 * Define special categories as content types and the conditions in which to segregate them
	 * Used by gv_get_segregated_categories() and gv_
	 * segregation_conditions apply to primary content only. sidebar headlines etc assume segregation
	 * segregate_headlines - use if headlines will be a waste for this , blocks them from showing as title only
	 */
	$gv->category_content_types = array(
		'feature' => array('title' => 'feature'),
	    );

	/**
	 * Set a custom site description using a lingua string. To be used in social media sharing etc.
	 * 
	 * Note: No tagline set here, so the one in WP Admin > Settings > General will be used
	 */
	$gv->site_description = "A global anti-censorship network of bloggers and online activists dedicated to protecting freedom of expression and free access to information online.";
	
	/**
	 * Sponsors definition to be used by gv_get_sponsors()
	 */
	$gv->sponsors = array(
		'hivos' => array(
			"name" => "Hivos",
			"slug" => "hivos",
			"description" => 'Hivos, the Humanist Institute for Development Cooperation',
			"url" => "http://www.hivos.org/",
			"status" => 'featured',
			),
	);


	/**
	 * Define new categories to force addition of on all sites using this theme.
	 *
	 * Used to add categories to all lingua sites automatically. Array used to be defined in the function.
	 */
//	$gv->new_categories = array(
//		// Nepali Lang dec31 09
//		'Nepali' => array(
//			'slug' => 'nepali',
//			'description' => 'ne',
//			'parent' => gv_slug2cat('languages')
//		),
//	);
	
	/**
	 * NEW CATEGORIES TO MATCH GV NEWS AND CLEAN UP TAXONOMY
	 */

	/**
	 * New topic categories to merge old freedom-of-expression and arrest-and-harassment categories into
	 */
	$gv->new_categories["Free Expression"] = array(
		"slug" => "free-expression",
		'description' => '',
		'parent' => "topics"
		);		
	$gv->new_categories["Legal Threats"] = array(
		"slug" => "legal-threats",
		'description' => '',
		'parent' => "topics"
		);		
	
	// SPECIAL: temporary categories to migrate awkward old auto-slug categories
	// term_exists() considers "iraq" and "iraq-country" the same, so we use these as interim
	// terms to migrate iraq-country to before migrating back to just "iraq"
	$gv->new_categories["Country of Iraq"] = array(
		"slug" => "country-of-iraq",
		'description' => '',
		'parent' => "middle-east-north-africa"
		);
	$gv->new_categories["Country of Morocco"] = array(
		"slug" => "country-of-morocco",
		'description' => '',
		'parent' => "middle-east-north-africa"
		);
	$gv->new_categories["Country of Oman"] = array(
		"slug" => "country-of-oman",
		'description' => '',
		'parent' => "middle-east-north-africa"
		);
	$gv->new_categories["Country of Australia"] = array(
		"slug" => "country-of-australia",
		'description' => '',
		'parent' => "oceania"
		);
	
	// WORLD PARENT
	$gv->new_categories['WORLD'] = array(
		'slug' => 'world',
		'description' => '',
		'parent' => '',
	);

	//REGION AND CHILDREN: Caribbean
	// $gv->new_categories["Caribbean"] = array("slug" => "caribbean", 'description' => '', 'parent' => "world");
 
	// $gv->new_categories["Anguilla"] = array("slug" => "anguilla", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Antigua and Barbuda"] = array("slug" => "antigua-and-barbuda", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Aruba"] = array("slug" => "aruba", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Bahamas"] = array("slug" => "bahamas", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Barbados"] = array("slug" => "barbados", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Belize"] = array("slug" => "belize", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Bermuda"] = array("slug" => "bermuda", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Bonaire"] = array("slug" => "bonaire", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["British Virgin Islands"] = array("slug" => "british-virgin-islands", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Cayman Islands"] = array("slug" => "cayman-islands", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Curaçao"] = array("slug" => "curacao", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Dominica"] = array("slug" => "dominica", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["French Guiana"] = array("slug" => "french-guiana", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Grenada"] = array("slug" => "grenada", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Guadeloupe"] = array("slug" => "guadeloupe", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Guyana"] = array("slug" => "guyana", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Haiti"] = array("slug" => "haiti", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Jamaica"] = array("slug" => "jamaica", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Martinique"] = array("slug" => "martinique", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Montserrat"] = array("slug" => "montserrat", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Saint Lucia"] = array("slug" => "saint-lucia", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["St. Barthélémy"] = array("slug" => "st-barthelemy", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["St. Eustatius"] = array("slug" => "st-eustatius", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["St. Maarten"] = array("slug" => "st-maarten", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["St. Vincent & the Grenadines"] = array("slug" => "st-vincent-the-grenadines", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["St.Kitts & Nevis"] = array("slug" => "stkitts-nevis", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Suriname"] = array("slug" => "suriname", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Trinidad & Tobago"] = array("slug" => "trinidad-tobago", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["Turks & Caicos Isl."] = array("slug" => "turks-caicos-isl", 'description' => '', 'parent' => "caribbean");
	// $gv->new_categories["US Virgin Islands"] = array("slug" => "us-virgin-islands", 'description' => '', 'parent' => "caribbean");
		
//REGION AND CHILDREN: Central Asia & Caucasus
	// $gv->new_categories["Central Asia & Caucasus"] = array("slug" => "central-asia-caucasus", 'description' => '', 'parent' => "world");
 
	// $gv->new_categories["Afghanistan"] = array("slug" => "afghanistan", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Armenia"] = array("slug" => "armenia", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Azerbaijan"] = array("slug" => "azerbaijan", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Georgia"] = array("slug" => "georgia", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Kazakhstan"] = array("slug" => "kazakhstan", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Kyrgyzstan"] = array("slug" => "kyrgyzstan", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Mongolia"] = array("slug" => "mongolia", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Tajikistan"] = array("slug" => "tajikistan", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Turkmenistan"] = array("slug" => "turkmenistan", 'description' => '', 'parent' => "central-asia-caucasus");
	// $gv->new_categories["Uzbekistan"] = array("slug" => "uzbekistan", 'description' => '', 'parent' => "central-asia-caucasus");
		
//REGION AND CHILDREN: East Asia
	// $gv->new_categories["East Asia"] = array("slug" => "east-asia", 'description' => '', 'parent' => "world");		
 
	// $gv->new_categories["Brunei"] = array("slug" => "brunei", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Cambodia"] = array("slug" => "cambodia", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["China"] = array("slug" => "china", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Hong Kong (China)"] = array("slug" => "hong-kong-china", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Indonesia"] = array("slug" => "indonesia", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Japan"] = array("slug" => "japan", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Laos"] = array("slug" => "laos", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Macau (China)"] = array("slug" => "macau-china", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Malaysia"] = array("slug" => "malaysia", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Myanmar (Burma)"] = array("slug" => "myanmar-burma", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["North Korea"] = array("slug" => "north-korea", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Philippines"] = array("slug" => "philippines", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Singapore"] = array("slug" => "singapore", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["South Korea"] = array("slug" => "south-korea", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Taiwan (ROC)"] = array("slug" => "taiwan-roc", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Thailand"] = array("slug" => "thailand", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Timor-Leste"] = array("slug" => "east-timor", 'description' => '', 'parent' => "east-asia");
	// $gv->new_categories["Vietnam"] = array("slug" => "vietnam", 'description' => '', 'parent' => "east-asia");
		
//REGION AND CHILDREN: Eastern & Central Europe
	// $gv->new_categories["Eastern & Central Europe"] = array("slug" => "eastern-central-europe", 'description' => '', 'parent' => "world");		
 
	// $gv->new_categories["Albania"] = array("slug" => "albania", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Belarus"] = array("slug" => "belarus", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Bosnia Herzegovina"] = array("slug" => "bosnia-herzegovina", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Bulgaria"] = array("slug" => "bulgaria", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Croatia"] = array("slug" => "croatia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Czech Republic"] = array("slug" => "czech-republic", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Estonia"] = array("slug" => "estonia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Hungary"] = array("slug" => "hungary", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Kosovo"] = array("slug" => "kosovo", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Latvia"] = array("slug" => "latvia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Lithuania"] = array("slug" => "lithuania", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Macedonia"] = array("slug" => "macedonia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Moldova"] = array("slug" => "moldova", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Montenegro"] = array("slug" => "montenegro", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Poland"] = array("slug" => "poland", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Romania"] = array("slug" => "romania", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Russia"] = array("slug" => "russia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Serbia"] = array("slug" => "serbia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Slovakia"] = array("slug" => "slovakia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Slovenia"] = array("slug" => "slovenia", 'description' => '', 'parent' => "eastern-central-europe");
	// $gv->new_categories["Ukraine"] = array("slug" => "ukraine", 'description' => '', 'parent' => "eastern-central-europe");
		
//REGION AND CHILDREN: Latin America
	// $gv->new_categories["Latin America"] = array("slug" => "latin-america", 'description' => '', 'parent' => "world");		
 
	// $gv->new_categories["Argentina"] = array("slug" => "argentina", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Bolivia"] = array("slug" => "bolivia", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Brazil"] = array("slug" => "brazil", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Chile"] = array("slug" => "chile", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Colombia"] = array("slug" => "colombia", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Costa Rica"] = array("slug" => "costa-rica", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Cuba"] = array("slug" => "cuba", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Dominican Republic"] = array("slug" => "dominican-republic", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Ecuador"] = array("slug" => "ecuador", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["El Salvador"] = array("slug" => "el-salvador", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Guatemala"] = array("slug" => "guatemala", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Honduras"] = array("slug" => "honduras", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Mexico"] = array("slug" => "mexico", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Nicaragua"] = array("slug" => "nicaragua", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Panama"] = array("slug" => "panama", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Paraguay"] = array("slug" => "paraguay", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Peru"] = array("slug" => "peru", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Puerto Rico (U.S.)"] = array("slug" => "puerto-rico-us", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Uruguay"] = array("slug" => "uruguay", 'description' => '', 'parent' => "latin-america");
	// $gv->new_categories["Venezuela"] = array("slug" => "venezuela", 'description' => '', 'parent' => "latin-america");

//REGION AND CHILDREN: Middle East & North Africa
	// $gv->new_categories["Middle East & North Africa"] = array("slug" => "middle-east-north-africa", 'description' => '', 'parent' => "world");
 
	// $gv->new_categories["Algeria"] = array("slug" => "algeria", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Bahrain"] = array("slug" => "bahrain", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Egypt"] = array("slug" => "egypt", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Iran"] = array("slug" => "iran", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Iraq"] = array("slug" => "iraq", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Israel"] = array("slug" => "israel", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Jordan"] = array("slug" => "jordan", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Kuwait"] = array("slug" => "kuwait", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Lebanon"] = array("slug" => "lebanon", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Libya"] = array("slug" => "libya", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Morocco"] = array("slug" => "morocco", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Oman"] = array("slug" => "oman", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Palestine"] = array("slug" => "palestine", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Qatar"] = array("slug" => "qatar", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Saudi Arabia"] = array("slug" => "saudi-arabia", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Syria"] = array("slug" => "syria", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Tunisia"] = array("slug" => "tunisia", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Turkey"] = array("slug" => "turkey", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["United Arab Emirates"] = array("slug" => "united-arab-emirates", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Western Sahara"] = array("slug" => "western-sahara", 'description' => '', 'parent' => "middle-east-north-africa");
	// $gv->new_categories["Yemen"] = array("slug" => "yemen", 'description' => '', 'parent' => "middle-east-north-africa");
		
//REGION AND CHILDREN: North America
	// $gv->new_categories["North America"] = array("slug" => "north-america", 'description' => '', 'parent' => "world");		
 
	// $gv->new_categories["Canada"] = array("slug" => "canada", 'description' => '', 'parent' => "north-america");
	// $gv->new_categories["U.S.A."] = array("slug" => "usa", 'description' => '', 'parent' => "north-america");
		
//REGION AND CHILDREN: Oceania
	// $gv->new_categories["Oceania"] = array("slug" => "oceania", 'description' => '', 'parent' => "world");		
 
	// $gv->new_categories["American Samoa"] = array("slug" => "american-samoa", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Australia"] = array("slug" => "australia", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Cook Islands"] = array("slug" => "cook-islands", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Fiji"] = array("slug" => "fiji", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Marshall Islands"] = array("slug" => "marshall-islands", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["New Caledonia"] = array("slug" => "new-caledonia", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["New Zealand"] = array("slug" => "new-zealand", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Papua New Guinea"] = array("slug" => "papua-new-guinea", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Samoa"] = array("slug" => "samoa", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Tahiti"] = array("slug" => "tahiti", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Tonga"] = array("slug" => "tonga", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Vanuatu"] = array("slug" => "vanuatu", 'description' => '', 'parent' => "oceania");
	// $gv->new_categories["Wallis & Futuna"] = array("slug" => "wallis-futuna", 'description' => '', 'parent' => "oceania");
		
//REGION AND CHILDREN: South Asia
	// $gv->new_categories["South Asia"] = array("slug" => "south-asia", 'description' => '', 'parent' => "world");
 
	// $gv->new_categories["Bangladesh"] = array("slug" => "bangladesh", 'description' => '', 'parent' => "south-asia");
	// $gv->new_categories["Bhutan"] = array("slug" => "bhutan", 'description' => '', 'parent' => "south-asia");
	// $gv->new_categories["India"] = array("slug" => "india", 'description' => '', 'parent' => "south-asia");
	// $gv->new_categories["Maldives"] = array("slug" => "maldives", 'description' => '', 'parent' => "south-asia");
	// $gv->new_categories["Nepal"] = array("slug" => "nepal", 'description' => '', 'parent' => "south-asia");
	// $gv->new_categories["Pakistan"] = array("slug" => "pakistan", 'description' => '', 'parent' => "south-asia");
	// $gv->new_categories["Sri Lanka"] = array("slug" => "sri-lanka", 'description' => '', 'parent' => "south-asia");
		
//REGION AND CHILDREN: Sub-Saharan Africa
	// $gv->new_categories["Sub-Saharan Africa"] = array("slug" => "sub-saharan-africa", 'description' => '', 'parent' => "world");		
 
	// $gv->new_categories["Angola"] = array("slug" => "angola", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Benin"] = array("slug" => "benin", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Botswana"] = array("slug" => "botswana", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Burkina Faso"] = array("slug" => "burkina-faso", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Burundi"] = array("slug" => "burundi", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Cameroon"] = array("slug" => "cameroon", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Cape Verde"] = array("slug" => "cape-verde", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Central African Republic"] = array("slug" => "central-african-republic", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Chad"] = array("slug" => "chad", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Comoros"] = array("slug" => "comoros", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Cote d'Ivoire"] = array("slug" => "cote-divoire", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["D.R. of Congo"] = array("slug" => "dr-of-congo", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Djibouti"] = array("slug" => "djibouti", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Equatorial Guinea"] = array("slug" => "equatorial-guinea", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Eritrea"] = array("slug" => "eritrea", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Ethiopia"] = array("slug" => "ethiopia", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Gabon"] = array("slug" => "gabon", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Gambia"] = array("slug" => "gambia", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Ghana"] = array("slug" => "ghana", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Guinea"] = array("slug" => "guinea", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Guinea-Bissau"] = array("slug" => "guinea-bissau", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Kenya"] = array("slug" => "kenya", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Lesotho"] = array("slug" => "lesotho", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Liberia"] = array("slug" => "liberia", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Madagascar"] = array("slug" => "madagascar", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Malawi"] = array("slug" => "malawi", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Mali"] = array("slug" => "mali", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Mauritania"] = array("slug" => "mauritania", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Mauritius"] = array("slug" => "mauritius", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Mayotte"] = array("slug" => "mayotte", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Mozambique"] = array("slug" => "mozambique", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Namibia"] = array("slug" => "namibia", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Niger"] = array("slug" => "niger", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Nigeria"] = array("slug" => "nigeria", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Republic of Congo"] = array("slug" => "republic-of-congo", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Reunion"] = array("slug" => "reunion", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Rwanda"] = array("slug" => "rwanda", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Saint Helena"] = array("slug" => "saint-helena", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Sao Tome and Principe"] = array("slug" => "sao-tome-and-principe", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Senegal"] = array("slug" => "senegal", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Seychelles"] = array("slug" => "seychelles", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Sierra Leone"] = array("slug" => "sierra-leone", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Somalia"] = array("slug" => "somalia", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Somaliland"] = array("slug" => "somaliland", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["South Africa"] = array("slug" => "south-africa", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["South Sudan"] = array("slug" => "south-sudan", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Sudan"] = array("slug" => "sudan", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Swaziland"] = array("slug" => "swaziland", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Tanzania"] = array("slug" => "tanzania", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Togo"] = array("slug" => "togo", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Uganda"] = array("slug" => "uganda", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Zambia"] = array("slug" => "zambia", 'description' => '', 'parent' => "sub-saharan-africa");
	// $gv->new_categories["Zimbabwe"] = array("slug" => "zimbabwe", 'description' => '', 'parent' => "sub-saharan-africa");
		
//REGION AND CHILDREN: Western Europe
	// $gv->new_categories["Western Europe"] = array("slug" => "western-europe", 'description' => '', 'parent' => "world");
 
	// $gv->new_categories["Austria"] = array("slug" => "austria", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Belgium"] = array("slug" => "belgium", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Cyprus"] = array("slug" => "cyprus", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Denmark"] = array("slug" => "denmark", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Finland"] = array("slug" => "finland", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["France"] = array("slug" => "france", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Germany"] = array("slug" => "germany", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Greece"] = array("slug" => "greece", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Iceland"] = array("slug" => "iceland", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Ireland"] = array("slug" => "ireland", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Italy"] = array("slug" => "italy", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Liechtenstein"] = array("slug" => "liechtenstein", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Luxembourg"] = array("slug" => "luxembourg", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Malta"] = array("slug" => "malta", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Monaco"] = array("slug" => "monaco", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Netherlands"] = array("slug" => "netherlands", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Norway"] = array("slug" => "norway", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Portugal"] = array("slug" => "portugal", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["San Marino"] = array("slug" => "san-marino", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Spain"] = array("slug" => "spain", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Sweden"] = array("slug" => "sweden", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Switzerland"] = array("slug" => "switzerland", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["United Kingdom"] = array("slug" => "united-kingdom", 'description' => '', 'parent' => "western-europe");
	// $gv->new_categories["Vatican City"] = array("slug" => "vatican-city", 'description' => '', 'parent' => "western-europe");
		

/**
 * Define term migrations for the term migration system
 * Depends on gv-plugin being active
 * see gv-term-migration.php in gv-plugin
 */
function advox_define_term_migrations() {

	/**
	 * Set up base arguments for topic merges
	 * In these cases we are deleting source term 
	 */
	$args = 	array(
		'source_term' => '',
		'source_taxonomy' => 'category',
		'target_term' => '', 
		'target_taxonomy' => 'category', 
		'delete_source_term' => true,	
	);

	/**
	 * Single category merge+forward+delete (deleting source category)
	 */
	
	/**
	 * TOPIC CLEANUP
	 */
	// Merge duplicate opinion-type into type>opinion
	$args['source_term'] = 'opinion-type';
	$args['target_term'] = 'opinion';	
	gv_define_term_migration($args);
	// Migrate freedom-of-expression->free-expression
	$args['source_term'] = 'freedom-of-expression';
	$args['target_term'] = 'free-expression';	
	gv_define_term_migration($args);
	// Migrate arrest and harassmetn to legal threats
	$args['source_term'] = 'arrest-and-harassment';
	$args['target_term'] = 'legal-threats';	
	gv_define_term_migration($args);
	
	/**
	 * MESSY COUNTRY AUTO-TAG CLEANUP
	 */
	// CONVERT iraq-countries to country-of-iraq then to true iraq
	$args['source_term'] = 'iraq-countries';
	$args['target_term'] = 'country-of-iraq';	
	gv_define_term_migration($args);
	$args['source_term'] = 'country-of-iraq';
	$args['target_term'] = 'iraq';	
	gv_define_term_migration($args);
	// CONVERT morocco-middle-east-north-africa to country-of-morocco then to true morocco
	$args['source_term'] = 'morocco-middle-east-north-africa';
	$args['target_term'] = 'country-of-morocco';	
	gv_define_term_migration($args);
	$args['source_term'] = 'country-of-morocco';
	$args['target_term'] = 'morocco';	
	gv_define_term_migration($args);
	// CONVERT iraq-countries to country-of-oman then to true oman
	$args['source_term'] = 'oman-countries';
	$args['target_term'] = 'country-of-oman';	
	gv_define_term_migration($args);
	$args['source_term'] = 'country-of-oman';
	$args['target_term'] = 'oman';	
	gv_define_term_migration($args);
	// CONVERT australia-countries to country-of-australia then to true oman
	$args['source_term'] = 'australia-countries';
	$args['target_term'] = 'country-of-australia';	
	gv_define_term_migration($args);
	$args['source_term'] = 'country-of-australia';
	$args['target_term'] = 'australia';	
	gv_define_term_migration($args);
	
	/**
	 * Misnamed Country Cleanup
	 * 
	 * Regions and Countries -> DELETE
	 */
	$args['source_term'] = 'democratic-republic-of-congo';
	$args['target_term'] = 'republic-of-congo';	
	gv_define_term_migration($args);
	$args['source_term'] = 'europe';
	$args['target_term'] = 'western-europe';
	gv_define_term_migration($args);
	$args['source_term'] = 'hong-kong';
	$args['target_term'] = 'hong-kong-china';
	gv_define_term_migration($args);
	$args['source_term'] = 'macau';
	$args['target_term'] = 'macau-china';
	gv_define_term_migration($args);
	$args['source_term'] = 'moldavia';
	$args['target_term'] = 'moldova';
	gv_define_term_migration($args);
	$args['source_term'] = 'burma';
	$args['target_term'] = 'myanmar-burma';
	gv_define_term_migration($args);
	$args['source_term'] = 'myanmar';
	$args['target_term'] = 'myanmar-burma';
	gv_define_term_migration($args);
	$args['source_term'] = 'southeast-asia';
	$args['target_term'] = 'south-asia';
	gv_define_term_migration($args);
	$args['source_term'] = 'taiwan';
	$args['target_term'] = 'taiwan-roc';
	gv_define_term_migration($args);
	$args['source_term'] = 'the-netherlands';
	$args['target_term'] = 'netherlands';
	gv_define_term_migration($args);
	$args['source_term'] = 'united-states-countries';
	$args['target_term'] = 'usa';
	gv_define_term_migration($args);
	
	
	/**
	 * Delete source and forward it to target without re-assigning posts 
	 */
	$args['skip_post_assignment'] = true;
	
	// Latin America and Caribbean -> Delete forward Latin America
	$args['source_term'] = 'latin-america-and-caribbean';
	$args['target_term'] = 'latin-america';
	gv_define_term_migration($args);
	
	// UNSET skip_post_assignments to avoid it affecting anything after by accident
	unset($args['skip_post_assignment']);
	
	/**
	 * Set up base arguments we need for parent-switching AND assignment of all post to the parent in the process
	 * Don't delete source term
	 * Use new parent (region) as both target_term and source_new_parent so that posts get assigned to it
	 * OTHERWISE don't set target_term OR target_taxonomy
	 */
	$args = 	array(
		'source_term' => '',
		'source_taxonomy' => 'category',
		'target_term' => '', 
//		'target_taxonomy' => 'category', 
		'source_new_parent' => '',
	);

	/**
	 * Move opinion into type
	 */
	$args['source_new_parent'] = 'type';	
	$args['source_term'] = 'opinion';
	gv_define_term_migration($args);

	/**
	 * Convert existing caribbean countries
	 */
	$args['source_new_parent'] = 'caribbean';
	
	$args['source_term'] = 'saint-lucia';
	gv_define_term_migration($args);
	
	/**
	 * Convert existing CAA countries
	 */
	$args['source_new_parent'] = 'central-asia-caucasus';
	
	$args['source_term'] = 'afghanistan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'armenia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'azerbaijan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'georgia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'kazakhstan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'kyrgyzstan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'tajikistan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'uzbekistan';
	gv_define_term_migration($args);	

	/**
	 * Convert existing countries in east asia
	 */
	$args['source_new_parent'] = 'east-asia';
	
	$args['source_term'] = 'brunei';
	gv_define_term_migration($args);	
	$args['source_term'] = 'cambodia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'china';
	gv_define_term_migration($args);	
	$args['source_term'] = 'indonesia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'japan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'malaysia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'philippines';
	gv_define_term_migration($args);	
	$args['source_term'] = 'singapore';
	gv_define_term_migration($args);	
	$args['source_term'] = 'south-korea';
	gv_define_term_migration($args);	
	$args['source_term'] = 'taiwan-roc';
	gv_define_term_migration($args);	
	$args['source_term'] = 'thailand';
	gv_define_term_migration($args);	
	$args['source_term'] = 'east-timor';
	gv_define_term_migration($args);	
	$args['source_term'] = 'vietnam';
	gv_define_term_migration($args);	

	
	/**
	 * Convert existing countries in eastern-central-europe
	 */
	$args['source_new_parent'] = 'eastern-central-europe';
	
	$args['source_term'] = 'belarus';
	gv_define_term_migration($args);	
	$args['source_term'] = 'bulgaria';
	gv_define_term_migration($args);	
	$args['source_term'] = 'croatia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'hungary';
	gv_define_term_migration($args);	
	$args['source_term'] = 'macedonia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'moldova';
	gv_define_term_migration($args);	
	$args['source_term'] = 'poland';
	gv_define_term_migration($args);	
	$args['source_term'] = 'russia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'serbia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'slovakia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'ukraine';
	gv_define_term_migration($args);	
	
	/**
	 * Convert existing countries in latin-america
	 */
	$args['source_new_parent'] = 'latin-america';
	
	$args['source_term'] = 'argentina';
	gv_define_term_migration($args);		
	$args['source_term'] = 'brazil';
	gv_define_term_migration($args);		
	$args['source_term'] = 'chile';
	gv_define_term_migration($args);		
	$args['source_term'] = 'colombia';
	gv_define_term_migration($args);		
	$args['source_term'] = 'costa-rica';
	gv_define_term_migration($args);		
	$args['source_term'] = 'cuba';
	gv_define_term_migration($args);		
	$args['source_term'] = 'ecuador';
	gv_define_term_migration($args);		
	$args['source_term'] = 'guatemala';
	gv_define_term_migration($args);		
	$args['source_term'] = 'mexico';
	gv_define_term_migration($args);		
	$args['source_term'] = 'paraguay';
	gv_define_term_migration($args);		
	$args['source_term'] = 'peru';
	gv_define_term_migration($args);		
	$args['source_term'] = 'uruguay';
	gv_define_term_migration($args);		
	$args['source_term'] = 'venezuela';
	gv_define_term_migration($args);		

	/**
	 * Convert existing countries in middle-east-north-africa
	 */
	// FIRST CHANGE PARENT OF MENA ITSELF
	$args['source_new_parent'] = 'world';
	$args['source_term'] = 'middle-east-north-africa';
	gv_define_term_migration($args);	
	
	// SPECIAL: MIGRATE IRAQ FROM iraq-countries TO iraq
	$args['source_new_parent'] = 'middle-east-north-africa';
	
	$args['source_term'] = 'iran';
	gv_define_term_migration($args);	
	$args['source_term'] = 'israel';
	gv_define_term_migration($args);	
	$args['source_term'] = 'kuwait';
	gv_define_term_migration($args);	
	$args['source_term'] = 'libya';
	gv_define_term_migration($args);	
	$args['source_term'] = 'turkey';
	gv_define_term_migration($args);	
	$args['source_term'] = 'united-arab-emirates';
	gv_define_term_migration($args);	
	$args['source_term'] = 'western-sahara';
	gv_define_term_migration($args);	
	$args['source_term'] = 'yemen';
	gv_define_term_migration($args);	
	
	/**
	 * Convert existing countries in north-america
	 */
	// FIRST CHANGE PARENT OF north-america ITSELF
	$args['source_new_parent'] = 'world';
	$args['source_term'] = 'north-america';
	gv_define_term_migration($args);	
	
	$args['source_new_parent'] = 'north-america';
	
	$args['source_term'] = 'canada';
	gv_define_term_migration($args);	
	$args['source_term'] = 'usa';
	gv_define_term_migration($args);	
	
	
	/**
	 * Convert existing countries in oceania
	 */
	$args['source_new_parent'] = 'oceania';
	
	$args['source_term'] = 'fiji';
	gv_define_term_migration($args);		

	/**
	 * Convert existing countries in south-asia
	 */
	// FIRST CHANGE PARENT OF south-asia ITSELF
	$args['source_new_parent'] = 'world';
	$args['source_term'] = 'south-asia';
	gv_define_term_migration($args);	
	
	$args['source_new_parent'] = 'south-asia';
	
	$args['source_term'] = 'bangladesh';
	gv_define_term_migration($args);	
	$args['source_term'] = 'india';
	gv_define_term_migration($args);	
	$args['source_term'] = 'maldives';
	gv_define_term_migration($args);	
	$args['source_term'] = 'nepal';
	gv_define_term_migration($args);	
	$args['source_term'] = 'pakistan';
	gv_define_term_migration($args);	

		
	/**
	 * Convert existing countries in sub-saharan-africa
	 */
	// FIRST CHANGE PARENT OF sub-saharan-africa ITSELF
	$args['source_new_parent'] = 'world';
	$args['source_term'] = 'sub-saharan-africa';
	gv_define_term_migration($args);	
	
	$args['source_new_parent'] = 'sub-saharan-africa';
	
	$args['source_term'] = 'angola';
	gv_define_term_migration($args);	
	$args['source_term'] = 'burundi';
	gv_define_term_migration($args);	
	$args['source_term'] = 'cameroon';
	gv_define_term_migration($args);	
	$args['source_term'] = 'chad';
	gv_define_term_migration($args);	
	$args['source_term'] = 'cote-divoire';
	gv_define_term_migration($args);	
	$args['source_term'] = 'ethiopia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'gambia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'guinea';
	gv_define_term_migration($args);	
	$args['source_term'] = 'guinea-bissau';
	gv_define_term_migration($args);	
	$args['source_term'] = 'kenya';
	gv_define_term_migration($args);	
	$args['source_term'] = 'madagascar';
	gv_define_term_migration($args);	
	$args['source_term'] = 'mali';
	gv_define_term_migration($args);	
	$args['source_term'] = 'mauritania';
	gv_define_term_migration($args);	
	$args['source_term'] = 'mozambique';
	gv_define_term_migration($args);	
	$args['source_term'] = 'nigeria';
	gv_define_term_migration($args);	
	$args['source_term'] = 'senegal';
	gv_define_term_migration($args);	
	$args['source_term'] = 'somalia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'south-africa';
	gv_define_term_migration($args);	
	$args['source_term'] = 'sudan';
	gv_define_term_migration($args);	
	$args['source_term'] = 'tanzania';
	gv_define_term_migration($args);	
	$args['source_term'] = 'zambia';
	gv_define_term_migration($args);	
	$args['source_term'] = 'zimbabwe';
	gv_define_term_migration($args);	
	
	/**
	 * Convert existing countries in western europe
	 */
	$args['source_new_parent'] = 'western-europe';
	
	$args['source_term'] = 'denmark';
	gv_define_term_migration($args);	
	$args['source_term'] = 'france';
	gv_define_term_migration($args);	
	$args['source_term'] = 'germany';
	gv_define_term_migration($args);	
	$args['source_term'] = 'greece';
	gv_define_term_migration($args);	
	$args['source_term'] = 'iceland';
	gv_define_term_migration($args);	
	$args['source_term'] = 'italy';
	gv_define_term_migration($args);	
	$args['source_term'] = 'monaco';
	gv_define_term_migration($args);	
	$args['source_term'] = 'portugal';
	gv_define_term_migration($args);	
	$args['source_term'] = 'spain';
	gv_define_term_migration($args);	
	$args['source_term'] = 'sweden';
	gv_define_term_migration($args);	
	$args['source_term'] = 'united-kingdom';
	gv_define_term_migration($args);	
	
	/**
	 * Convert existing countries in 
	 */
//	$args['source_new_parent'] = '';
//	
//	$args['source_term'] = '';
//	gv_define_term_migration($args);	
	
		
}
add_action('init', 'advox_define_term_migrations');

/**
 * Filter the default stats report with specific news theme reports.
 *
 * @see gv_stats_factory->load_reports() which runs the gv_stats_default_reports filter
 * @param array $reports Slugs of stats report classes extended from gv_stats_report
 * @return array Filter list of report class names
 */
function advox_stats_reports($reports) {
	// Load the theme reports
	include_once __DIR__ . '/gv-advox-stats-reports.php';

	// Merge in an array of our cutom reports for special category subsets
	$reports = array_merge($reports, array(
		'gv_stats_report_countries',
		'gv_stats_report_regions',
		'gv_stats_report_topics',
	));

	/**
	 * Remove the default category report which is pretty useless for this site
	 */
	$reports = array_diff($reports, array(
		'gv_stats_report_categories',
	));

	return $reports;
}
add_action('gv_stats_default_reports', 'advox_stats_reports');

/**
 * Register the specialized stats pages advox needs gv_stats to display in the admin.
 * 
 * Note priority 100 so it comes after both gv_lingua_init (priority 10) and
 * project_theme_register_stats_pages (priority 99) on init hook
 */
function advocacy_theme_register_stats_pages() {
	global $gv_stats;
	
	if (isset($gv_stats) AND is_object($gv_stats)) :

		/**
		 * Custom stats pages for Advox regions, countries and topics
		 */
		$gv_stats->add_stats_page_type(array(
			'page_slug' => 'gv_stats_regions_old', 
			'page_title' => 'Region Category Stats', 
			'menu_title' => 'Region Stats', 
			'object_label' => 'Region',
			'description' => "Note: These numbers reflect all posts in the region category or any of it's child countries, rather than just posts with the indicated category. This matches the theme behavior, where region archives show posts in child categories even if the region category isn't attached to the post. <br /> Note: Any given post might be in several of these categories, so adding up the various sections will likely give you a higher number than the actual posts for the period.<br> Note: The chosen intersector will organize posts into those that are also in the category (or it's children) and those that aren't. ",
			'data_skeleton_callback' => array('callback' => array(&$gv_stats, 'category_children_data_skeleton'), 'arg' =>array('parent'=>gv_slug2cat('world'))),
			'query_callback' => array('callback' => array(&$gv_stats, 'category_children_stats_query'), 'arg'=>array('parent' => gv_slug2cat('world'), 'include_grandchildren' => true)),
		));
		$gv_stats->add_stats_page_type(array(
			'page_slug' => 'gv_stats_countries_old', 
			'page_title' => 'Country Category Stats', 
			'menu_title' => 'Country Stats', 
			'object_label' => 'Country',
			'description' => "Note: Any given post might be in several of these categories, so adding up the various sections will likely give you a higher number than the actual posts for the period. <br> Note: The chosen intersector will organize posts into those that are also in the category (or it's children) and those that aren't.",
			'data_skeleton_callback' => array('callback' => array(&$gv_stats, 'category_children_data_skeleton'), 'arg' => array('parent'=>gv_slug2cat('world'), 'grandchildren_only' => true)),
			'query_callback' => array('callback' => array(&$gv_stats, 'category_children_stats_query'), 'arg'=>array('parent' => gv_slug2cat('world'), 'grandchildren_only' => true)),
		));	
		$gv_stats->add_stats_page_type(array(
			'page_slug' => 'gv_stats_topics_old', 
			'page_title' => 'Topic Category Stats', 
			'menu_title' => 'Topic Stats', 
			'object_label' => 'Topic',
			'description' => "Note: Any given post might be in several of these categories, so adding up the various sections will likely give you a higher number than the actual posts for the period.<br> Note: The chosen intersector will organize posts into those that are also in the category (or it's children) and those that aren't.",
			'data_skeleton_callback' => array('callback' => array(&$gv_stats, 'category_children_data_skeleton'), 'arg' =>array('parent'=>gv_slug2cat('topics'))),
			'query_callback' => array('callback' => array(&$gv_stats, 'category_children_stats_query'), 'arg'=>array('parent' => gv_slug2cat('topics'))),
		));
		/**
		 * Register our special stats pages. 
		 * Defaults should already be registered by GV Project Theme in project_theme_register_stats_pages()
		 */
		$gv_stats->register_stats_pages(array(
			// unregister generic categories because we cover it with specialist sections below
			'gv_stats_categories_old' => false,
			'gv_stats_active_users_local_old' => array('1_month_ago', '2_months_ago', '3_months_ago', 'past_year', 'last_year', 'all_time'), // DEBUGGING ONLY TEMPORARY
			'gv_stats_regions_old' => array('1_month_ago', 'past_year'),
			'gv_stats_countries_old' => array('1_month_ago', 'past_year'),			
			'gv_stats_topics_old' => array('1_month_ago', 'past_year'),
		));		
	endif;
}
add_action('init', 'advocacy_theme_register_stats_pages', 100);
	
endif; // is_object($gv)

?>