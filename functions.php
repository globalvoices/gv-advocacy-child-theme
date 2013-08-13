<?php
/**
 * Functions.php file for GV Advocacy Child Theme
 *
 * Assumes parent is gv-project-theme.
 * This code will run before the functions.php in that theme.
 */

if (is_object($gv)) :
	// Special map directory for gmapez
	$gv->dir['map'] = "/wp-content/map/";

	/**
	 * Define an image to show in the header.
	 * Project theme generic has none, so it will use site title
	 */
	$gv->settings['header_img'] = get_bloginfo('template_url') . '/images/advocacy-temptitle2.png';

	/**
	 * Filter the apple touch icon to be an RV logo
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
		return gv_get_dir('theme_images') ."advocacy-logo-square-365.png";
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
	add_filter('gv_og_image', 'gvadvocacy_theme_gv_og_image');
	
	/**
	 * Define the hierarchical structure of the taxonomy by its parents
	 */
	$gv->taxonomy_outline = array(
		'countries' => 1,
		'topics' => 1,
		'special' => 1,
		'type' => 1,
	);

	/**
	 * Define Categories to be inserted into post data before returning content for translation during fetch
	 * @see gv_lingua::reply_to_ping()
	 */
	$gv->lingua_site_categories[] = 'gv-advocacy';

	/**
	 *  Define the order of importance of the taxonomies (all taxonomy slugs should work...)
	 */
	$gv->taxonomy_priority = array ('countries', 'special', 'topics', 'type');

	/**
	 * Register "public taxonomies" for gv_taxonomies system to display automatically on posts
	 * 
	 * Should completely replace old backbone/taxonomy_priority system eventually
	 */
	function gv_advox_register_taxonomies($param) {

		// Unregister defaults as they aren't useful for this site
		gv_unregister_public_taxonomy('category');
		gv_unregister_public_taxonomy('post_tag');

		// Register REGIONS as terms with parent regions-countries
		$countries_category_id = gv_slug2cat('regions-countries');
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'regions',
			'taxonomy' => 'category',
			'parent' => $countries_category_id,
			'labels' => array(
				'name' => _lingua('regions'), 
			),		
		));
		
		// Register Countries as terms with grandparent regions-countries
		// NOTE: Only applies to some right now. 
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'countries',
			'taxonomy' => 'category',
			'grandparent' => $countries_category_id,
			'labels' => array(
				'name' => _lingua('countries'), 
			),		
		));

		// Register SPECIAL as terms with parent SPECIAL
		$special_category_id = gv_slug2cat('special');
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'special',
			'taxonomy' => 'category',
			'parent' => $special_category_id,
			'labels' => array(
				'name' => _lingua('special_topics'), 
			),			
		));
		
		// Register TOPICS as terms with parent TOPICS
		$topics_category_id = gv_slug2cat('topics');
		gv_register_public_taxonomy('category', array(
			'subtaxonomy_slug' => 'topics',
			'taxonomy' => 'category',
			'parent' => $topics_category_id,
			'labels' => array(
				'name' => _lingua('topics'), 
			),	
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
	 * Filter gv_post_archive_truncate_count limit to show more posts on homepage
	 * @param type $limit
	 * @param type $args
	 * @return int
	 */
	function gv_advox_gv_project_theme_home_truncate_count($truncate_count) {
		return 4;
	}
	add_filter('gv_project_theme_home_truncate_count', 'gv_advox_gv_project_theme_home_truncate_count', 10);
	
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
	 */
	$gv->site_description = "A project of Global Voices Online, we seek to build a global anti-censorship network of bloggers and online activists dedicated to protecting freedom of expression and free access to information online.";
	
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
	 * Define badgeset arrays for use with [gvbadges id="$slug"] shortcode
	 */

	/**
	 * General GV Badges - Based on lingua site slug
	 */
	$gv->badgesets['advocacy_general'] = array(
		'label' => "Global Voices Advocacy - Defending free speech online",
		'url' => "http://advocacy.globalvoicesonline.org/",
		'css' => "margin:3px 0;",
		'files' => array(
			'http://img.globalvoicesonline.org/Badges/advocacy/gv-advocacy-badge-125.gif',
			'http://img.globalvoicesonline.org/Badges/advocacy/gv-advocacy-badge-150.gif',
			'http://img.globalvoicesonline.org/Badges/advocacy/gv-advocacy-badge-200.gif',
			'http://img.globalvoicesonline.org/Badges/advocacy/gv-advocacy-badge-400.gif'
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

endif; // is_object($gv)

?>