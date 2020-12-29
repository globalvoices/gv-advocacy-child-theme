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

endif; // is_object($gv)

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