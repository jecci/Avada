<?php
/**
 * Add an element to fusion-builder.
 *
 * @package fusion-builder
 * @since 3.2
 */

if ( fusion_is_element_enabled( 'fusion_tb_woo_additional_info' ) ) {

	if ( ! class_exists( 'FusionTB_Woo_Additional_Info' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @since 3.2
		 */
		class FusionTB_Woo_Additional_Info extends Fusion_Woo_Component {

			/**
			 * An array of the shortcode defaults.
			 *
			 * @access protected
			 * @since 3.2
			 * @var array
			 */
			protected $defaults;

			/**
			 * The internal container counter.
			 *
			 * @access private
			 * @since 3.2
			 * @var int
			 */
			private $counter = 1;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 3.2
			 */
			public function __construct() {
				parent::__construct( 'fusion_tb_woo_additional_info' );
				add_filter( 'fusion_attr_fusion_tb_woo_additional_info-shortcode', [ $this, 'attr' ] );

				// Ajax mechanism for live editor.
				add_action( 'wp_ajax_get_fusion_tb_woo_additional_info', [ $this, 'ajax_render' ] );
			}


			/**
			 * Check if component should render
			 *
			 * @access public
			 * @since 3.2
			 * @return boolean
			 */
			public function should_render() {
				return is_singular();
			}

			/**
			 * Gets the default values.
			 *
			 * @static
			 * @access public
			 * @since 3.2
			 * @return array
			 */
			public static function get_element_defaults() {
				$fusion_settings = awb_get_fusion_settings();
				return [
					'show_tab_title'                   => 'yes',
					'title_size'                       => 'h3',

					// Element margin.
					'margin_top'                       => '',
					'margin_right'                     => '',
					'margin_bottom'                    => '',
					'margin_left'                      => '',

					// Cell padding.
					'cell_padding_top'                 => '',
					'cell_padding_right'               => '',
					'cell_padding_bottom'              => '',
					'cell_padding_left'                => '',

					'table_cell_backgroundcolor'       => '',
					'heading_cell_backgroundcolor'     => '',

					// Heading styles.
					'heading_color'                    => '',
					'fusion_font_family_heading_font'  => '',
					'fusion_font_variant_heading_font' => '',
					'heading_font_size'                => '',
					'heading_text_transform'           => '',
					'heading_line_height'              => '',
					'heading_letter_spacing'           => '',

					// Text styles.
					'text_color'                       => '',
					'fusion_font_family_text_font'     => '',
					'fusion_font_variant_text_font'    => '',
					'text_font_size'                   => '',
					'text_text_transform'              => '',
					'text_line_height'                 => '',
					'text_letter_spacing'              => '',

					'border_color'                     => '',

					'hide_on_mobile'                   => fusion_builder_default_visibility( 'string' ),
					'class'                            => '',
					'id'                               => '',
					'animation_type'                   => '',
					'animation_direction'              => 'down',
					'animation_speed'                  => '0.1',
					'animation_delay'                  => '',
					'animation_offset'                 => $fusion_settings->get( 'animation_offset' ),
					'animation_color'                  => '',

					'responsive_typography'            => 0.0 < $fusion_settings->get( 'typography_sensitivity' ),
				];
			}

			/**
			 * Render for live editor.
			 *
			 * @static
			 * @access public
			 * @since 3.2
			 * @param array $defaults An array of defaults.
			 * @return void
			 */
			public function ajax_render( $defaults ) {
				global $product, $post;
				check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

				$live_request = false;

				// From Ajax Request.
				if ( isset( $_POST['model'] ) && isset( $_POST['model']['params'] ) && ! apply_filters( 'fusion_builder_live_request', false ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$defaults     = $_POST['model']['params']; // phpcs:ignore WordPress.Security
					$return_data  = [];
					$live_request = true;
					fusion_set_live_data();
					add_filter( 'fusion_builder_live_request', '__return_true' );
				}

				if ( class_exists( 'Fusion_App' ) && $live_request ) {

					$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : get_the_ID(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
					if ( ( ! $post_id || -99 === $post_id ) || ( isset( $_POST['post_id'] ) && 'fusion_tb_section' === get_post_type( $_POST['post_id'] ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
						echo wp_json_encode( [] );
						wp_die();
					}

					$this->emulate_product();

					if ( ! $this->is_product() ) {
						echo wp_json_encode( $return_data );
						wp_die();
					}

					// We need to set global $post because Woo template expects it.
					$post = get_post( $product->get_id() );

					// Ensure legacy templates are not used.
					fusion_library()->woocommerce->init_single_product();

					$return_data['woo_additional_info'] = $this->get_woo_addtional_info_content( $defaults );
					$this->restore_product();

					// Restore global $post.
					$post = null;
				}

				echo wp_json_encode( $return_data );
				wp_die();
			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 3.2
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {
				$this->defaults = self::get_element_defaults();
				$this->args     = FusionBuilder::set_shortcode_defaults( $this->defaults, $args, 'fusion_tb_woo_additional_info' );

				$this->emulate_product();

				if ( ! $this->is_product() ) {
					return;
				}

				$html = '<div ' . FusionBuilder::attributes( 'fusion_tb_woo_additional_info-shortcode' ) . '>' . $this->get_woo_addtional_info_content( $this->args ) . '</div>';

				$this->restore_product();

				$this->counter++;

				$this->on_render();

				return apply_filters( 'fusion_component_' . $this->shortcode_handle, $html, $args );
			}

			/**
			 * Builds HTML for Woo Rating element.
			 *
			 * @static
			 * @access public
			 * @since 3.2
			 * @param array $args The arguments.
			 * @return string
			 */
			public function get_woo_addtional_info_content( $args ) {
				global $product;

				if ( 'no' === $args['show_tab_title'] ) {
					add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 99 );
				}

				$content = '';
				if ( function_exists( 'woocommerce_product_additional_information_tab' ) && is_object( $product ) ) {
					ob_start();
					woocommerce_product_additional_information_tab();
					$content = ob_get_clean();
				}

				if ( 'no' === $args['show_tab_title'] ) {
					remove_filter( 'woocommerce_product_additional_information_heading', '__return_false', 99 );
				}

				if ( 'yes' === $args['show_tab_title'] && ! $this->is_default( 'title_size' ) ) {
					$opening_tag = '<' . $this->args['title_size'] . ' class="fusion-woocommerce-tab-title';
					$closing_tag = '</' . $this->args['title_size'] . '>';
					$count       = 1;
					$content     = str_replace( [ '<h3 class="fusion-woocommerce-tab-title', '</h3>' ], [ $opening_tag, $closing_tag ], $content, $count );
				}

				if ( $this->args['responsive_typography'] && ! $this->is_default( 'heading_font_size' ) ) {
					$font_size = fusion_library()->sanitize->get_value_with_unit( $this->args['heading_font_size'] );
					$data      = awb_get_responsive_type_data( $this->args['title_size'], $font_size, $font_size );
					$content   = str_replace(
						'class="woocommerce-product-attributes-item__label"',
						'class="woocommerce-product-attributes-item__label ' . $data['class'] . '" style="' . $data['font_size'] . $data['line_height'] . '"',
						$content
					);
				}

				return apply_filters( 'fusion_woo_component_content', $content, $this->shortcode_handle, $this->args );
			}

			/**
			 * Get the style variables.
			 *
			 * @access protected
			 * @since 3.9
			 * @return string
			 */
			protected function get_style_variables() {
				$custom_vars = [];

				$heading_styles = Fusion_Builder_Element_Helper::get_font_styling( $this->args, 'heading_font', 'array' );
				foreach ( $heading_styles as $rule => $value ) {
					$custom_vars[ 'heading-' . $rule ] = $value;
				}

				$text_styles = Fusion_Builder_Element_Helper::get_font_styling( $this->args, 'text_font', 'array' );
				foreach ( $text_styles as $rule => $value ) {
					$custom_vars[ 'text-' . $rule ] = $value;
				}

				// Get spacing.
				$sides = [ 'top', 'right', 'bottom', 'left' ];

				foreach ( $sides as $side ) {

					// Cell padding.
					$cell_padding_name = 'cell_padding_' . $side;

					// Add cell padding to style.
					if ( '' !== $this->args[ $cell_padding_name ] ) {
						$custom_vars[ $cell_padding_name ] = fusion_library()->sanitize->get_value_with_unit( $this->args[ $cell_padding_name ] );
					}

					// Element margin.
					$margin_name = 'margin_' . $side;

					if ( '' !== $this->args[ $margin_name ] ) {
						$custom_vars[ $margin_name ] = fusion_library()->sanitize->get_value_with_unit( $this->args[ $margin_name ] );
					}
				}

				$css_vars_options = [
					'heading_font_size'            => [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ],
					'heading_letter_spacing'       => [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ],
					'text_font_size'               => [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ],
					'text_letter_spacing'          => [ 'callback' => [ 'Fusion_Sanitize', 'get_value_with_unit' ] ],
					'heading_color'                => [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ],
					'text_color'                   => [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ],
					'border_color'                 => [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ],
					'table_cell_backgroundcolor'   => [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ],
					'heading_cell_backgroundcolor' => [ 'callback' => [ 'Fusion_Sanitize', 'color' ] ],
					'heading_line_height',
					'heading_text_transform',
					'text_line_height',
					'text_text_transform',
				];

				$styles = $this->get_css_vars_for_options( $css_vars_options ) . $this->get_custom_css_vars( $custom_vars );

				return $styles;
			}


			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 3.2
			 * @return array
			 */
			public function attr() {
				$attr = [
					'class' => 'fusion-woo-additional-info-tb fusion-woo-additional-info-tb-' . $this->counter,
					'style' => '',
				];

				$attr = fusion_builder_visibility_atts( $this->args['hide_on_mobile'], $attr );

				if ( $this->args['animation_type'] ) {
					$attr = Fusion_Builder_Animation_Helper::add_animation_attributes( $this->args, $attr );
				}

				$attr['style'] .= $this->get_style_variables();

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				return $attr;
			}

			/**
			 * Load base CSS.
			 *
			 * @access public
			 * @since 3.2
			 * @return void
			 */
			public function add_css_files() {
				FusionBuilder()->add_element_css( FUSION_BUILDER_PLUGIN_DIR . 'assets/css/components/woo-additional-info.min.css' );

				if ( class_exists( 'Avada' ) ) {
					Fusion_Dynamic_CSS::enqueue_style( Avada::$template_dir_path . '/assets/css/dynamic/woocommerce/woo-additional-info.min.css', Avada::$template_dir_url . '/assets/css/dynamic/woocommerce/woo-additional-info.min.css' );
				}
			}
		}
	}

	new FusionTB_Woo_Additional_Info();
}

/**
 * Map shortcode to Avada Builder
 *
 * @since 3.2
 */
function fusion_component_woo_additional_info() {

	fusion_builder_map(
		fusion_builder_frontend_data(
			'FusionTB_Woo_Additional_Info',
			[
				'name'         => esc_attr__( 'Woo Additional Information', 'fusion-builder' ),
				'shortcode'    => 'fusion_tb_woo_additional_info',
				'icon'         => 'fusiona-woo-additional-info',
				'component'    => true,
				'templates'    => [ 'content', 'post_cards', 'page_title_bar' ],
				'subparam_map' => [
					'heading_font'           => 'hcell_typography',
					'heading_font_size'      => 'hcell_typography',
					'heading_text_transform' => 'hcell_typography',
					'heading_line_height'    => 'hcell_typography',
					'heading_letter_spacing' => 'hcell_typography',
					'heading_color'          => 'hcell_typography',
					'text_font'              => 'text_typography',
					'text_font_size'         => 'text_typography',
					'text_text_transform'    => 'text_typography',
					'text_line_height'       => 'text_typography',
					'text_letter_spacing'    => 'text_typography',
					'text_color'             => 'text_typography',
				],
				'params'       => [
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Show Heading', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose to have heading displayed.', 'fusion-builder' ),
						'param_name'  => 'show_tab_title',
						'value'       => [
							'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
							'no'  => esc_attr__( 'No', 'fusion-builder' ),
						],
						'default'     => 'yes',
						'callback'    => [
							'function' => 'fusion_ajax',
							'action'   => 'get_fusion_tb_woo_additional_info',
							'ajax'     => true,
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'HTML Heading Tag', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose HTML tag of the heading, either div, p or the heading tag, h1-h6.', 'fusion-builder' ),
						'param_name'  => 'title_size',
						'value'       => [
							'h1'  => 'H1',
							'h2'  => 'H2',
							'h3'  => 'H3',
							'h4'  => 'H4',
							'h5'  => 'H5',
							'h6'  => 'H6',
							'div' => 'DIV',
							'p'   => 'P',
						],
						'default'     => 'h3',
						'callback'    => [
							'function' => 'fusion_ajax',
							'action'   => 'get_fusion_tb_woo_additional_info',
							'ajax'     => true,
						],
					],
					[
						'type'             => 'dimension',
						'remove_from_atts' => true,
						'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
						'description'      => esc_attr__( 'In pixels or percentage, ex: 10px or 10%.', 'fusion-builder' ),
						'param_name'       => 'margin',
						'value'            => [
							'margin_top'    => '',
							'margin_right'  => '',
							'margin_bottom' => '',
							'margin_left'   => '',
						],
						'group'            => esc_attr__( 'Design', 'fusion-builder' ),
						'callback'         => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'             => 'dimension',
						'remove_from_atts' => true,
						'heading'          => esc_attr__( 'Table Cell Padding', 'fusion-builder' ),
						'description'      => esc_attr__( 'Enter values including any valid CSS unit, ex: 10px or 10%. Leave empty to use default 5px 0 5px 0 value.', 'fusion-builder' ),
						'param_name'       => 'cell_padding',
						'value'            => [
							'cell_padding_top'    => '',
							'cell_padding_right'  => '',
							'cell_padding_bottom' => '',
							'cell_padding_left'   => '',
						],
						'group'            => esc_attr__( 'Design', 'fusion-builder' ),
						'callback'         => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Heading Cell Background Color', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the heading cell background color. ', 'fusion-builder' ),
						'param_name'  => 'heading_cell_backgroundcolor',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'callback'    => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Table Cell Background Color', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the table cell background color. ', 'fusion-builder' ),
						'param_name'  => 'table_cell_backgroundcolor',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'callback'    => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Table Border Color', 'fusion-builder' ),
						'description' => esc_html__( 'Controls the color of the table border, ex: #000.' ),
						'param_name'  => 'border_color',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
						'callback'    => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'             => 'typography',
						'remove_from_atts' => true,
						'global'           => true,
						'heading'          => esc_attr__( 'Heading Cell Typography', 'fusion-builder' ),
						'description'      => esc_html__( 'Controls the typography of the heading cell.', 'fusion-builder' ),
						'param_name'       => 'hcell_typography',
						'group'            => esc_attr__( 'Design', 'fusion-builder' ),
						'choices'          => [
							'font-family'    => 'heading_font',
							'font-size'      => 'heading_font_size',
							'text-transform' => 'heading_text_transform',
							'line-height'    => 'heading_line_height',
							'letter-spacing' => 'heading_letter_spacing',
							'color'          => 'heading_color',
						],
						'default'          => [
							'font-family'    => '',
							'variant'        => '',
							'font-size'      => '',
							'text-transform' => '',
							'line-height'    => '',
							'letter-spacing' => '',
							'color'          => '',
						],
						'callback'         => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'             => 'typography',
						'remove_from_atts' => true,
						'global'           => true,
						'heading'          => esc_attr__( 'Text Typography', 'fusion-builder' ),
						'description'      => esc_html__( 'Controls the typography of the text.', 'fusion-builder' ),
						'param_name'       => 'text_typography',
						'group'            => esc_attr__( 'Design', 'fusion-builder' ),
						'choices'          => [
							'font-family'    => 'text_font',
							'font-size'      => 'text_font_size',
							'text-transform' => 'text_text_transform',
							'line-height'    => 'text_line_height',
							'letter-spacing' => 'text_letter_spacing',
							'color'          => 'text_color',
						],
						'default'          => [
							'font-family'    => '',
							'variant'        => '',
							'font-size'      => '',
							'text-transform' => '',
							'line-height'    => '',
							'letter-spacing' => '',
							'color'          => '',
						],
						'callback'         => [
							'function' => 'fusion_style_block',
						],
					],
					[
						'type'        => 'checkbox_button_set',
						'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
						'param_name'  => 'hide_on_mobile',
						'value'       => fusion_builder_visibility_options( 'full' ),
						'default'     => fusion_builder_default_visibility( 'array' ),
						'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
						'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
						'param_name'  => 'class',
						'value'       => '',
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
						'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
						'param_name'  => 'id',
						'value'       => '',
					],
					'fusion_animation_placeholder' => [
						'preview_selector' => '.fusion-woo-additional-info-tb',
					],
				],
				'callback'     => [
					'function' => 'fusion_ajax',
					'action'   => 'get_fusion_tb_woo_additional_info',
					'ajax'     => true,
				],
			]
		)
	);
}
add_action( 'fusion_builder_before_init', 'fusion_component_woo_additional_info' );
