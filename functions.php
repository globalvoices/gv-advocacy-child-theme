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
	 * Define the hierarchical structure of the taxonomy by its parents
	 */
	$gv->taxonomy_outline = array(
		'countries' => 1,
		'topics' => 1,
		'special' => 1,
		'type' => 1,
	);

	/**
	 * Define the hierarchical

	/**
	 *  Define the order of importance of the taxonomies (all taxonomy slugs should work...)
	 */
	$gv->taxonomy_priority = array ('countries', 'special', 'topics', 'type');

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