<?php
/**
 * Post Metabox options.
 *
 * @author     ThemeFusion
 * @copyright  (c) Copyright by ThemeFusion
 * @link       https://avada.com
 * @package    Avada
 * @subpackage Core
 */

/**
 * Post page settings
 *
 * @param array $sections An array of our sections.
 * @return array
 */
function avada_page_options_tab_seo( $sections ) {
	$settings_link = '<a href="' . Avada()->settings->get_setting_link( 'meta_tags_separator' ) . '" target="_blank" rel="noopener noreferrer">' . Avada()->settings->get( 'meta_tags_separator' ) . '</a>';
	
	$sections['seo'] = [
		'label'    => esc_html__( 'SEO', 'Avada' ),
		'id'       => 'seo',
		'alt_icon' => 'fusiona-feather',
		'fields'   => [

			'seo_title'        => [
				'id'          => 'seo_title',
				'type'        => 'text',
				'label'       => esc_html__( 'SEO Title', 'Avada' ),
				/* translators: Option value (defaults). */
				'description' => sprintf( esc_html__( 'Insert the SEO title. Available placeholders are [site_title], [site_tagline], [post_title], and [separator]. Separator is currently set to: %s.', 'Avada' ), $settings_link ),
				'default'     => '',

			], 
			'meta_description' => [
				'id'          => 'meta_description',
				'type'        => 'textarea',
				/* translators: Option value (defaults). */
				'label'       => esc_html__( 'Meta Descriptions', 'Avada' ),
				'description' => sprintf( esc_html__( 'Insert your post meta description. Available placeholders are [site_title], [site_tagline], [post_title], and [separator]. Separator is currently set to: %s.', 'Avada' ), $settings_link ),
			],
		],
	];

	return $sections;
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
