<?php

/**
 * country categories variant of gv_stats_report_category_children
 */
class gv_stats_report_country_category_post_counts extends gv_stats_report_category_children {
	public $slug = 'gv_stats_report_country_category_post_counts';
	public $menu_title = 'Country Category Posts';
	public $page_title = 'Country Category Post Counts';
	public $page_slug = 'gv_stats_report_country_category_post_counts';

	/**
	 * Customized ->query to set the desired term as an argument while requesting parent::query($args)
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function query(array $args = array()) {
		$args = array_merge($args, array(
			'parent' => gv_slug2cat('world'),
			'grandchildren_only' => true,
		));

		return parent::query($args);
	}
}

/**
 * language categories variant of gv_stats_report_category_children
 */
class gv_stats_report_language_category_post_counts extends gv_stats_report_category_children {
	public $slug = 'gv_stats_report_language_category_post_counts';
	public $menu_title = 'Language Category Posts';
	public $page_title = 'Language Category Post Counts';
	public $page_slug = 'gv_stats_report_language_category_post_counts';

	/**
	 * Customized ->query to set the desired term as an argument while requesting parent::query($args)
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function query(array $args = array()) {
		$args = array_merge($args, array(
			'parent' => gv_slug2cat('languages')
		));

		return parent::query($args);
	}
}

/**
 * region categories variant of gv_stats_report_category_children
 */
class gv_stats_report_region_category_post_counts extends gv_stats_report_category_children {
	public $slug = 'gv_stats_report_region_category_post_counts';
	public $notes = "Note: These numbers reflect all posts in the region category or any of it's child countries, rather than just posts with the indicated category. This matches the theme behavior, where region archives show posts in child categories even if the region category isn't attached to the post. <br /> Note: Any given post might be in several of these categories, so adding up the various sections will likely give you a higher number than the actual posts for the period.<br> Note: The chosen intersector will organize posts into those that are also in the category (or it's children) and those that aren't.";
	public $menu_title = 'Region Category Posts';
	public $page_title = 'Region Category Post Counts';
	public $page_slug = 'gv_stats_report_region_category_post_counts';

	/**
	 * Customized ->query to set the desired term as an argument while requesting parent::query($args)
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function query(array $args = array()) {
		$args = array_merge($args, array(
			'parent' => gv_slug2cat('world'),
			'include_grandchildren' => true,
		));

		return parent::query($args);
	}
}

/**
 * special categories variant of gv_stats_report_category_children
 */
class gv_stats_report_special extends gv_stats_report_category_children {
	public $slug = 'gv_stats_report_special';
	public $description = "Note: Any given post might be in several of these categories, so adding up the various sections will likely give you a higher number than the actual posts for the period.<br> Note: The chosen intersector will organize posts into those that are also in the category (or it's children) and those that aren't.";
	public $menu_title = 'Special Topic Stats';
	public $page_title = 'Special Topic Category Stats';
	public $page_slug = 'gv_stats_special';

	/**
	 * Customized ->query to set the desired term as an argument while requesting parent::query($args)
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function query(array $args = array()) {
		$args = array_merge($args, array(
			'parent' => gv_slug2cat('special')
		));

		return parent::query($args);
	}
}

/**
 * topic categories variant of gv_stats_report_category_children
 */
class gv_stats_report_topic_category_post_counts extends gv_stats_report_category_children {
	public $slug = 'gv_stats_report_topic_category_post_counts';
	public $menu_title = 'Topic Category Posts';
	public $page_title = 'Topic Category Post Counts';
	public $page_slug = 'gv_stats_report_topic_category_post_counts';

	/**
	 * Customized ->query to set the desired term as an argument while requesting parent::query($args)
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function query(array $args = array()) {
		$args = array_merge($args, array(
			'parent' => gv_slug2cat('topics')
		));

		return parent::query($args);
	}
}