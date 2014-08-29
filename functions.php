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
	$gv->settings['header_img'] = get_bloginfo('stylesheet_directory') . '/images/advox-logo-2014-dark-600.png';
//	$gv->settings['header_img'] = get_bloginfo('stylesheet_directory') . '/images/advox-logo-2014-white-600.png';

	/**
	 * Enable Featured posts - Tells GV Query Manipulation to prefetch featured posts before main loop and exclude their ids.
	 * @see gv_load_featured_posts()
	 */
	$gv->use_featured_posts = true;	
	
	/**
	 * Filter the favicon directory used by gv_display_head_icons()
	 * 
	 * @param string $dir Default directory (no trailing /) to find favicons in
	 * @return string desired directory (no trailing /)
	 */
	function gvadvocacy_theme_gv_favicon_dir($dir) {
		return 'https://s3.amazonaws.com/static.globalvoices/img/tmpl/favicon-advox';
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

/**
 * Register CSS variants specific to the GV News theme
 * 
 * Attached to 'wp' action so as to come before wp_head where gv_output_css_variants acts
 * 
 * @see gv_add_css_variant() which is used to register variants
 * @see gv_output_css_variants() which echos out the CSS of variants activated by ?gv_css_variant=$variant_label
 */
function gv_advox_css_variants() {
	
	/**
	 * Big red header
	 */
	gv_add_css_variant(array(
		'label' => 'red_header',
		'css' => "
#MEDIUM-RED-GRADIENT,
.header-container,
.header-nav-bar-container,
.medium-red-gradient {
	background: #D10006;
}
#WHITE-TEXT,
.header-container a,
.header-mission-statement,
.header-tools .menu-toggle,
.white-text {
	color: #fff; 
}
#DARK-RED-TEXT,
.header-container .toggle-menu-content a,
.header-container .menu-opened .menu-toggle,
.dark-red-text {
	color: #9d0106;
	}
#MEDIUM-RED-TEXT,
.header-container .toggle-menu-content a:hover,
.warning a   {
	color: #c80005;
	}
#MEDIUM-RED-HIGHLIGHT-BORDER,
.header-nav-bar li,
.header-nav-bar-container input,
.header .site-subscribe-icons,
.medium-red-hightlight-border {
	border-color: #BC0005;
}
#RED-TEXT-SHADOW,
#header-menu a,
.header-container,
.red-text-shadow {
	text-shadow: 1px 1px 2px #891818;
	}
#NO-TEXT-SHADOW,
.header-container .menu-opened .toggle-menu-heading,
.header-container .toggle-menu-content,
.no-text-shadow {
	text-shadow: none;
}

.header-container .site-search input[type='submit'] {
	background-color: #800205;
}
.header-container .site-search input {
	outline: 1px solid #800205;
}

#BIGGER-TEXT,
.menu-item a,
.bigger-text {
	font-size: 120%;
	text-transform: uppercase;
}
.header-nav-bar .menu li {
	padding-top: 6px;
	padding-top: .375rem;
	padding-bottom: 6px;
	padding-bottom: .375rem;
}

@media
/*only screen and (max-width: 960px) {*/
only screen and (max-width: 1008px) {
	.menu-item a {
		font-size: 90%;
	}
}
		",
	));
	
	/**
	 * White header variant to disable the red_header one. See gv_advox_replace_red_logo
	 */
	gv_add_css_variant(array(
		'label' => 'white_header',
		'css' => "",
	));
	
	/**
	 * White header variant with white menu bar!
	 */
	gv_add_css_variant(array(
		'label' => 'white_header_white_stripe',
		'css' => "

.header-nav-bar-container {
	background: transparent;
}
#DARK-RED-TEXT,
.header-nav-bar-container,
#header-menu a,
#header-menu a:hover,
.dark-red-text {
	color: #c80005;
	text-shadow: none;
	}
.header-nav-bar .menu li {
	padding: 0;
	font-size: 110%;
	text-transform: uppercase;
}
		
		",
	));
	/**
	 * Scarlet header just different shade of red
	 */
	gv_add_css_variant(array(
		'label' => 'scarlet_header',
		'css' => "

#MEDIUM-RED-GRADIENT,
.header-container,
.header-nav-bar-container,
.medium-red-gradient {
	background: #E92E00;
}
		
		",
	));
	
	/**
	 * Hide header mission statemtn
	 */
	gv_add_css_variant(array(
		'label' => 'hide_mission_statement',
		'css' => "
#header-mission-statement {display:none;}	
		",
		
	));
	

}
add_action('wp', 'gv_advox_css_variants');
	
/**
 * Red Header variant: jQuery to replace default header image
 * 
 * Makes it so that if red_header CSS variant is active the header image is automatically
 * replced with an all-white version. 
 * 
 * DELETE when the variant is no longer needed.
 */
function gv_advox_replace_red_logo() {
	
	/**
	 * TEMPORARY: keep it emabled all the time, unless white_header is enabled
	 */
	if (!gv_is_active_css_variant('white_header') AND !gv_is_active_css_variant('white_header_white_stripe'))
		gv_activate_css_variant('red_header');
	
	/**
	 * If red header is active replace the logo with a white version
	 */
	if (gv_is_active_css_variant('red_header')) :

		$white_icon_url = get_stylesheet_directory_uri() . '/images/advox-logo-2014-white-600.png';

		echo "
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#logo').attr('src', '$white_icon_url');
			console.log('test');
	});
</script>
		";
	endif;
}
add_action('wp_head', 'gv_advox_replace_red_logo');
	
endif; // is_object($gv)

?>