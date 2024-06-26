<?php
/**
 * Add an element to fusion-builder.
 *
 * @package fusion-builder
 * @since 1.0
 */

if ( fusion_is_element_enabled( 'fusion_section_separator' ) ) {

	if ( ! class_exists( 'FusionSC_SectionSeparator' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @since 1.0
		 */
		class FusionSC_SectionSeparator extends Fusion_Element {

			/**
			 * The section separator counter.
			 *
			 * @access private
			 * @since 1.0
			 * @var int
			 */
			private $element_counter = 1;

			/**
			 * An array of the bg image separators.
			 *
			 * @access protected
			 * @since 3.2
			 * @var array
			 */
			protected $bg_image_separators = [ 'grunge', 'music', 'waves_brush', 'paper', 'squares', 'circles', 'paint', 'grass', 'splash', 'custom' ];

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();
				add_filter( 'fusion_attr_section-separator-shortcode', [ $this, 'attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-svg-wrapper', [ $this, 'svg_wrapper_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-spacer', [ $this, 'spacer_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-spacer-height', [ $this, 'spacer_height_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-icon', [ $this, 'icon_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-divider-candy', [ $this, 'divider_candy_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-divider-candy-arrow', [ $this, 'divider_candy_arrow_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-divider-rounded-split', [ $this, 'divider_rounded_split_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-divider-svg', [ $this, 'divider_svg_attr' ] );
				add_filter( 'fusion_attr_section-separator-shortcode-divider-svg-bg-image', [ $this, 'divider_svg_bg_image_attr' ] );

				add_shortcode( 'fusion_section_separator', [ $this, 'render' ] );
			}

			/**
			 * Gets the default values.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function get_element_defaults() {
				$fusion_settings = awb_get_fusion_settings();

				return [
					'divider_type'          => 'triangle',
					'divider_position'      => 'center',
					'hide_on_mobile'        => fusion_builder_default_visibility( 'string' ),
					'class'                 => '',
					'id'                    => '',
					'backgroundcolor'       => $fusion_settings->get( 'section_sep_bg' ),
					'bordersize'            => $fusion_settings->get( 'section_sep_border_size' ),
					'bordercolor'           => $fusion_settings->get( 'section_sep_border_color' ),
					'divider_candy'         => 'top',
					'icon'                  => '',
					'icon_color'            => $fusion_settings->get( 'icon_color' ),
					'divider_height'        => '',
					'divider_height_medium' => '',
					'divider_height_small'  => '',
					'divider_repeat'        => 1,
					'divider_repeat_medium' => 1,
					'divider_repeat_small'  => 1,
					'margin_bottom'         => '',
					'margin_left'           => '',
					'margin_right'          => '',
					'margin_top'            => '',
					'custom_svg'            => '',
				];
			}

			/**
			 * Maps settings to param variables.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function settings_to_params() {
				return [
					'section_sep_bg'           => 'backgroundcolor',
					'section_sep_border_size'  => 'bordersize',
					'section_sep_border_color' => 'bordercolor',
					'icon_color'               => 'icon_color',
				];
			}

			/**
			 * Used to set any other variables for use on front-end editor template.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function get_element_extras() {
				$fusion_settings = awb_get_fusion_settings();
				return [
					'container_padding_100' => $fusion_settings->get( 'container_padding_100' ),
					'layout'                => fusion_get_option( 'layout' ),
					'site_width'            => esc_attr( $fusion_settings->get( 'site_width' ) ),
					'header_position'       => esc_attr( fusion_get_option( 'header_position' ) ),
					'side_header_width'     => $fusion_settings->get( 'side_header_width' ),
					'hundredp_padding'      => esc_attr( $fusion_settings->get( 'hundredp_padding' ) ),
					'visibility_large'      => $fusion_settings->get( 'visibility_large' ),
					'visibility_medium'     => $fusion_settings->get( 'visibility_medium' ),
					'visibility_small'      => $fusion_settings->get( 'visibility_small' ),
				];
			}

			/**
			 * Maps settings to extra variables.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function settings_to_extras() {

				return [
					'container_padding_100' => 'container_padding_100',
					'layout'                => 'layout',
					'site_width'            => 'site_width',
					'header_position'       => 'header_position',
					'side_header_width'     => 'side_header_width',
					'hundredp_padding'      => 'hundredp_padding',
				];
			}

			/**
			 * Change args to valid values based on other options.
			 *
			 * @access public
			 * @since 3.4
			 * @return void
			 */
			public function validate_args() {
				$this->args['bordersize']      = FusionBuilder::validate_shortcode_attr_value( $this->args['bordersize'], 'px' );
				$this->args['backgroundcolor'] = esc_attr( $this->args['backgroundcolor'] );
			}

			/**
			 * Render the shortcode
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {
				global $fusion_fwc_type, $fusion_col_type;

				$this->defaults = self::get_element_defaults();
				$this->args     = FusionBuilder::set_shortcode_defaults( self::get_element_defaults(), $args, 'fusion_section_separator' );

				$this->validate_args();

				$candy = '';
				$icon  = '';

				if ( 'triangle' === $this->args['divider_type'] ) {
					if ( $this->args['icon'] ) {
						if ( ! $this->args['icon_color'] ) {
							$this->args['icon_color'] = $this->args['bordercolor'];
						}

						$icon = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-icon' ) . '></div>';
					}

					$candy = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-candy-arrow' ) . '></div><div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-candy' ) . '></div>';

					if ( false !== strpos( $this->args['divider_candy'], 'top' ) && false !== strpos( $this->args['divider_candy'], 'bottom' ) ) {
						$candy = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-candy' ) . '></div>';
					}

					$candy = $icon . $candy;
				} elseif ( 'bigtriangle' === $this->args['divider_type'] ) {
					$candy = '<svg class="fusion-big-triangle-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

					if ( 'top' === $this->args['divider_candy'] ) {
						if ( 'right' === $this->args['divider_position'] ) {
							$candy .= '<path d="M0 100 L75 0 L100 100 Z"></path>';
						} elseif ( 'left' === $this->args['divider_position'] ) {
							$candy .= '<path d="M0 100 L25 2 L100 100 Z"></path>';
						} else {
							$candy .= '<path d="M0 100 L50 2 L100 100 Z"></path>';
						}
					} else {
						if ( 'right' === $this->args['divider_position'] ) {
							$candy .= '<path d="M-1 -1 L75 99 L101 -1 Z"></path>';
						} elseif ( 'left' === $this->args['divider_position'] ) {
							$candy .= '<path d="M0 -1 L25 100 L101 -1 Z"></path>';
						} else {
							$candy .= '<path d="M-1 -1 L50 99 L101 -1 Z"></path>';
						}
					}

					$candy .= '</svg>';
				} elseif ( 'slant' === $this->args['divider_type'] ) {
					$candy = '<svg class="fusion-slant-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

					if ( 'left' === $this->args['divider_position'] && 'top' === $this->args['divider_candy'] ) {
						$candy .= '<path d="M100 0 L100 100 L0 0 Z"></path>';
					} elseif ( 'right' === $this->args['divider_position'] && 'top' === $this->args['divider_candy'] ) {
						$candy .= '<path d="M0 100 L0 0 L100 0 Z"></path>';
					} elseif ( 'right' === $this->args['divider_position'] && 'bottom' === $this->args['divider_candy'] ) {
						$candy .= '<path d="M100 0 L0 100 L101 100 Z"></path>';
					} else {
						$candy .= '<path d="M0 0 L0 100 L100 100 Z"></path>';
					}
					$candy .= '</svg>';
				} elseif ( 'rounded-split' === $this->args['divider_type'] ) {
					$candy = sprintf( '<div %s></div>', FusionBuilder::attributes( 'section-separator-shortcode-divider-rounded-split' ) );
				} elseif ( 'big-half-circle' === $this->args['divider_type'] ) {
					$candy = '<svg class="fusion-big-half-circle-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

					if ( 'top' === $this->args['divider_candy'] ) {
						$candy .= '<path d="M0 100 C40 0 60 0 100 100 Z"></path>';
					} else {
						$candy .= '<path d="M0 0 C55 180 100 0 100 0 Z"></path>';
					}

					$candy .= '</svg>';
				} elseif ( 'curved' === $this->args['divider_type'] ) {
					$candy = '<svg class="fusion-curved-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

					if ( 'left' === $this->args['divider_position'] ) {
						if ( 'top' === $this->args['divider_candy'] ) {
							$candy .= '<path d="M0 100 C 20 0 50 0 100 100 Z"></path>';
						} else {
							$candy .= '<path d="M0 0 C 20 100 50 100 100 0 Z"></path>';
						}
					} else {
						if ( 'top' === $this->args['divider_candy'] ) {
							$candy .= '<path d="M0 100 C 60 0 75 0 100 100 Z"></path>';
						} else {
							$candy .= '<path d="M0 0 C 50 100 80 100 100 0 Z"></path>';
						}
					}
					$candy .= '</svg>';
				} elseif ( 'clouds' === $this->args['divider_type'] ) {
					$candy  = '<svg class="fusion-clouds-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$candy .= '<path d="M-5 100 Q 0 20 5 100 Z"></path>
								<path d="M0 100 Q 5 0 10 100"></path>
								<path d="M5 100 Q 10 30 15 100"></path>
								<path d="M10 100 Q 15 10 20 100"></path>
								<path d="M15 100 Q 20 30 25 100"></path>
								<path d="M20 100 Q 25 -10 30 100"></path>
								<path d="M25 100 Q 30 10 35 100"></path>
								<path d="M30 100 Q 35 30 40 100"></path>
								<path d="M35 100 Q 40 10 45 100"></path>
								<path d="M40 100 Q 45 50 50 100"></path>
								<path d="M45 100 Q 50 20 55 100"></path>
								<path d="M50 100 Q 55 40 60 100"></path>
								<path d="M55 100 Q 60 60 65 100"></path>
								<path d="M60 100 Q 65 50 70 100"></path>
								<path d="M65 100 Q 70 20 75 100"></path>
								<path d="M70 100 Q 75 45 80 100"></path>
								<path d="M75 100 Q 80 30 85 100"></path>
								<path d="M80 100 Q 85 20 90 100"></path>
								<path d="M85 100 Q 90 50 95 100"></path>
								<path d="M90 100 Q 95 25 100 100"></path>
								<path d="M95 100 Q 100 15 105 100 Z"></path>';
					$candy .= '</svg>';
				} elseif ( 'horizon' === $this->args['divider_type'] ) {
					$y_min = ( 'top' === $this->args['divider_candy'] ) ? '-0.5' : '0';
					$candy = '<svg class="fusion-horizon-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 ' . $y_min . ' 1024 178" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					if ( 'top' === $this->args['divider_candy'] ) {
						$candy .= '<path class="st0" d="M1024 177.371H0V.219l507.699 133.939L1024 .219v177.152z"/>
									<path class="st1" d="M1024 177.781H0V39.438l507.699 94.925L1024 39.438v138.343z"/>
									<path class="st2" d="M1024 177.781H0v-67.892l507.699 24.474L1024 109.889v67.892z"/>
									<path class="st3" d="M1024 177.781H0v-3.891l507.699-39.526L1024 173.889v3.892z"/>
								';
					} else {
						$candy .= '<path class="st0" d="M1024 177.193L507.699 43.254 0 177.193V.041h1024v177.152z"/>
									<path class="st1" d="M1024 138.076L507.699 43.152 0 138.076V-.266h1024v138.342z"/>
									<path class="st2" d="M1024 67.728L507.699 43.152 0 67.728V-.266h1024v67.994z"/>
									<path class="st3" d="M1024 3.625L507.699 43.152 0 3.625V-.266h1024v3.891z"/>
								';
					}
					$candy .= '</svg>';
				} elseif ( 'hills' === $this->args['divider_type'] ) {
					if ( 'top' === $this->args['divider_candy'] ) {
						$candy  = '<svg class="fusion-hills-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 74 1024 107" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
						$candy .= '<path class="st4" d="M0 182.086h1024v-77.312c-49.05 20.07-120.525 42.394-193.229 42.086-128.922-.512-159.846-72.294-255.795-72.294-89.088 0-134.656 80.179-245.043 82.022S169.063 99.346 49.971 97.401C32.768 97.094 16.077 99.244 0 103.135v78.951z"/>';
					} else {
						$candy  = '<svg class="fusion-hills-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 1 1024 107" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
						$candy .= '<path class="st4" d="M0 0h1024v77.3c-49-20.1-120.5-42.4-193.2-42.1-128.9.5-159.8 72.3-255.8 72.3-89.1 0-134.7-80.2-245-82-110.4-1.8-160.9 57.2-280 59.2-17.2.3-33.9-1.8-50-5.7V0z"/>';
					}
					$candy .= '</svg>';
				} elseif ( 'hills_opacity' === $this->args['divider_type'] ) {
					$y_min = ( 'top' === $this->args['divider_candy'] ) ? '-0.5' : '0';
					$candy = '<svg class="fusion-hills-opacity-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 ' . $y_min . ' 1024 182" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					if ( 'top' === $this->args['divider_candy'] ) {
						$candy .= '<path class="st0" d="M0 182.086h1024V41.593c-28.058-21.504-60.109-37.581-97.075-37.581-112.845 0-198.144 93.798-289.792 93.798S437.658 6.777 351.846 6.777s-142.234 82.125-238.49 82.125c-63.078 0-75.776-31.744-113.357-53.658L0 182.086z"/>
									<path class="st1" d="M1024 181.062v-75.878c-39.731 15.872-80.794 27.341-117.658 25.805-110.387-4.506-191.795-109.773-325.53-116.224-109.158-5.12-344.166 120.115-429.466 166.298H1024v-.001z"/>
									<path class="st2" d="M0 182.086h1024V90.028C966.451 59.103 907.059 16.3 824.115 15.071 690.278 13.023 665.19 102.93 482.099 102.93S202.138-1.62 74.24.019C46.49.326 21.811 4.217 0 9.849v172.237z"/>
									<path class="st3" d="M0 182.086h1024V80.505c-37.171 19.558-80.691 35.328-139.571 36.25-151.142 2.355-141.619-28.57-298.496-29.184s-138.854 47.002-305.459 43.725C132.813 128.428 91.238 44.563 0 28.179v153.907z"/>
									<path class="st4" d="M0 182.086h1024v-77.312c-49.05 20.07-120.525 42.394-193.229 42.086-128.922-.512-159.846-72.294-255.795-72.294-89.088 0-134.656 80.179-245.043 82.022S169.063 99.346 49.971 97.401C32.768 97.094 16.077 99.244 0 103.135v78.951z"/>
								';
					} else {
						$candy .= '<path class="st0" d="M0 0h1024v140.5C995.9 162 963.9 178 926.9 178c-112.8 0-198.1-93.8-289.8-93.8s-199.5 91-285.3 91-142.2-82.1-238.5-82.1c-63.1 0-75.7 31.6-113.3 53.6V0z"/>
									<path class="st1" d="M1024 0v75.9C984.3 60 942.2 48.6 905.3 50.1c-110.4 4.5-191.8 109.8-325.5 116.2C470.6 171.5 235.6 46.1 150.3 0H1024z"/>
									<path class="st2" d="M0 0h1024v92c-57.5 30.9-116.9 73.7-199.9 75-133.8 2-158.9-87.9-342-87.9S202.1 183.7 74.2 182c-27.8-.3-52.4-4.2-74.2-9.7V0z"/>
									<path class="st3" d="M0 0h1024v101.6C986.8 82 943.3 66.3 884.4 65.4 733.3 63 742.8 94 585.9 94.6S447 47.6 280.4 50.9C132.8 53.6 91.2 137.5 0 154V0z"/>
									<path class="st4" d="M0 0h1024v77.3c-49-20.1-120.5-42.4-193.2-42.1-128.9.5-159.8 72.3-255.8 72.3-89.1 0-134.7-80.2-245-82-110.4-1.8-160.9 57.2-280 59.2-17.2.3-33.9-1.8-50-5.7V0z"/>
								';
					}
					$candy .= '</svg>';
				} elseif ( 'waves' === $this->args['divider_type'] ) {
					$y_min = ( 'top' === $this->args['divider_candy'] ) ? '54' : '1';
					$candy = '<svg class="fusion-waves-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 ' . $y_min . ' 1024 162" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

					if ( 'left' === $this->args['divider_position'] ) {
						if ( 'top' === $this->args['divider_candy'] ) {
							$candy .= '<path class="st3" d="M0 216.312h1024v-3.044c-50.8-17.1-108.7-30.7-172.7-37.9-178.6-19.8-220 36.8-404.9 21.3-206.6-17.2-228-126.5-434.5-141.6-3.9-.3-7.9-.5-11.9-.7v161.944z"/>';
						} else {
							$candy .= '<path class="st3" d="M0 162.1c4-.2 8-.4 11.9-.7C218.4 146.3 239.8 37 446.4 19.8 631.3 4.3 672.7 60.9 851.3 41.1c64-7.2 121.9-20.8 172.7-37.9V.156H0V162.1z"/>';
						}
					} else {
						if ( 'top' === $this->args['divider_candy'] ) {
							$candy .= '<path class="st3" d="M1024.1 54.368c-4 .2-8 .4-11.9.7-206.5 15.1-227.9 124.4-434.5 141.6-184.9 15.5-226.3-41.1-404.9-21.3-64 7.2-121.9 20.8-172.7 37.9v3.044h1024V54.368z"/>';
						} else {
							$candy .= '<path class="st3" d="M1024.1.156H.1V3.2c50.8 17.1 108.7 30.7 172.7 37.9 178.6 19.8 220-36.8 404.9-21.3 206.6 17.2 228 126.5 434.5 141.6 3.9.3 7.9.5 11.9.7V.156z"/>';
						}
					}

					$candy .= '</svg>';
				} elseif ( 'waves_opacity' === $this->args['divider_type'] ) {
					$y_min = ( 'top' === $this->args['divider_candy'] ) ? '0' : '1';
					$candy = '<svg class="fusion-waves-opacity-candy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 ' . $y_min . ' 1024 216" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

					if ( 'left' === $this->args['divider_position'] ) {
						if ( 'top' === $this->args['divider_candy'] ) {
							$candy .= '<path class="st0" d="M0 216.068h1024l.1-105.2c-14.6-3.2-30.2-5.8-47.1-7.6-178.6-19.6-279.5 56.8-464.3 41.3-206.5-17.2-248.4-128.8-455-143.8-19-1.3-38.3-.2-57.7.3v215z"/>
										<path class="st1" d="M0 20.068v196.144h1024v-79.744c-22.7-6.4-47.9-11.4-76.2-14.6-178.6-19.8-272.2 53.9-457.1 38.4-206.6-17.2-197.3-124.7-403.9-139.8-27.2-2-56.6-2-86.8-.4z"/>
										<path class="st2" d="M0 216.212h1024v-35.744c-45.1-15.4-95.2-27.7-150-33.7-178.6-19.8-220.6 46.8-405.4 31.3-206.6-17.2-197.8-114.7-404.4-129.7-20.4-1.5-42-2-64.2-1.7v169.544z"/>
										<path class="st3" d="M0 216.312h1024v-3.044c-50.8-17.1-108.7-30.7-172.7-37.9-178.6-19.8-220 36.8-404.9 21.3-206.6-17.2-228-126.5-434.5-141.6-3.9-.3-7.9-.5-11.9-.7v161.944z"/>
									';
						} else {
							$candy .= '<path class="st0" d="M0 215.4c19.4.5 38.7 1.6 57.7.3 206.6-15 248.5-126.6 455-143.8 184.8-15.5 285.7 60.9 464.3 41.3 16.9-1.8 32.5-4.4 47.1-7.6L1024 .4H0v215z"/>
										<path class="st1" d="M0 196.4c30.2 1.6 59.6 1.6 86.8-.4C293.4 180.9 284.1 73.4 490.7 56.2c184.9-15.5 278.5 58.2 457.1 38.4 28.3-3.2 53.5-8.2 76.2-14.6V.256H0V196.4z"/>
										<path class="st2" d="M0 169.8c22.2.3 43.8-.2 64.2-1.7C270.8 153.1 262 55.6 468.6 38.4 653.4 22.9 695.4 89.5 874 69.7c54.8-6 104.9-18.3 150-33.7V.256H0V169.8z"/>
										<path class="st3" d="M0 162.1c4-.2 8-.4 11.9-.7C218.4 146.3 239.8 37 446.4 19.8 631.3 4.3 672.7 60.9 851.3 41.1c64-7.2 121.9-20.8 172.7-37.9V.156H0V162.1z"/>
									';
						}
					} else {
						if ( 'top' === $this->args['divider_candy'] ) {
							$candy .= '<path class="st0" d="M1024.1 1.068c-19.4-.5-38.7-1.6-57.7-.3-206.6 15-248.5 126.6-455 143.8-184.8 15.5-285.7-60.9-464.3-41.3-16.9 1.8-32.5 4.4-47.1 7.6l.1 105.2h1024v-215z"/>
										<path class="st1" d="M1024.1 20.068c-30.2-1.6-59.6-1.6-86.8.4-206.6 15.1-197.3 122.6-403.9 139.8-184.9 15.5-278.5-58.2-457.1-38.4-28.3 3.2-53.5 8.2-76.2 14.6v79.744h1024V20.068z"/>
										<path class="st2" d="M1024.1 46.668c-22.2-.3-43.8.2-64.2 1.7-206.6 15-197.8 112.5-404.4 129.7-184.8 15.5-226.8-51.1-405.4-31.3-54.8 6-104.9 18.3-150 33.7v35.744h1024V46.668z"/>
										<path class="st3" d="M1024.1 54.368c-4 .2-8 .4-11.9.7-206.5 15.1-227.9 124.4-434.5 141.6-184.9 15.5-226.3-41.1-404.9-21.3-64 7.2-121.9 20.8-172.7 37.9v3.044h1024V54.368z"/>
									';
						} else {
							$candy .= '<path class="st0" d="M1024.1.4H.1L0 105.6c14.6 3.2 30.2 5.8 47.1 7.6 178.6 19.6 279.5-56.8 464.3-41.3 206.5 17.2 248.4 128.8 455 143.8 19 1.3 38.3.2 57.7-.3V.4z"/>
										<path class="st1" d="M1024.1 196.4V.256H.1V80C22.8 86.4 48 91.4 76.3 94.6c178.6 19.8 272.2-53.9 457.1-38.4C740 73.4 730.7 180.9 937.3 196c27.2 2 56.6 2 86.8.4z"/>
										<path class="st2" d="M1024.1.256H.1V36c45.1 15.4 95.2 27.7 150 33.7 178.6 19.8 220.6-46.8 405.4-31.3 206.6 17.2 197.8 114.7 404.4 129.7 20.4 1.5 42 2 64.2 1.7V.256z"/>
										<path class="st3" d="M1024.1.156H.1V3.2c50.8 17.1 108.7 30.7 172.7 37.9 178.6 19.8 220-36.8 404.9-21.3 206.6 17.2 228 126.5 434.5 141.6 3.9.3 7.9.5 11.9.7V.156z"/>
									';
						}
					}

					$candy .= '</svg>';
				} elseif ( 'grunge' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '43px';

					$this->args['svg_element']  = '<svg width="1463" viewBox="0 0 1463 43" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<path d="M0.0160014 42.9619H1463V9L1457.65 5.68624L1452.32 6.83201C1447.24 6.98142 1439.58 4.30928 1438 5.1512C1433.07 4.10435 1438.68 11.5292 1433.65 11.7432C1428.04 11.9815 1428.94 8.52022 1426 8.67669C1424.9 8.73524 1421.27 10.3481 1420.5 10.2118C1414.64 9.15693 1410.05 9.20837 1413.56 7.70523C1409.62 7.00464 1410.86 5.74478 1409.65 4.41831C1413.55 4.35269 1405.99 2.65169 1411.17 3.04943C1411.26 1.9087 1399.43 2.47479 1400.5 1.00093C1395.36 1.72474 1400.25 2.60021 1395.17 2.70216C1390.77 2.78595 1375.26 0.649608 1370.5 1.00091C1363.12 1.55512 1366.17 2.11766 1361.41 2.25294C1355.73 2.41244 1350.42 3.16048 1344.88 3.72276C1343.92 3.82371 1340.75 3.50774 1339.22 3.56427C1337.06 3.64402 1333.44 4.10435 1330.78 4.12051C1326.87 4.14675 1323.6 3.97514 1319.86 3.44111C1319.62 5.69835 1309.3 2.94444 1303.35 2.50531C1297.09 4.22145 1282.07 4.09123 1270.59 4.67674C1270.38 5.16634 1273.92 5.4167 1272.73 5.56711C1266.97 6.28789 1258 6.47465 1252.68 5.68623C1255.75 4.68784 1247.22 4.02561 1247.99 2.50531C1241.68 2.25899 1237.47 4.06902 1229.82 4.27092C1231.56 5.84977 1224.1 4.4607 1222.96 4.73832C1221.87 5.01088 1222.48 1.36761 1219.92 1.1435C1216.74 0.413638 1219.67 2.72134 1214.2 2.76475C1210.63 2.85964 1219.4 3.64604 1217.45 4.73832C1213.14 3.90548 1215.4 5.59942 1214.2 4.27092C1219.7 3.4179 1208.75 3.95899 1212.02 2.76475C1208.68 2.89901 1201.36 1.74112 1204.68 2.65775C1206.74 2.7587 1204.66 3.02722 1205.05 3.69147C1201.03 3.48149 1199.26 4.0246 1196.73 4.12151C1196.24 4.13968 1190.48 4.63636 1189.8 4.56065C1188.95 4.47181 1188.44 6.15868 1186.77 6.19098C1185.92 6.20713 1185.45 4.53137 1182.88 4.51522C1180.49 4.50714 1177.8 1.94504 1174.96 2.153C1168.35 3.80251 1160.67 5.47222 1154 4.56065C1153.76 5.12899 1151.2 6.66343 1149.57 6.73005C1143.29 6.17887 1128.95 8.2019 1118.19 8.20997C1115.62 8.20997 1116.05 10.1533 1113.62 10.229C1112.24 10.2724 1111.45 7.18634 1108.04 7.4367C1107.58 7.47001 1110.31 5.46717 1108.04 5.68623C1099.45 6.52815 1108.44 7.28326 1105.2 8.87321C1100.4 9.22351 1098.78 11.2314 1098 10.229C1093.62 10.4369 1093.53 6.4696 1088.71 6.82999C1086.88 6.82999 1087.4 7.67191 1085.89 7.78094C1083.71 7.72541 1088.12 6.58468 1085.05 6.82999C1081.2 8.65718 1079.27 7.96568 1072.47 8.87321C1070.87 7.82939 1063.6 4.99978 1059.77 4.70904C1059.6 5.4702 1059.03 2.85561 1056.09 2.6103C1056.85 -0.156728 1046.52 6.23843 1050.38 4.04177C1039.75 3.99129 1047.77 4.38499 1038.19 4.70904C1037.95 5.61153 1036.86 2.30745 1034.98 2.6103C1034.83 1.55033 1036.67 2.09949 1033.23 2.153C1032.13 3.63797 1031.87 2.55781 1030.76 4.04177C1028.36 3.58446 1015.73 6.69977 1016.47 5.18149C1012.05 4.7262 1007.14 6.3101 1003.52 6.14353C990.194 6.79567 991.325 3.83583 977.99 6.19098C977.18 5.46178 979.157 4.4355 978.195 4.19521C960.339 5.27537 940.466 5.62667 920.017 5.76497C918.646 6.25155 917.238 6.72702 915.797 7.19038C912.741 5.56106 912.67 9.83728 908.269 9.77873C906.28 9.75349 906.617 6.23843 904.788 6.1506C905.205 6.1718 900.835 6.58065 899.232 6.60285C891.16 6.7038 884.008 8.63295 878.552 8.71472C877.061 11.4343 869.156 9.77469 868.647 9.77873C868.647 9.77873 866.695 8.4785 866.361 8.71472C866.106 8.91315 867.491 10.6102 867.212 10.7408C865.367 10.9508 863.509 10.7754 861.706 10.2209C858.833 8.90855 858.487 7.82939 854.912 7.77589C852.307 7.73652 847.78 7.63355 845.235 7.71431C839.45 7.89804 826.826 7.66787 823.433 7.61336C822.523 7.93753 820.061 9.79074 819.119 9.77873C818.015 9.73129 818.715 7.54068 817.899 7.57904C812.703 7.83242 816.107 11.6312 811.524 11.2486C811.432 10.4874 811.797 9.56977 808.832 9.77873C802.901 9.51121 808.181 11.9441 804.861 9.77873C803.185 9.34667 801.857 7.03391 800.45 8.08379C802.619 8.14335 800.011 9.19726 799.307 10.229C794.116 10.7125 790.72 10.023 786.354 10.229C784.432 12.7719 778.002 7.52352 774.617 9.20231C775.385 9.20231 773.831 7.11972 772.257 7.20048C771.858 6.54531 770.095 7.70321 770.733 6.69573C767.852 6.96728 769.863 8.2766 769.21 9.35474C778.865 8.91864 764.788 12.3953 763.114 10.8013C763.175 9.99981 761.85 9.33455 762.201 8.63901C754.76 9.2013 745.79 12.6003 737.588 11.7432C737.338 12.3126 733.496 12.6851 731.873 12.7527C729.938 11.3051 730.615 11.4666 726.365 10.8013C726.475 10.1805 728.032 10.3693 727.682 9.58491C719.946 11.402 728.628 7.66484 722.348 8.99334C720.556 10.55 725.2 8.28972 723.414 9.84637C721.585 9.84031 722.106 10.6903 720.594 10.8013C719.156 10.4864 713.749 12.1541 712.591 11.7432C712.362 11.6625 702.028 7.91924 701.775 7.89501C698.019 7.56188 695.374 9.47891 689.584 9.21947C689.255 9.59399 683.69 10.2461 682.345 10.2512C674.78 9.7121 676.459 12.4731 667.486 11.7432C667.239 12.3136 667.204 10.1825 665.581 10.2512C660.619 9.92914 667.999 12.9173 663.782 12.8637C662.944 13.7117 662.163 16.7483 659.105 16.7907C657.042 16.6897 657.131 15.9033 656.743 15.2431C661.866 14.5213 654.791 14.5415 656.057 12.8637C650.723 13.2675 643.461 13.1545 641.198 12.1026C642.459 11.2536 642.62 10.7499 639.674 9.72422C634.613 10.3425 638.541 10.9325 633.484 10.2512C630.996 11.4807 634.798 12.5861 629.19 13.1282C629.571 11.8704 625.964 12.1187 625.116 11.6493C624.43 11.2728 624.177 10.5621 619.863 10.7337C614.987 9.95135 618.983 11.8603 615.672 11.6493C609.223 11.8189 614.726 11.6927 609.577 11.6493C605.75 11.615 598.921 12.4256 593.575 12.1026C591.617 13.1959 595.404 13.2837 591.83 13.3776C586.603 12.9021 591.585 13.5512 591.289 12.1026C588.704 12.0602 585.593 11.6392 587.099 12.2449C584.736 12.5357 586.845 12.3893 584.944 12.8496C582.236 12.6901 582.169 13.4876 580.745 13.7945C576.452 12.4085 572.211 13.2898 565.612 12.2449C562.939 12.4246 564.463 12.7022 560.049 12.2449C554.784 12.9142 557.422 12.3116 553.145 12.6487C543.88 14.3265 537.365 10.1977 529.686 11.5322C531.235 12.3217 535.041 13.099 532.329 13.9712C530.441 12.2308 521.156 12.0784 515.788 12.8971C515.524 11.9794 517.397 11.8361 517.265 10.9669C514.769 10.017 507.827 9.69898 510.257 10.9669C512.136 10.8256 508.42 11.832 511.298 12.0572C510.941 12.9657 513.264 11.5282 513.045 12.3943C511.658 12.3852 511.382 12.7769 510.257 12.8627C506.563 12.8405 511.402 11.4807 507.489 11.5322C503.724 13.3968 503.466 12.886 506.727 13.8854C501.189 14.0065 506.698 13.4281 500.528 13.7723C502.373 11.9956 496.749 10.448 495.297 11.3636L496.457 12.2893C493.05 13.2342 495.459 17.2096 490.344 17.5306C490.406 16.832 490.231 13.9066 488.081 13.6915C481.461 13.0273 486.009 11.8068 481.582 12.0572C481.441 11.1547 478.934 12.4983 477.092 12.1672C475.787 13.0071 474.436 13.8319 470.103 13.5805C470.168 12.1288 470.129 12.572 467.104 12.1672C467.957 14.0974 469.378 13.9813 471.386 15.5268C470 15.5137 464.42 14.4194 463.294 14.5021C459.761 13.4745 465.329 12.6528 456.276 13.4927C456.644 12.8042 458.596 12.6911 459.119 12.0572C451.911 12.3741 452.004 10.4742 449.579 12.9879C453.698 13.1423 450.966 11.616 452.128 13.4654C449.503 13.6673 446.34 12.4902 447.674 14.1226C447.657 14.9221 446.636 14.8282 446.912 15.5268C446.313 16.1325 442.804 16.0295 440.435 15.9922C435.273 15.5742 445.066 14.6344 442.425 13.8904C437.737 13.7279 431.579 15.3794 429.006 14.5021C427.634 15.3976 428.592 15.1795 424.434 15.4703C422.224 13.8288 424.908 15.1543 420.229 15.2038C418.786 13.958 423.285 13.0212 416.814 12.9203C410.467 13.0091 418.468 17.3277 416.206 18.0354C418.659 18.32 421.762 16.4313 422.893 17.1592C421.014 17.8365 416.717 19.7434 414.909 19.0449C419.717 17.1793 409.787 14.7303 409.269 13.1979C411.204 13.2423 414.563 13.1212 412.059 12.7295C407.716 12.464 409.76 11.0133 404.622 10.4641C405.484 8.10799 395.862 12.1722 391.371 11.6261C388.999 13.0828 380.768 13.6602 375.668 12.4831C375.077 10.9527 376.564 12.2157 377.192 11.1224C373.495 10.9748 374.019 11.6908 370.334 12.1147C371.553 12.9697 370.357 14.9655 372.62 15.4703C372.673 14.8353 375.706 14.3033 375.984 14.9211C373.156 17.9213 369.789 14.2407 366.359 13.8914C363.083 13.5583 366.187 13.8975 362.333 13.3554C362.671 12.7497 362.858 13.3584 359.32 13.3554C358.244 14.8282 359.407 15.1291 359.32 16.2688C357.92 16.2348 355.743 12.5038 354.367 12.8446C352.026 12.5216 351.212 12.1117 349.364 11.6261C346.652 12.8059 344.779 12.6948 341.925 12.7659C338.786 11.7826 337.335 17.0542 334.705 17.4014C331.3 16.4131 326.735 11.9188 322.695 12.4831C323.353 12.3903 317.131 16.2607 315.472 16.2688C312.576 14.8716 314.614 16.1527 309.517 16.4394C309.624 15.8226 307.054 15.7025 306.709 14.9211C302.356 14.6546 306.046 15.8377 301.271 15.4198C301.628 14.5042 297.722 15.2573 297.947 14.3901C293.603 14.1297 298.113 12.6619 292.978 12.1127C289.076 11.9986 291.719 13.1404 286.517 13.4715C284.694 14.6718 285.456 13.9267 283.346 14.3366C280.875 14.3194 278.16 11.2748 276.595 9.9594C275.216 7.41951 272.843 11.4292 270.135 9.9594C261.592 10.3067 262.142 11.5847 257.927 10.4641C254.243 10.5684 255.608 6.85148 251.94 6.37875C251.61 6.75428 251.853 7.33272 250.508 7.33979C246.029 6.48475 241.316 7.64264 236.657 7.73652C233.38 7.79507 230.13 7.07732 227.001 7.19038C224.072 7.29739 221.515 8.06562 218.657 8.10801C214.192 8.17262 209.763 7.50232 204.857 7.53462C195.735 7.5962 186.181 8.49061 178.537 8.34222C176.727 8.30689 176.688 7.82233 174.409 7.83747C169.952 7.85059 163.074 13.3211 159.5 11.7432C154.014 13.4069 150.056 8.10801 142.588 8.11811C135.313 8.13325 126.485 8.02826 117.696 7.96164C111.427 7.91318 104.317 8.82778 96.9036 8.80154C87.1801 8.76621 69.1759 15.3824 66 17.4014C56.0517 16.9108 48.0438 5.18348 40.0195 6.93091C31.9228 5.07647 25.5946 7.65775 13.3505 6.93091C13.2042 6.99553 11.2153 7.39267 11.0646 7.43566L8.64532 8.08076C8.13988 8.17632 7.60955 8.26448 7.05433 8.34524C5.94337 8.50575 4.73641 8.63598 3.45173 8.73895C2.35754 8.82678 1.19629 8.89037 0 8.94085L0.0160014 42.9619ZM1397.27 4.41831C1397.65 5.54389 1396.95 6.27881 1395.75 6.83201C1391.1 6.97839 1396.5 5.97091 1394.7 5.1512C1397.18 5.10577 1394.65 4.50815 1397.27 4.41831ZM1355.86 2.70216C1358.3 3.14735 1357.6 4.07609 1362.4 3.72276C1362.6 4.78576 1362.9 5.2572 1359.89 5.1512C1360.6 5.54288 1361.29 5.9487 1361.19 6.61396C1355.63 6.64627 1359.93 5.29556 1360.88 4.12051C1360.01 3.62181 1355.4 3.65311 1355.86 2.70216ZM1336.36 5.48736C1342.15 7.10256 1345.03 4.26385 1350.26 4.12051C1352 5.52068 1352.1 5.3743 1348.69 5.48837C1346.24 5.5661 1335.03 6.87542 1336.35 5.48837L1336.36 5.48736ZM1326.97 7.20048C1326.89 8.9782 1328.63 7.78195 1326.5 8.8187C1320.52 7.97173 1327.24 5.37329 1333.56 5.95476L1326.97 7.20048ZM637.389 12.2449C634.843 12.8637 637.448 13.1121 634.778 12.1975C637.471 10.5944 631.898 12.3913 637.389 12.2449ZM602.719 14.2669C605.262 15.2098 603.695 16.6019 603.481 17.8002C598.895 17.4186 606.434 14.2669 601.195 15.2764C600.866 13.5189 600.364 15.0614 602.719 14.2669ZM524.633 15.0069C524.428 16.2375 521.788 18.2029 520.061 17.5306C520.267 16.299 522.904 14.3346 524.633 15.0069ZM410.559 15.1432C407.496 15.6813 408.953 17.3095 405.003 16.0164C406.458 14.9019 405.65 15.0069 410.559 15.1432ZM391.357 12.1147C395.32 12.0461 393.524 13.9601 392.812 15.4703C388.845 15.5359 390.64 13.6229 391.354 12.1127L391.357 12.1147ZM366.318 14.8646C367.367 15.3047 367.748 15.968 367.598 16.8129C364.259 16.9865 367.02 15.8912 363.095 16.2688C364.326 15.5631 362.86 14.7586 366.317 14.8625L366.318 14.8646Z" fill="' . $this->args['backgroundcolor'] . '"/>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'music' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '297px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 1024 297" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<path d="M0 206C0 201.029 4.02944 197 9 197V197C13.9706 197 18 201.029 18 206V297H0V206Z"/>
													<path d="M31.03 154C31.03 149.029 35.0595 145 40.03 145V145C45.0006 145 49.03 149.029 49.03 154V297H31.03V154Z"/>
													<path d="M62.06 112C62.06 107.029 66.0894 103 71.06 103V103C76.0306 103 80.06 107.029 80.06 112V297H62.06V112Z"/>
													<path d="M93.09 136C93.09 131.029 97.1194 127 102.09 127V127C107.061 127 111.09 131.029 111.09 136V297H93.09V136Z"/>
													<path d="M124.12 197C124.12 192.029 128.149 188 133.12 188V188C138.091 188 142.12 192.029 142.12 197V297H124.12V197Z"/>
													<path d="M155.15 259C155.15 254.029 159.179 250 164.15 250V250C169.121 250 173.15 254.029 173.15 259V297H155.15V259Z"/>
													<path d="M186.18 206C186.18 201.029 190.209 197 195.18 197V197C200.151 197 204.18 201.029 204.18 206V297H186.18V206Z"/>
													<path d="M217.21 127C217.21 122.029 221.239 118 226.21 118V118C231.181 118 235.21 122.029 235.21 127V297H217.21V127Z"/>
													<path d="M248.24 182C248.24 177.029 252.269 173 257.24 173V173C262.211 173 266.24 177.029 266.24 182V297H248.24V182Z"/>
													<path d="M279.27 240C279.27 235.029 283.299 231 288.27 231V231C293.241 231 297.27 235.029 297.27 240V297H279.27V240Z"/>
													<path d="M310.3 268C310.3 263.029 314.329 259 319.3 259V259C324.271 259 328.3 263.029 328.3 268V297H310.3V268Z"/>
													<path d="M341.33 228C341.33 223.029 345.359 219 350.33 219V219C355.301 219 359.33 223.029 359.33 228V297H341.33V228Z"/>
													<path d="M372.36 173C372.36 168.029 376.389 164 381.36 164V164C386.331 164 390.36 168.029 390.36 173V297H372.36V173Z"/>
													<path d="M403.39 206C403.39 201.029 407.419 197 412.39 197V197C417.361 197 421.39 201.029 421.39 206V297H403.39V206Z"/>
													<path d="M434.42 221C434.42 216.029 438.449 212 443.42 212V212C448.391 212 452.42 216.029 452.42 221V297H434.42V221Z"/>
													<path d="M465.45 234C465.45 229.029 469.479 225 474.45 225V225C479.421 225 483.45 229.029 483.45 234V297H465.45V234Z"/>
													<path d="M496.48 206C496.48 201.029 500.509 197 505.48 197V197C510.451 197 514.48 201.029 514.48 206V297H496.48V206Z"/>
													<path d="M527.51 164C527.51 159.029 531.539 155 536.51 155V155C541.481 155 545.51 159.029 545.51 164V297H527.51V164Z"/>
													<path d="M558.54 112C558.54 107.029 562.569 103 567.54 103V103C572.511 103 576.54 107.029 576.54 112V297H558.54V112Z"/>
													<path d="M589.57 173C589.57 168.029 593.6 164 598.57 164V164C603.541 164 607.57 168.029 607.57 173V297H589.57V173Z"/>
													<path d="M620.6 216C620.6 211.029 624.63 207 629.6 207V207C634.571 207 638.6 211.029 638.6 216V297H620.6V216Z"/>
													<path d="M651.63 245C651.63 240.029 655.66 236 660.63 236V236C665.601 236 669.63 240.029 669.63 245V297H651.63V245Z"/>
													<path d="M682.66 221C682.66 216.029 686.69 212 691.66 212V212C696.631 212 700.66 216.029 700.66 221V297H682.66V221Z"/>
													<path d="M713.69 173C713.69 168.029 717.72 164 722.69 164V164C727.661 164 731.69 168.029 731.69 173V297H713.69V173Z"/>
													<path d="M744.72 154C744.72 149.029 748.75 145 753.72 145V145C758.691 145 762.72 149.029 762.72 154V297H744.72V154Z"/>
													<path d="M775.75 206C775.75 201.029 779.78 197 784.75 197V197C789.721 197 793.75 201.029 793.75 206V297H775.75V206Z"/>
													<path d="M806.78 240C806.78 235.029 810.81 231 815.78 231V231C820.751 231 824.78 235.029 824.78 240V297H806.78V240Z"/>
													<path d="M837.81 234C837.81 229.029 841.84 225 846.81 225V225C851.781 225 855.81 229.029 855.81 234V297H837.81V234Z"/>
													<path d="M868.84 206C868.84 201.029 872.87 197 877.84 197V197C882.811 197 886.84 201.029 886.84 206V297H868.84V206Z"/>
													<path d="M899.87 154C899.87 149.029 903.9 145 908.87 145V145C913.841 145 917.87 149.029 917.87 154V297H899.87V154Z"/>
													<path d="M930.9 240C930.9 235.029 934.93 231 939.9 231V231C944.871 231 948.9 235.029 948.9 240V297H930.9V240Z"/>
													<path d="M961.93 206C961.93 201.029 965.96 197 970.93 197V197C975.901 197 979.93 201.029 979.93 206V297H961.93V206Z"/>
													<path d="M992.96 234C992.96 229.029 996.99 225 1001.96 225V225C1006.93 225 1010.96 229.029 1010.96 234V297H992.96V234Z"/>
													<rect opacity="0.1" y="127" width="18" height="57" rx="9"/>
													<rect opacity="0.1" x="496" y="151" width="18" height="33" rx="9"/>
													<rect opacity="0.16" x="279" y="115" width="18" height="57" rx="9"/>
													<rect opacity="0.1" x="931" y="161" width="18" height="57" rx="9"/>
													<rect opacity="0.1" x="124" y="132" width="18" height="43" rx="9"/>
													<rect opacity="0.1" x="590" y="108" width="18" height="43" rx="9"/>
													<rect opacity="0.1" x="465" y="169" width="18" height="43" rx="9"/>
													<rect opacity="0.1" x="310" y="202" width="18" height="43" rx="9"/>
													<rect opacity="0.16" x="31" y="103" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="745" y="39" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="652" y="194" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="776" y="155" width="18" height="29" rx="9"/>
													<rect opacity="0.16" x="931" y="119" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="528" y="113" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="248" y="131" width="18" height="29" rx="9"/>
													<rect opacity="0.16" x="403" y="139" width="18" height="45" rx="9"/>
													<rect opacity="0.16" x="186" y="155" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="900" y="103" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="621" y="165" width="18" height="29" rx="9"/>
													<rect opacity="0.1" x="31" y="40" width="18" height="45" rx="9"/>
													<rect opacity="0.1" x="745" y="86" width="18" height="45" rx="9"/>
													<rect opacity="0.16" x="652" y="138" width="18" height="38" rx="9"/>
													<rect opacity="0.16" x="776" y="99" width="18" height="38" rx="9"/>
													<rect opacity="0.16" x="528" y="50" width="18" height="45" rx="9"/>
													<rect opacity="0.1" x="248" y="85" width="18" height="31" rx="9"/>
													<rect opacity="0.1" x="186" y="105" width="18" height="34" rx="9"/>
													<rect opacity="0.1" x="900" y="53" width="18" height="34" rx="9"/>
													<rect opacity="0.1" x="621" y="115" width="18" height="34" rx="9"/>
													<rect opacity="0.1" x="93" y="60" width="18" height="53" rx="9"/>
													<rect opacity="0.1" y="82" width="18" height="28" rx="9"/>
													<rect opacity="0.16" x="496" y="110" width="18" height="28" rx="9"/>
													<rect opacity="0.1" x="279" y="189" width="18" height="28" rx="9"/>
													<rect opacity="0.1" x="683" y="171" width="18" height="28" rx="9"/>
													<rect opacity="0.16" x="124" y="84" width="18" height="28" rx="9"/>
													<rect opacity="0.16" x="838" y="184" width="18" height="28" rx="9"/>
													<rect opacity="0.1" x="838" y="133" width="18" height="39" rx="9"/>
													<rect opacity="0.1" x="590" y="60" width="18" height="28" rx="9"/>
													<rect opacity="0.1" x="310" y="154" width="18" height="28" rx="9"/>
													<rect opacity="0.1" x="62" y="40" width="18" height="50" rx="9"/>
													<rect opacity="0.1" x="714" y="68" width="18" height="50" rx="9"/>
													<rect opacity="0.1" x="559" y="29" width="18" height="61" rx="9"/>
													<rect opacity="0.1" x="341" y="156" width="18" height="50" rx="9"/>
													<rect opacity="0.1" x="807" y="168" width="18" height="50" rx="9"/>
													<rect opacity="0.1" x="217" y="48" width="18" height="57" rx="9"/>
													<rect opacity="0.16" x="993" y="137" width="18" height="75" rx="9"/>
													<rect opacity="0.1" x="372" y="85" width="18" height="66" rx="9"/>
													<rect opacity="0.1" x="869" y="118" width="18" height="66" rx="9"/>
													<rect opacity="0.1" x="155" y="143" width="18" height="59" rx="9"/>
													<rect opacity="0.1" x="962" y="90" width="18" height="59" rx="9"/>
													<rect opacity="0.1" x="62" y="4" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="714" y="133" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="559" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="341" y="120" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="807" y="132" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="869" y="89" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="155" y="219" width="18" height="18" rx="9"/>
													<rect opacity="0.1" x="962" y="166" width="18" height="18" rx="9"/>
													<rect opacity="0.16" x="434" y="181" width="18" height="18" rx="9"/>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'waves_brush' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '124px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 1666 124" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<g clip-path="url(#clip0)">
													<path fill-rule="evenodd" clip-rule="evenodd" d="M0 59.3392C53.3211 62.6751 74.4542 68.7308 96.869 75.1537C111.655 79.3905 126.998 83.7872 152.507 87.6684C246.274 101.936 310.235 76.6378 372.713 51.9265C416.933 34.4369 460.409 17.2411 513.185 14.5738C601.391 10.1158 638.744 28.1556 674.912 45.6234C690.96 53.3737 706.774 61.0114 726.694 66.5215C763.186 76.6157 806.204 65.2888 851.121 53.4618C885.984 44.2821 921.991 34.8012 956.979 34.8012C997.762 34.8012 1020.74 48.0409 1042.34 60.4857C1063.14 72.4726 1082.66 83.7221 1115.59 81.6921C1143.95 79.9433 1163.18 69.8168 1186.13 57.7352C1217.48 41.2339 1255.75 21.0856 1333.67 13.6544C1422.62 5.17156 1473.48 22.4439 1515.72 36.7884C1537.57 44.2106 1557.12 50.849 1578.44 52.7301C1640.97 58.2467 1656.22 59.3392 1666.13 59.3392V123.986H0V59.3392ZM626.861 39.353C612.488 34.9409 587.866 30.9952 586.217 32.6652C585.401 33.4911 591.747 34.8251 600.028 36.566C608.491 38.3452 618.975 40.5494 625.901 43.0703C631.855 45.1667 637.663 47.8077 642.601 50.0529C649.535 53.2058 654.752 55.5783 656.248 54.5667C658.808 52.8346 641.233 43.765 626.861 39.353ZM1218.9 62.3402V62.3406C1212.94 65.1327 1203.36 69.6209 1191.08 72.956C1172.69 77.2855 1173.15 77.8808 1173.94 78.9088C1173.96 78.9334 1173.98 78.9582 1173.99 78.9834C1174.01 79.0063 1174.03 79.0294 1174.05 79.0529C1174.07 79.0929 1174.1 79.1338 1174.13 79.1757C1174.52 79.7867 1191.86 78.9063 1204.7 74.0511C1217.15 68.5849 1227.66 60.0642 1225.48 59.7547C1224.67 59.64 1222.41 60.6959 1218.9 62.3402ZM1251.33 48.0631C1245.43 49.8427 1241.65 50.9807 1241.29 50.0478C1240.52 48.0245 1254.08 42.8646 1268.07 39.138C1281.98 36.0034 1297.03 36.9174 1296.95 37.5095C1296.94 37.6001 1296.94 37.6868 1296.94 37.7705C1296.91 38.7801 1296.9 39.3649 1278.71 41.4756C1268.05 43.0265 1258.24 45.983 1251.33 48.0631ZM286.677 97.0036C286.43 96.0461 282.539 96.8712 276.456 98.1615C269.345 99.6697 259.238 101.813 248.45 102.489C230.101 103.109 230.021 103.69 229.882 104.693C229.871 104.776 229.859 104.862 229.836 104.951C229.688 105.534 244.559 107.669 258.769 105.682C273.128 103.112 287.212 99.0803 286.677 97.0036ZM965.809 54.8064C968.337 54.8064 970.386 53.4814 970.386 51.8469C970.386 50.2127 968.337 48.8874 965.809 48.8874C965.436 48.8873 965.04 48.8796 964.637 48.8718C962.304 48.8267 959.706 48.7764 959.706 50.1698C959.706 50.9154 960.45 51.9456 961.499 52.8455C962.132 53.9887 963.823 54.8064 965.809 54.8064ZM566.059 43.7575C567.744 43.7575 569.11 42.6091 569.11 41.1926C569.11 39.776 567.744 38.6276 566.059 38.6276C564.373 38.6276 563.007 39.776 563.007 41.1926C563.007 42.6091 564.373 43.7575 566.059 43.7575Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M782.272 58.7885C781.355 58.0241 768.229 61.0585 753.759 60.2544C739.56 58.8995 740.477 59.6638 739.935 60.7653C739.664 61.3161 749.54 64.8907 760.125 64.3965C770.98 63.3515 783.189 59.5528 782.272 58.7885Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1365.46 1.61274C1365.85 -1.05535 1395.63 0.11857 1412.68 1.35242C1429.74 2.58628 1451.31 6.38023 1448.6 7.88626C1445.9 9.39228 1429.32 5.51596 1412.23 3.86855C1395.84 2.18638 1365.06 4.28082 1365.46 1.61274Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M599.703 6.32478C600.487 3.74412 620.685 8.60805 632.142 11.9465C643.598 15.2849 657.662 21.7094 655.52 22.8576C653.378 24.0059 642.753 18.1215 631.347 14.3703C620.43 10.6721 598.919 8.90544 599.703 6.32478Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M935.239 28.6501C936.625 29.4482 951.066 26.7015 968.587 27.8807C986 29.6116 984.614 28.8135 984.833 27.7102C984.942 27.1586 971.815 23.2857 959.417 23.5206C946.909 24.3072 933.854 27.852 935.239 28.6501Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M539.802 3.35783C539.802 3.35783 545.77 2.27983 532.453 2.71829C519.137 3.15675 486.715 3.16721 468.597 6.10662C436.962 11.4912 416.043 22.1625 418.605 24.3813C421.167 26.6001 448.462 10.2554 481.853 8.15584C495.795 7.15393 521.66 6.3594 533.786 4.56015C545.911 2.76089 539.802 3.35783 539.802 3.35783Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M237.8 77.2209C237.532 80.5169 210.482 82.3409 194.001 82.0675C177.52 81.7942 156.532 78.3298 157.3 75.497C158.067 72.6642 177.427 78.9728 194.155 79.8724C210.248 80.891 238.067 73.925 237.8 77.2209Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1590.54 39.7177C1590.33 38.8729 1573.25 41.743 1564.21 38.9695C1538.6 32.37 1515.19 22.7021 1516.19 25.247C1517.19 27.7918 1553.29 40.337 1565.05 42.3487C1582.35 46.1956 1590.75 40.5625 1590.54 39.7177Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1507.46 21.9555C1507.46 23.3721 1506.09 24.5204 1504.4 24.5204C1502.72 24.5204 1501.35 23.3721 1501.35 21.9555C1501.35 20.5389 1502.72 19.3905 1504.4 19.3905C1506.09 19.3905 1507.46 20.5389 1507.46 21.9555Z" fill="' . $this->args['backgroundcolor'] . '"/>
													</g>
													<defs>
													<clipPath id="clip0">
													<rect width="1666" height="124" fill="white"/>
													</clipPath>
													</defs>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'paper' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '102px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 1667 102" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<path d="M0 102V18L14 23H34L43 28H70L83 23L88 18L110 23L165 38C169.13 36.9132 174.712 35.4721 180.5 34.0232C184.719 32.9671 190.047 35.9301 194 35C201.258 33.2924 206.255 28 208 28C209.361 28 213.031 30.7641 215.5 29.5C216.777 28.8461 216.634 24.4684 218 23.652C221.756 21.407 227.081 29.2742 229.5 27.5L240.5 20.625H249.5L256 17.4737L267 14L278 25L280.5 31.652L287 29.5L291.5 35.5L298 38L304 35.5L314 38L325 37L329.5 38H336L348 35.5L354 28H365L370.5 20.5L382.5 20.875L389.5 17L402 20.875L409.5 17L424.5 18.5L435.5 17L451 18.5L463 17L471.5 23L478.5 20.875L487 24.5L498.5 25.5L505 28H510C510.958 29.5968 510.605 33.4726 512.5 35.5C514.561 37.7047 518.916 38 521 38H530L585 28L616 17L632 10L651.5 13.5L668.5 21.7L676.5 18.1L686 23.5L694.5 21.7L705.5 27.5L717 26.2L727 30.6786H733.5L744 37.5L754 38L786 28H814L868 17L887 19.1111L898 23L910 21.6667L917 24L927 22.3333L933 24L943.5 20.1957L956.5 21L964 17.5217L968 17L980 10H1005L1015 17H1052L1110 10L1132 0L1141 1.8L1156.5 8L1165.5 6.7L1180.5 11.625H1188.75L1195.5 14.6944H1201.5L1209.5 18L1221 19.3889L1235 27L1268 38L1311 28L1316 23L1338 17L1354 28L1364 38L1392 28.6667L1404.5 30L1409 23H1419.5L1427 17L1437 20L1445 28.6667L1456 23L1470.5 28.6667L1497.5 17L1505 10L1514 13L1522 10L1530.5 12L1536 5L1543.5 8.05L1553 5.40854L1563 10L1567 0L1584 8.05L1594 6.55L1604.5 2L1614.5 4.75L1631 11.5L1647.5 8.05L1667 18V102H0Z" fill="' . $this->args['backgroundcolor'] . '"/>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'circles' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '164px';

					$this->args['svg_element']  = '<svg width="100%" viewBox="0 0 1142 205" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<g clip-path="url(#clip0)">
													<path d="M34.5 205C55.2107 205 72 188.211 72 167.5C72 146.789 55.2107 130 34.5 130C13.7893 130 -3 146.789 -3 167.5C-3 188.211 13.7893 205 34.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M116.5 205C137.211 205 154 188.211 154 167.5C154 146.789 137.211 130 116.5 130C95.7893 130 79 146.789 79 167.5C79 188.211 95.7893 205 116.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M198.5 205C219.211 205 236 188.211 236 167.5C236 146.789 219.211 130 198.5 130C177.789 130 161 146.789 161 167.5C161 188.211 177.789 205 198.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M280.5 205C301.211 205 318 188.211 318 167.5C318 146.789 301.211 130 280.5 130C259.789 130 243 146.789 243 167.5C243 188.211 259.789 205 280.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M362.5 205C383.211 205 400 188.211 400 167.5C400 146.789 383.211 130 362.5 130C341.789 130 325 146.789 325 167.5C325 188.211 341.789 205 362.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M444.5 205C465.211 205 482 188.211 482 167.5C482 146.789 465.211 130 444.5 130C423.789 130 407 146.789 407 167.5C407 188.211 423.789 205 444.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M526.5 205C547.211 205 564 188.211 564 167.5C564 146.789 547.211 130 526.5 130C505.789 130 489 146.789 489 167.5C489 188.211 505.789 205 526.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M608.5 205C629.211 205 646 188.211 646 167.5C646 146.789 629.211 130 608.5 130C587.789 130 571 146.789 571 167.5C571 188.211 587.789 205 608.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M690.5 205C711.211 205 728 188.211 728 167.5C728 146.789 711.211 130 690.5 130C669.789 130 653 146.789 653 167.5C653 188.211 669.789 205 690.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M772.5 205C793.211 205 810 188.211 810 167.5C810 146.789 793.211 130 772.5 130C751.789 130 735 146.789 735 167.5C735 188.211 751.789 205 772.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M854.5 205C875.211 205 892 188.211 892 167.5C892 146.789 875.211 130 854.5 130C833.789 130 817 146.789 817 167.5C817 188.211 833.789 205 854.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M936.5 205C957.211 205 974 188.211 974 167.5C974 146.789 957.211 130 936.5 130C915.789 130 899 146.789 899 167.5C899 188.211 915.789 205 936.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M34.7998 77C45.8455 77 54.7998 68.0457 54.7998 57C54.7998 45.9543 45.8455 37 34.7998 37C23.7541 37 14.7998 45.9543 14.7998 57C14.7998 68.0457 23.7541 77 34.7998 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M115.8 77C126.846 77 135.8 68.0457 135.8 57C135.8 45.9543 126.846 37 115.8 37C104.754 37 95.7998 45.9543 95.7998 57C95.7998 68.0457 104.754 77 115.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M196.8 77C207.846 77 216.8 68.0457 216.8 57C216.8 45.9543 207.846 37 196.8 37C185.754 37 176.8 45.9543 176.8 57C176.8 68.0457 185.754 77 196.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M277.8 77C288.846 77 297.8 68.0457 297.8 57C297.8 45.9543 288.846 37 277.8 37C266.754 37 257.8 45.9543 257.8 57C257.8 68.0457 266.754 77 277.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M359.8 77C370.846 77 379.8 68.0457 379.8 57C379.8 45.9543 370.846 37 359.8 37C348.754 37 339.8 45.9543 339.8 57C339.8 68.0457 348.754 77 359.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M441.8 77C452.846 77 461.8 68.0457 461.8 57C461.8 45.9543 452.846 37 441.8 37C430.754 37 421.8 45.9543 421.8 57C421.8 68.0457 430.754 77 441.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M523.8 77C534.846 77 543.8 68.0457 543.8 57C543.8 45.9543 534.846 37 523.8 37C512.754 37 503.8 45.9543 503.8 57C503.8 68.0457 512.754 77 523.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M605.8 77C616.846 77 625.8 68.0457 625.8 57C625.8 45.9543 616.846 37 605.8 37C594.754 37 585.8 45.9543 585.8 57C585.8 68.0457 594.754 77 605.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M686.8 77C697.846 77 706.8 68.0457 706.8 57C706.8 45.9543 697.846 37 686.8 37C675.754 37 666.8 45.9543 666.8 57C666.8 68.0457 675.754 77 686.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M768.8 77C779.846 77 788.8 68.0457 788.8 57C788.8 45.9543 779.846 37 768.8 37C757.754 37 748.8 45.9543 748.8 57C748.8 68.0457 757.754 77 768.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M851.8 77C862.846 77 871.8 68.0457 871.8 57C871.8 45.9543 862.846 37 851.8 37C840.754 37 831.8 45.9543 831.8 57C831.8 68.0457 840.754 77 851.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M933.8 77C944.846 77 953.8 68.0457 953.8 57C953.8 45.9543 944.846 37 933.8 37C922.754 37 913.8 45.9543 913.8 57C913.8 68.0457 922.754 77 933.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M10.8 15.5001C10.8 8.57825 6.26267 2.71607 0 0.725586V30.2748C6.26267 28.2843 10.8 22.4221 10.8 15.5001Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M74.2998 31C82.8602 31 89.7998 24.0604 89.7998 15.5C89.7998 6.93959 82.8602 0 74.2998 0C65.7394 0 58.7998 6.93959 58.7998 15.5C58.7998 24.0604 65.7394 31 74.2998 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M156.3 31C164.86 31 171.8 24.0604 171.8 15.5C171.8 6.93959 164.86 0 156.3 0C147.739 0 140.8 6.93959 140.8 15.5C140.8 24.0604 147.739 31 156.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M237.3 31C245.86 31 252.8 24.0604 252.8 15.5C252.8 6.93959 245.86 0 237.3 0C228.739 0 221.8 6.93959 221.8 15.5C221.8 24.0604 228.739 31 237.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M318.3 31C326.86 31 333.8 24.0604 333.8 15.5C333.8 6.93959 326.86 0 318.3 0C309.739 0 302.8 6.93959 302.8 15.5C302.8 24.0604 309.739 31 318.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M400.3 31C408.86 31 415.8 24.0604 415.8 15.5C415.8 6.93959 408.86 0 400.3 0C391.739 0 384.8 6.93959 384.8 15.5C384.8 24.0604 391.739 31 400.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M482.3 31C490.86 31 497.8 24.0604 497.8 15.5C497.8 6.93959 490.86 0 482.3 0C473.739 0 466.8 6.93959 466.8 15.5C466.8 24.0604 473.739 31 482.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M564.3 31C572.86 31 579.8 24.0604 579.8 15.5C579.8 6.93959 572.86 0 564.3 0C555.739 0 548.8 6.93959 548.8 15.5C548.8 24.0604 555.739 31 564.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M646.3 31C654.86 31 661.8 24.0604 661.8 15.5C661.8 6.93959 654.86 0 646.3 0C637.739 0 630.8 6.93959 630.8 15.5C630.8 24.0604 637.739 31 646.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M728.3 31C736.86 31 743.8 24.0604 743.8 15.5C743.8 6.93959 736.86 0 728.3 0C719.739 0 712.8 6.93959 712.8 15.5C712.8 24.0604 719.739 31 728.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M811.3 31C819.86 31 826.8 24.0604 826.8 15.5C826.8 6.93959 819.86 0 811.3 0C802.739 0 795.8 6.93959 795.8 15.5C795.8 24.0604 802.739 31 811.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M894.3 31C902.86 31 909.8 24.0604 909.8 15.5C909.8 6.93959 902.86 0 894.3 0C885.739 0 878.8 6.93959 878.8 15.5C878.8 24.0604 885.739 31 894.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M22.8 103C22.8 89.3129 12.9793 77.9195 0 75.4819V130.518C12.9793 128.08 22.8 116.687 22.8 103Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M157.8 131C173.264 131 185.8 118.464 185.8 103C185.8 87.536 173.264 75 157.8 75C142.336 75 129.8 87.536 129.8 103C129.8 118.464 142.336 131 157.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M238.8 131C254.264 131 266.8 118.464 266.8 103C266.8 87.536 254.264 75 238.8 75C223.336 75 210.8 87.536 210.8 103C210.8 118.464 223.336 131 238.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M320.8 131C336.264 131 348.8 118.464 348.8 103C348.8 87.536 336.264 75 320.8 75C305.336 75 292.8 87.536 292.8 103C292.8 118.464 305.336 131 320.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M402.8 131C418.264 131 430.8 118.464 430.8 103C430.8 87.536 418.264 75 402.8 75C387.336 75 374.8 87.536 374.8 103C374.8 118.464 387.336 131 402.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M484.8 131C500.264 131 512.8 118.464 512.8 103C512.8 87.536 500.264 75 484.8 75C469.336 75 456.8 87.536 456.8 103C456.8 118.464 469.336 131 484.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M566.8 131C582.264 131 594.8 118.464 594.8 103C594.8 87.536 582.264 75 566.8 75C551.336 75 538.8 87.536 538.8 103C538.8 118.464 551.336 131 566.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M648.8 131C664.264 131 676.8 118.464 676.8 103C676.8 87.536 664.264 75 648.8 75C633.336 75 620.8 87.536 620.8 103C620.8 118.464 633.336 131 648.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M730.8 131C746.264 131 758.8 118.464 758.8 103C758.8 87.536 746.264 75 730.8 75C715.336 75 702.8 87.536 702.8 103C702.8 118.464 715.336 131 730.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M812.8 131C828.264 131 840.8 118.464 840.8 103C840.8 87.536 828.264 75 812.8 75C797.336 75 784.8 87.536 784.8 103C784.8 118.464 797.336 131 812.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M894.8 131C910.264 131 922.8 118.464 922.8 103C922.8 87.536 910.264 75 894.8 75C879.336 75 866.8 87.536 866.8 103C866.8 118.464 879.336 131 894.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M74.7998 131C90.2638 131 102.8 118.464 102.8 103C102.8 87.536 90.2638 75 74.7998 75C59.3358 75 46.7998 87.536 46.7998 103C46.7998 118.464 59.3358 131 74.7998 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1960 153H0V205H1960V153Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1014.5 205C1035.21 205 1052 188.211 1052 167.5C1052 146.789 1035.21 130 1014.5 130C993.789 130 977 146.789 977 167.5C977 188.211 993.789 205 1014.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1096.5 205C1117.21 205 1134 188.211 1134 167.5C1134 146.789 1117.21 130 1096.5 130C1075.79 130 1059 146.789 1059 167.5C1059 188.211 1075.79 205 1096.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1178.5 205C1199.21 205 1216 188.211 1216 167.5C1216 146.789 1199.21 130 1178.5 130C1157.79 130 1141 146.789 1141 167.5C1141 188.211 1157.79 205 1178.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1260.5 205C1281.21 205 1298 188.211 1298 167.5C1298 146.789 1281.21 130 1260.5 130C1239.79 130 1223 146.789 1223 167.5C1223 188.211 1239.79 205 1260.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1342.5 205C1363.21 205 1380 188.211 1380 167.5C1380 146.789 1363.21 130 1342.5 130C1321.79 130 1305 146.789 1305 167.5C1305 188.211 1321.79 205 1342.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1424.5 205C1445.21 205 1462 188.211 1462 167.5C1462 146.789 1445.21 130 1424.5 130C1403.79 130 1387 146.789 1387 167.5C1387 188.211 1403.79 205 1424.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1506.5 205C1527.21 205 1544 188.211 1544 167.5C1544 146.789 1527.21 130 1506.5 130C1485.79 130 1469 146.789 1469 167.5C1469 188.211 1485.79 205 1506.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1588.5 205C1609.21 205 1626 188.211 1626 167.5C1626 146.789 1609.21 130 1588.5 130C1567.79 130 1551 146.789 1551 167.5C1551 188.211 1567.79 205 1588.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1670.5 205C1691.21 205 1708 188.211 1708 167.5C1708 146.789 1691.21 130 1670.5 130C1649.79 130 1633 146.789 1633 167.5C1633 188.211 1649.79 205 1670.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1752.5 205C1773.21 205 1790 188.211 1790 167.5C1790 146.789 1773.21 130 1752.5 130C1731.79 130 1715 146.789 1715 167.5C1715 188.211 1731.79 205 1752.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1834.5 205C1855.21 205 1872 188.211 1872 167.5C1872 146.789 1855.21 130 1834.5 130C1813.79 130 1797 146.789 1797 167.5C1797 188.211 1813.79 205 1834.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1916.5 205C1937.21 205 1954 188.211 1954 167.5C1954 146.789 1937.21 130 1916.5 130C1895.79 130 1879 146.789 1879 167.5C1879 188.211 1895.79 205 1916.5 205Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1014.8 77C1025.85 77 1034.8 68.0457 1034.8 57C1034.8 45.9543 1025.85 37 1014.8 37C1003.75 37 994.8 45.9543 994.8 57C994.8 68.0457 1003.75 77 1014.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1095.8 77C1106.85 77 1115.8 68.0457 1115.8 57C1115.8 45.9543 1106.85 37 1095.8 37C1084.75 37 1075.8 45.9543 1075.8 57C1075.8 68.0457 1084.75 77 1095.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1176.8 77C1187.85 77 1196.8 68.0457 1196.8 57C1196.8 45.9543 1187.85 37 1176.8 37C1165.75 37 1156.8 45.9543 1156.8 57C1156.8 68.0457 1165.75 77 1176.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1257.8 77C1268.85 77 1277.8 68.0457 1277.8 57C1277.8 45.9543 1268.85 37 1257.8 37C1246.75 37 1237.8 45.9543 1237.8 57C1237.8 68.0457 1246.75 77 1257.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1339.8 77C1350.85 77 1359.8 68.0457 1359.8 57C1359.8 45.9543 1350.85 37 1339.8 37C1328.75 37 1319.8 45.9543 1319.8 57C1319.8 68.0457 1328.75 77 1339.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1421.8 77C1432.85 77 1441.8 68.0457 1441.8 57C1441.8 45.9543 1432.85 37 1421.8 37C1410.75 37 1401.8 45.9543 1401.8 57C1401.8 68.0457 1410.75 77 1421.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1503.8 77C1514.85 77 1523.8 68.0457 1523.8 57C1523.8 45.9543 1514.85 37 1503.8 37C1492.75 37 1483.8 45.9543 1483.8 57C1483.8 68.0457 1492.75 77 1503.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1585.8 77C1596.85 77 1605.8 68.0457 1605.8 57C1605.8 45.9543 1596.85 37 1585.8 37C1574.75 37 1565.8 45.9543 1565.8 57C1565.8 68.0457 1574.75 77 1585.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1666.8 77C1677.85 77 1686.8 68.0457 1686.8 57C1686.8 45.9543 1677.85 37 1666.8 37C1655.75 37 1646.8 45.9543 1646.8 57C1646.8 68.0457 1655.75 77 1666.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1748.8 77C1759.85 77 1768.8 68.0457 1768.8 57C1768.8 45.9543 1759.85 37 1748.8 37C1737.75 37 1728.8 45.9543 1728.8 57C1728.8 68.0457 1737.75 77 1748.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1831.8 77C1842.85 77 1851.8 68.0457 1851.8 57C1851.8 45.9543 1842.85 37 1831.8 37C1820.75 37 1811.8 45.9543 1811.8 57C1811.8 68.0457 1820.75 77 1831.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1913.8 77C1924.85 77 1933.8 68.0457 1933.8 57C1933.8 45.9543 1924.85 37 1913.8 37C1902.75 37 1893.8 45.9543 1893.8 57C1893.8 68.0457 1902.75 77 1913.8 77Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1054.3 31C1062.86 31 1069.8 24.0604 1069.8 15.5C1069.8 6.93959 1062.86 0 1054.3 0C1045.74 0 1038.8 6.93959 1038.8 15.5C1038.8 24.0604 1045.74 31 1054.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M975.3 31C983.86 31 990.8 24.0604 990.8 15.5C990.8 6.93959 983.86 0 975.3 0C966.739 0 959.8 6.93959 959.8 15.5C959.8 24.0604 966.739 31 975.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1136.3 31C1144.86 31 1151.8 24.0604 1151.8 15.5C1151.8 6.93959 1144.86 0 1136.3 0C1127.74 0 1120.8 6.93959 1120.8 15.5C1120.8 24.0604 1127.74 31 1136.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1217.3 31C1225.86 31 1232.8 24.0604 1232.8 15.5C1232.8 6.93959 1225.86 0 1217.3 0C1208.74 0 1201.8 6.93959 1201.8 15.5C1201.8 24.0604 1208.74 31 1217.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1298.3 31C1306.86 31 1313.8 24.0604 1313.8 15.5C1313.8 6.93959 1306.86 0 1298.3 0C1289.74 0 1282.8 6.93959 1282.8 15.5C1282.8 24.0604 1289.74 31 1298.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1380.3 31C1388.86 31 1395.8 24.0604 1395.8 15.5C1395.8 6.93959 1388.86 0 1380.3 0C1371.74 0 1364.8 6.93959 1364.8 15.5C1364.8 24.0604 1371.74 31 1380.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1462.3 31C1470.86 31 1477.8 24.0604 1477.8 15.5C1477.8 6.93959 1470.86 0 1462.3 0C1453.74 0 1446.8 6.93959 1446.8 15.5C1446.8 24.0604 1453.74 31 1462.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1544.3 31C1552.86 31 1559.8 24.0604 1559.8 15.5C1559.8 6.93959 1552.86 0 1544.3 0C1535.74 0 1528.8 6.93959 1528.8 15.5C1528.8 24.0604 1535.74 31 1544.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1626.3 31C1634.86 31 1641.8 24.0604 1641.8 15.5C1641.8 6.93959 1634.86 0 1626.3 0C1617.74 0 1610.8 6.93959 1610.8 15.5C1610.8 24.0604 1617.74 31 1626.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1708.3 31C1716.86 31 1723.8 24.0604 1723.8 15.5C1723.8 6.93959 1716.86 0 1708.3 0C1699.74 0 1692.8 6.93959 1692.8 15.5C1692.8 24.0604 1699.74 31 1708.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1791.3 31C1799.86 31 1806.8 24.0604 1806.8 15.5C1806.8 6.93959 1799.86 0 1791.3 0C1782.74 0 1775.8 6.93959 1775.8 15.5C1775.8 24.0604 1782.74 31 1791.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1874.3 31C1882.86 31 1889.8 24.0604 1889.8 15.5C1889.8 6.93959 1882.86 0 1874.3 0C1865.74 0 1858.8 6.93959 1858.8 15.5C1858.8 24.0604 1865.74 31 1874.3 31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1939.8 15.5C1939.8 24.0604 1946.74 31 1955.3 31C1956.94 31 1958.52 30.7458 1960 30.2746V0.725376C1958.52 0.254223 1956.94 0 1955.3 0C1946.74 0 1939.8 6.93959 1939.8 15.5Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1137.8 131C1153.26 131 1165.8 118.464 1165.8 103C1165.8 87.536 1153.26 75 1137.8 75C1122.34 75 1109.8 87.536 1109.8 103C1109.8 118.464 1122.34 131 1137.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1218.8 131C1234.26 131 1246.8 118.464 1246.8 103C1246.8 87.536 1234.26 75 1218.8 75C1203.34 75 1190.8 87.536 1190.8 103C1190.8 118.464 1203.34 131 1218.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1300.8 131C1316.26 131 1328.8 118.464 1328.8 103C1328.8 87.536 1316.26 75 1300.8 75C1285.34 75 1272.8 87.536 1272.8 103C1272.8 118.464 1285.34 131 1300.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1382.8 131C1398.26 131 1410.8 118.464 1410.8 103C1410.8 87.536 1398.26 75 1382.8 75C1367.34 75 1354.8 87.536 1354.8 103C1354.8 118.464 1367.34 131 1382.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1464.8 131C1480.26 131 1492.8 118.464 1492.8 103C1492.8 87.536 1480.26 75 1464.8 75C1449.34 75 1436.8 87.536 1436.8 103C1436.8 118.464 1449.34 131 1464.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1546.8 131C1562.26 131 1574.8 118.464 1574.8 103C1574.8 87.536 1562.26 75 1546.8 75C1531.34 75 1518.8 87.536 1518.8 103C1518.8 118.464 1531.34 131 1546.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1628.8 131C1644.26 131 1656.8 118.464 1656.8 103C1656.8 87.536 1644.26 75 1628.8 75C1613.34 75 1600.8 87.536 1600.8 103C1600.8 118.464 1613.34 131 1628.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1710.8 131C1726.26 131 1738.8 118.464 1738.8 103C1738.8 87.536 1726.26 75 1710.8 75C1695.34 75 1682.8 87.536 1682.8 103C1682.8 118.464 1695.34 131 1710.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1792.8 131C1808.26 131 1820.8 118.464 1820.8 103C1820.8 87.536 1808.26 75 1792.8 75C1777.34 75 1764.8 87.536 1764.8 103C1764.8 118.464 1777.34 131 1792.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1874.8 131C1890.26 131 1902.8 118.464 1902.8 103C1902.8 87.536 1890.26 75 1874.8 75C1859.34 75 1846.8 87.536 1846.8 103C1846.8 118.464 1859.34 131 1874.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1926.8 103C1926.8 118.464 1939.34 131 1954.8 131C1956.58 131 1958.31 130.834 1960 130.518V75.4819C1958.31 75.1655 1956.58 75 1954.8 75C1939.34 75 1926.8 87.536 1926.8 103Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1054.8 131C1070.26 131 1082.8 118.464 1082.8 103C1082.8 87.536 1070.26 75 1054.8 75C1039.34 75 1026.8 87.536 1026.8 103C1026.8 118.464 1039.34 131 1054.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M974.8 131C990.264 131 1002.8 118.464 1002.8 103C1002.8 87.536 990.264 75 974.8 75C959.336 75 946.8 87.536 946.8 103C946.8 118.464 959.336 131 974.8 131Z" fill="' . $this->args['backgroundcolor'] . '"/>
													</g>
													<defs>
													<clipPath id="clip0">
													<rect width="1142" height="205" fill="white"/>
													</clipPath>
													</defs>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'squares' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '140px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 2463 360" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<g clip-path="url(#clip0)">
													<path d="M-0.628906 314.356L76.3711 238.263L153.371 314.356L230.371 238.263L307.371 314.356L384.371 238.263L461.371 314.356L538.371 238.263L615.371 314.356L692.371 238.263L769.371 314.356L846.371 238.263L923.371 314.356L1000.37 238.263L1077.37 314.356L1154.37 238.263L1231.37 314.356L1308.37 238.263L1385.37 314.356L1462.37 238.263L1539.37 314.356L1616.37 238.263L1693.37 314.356L1770.37 238.263L1847.37 314.356L1924.37 238.263L2001.37 314.356L2078.37 238.263L2155.37 314.356L2232.37 238.263L2309.37 314.356L2386.37 238.263L2463.37 314.356V360.263H1847.37H1231.37H615.371H-0.628906V314.356Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M222.668 231.224L153.5 162.001L84.332 231.224L153.5 300.446L222.668 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1454.67 231.224L1385.5 162.001L1316.33 231.224L1385.5 300.446L1454.67 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M838.668 231.224L769.5 162.001L700.332 231.224L769.5 300.446L838.668 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M2070.67 231.224L2001.5 162.001L1932.33 231.224L2001.5 300.446L2070.67 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M145.669 156.223L76.5003 87.001L7.33203 156.223L76.5003 225.446L145.669 156.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1377.67 156.223L1308.5 87.001L1239.33 156.223L1308.5 225.446L1377.67 156.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M761.669 156.223L692.5 87.001L623.332 156.223L692.5 225.446L761.669 156.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1993.67 156.223L1924.5 87.001L1855.33 156.223L1924.5 225.446L1993.67 156.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M684.669 231.223L615.5 162.001L546.332 231.223L615.5 300.446L684.669 231.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1916.67 231.223L1847.5 162.001L1778.33 231.223L1847.5 300.446L1916.67 231.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M184.614 96.8333L152.807 65.001L121 96.8333L152.807 128.665L184.614 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1416.61 96.8333L1384.81 65.001L1353 96.8333L1384.81 128.665L1416.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1263.61 96.8333L1231.81 65.001L1200 96.8333L1231.81 128.665L1263.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M800.614 96.8333L768.807 65.001L737 96.8333L768.807 128.665L800.614 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2032.61 96.8333L2000.81 65.001L1969 96.8333L2000.81 128.665L2032.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M647.614 96.8333L615.807 65.001L584 96.8333L615.807 128.665L647.614 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1879.61 96.8333L1847.81 65.001L1816 96.8333L1847.81 128.665L1879.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M338.614 96.8333L306.807 65.001L275 96.8333L306.807 128.665L338.614 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1570.61 96.8333L1538.81 65.001L1507 96.8333L1538.81 128.665L1570.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M954.614 96.8333L922.807 65.001L891 96.8333L922.807 128.665L954.614 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2186.61 96.8333L2154.81 65.001L2123 96.8333L2154.81 128.665L2186.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M492.614 96.8333L460.807 65.001L429 96.8333L460.807 128.665L492.614 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1724.61 96.8333L1692.81 65.001L1661 96.8333L1692.81 128.665L1724.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1108.61 96.8333L1076.81 65.001L1045 96.8333L1076.81 128.665L1108.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2340.61 96.8333L2308.81 65.001L2277 96.8333L2308.81 128.665L2340.61 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M213.785 59.6856L196.114 42.001L178.443 59.6856L196.114 77.3702L213.785 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1445.79 59.6856L1428.11 42.001L1410.44 59.6856L1428.11 77.3702L1445.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M829.785 59.6856L812.114 42.001L794.443 59.6856L812.114 77.3702L829.785 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M2061.79 59.6856L2044.11 42.001L2026.44 59.6856L2044.11 77.3702L2061.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M367.785 59.6856L350.114 42.001L332.443 59.6856L350.114 77.3702L367.785 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1599.79 59.6856L1582.11 42.001L1564.44 59.6856L1582.11 77.3702L1599.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M983.785 59.6856L966.114 42.001L948.443 59.6856L966.114 77.3702L983.785 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M2215.79 59.6856L2198.11 42.001L2180.44 59.6856L2198.11 77.3702L2215.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M521.785 59.6856L504.114 42.001L486.443 59.6856L504.114 77.3702L521.785 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1753.79 59.6856L1736.11 42.001L1718.44 59.6856L1736.11 77.3702L1753.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1137.79 59.6856L1120.11 42.001L1102.44 59.6856L1120.11 77.3702L1137.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M2369.79 59.6856L2352.11 42.001L2334.44 59.6856L2352.11 77.3702L2369.79 59.6856Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M124.094 59.6856L106.423 42.001L88.752 59.6856L106.423 77.3702L124.094 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1356.09 59.6856L1338.42 42.001L1320.75 59.6856L1338.42 77.3702L1356.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M740.094 59.6856L722.423 42.001L704.752 59.6856L722.423 77.3702L740.094 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1972.09 59.6856L1954.42 42.001L1936.75 59.6856L1954.42 77.3702L1972.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M278.094 59.6856L260.423 42.001L242.752 59.6856L260.423 77.3702L278.094 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1510.09 59.6856L1492.42 42.001L1474.75 59.6856L1492.42 77.3702L1510.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M894.094 59.6856L876.423 42.001L858.752 59.6856L876.423 77.3702L894.094 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2126.09 59.6856L2108.42 42.001L2090.75 59.6856L2108.42 77.3702L2126.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M432.094 59.6856L414.423 42.001L396.752 59.6856L414.423 77.3702L432.094 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1664.09 59.6856L1646.42 42.001L1628.75 59.6856L1646.42 77.3702L1664.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1048.09 59.6856L1030.42 42.001L1012.75 59.6856L1030.42 77.3702L1048.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2280.09 59.6856L2262.42 42.001L2244.75 59.6856L2262.42 77.3702L2280.09 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M170.401 18.6856L152.73 1.00098L135.059 18.6856L152.73 36.3702L170.401 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1402.4 18.6856L1384.73 1.00098L1367.06 18.6856L1384.73 36.3702L1402.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1249.4 18.6856L1231.73 1.00098L1214.06 18.6856L1231.73 36.3702L1249.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M786.401 18.6856L768.73 1.00098L751.059 18.6856L768.73 36.3702L786.401 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2018.4 18.6856L2000.73 1.00098L1983.06 18.6856L2000.73 36.3702L2018.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M633.401 18.6856L615.73 1.00098L598.059 18.6856L615.73 36.3702L633.401 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1865.4 18.6856L1847.73 1.00098L1830.06 18.6856L1847.73 36.3702L1865.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M324.401 18.6856L306.73 1.00098L289.059 18.6856L306.73 36.3702L324.401 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1556.4 18.6856L1538.73 1.00098L1521.06 18.6856L1538.73 36.3702L1556.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M940.401 18.6856L922.73 1.00098L905.059 18.6856L922.73 36.3702L940.401 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2172.4 18.6856L2154.73 1.00098L2137.06 18.6856L2154.73 36.3702L2172.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M478.401 18.6856L460.73 1.00098L443.059 18.6856L460.73 36.3702L478.401 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1710.4 18.6856L1692.73 1.00098L1675.06 18.6856L1692.73 36.3702L1710.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1094.4 18.6856L1076.73 1.00098L1059.06 18.6856L1076.73 36.3702L1094.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2326.4 18.6856L2308.73 1.00098L2291.06 18.6856L2308.73 36.3702L2326.4 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2431.57 96.8333L2463.38 128.665V65.001L2431.57 96.8333Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M586.662 59.6856L568.991 42.001L551.32 59.6856L568.991 77.3702L586.662 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1818.66 59.6856L1800.99 42.001L1783.32 59.6856L1800.99 77.3702L1818.66 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1202.66 59.6856L1184.99 42.001L1167.32 59.6856L1184.99 77.3702L1202.66 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2434.66 59.6856L2416.99 42.001L2399.32 59.6856L2416.99 77.3702L2434.66 59.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2445.63 18.6856L2463.3 36.3702V1.00098L2445.63 18.6856Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M31.0944 96.7678L-0.712885 64.9355L-0.712891 128.6L31.0944 96.7678Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M28.0078 59.9795L45.6785 77.6641L63.3492 59.9795L45.6785 42.2949L28.0078 59.9795Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1260.01 59.9795L1277.68 77.6641L1295.35 59.9795L1277.68 42.2949L1260.01 59.9795Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M644.008 59.9795L661.679 77.6641L679.349 59.9795L661.679 42.2949L644.008 59.9795Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1876.01 59.9795L1893.68 77.6641L1911.35 59.9795L1893.68 42.2949L1876.01 59.9795Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M16.9618 18.3154L-0.708981 0.630859L-0.708984 36L16.9618 18.3154Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M299.668 155.223L230.5 86.001L161.332 155.223L230.5 224.446L299.668 155.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1531.67 155.223L1462.5 86.001L1393.33 155.223L1462.5 224.446L1531.67 155.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M915.668 155.223L846.5 86.001L777.332 155.223L846.5 224.446L915.668 155.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2147.67 155.223L2078.5 86.001L2009.33 155.223L2078.5 224.446L2147.67 155.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M453.668 154.223L384.5 85.001L315.332 154.223L384.5 223.446L453.668 154.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1685.67 154.223L1616.5 85.001L1547.33 154.223L1616.5 223.446L1685.67 154.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1069.67 154.223L1000.5 85.001L931.332 154.223L1000.5 223.446L1069.67 154.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2301.67 154.223L2232.5 85.001L2163.33 154.223L2232.5 223.446L2301.67 154.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M607.668 153.223L538.5 84.001L469.332 153.223L538.5 222.446L607.668 153.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1839.67 153.223L1770.5 84.001L1701.33 153.223L1770.5 222.446L1839.67 153.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1223.67 153.223L1154.5 84.001L1085.33 153.223L1154.5 222.446L1223.67 153.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2455.67 153.223L2386.5 84.001L2317.33 153.223L2386.5 222.446L2455.67 153.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M68.5374 231.223L-0.630859 162.001V300.446L68.5374 231.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M376.568 231.224L307.4 162.001L238.232 231.224L307.4 300.446L376.568 231.224Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1608.57 231.224L1539.4 162.001L1470.23 231.224L1539.4 300.446L1608.57 231.224Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1300.57 231.224L1231.4 162.001L1162.23 231.224L1231.4 300.446L1300.57 231.224Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M992.568 231.224L923.4 162.001L854.232 231.224L923.4 300.446L992.568 231.224Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2224.57 231.224L2155.4 162.001L2086.23 231.224L2155.4 300.446L2224.57 231.224Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M530.469 231.224L461.301 162.001L392.133 231.224L461.301 300.446L530.469 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1762.47 231.224L1693.3 162.001L1624.13 231.224L1693.3 300.446L1762.47 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M1146.47 231.224L1077.3 162.001L1008.13 231.224L1077.3 300.446L1146.47 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M2378.47 231.224L2309.3 162.001L2240.13 231.224L2309.3 300.446L2378.47 231.224Z" fill="' . $this->args['backgroundcolor'] . '" fill-opacity="0.5"/>
													<path d="M2394.2 231.223L2463.37 300.446V162.001L2394.2 231.223Z" fill="' . $this->args['backgroundcolor'] . '"/>
													</g>
													<defs>
													<clipPath id="clip0">
													<rect width="100%" height="360" fill="white"/>
													</clipPath>
													</defs>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'paint' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '80px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 1803 80" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<path fill-rule="evenodd" clip-rule="evenodd" d="M0 80H1803V26C1784.41 25.8996 1762.68 9.75718 1738.97 9.57099C1693.26 7.04734 1626.64 5 1620 5C1613.36 5 1618.69 6.38627 1635.43 8.57938C1519.69 7.26321 1396.35 5.10623 1357.8 2.00005C1283.33 -3.99995 628.99 11.0001 613.147 19.0001C611.092 20.0374 598.481 19.7347 581.75 19.333C565.698 18.9477 545.856 18.4714 527.905 19.0001C484.229 17.4357 343.332 26.7 339 27.5C335.893 28.0739 354.583 28.5402 379.722 29.1674C389.625 29.4145 400.529 29.6866 411.495 30C300.112 32.3341 233.922 25.2149 204.382 19.0001C170.16 11.8001 53.8682 19.3334 0 26.0001V80ZM931.932 15C986.631 11.7818 1105.82 6.01373 1144.97 8.68733C1158.02 9.57804 1148.42 9.68297 1126.58 9.92161C1113.52 10.0644 1096.09 10.255 1076.5 10.6902C1056.27 11.1397 1033.75 11.9388 1011.4 12.7321C982.962 13.7411 954.791 14.7408 931.932 15Z" fill="' . $this->args['backgroundcolor'] . '"/>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'grass' === $this->args['divider_type'] ) {
					// Apply default height.
					$this->args['default_divider_height'] = '195px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 2241 195" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<g clip-path="url(#clip0)">
													<path fill-rule="evenodd" clip-rule="evenodd" d="M0 195H2245V141.26C2221.85 141.16 2194.8 125.095 2165.27 124.91C2108.36 122.398 2025.41 120.361 2017.14 120.361C2008.87 120.361 2015.51 121.74 2036.35 123.923C1892.24 122.613 1738.66 120.466 1690.66 117.375C1597.93 111.404 783.185 126.332 763.458 134.294C760.899 135.326 745.197 135.025 724.364 134.625C704.377 134.241 679.671 133.767 657.319 134.294C602.936 132.737 427.499 141.956 422.105 142.753C418.236 143.324 441.508 143.788 472.81 144.412C485.14 144.658 498.717 144.929 512.372 145.241C373.684 147.563 291.267 140.478 254.486 134.294C211.874 127.128 67.0738 134.625 0 141.26V195ZM1160.39 130.313C1228.5 127.11 1376.91 121.37 1425.66 124.03C1441.91 124.917 1429.95 125.021 1402.76 125.259C1386.5 125.401 1364.79 125.59 1340.4 126.024C1315.21 126.471 1287.17 127.266 1259.34 128.056C1223.93 129.06 1188.86 130.055 1160.39 130.313Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M142 71C80.9616 106.752 94.9881 152.563 109.631 171L142 157.726C95.7583 130.823 122.733 88.6991 142 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M-1 71C60.0384 106.752 46.0119 152.563 31.3689 171L-1 157.726C45.2417 130.823 18.2674 88.6991 -1 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1142 71C1080.96 106.752 1094.99 152.563 1109.63 171L1142 157.726C1095.76 130.823 1122.73 88.6991 1142 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M682 71C620.962 106.752 634.988 152.563 649.631 171L682 157.726C635.758 130.823 662.733 88.6991 682 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M276 71C214.962 106.752 228.988 152.563 243.631 171L276 157.726C229.758 130.823 256.733 88.6991 276 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1682 71C1620.96 106.752 1634.99 152.563 1649.63 171L1682 157.726C1635.76 130.823 1662.73 88.6991 1682 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M320.973 55.3945C252.243 95.6521 268.037 147.236 284.526 167.996L320.973 153.049C268.905 122.756 299.278 75.324 320.973 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1320.97 55.3945C1252.24 95.6521 1268.04 147.236 1284.53 167.996L1320.97 153.049C1268.9 122.756 1299.28 75.324 1320.97 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2158.31 56.3945C2227.04 96.6521 2211.25 148.236 2194.76 168.996L2158.31 154.049C2210.38 123.756 2180.01 76.324 2158.31 56.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M860.973 55.3945C792.243 95.6521 808.037 147.236 824.526 167.996L860.973 153.049C808.905 122.756 839.278 75.324 860.973 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1860.97 55.3945C1792.24 95.6521 1808.04 147.236 1824.53 167.996L1860.97 153.049C1808.9 122.756 1839.28 75.324 1860.97 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M283 71C221.962 106.752 235.988 152.563 250.631 171L283 157.726C236.758 130.823 263.733 88.6991 283 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1283 71C1221.96 106.752 1235.99 152.563 1250.63 171L1283 157.726C1236.76 130.823 1263.73 88.6991 1283 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2196.29 72C2257.32 107.752 2243.3 153.563 2228.65 172L2196.29 158.726C2242.53 131.823 2215.55 89.6991 2196.29 72Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M823 71C761.962 106.752 775.988 152.563 790.631 171L823 157.726C776.758 130.823 803.733 88.6991 823 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1823 71C1761.96 106.752 1775.99 152.563 1790.63 171L1823 157.726C1776.76 130.823 1803.73 88.6991 1823 71Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M479.741 55.3945C411.011 95.6521 426.805 147.236 443.293 167.996L479.741 153.049C427.672 122.756 458.046 75.324 479.741 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M73.741 55.3945C5.01077 95.6521 20.8049 147.236 37.2932 167.996L73.741 153.049C21.6722 122.756 52.0457 75.324 73.741 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1479.74 55.3945C1411.01 95.6521 1426.8 147.236 1443.29 167.996L1479.74 153.049C1427.67 122.756 1458.05 75.324 1479.74 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1999.54 56.3945C2068.27 96.6521 2052.48 148.236 2035.99 168.996L1999.54 154.049C2051.61 123.756 2021.24 76.324 1999.54 56.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1019.74 55.3945C951.011 95.6521 966.805 147.236 983.293 167.996L1019.74 153.049C967.672 122.756 998.046 75.324 1019.74 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2019.74 55.3945C1951.01 95.6521 1966.8 147.236 1983.29 167.996L2019.74 153.049C1967.67 122.756 1998.05 75.324 2019.74 55.3945Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M262 91C200.962 126.752 214.988 172.563 229.631 191L262 177.726C215.758 150.823 242.733 108.699 262 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1262 91C1200.96 126.752 1214.99 172.563 1229.63 191L1262 177.726C1215.76 150.823 1242.73 108.699 1262 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M802 91C740.962 126.752 754.988 172.563 769.631 191L802 177.726C755.758 150.823 782.733 108.699 802 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1802 91C1740.96 126.752 1754.99 172.563 1769.63 191L1802 177.726C1755.76 150.823 1782.73 108.699 1802 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M456.095 77.915C387.364 118.173 403.158 169.757 419.647 190.517L456.095 175.57C404.026 145.277 434.399 97.8445 456.095 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M50.0946 77.915C-18.6357 118.173 -2.84163 169.757 13.6467 190.517L50.0946 175.57C-1.9743 145.277 28.3992 97.8445 50.0946 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1456.09 77.915C1387.36 118.173 1403.16 169.757 1419.65 190.517L1456.09 175.57C1404.03 145.277 1434.4 97.8445 1456.09 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2023.19 78.915C2091.92 119.173 2076.13 170.757 2059.64 191.517L2023.19 176.57C2075.26 146.277 2044.89 98.8445 2023.19 78.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M996.095 77.915C927.364 118.173 943.158 169.757 959.647 190.517L996.095 175.57C944.026 145.277 974.399 97.8445 996.095 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1996.09 77.915C1927.36 118.173 1943.16 169.757 1959.65 190.517L1996.09 175.57C1944.03 145.277 1974.4 97.8445 1996.09 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M403 91C341.962 126.752 355.988 172.563 370.631 191L403 177.726C356.758 150.823 383.733 108.699 403 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1403 91C1341.96 126.752 1355.99 172.563 1370.63 191L1403 177.726C1356.76 150.823 1383.73 108.699 1403 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M943 91C881.962 126.752 895.988 172.563 910.631 191L943 177.726C896.758 150.823 923.733 108.699 943 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1943 91C1881.96 126.752 1895.99 172.563 1910.63 191L1943 177.726C1896.76 150.823 1923.73 108.699 1943 91Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M614.864 77.915C546.134 118.173 561.928 169.757 578.416 190.517L614.864 175.57C562.795 145.277 593.169 97.8445 614.864 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M208.864 77.915C140.134 118.173 155.928 169.757 172.416 190.517L208.864 175.57C156.795 145.277 187.169 97.8445 208.864 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1614.86 77.915C1546.13 118.173 1561.93 169.757 1578.42 190.517L1614.86 175.57C1562.8 145.277 1593.17 97.8445 1614.86 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1538.45 53.8788C1507.09 127.1 1550.45 159.203 1576.05 166.102L1596.48 132.421C1536.6 138.998 1532.84 82.8001 1538.45 53.8788Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M625 75C610.079 115.4 613.508 167.167 617.088 188L625 173C613.696 142.6 620.29 95 625 75Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M219 75C204.079 115.4 207.508 167.167 211.088 188L219 173C207.696 142.6 214.29 95 219 75Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1625 75C1610.08 115.4 1613.51 167.167 1617.09 188L1625 173C1613.7 142.6 1620.29 95 1625 75Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M954.807 52.085C939.886 92.485 943.315 144.252 946.894 165.085L954.807 150.085C943.503 119.685 950.097 72.085 954.807 52.085Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1954.81 52.085C1939.89 92.485 1943.31 144.252 1946.89 165.085L1954.81 150.085C1943.5 119.685 1950.1 72.085 1954.81 52.085Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1744.81 52.085C1729.89 92.485 1733.31 144.252 1736.89 165.085L1744.81 150.085C1733.5 119.685 1740.1 72.085 1744.81 52.085Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1491.81 52.085C1476.89 92.485 1480.31 144.252 1483.89 165.085L1491.81 150.085C1480.5 119.685 1487.1 72.085 1491.81 52.085Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1102.61 50.1699C1117.53 90.5699 1114.11 142.337 1110.53 163.17L1102.61 148.17C1113.92 117.77 1107.32 70.1699 1102.61 50.1699Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2102.61 50.1699C2117.53 90.5699 2114.11 142.337 2110.53 163.17L2102.61 148.17C2113.92 117.77 2107.32 70.1699 2102.61 50.1699Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M786.864 77.915C718.134 118.173 733.928 169.757 750.416 190.517L786.864 175.57C734.795 145.277 765.169 97.8445 786.864 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1786.86 77.915C1718.13 118.173 1733.93 169.757 1750.42 190.517L1786.86 175.57C1734.8 145.277 1765.17 97.8445 1786.86 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1154.86 77.915C1086.13 118.173 1101.93 169.757 1118.42 190.517L1154.86 175.57C1102.8 145.277 1133.17 97.8445 1154.86 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2154.86 77.915C2086.13 118.173 2101.93 169.757 2118.42 190.517L2154.86 175.57C2102.8 145.277 2133.17 97.8445 2154.86 77.915Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M62.8292 112.605C88.0007 205.659 135.882 190.516 156.676 171.313L148.974 122.623C114.417 186.883 77.1454 142.719 62.8292 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1062.83 112.605C1088 205.659 1135.88 190.516 1156.68 171.313L1148.97 122.623C1114.42 186.883 1077.15 142.719 1062.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M602.829 112.605C628.001 205.659 675.882 190.516 696.676 171.313L688.974 122.623C654.417 186.883 617.145 142.719 602.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1602.83 112.605C1628 205.659 1675.88 190.516 1696.68 171.313L1688.97 122.623C1654.42 186.883 1617.15 142.719 1602.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M231.825 102.242C260.169 207.022 314.083 189.971 337.498 168.348L328.826 113.523C289.914 185.88 247.945 136.151 231.825 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1231.83 102.242C1260.17 207.022 1314.08 189.971 1337.5 168.348L1328.83 113.523C1289.91 185.88 1247.95 136.151 1231.83 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M771.825 102.242C800.169 207.022 854.083 189.971 877.498 168.348L868.826 113.523C829.914 185.88 787.945 136.151 771.825 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1771.83 102.242C1800.17 207.022 1854.08 189.971 1877.5 168.348L1868.83 113.523C1829.91 185.88 1787.95 136.151 1771.83 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M203.829 112.605C229.001 205.659 276.882 190.516 297.676 171.313L289.974 122.623C255.417 186.883 218.145 142.719 203.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1203.83 112.605C1229 205.659 1276.88 190.516 1297.68 171.313L1289.97 122.623C1255.42 186.883 1218.15 142.719 1203.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M743.829 112.605C769.001 205.659 816.882 190.516 837.676 171.313L829.974 122.623C795.417 186.883 758.145 142.719 743.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1743.83 112.605C1769 205.659 1816.88 190.516 1837.68 171.313L1829.97 122.623C1795.42 186.883 1758.15 142.719 1743.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M390.593 102.242C418.936 207.022 472.851 189.971 496.265 168.348L487.593 113.523C448.682 185.88 406.713 136.151 390.593 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1390.59 102.242C1418.94 207.022 1472.85 189.971 1496.27 168.348L1487.59 113.523C1448.68 185.88 1406.71 136.151 1390.59 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M930.593 102.242C958.936 207.022 1012.85 189.971 1036.27 168.348L1027.59 113.523C988.682 185.88 946.713 136.151 930.593 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1930.59 102.242C1958.94 207.022 2012.85 189.971 2036.27 168.348L2027.59 113.523C1988.68 185.88 1946.71 136.151 1930.59 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M242.829 112.605C268.001 205.659 315.882 190.516 336.676 171.313L328.974 122.623C294.417 186.883 257.145 142.719 242.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1242.83 112.605C1268 205.659 1315.88 190.516 1336.68 171.313L1328.97 122.623C1294.42 186.883 1257.15 142.719 1242.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M782.829 112.605C808.001 205.659 855.882 190.516 876.676 171.313L868.974 122.623C834.417 186.883 797.145 142.719 782.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1782.83 112.605C1808 205.659 1855.88 190.516 1876.68 171.313L1868.97 122.623C1834.42 186.883 1797.15 142.719 1782.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M434.509 102.242C462.852 207.022 516.767 189.971 540.181 168.348L531.509 113.523C492.598 185.88 450.629 136.151 434.509 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1434.51 102.242C1462.85 207.022 1516.77 189.971 1540.18 168.348L1531.51 113.523C1492.6 185.88 1450.63 136.151 1434.51 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M974.509 102.242C1002.85 207.022 1056.77 189.971 1080.18 168.348L1071.51 113.523C1032.6 185.88 990.629 136.151 974.509 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1974.51 102.242C2002.85 207.022 2056.77 189.971 2080.18 168.348L2071.51 113.523C2032.6 185.88 1990.63 136.151 1974.51 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M383.829 112.605C409.001 205.659 456.882 190.516 477.676 171.313L469.974 122.623C435.417 186.883 398.145 142.719 383.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1383.83 112.605C1409 205.659 1456.88 190.516 1477.68 171.313L1469.97 122.623C1435.42 186.883 1398.15 142.719 1383.83 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M923.829 112.605C949.001 205.659 996.882 190.516 1017.68 171.313L1009.97 122.623C975.417 186.883 938.145 142.719 923.829 112.605Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M593.276 102.242C621.62 207.022 675.534 189.971 698.949 168.348L690.277 113.523C651.365 185.88 609.397 136.151 593.276 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1593.28 102.242C1621.62 207.022 1675.53 189.971 1698.95 168.348L1690.28 113.523C1651.37 185.88 1609.4 136.151 1593.28 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1133.28 102.242C1161.62 207.022 1215.53 189.971 1238.95 168.348L1230.28 113.523C1191.37 185.88 1149.4 136.151 1133.28 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2133.28 102.242C2161.62 207.022 2215.53 189.971 2238.95 168.348L2230.28 113.523C2191.37 185.88 2149.4 136.151 2133.28 102.242Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M191.645 111.778C98.0675 88.6302 88.0722 137.844 94.7717 165.344L141.082 182.238C101.665 120.839 158.367 109.682 191.645 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1191.65 111.778C1098.07 88.6302 1088.07 137.844 1094.77 165.344L1141.08 182.238C1101.67 120.839 1158.37 109.682 1191.65 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M731.645 111.778C638.068 88.6302 628.072 137.844 634.772 165.344L681.082 182.238C641.665 120.839 698.367 109.682 731.645 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M325.645 111.778C232.068 88.6302 222.072 137.844 228.772 165.344L275.082 182.238C235.665 120.839 292.367 109.682 325.645 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1731.65 111.778C1638.07 88.6302 1628.07 137.844 1634.77 165.344L1681.08 182.238C1641.67 120.839 1698.37 109.682 1731.65 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M376.875 101.31C271.505 75.2459 260.25 130.661 267.794 161.627L319.94 180.65C275.556 111.514 339.404 98.9503 376.875 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1376.88 101.31C1271.51 75.2459 1260.25 130.661 1267.79 161.627L1319.94 180.65C1275.56 111.514 1339.4 98.9503 1376.88 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2102.41 102.31C2207.78 76.2459 2219.03 131.661 2211.49 162.627L2159.35 181.65C2203.73 112.514 2139.88 99.9503 2102.41 102.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M916.875 101.31C811.505 75.2459 800.25 130.661 807.794 161.627L859.94 180.65C815.556 111.514 879.404 98.9503 916.875 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1916.88 101.31C1811.51 75.2459 1800.25 130.661 1807.79 161.627L1859.94 180.65C1815.56 111.514 1879.4 98.9503 1916.88 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M332.645 111.778C239.068 88.6302 229.072 137.844 235.772 165.344L282.082 182.238C242.665 120.839 299.367 109.682 332.645 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1332.65 111.778C1239.07 88.6302 1229.07 137.844 1235.77 165.344L1282.08 182.238C1242.67 120.839 1299.37 109.682 1332.65 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2146.64 112.778C2240.22 89.6302 2250.21 138.844 2243.51 166.344L2197.2 183.238C2236.62 121.839 2179.92 110.682 2146.64 112.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M872.645 111.778C779.068 88.6302 769.072 137.844 775.772 165.344L822.082 182.238C782.665 120.839 839.367 109.682 872.645 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1872.65 111.778C1779.07 88.6302 1769.07 137.844 1775.77 165.344L1822.08 182.238C1782.67 120.839 1839.37 109.682 1872.65 111.778Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M535.643 101.31C430.273 75.2459 419.018 130.661 426.562 161.627L478.707 180.65C434.324 111.514 498.171 98.9503 535.643 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M129.643 101.31C24.2727 75.2459 13.0178 130.661 20.5615 161.627L72.7072 180.65C28.3238 111.514 92.1711 98.9503 129.643 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M368.728 59.5257C347.452 165.966 403.319 174.705 433.912 165.77L450.56 112.818C383.5 160.28 368.064 97.0656 368.728 59.5257Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1535.64 101.31C1430.27 75.2459 1419.02 130.661 1426.56 161.627L1478.71 180.65C1434.32 111.514 1498.17 98.9503 1535.64 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1075.64 101.31C970.273 75.2459 959.018 130.661 966.562 161.627L1018.71 180.65C974.324 111.514 1038.17 98.9503 1075.64 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2075.64 101.31C1970.27 75.2459 1959.02 130.661 1966.56 161.627L2018.71 180.65C1974.32 111.514 2038.17 98.9503 2075.64 101.31Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M97.7591 93.1881C75.5973 125.059 93.2459 143.137 104.84 148.192L117.914 133.964C88.578 132.725 92.254 106.264 97.7591 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1097.76 93.1881C1075.6 125.059 1093.25 143.137 1104.84 148.192L1117.91 133.964C1088.58 132.725 1092.25 106.264 1097.76 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M637.759 93.1881C615.597 125.059 633.246 143.137 644.84 148.192L657.914 133.964C628.578 132.725 632.254 106.264 637.759 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1637.76 93.1881C1615.6 125.059 1633.25 143.137 1644.84 148.192L1657.91 133.964C1628.58 132.725 1632.25 106.264 1637.76 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M271.156 80.3787C246.202 116.266 266.074 136.622 279.13 142.314L293.851 126.293C260.818 124.898 264.958 95.1023 271.156 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1271.16 80.3787C1246.2 116.266 1266.07 136.622 1279.13 142.314L1293.85 126.293C1260.82 124.898 1264.96 95.1023 1271.16 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2208.13 81.3787C2233.08 117.266 2213.21 137.622 2200.16 143.314L2185.43 127.293C2218.47 125.898 2214.33 96.1023 2208.13 81.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M811.156 80.3787C786.202 116.266 806.074 136.622 819.13 142.314L833.851 126.293C800.818 124.898 804.958 95.1023 811.156 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1811.16 80.3787C1786.2 116.266 1806.07 136.622 1819.13 142.314L1833.85 126.293C1800.82 124.898 1804.96 95.1023 1811.16 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M238.759 93.1881C216.597 125.059 234.246 143.137 245.84 148.192L258.914 133.964C229.578 132.725 233.254 106.264 238.759 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1238.76 93.1881C1216.6 125.059 1234.25 143.137 1245.84 148.192L1258.91 133.964C1229.58 132.725 1233.25 106.264 1238.76 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M778.759 93.1881C756.597 125.059 774.246 143.137 785.84 148.192L798.914 133.964C769.578 132.725 773.254 106.264 778.759 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1778.76 93.1881C1756.6 125.059 1774.25 143.137 1785.84 148.192L1798.91 133.964C1769.58 132.725 1773.25 106.264 1778.76 93.1881Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M429.926 80.3787C404.971 116.266 424.844 136.622 437.9 142.314L452.621 126.293C419.588 124.898 423.727 95.1023 429.926 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1429.93 80.3787C1404.97 116.266 1424.84 136.622 1437.9 142.314L1452.62 126.293C1419.59 124.898 1423.73 95.1023 1429.93 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2049.36 81.3787C2074.31 117.266 2054.44 137.622 2041.39 143.314L2026.66 127.293C2059.7 125.898 2055.56 96.1023 2049.36 81.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M969.926 80.3787C944.971 116.266 964.844 136.622 977.9 142.314L992.621 126.293C959.588 124.898 963.727 95.1023 969.926 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1969.93 80.3787C1944.97 116.266 1964.84 136.622 1977.9 142.314L1992.62 126.293C1959.59 124.898 1963.73 95.1023 1969.93 80.3787Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M215.363 177C233.291 102.17 180.591 62.4875 152 52C168.742 63.0544 197.591 99.619 179.045 157.442L215.363 177Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1215.36 177C1233.29 102.17 1180.59 62.4875 1152 52C1168.74 63.0544 1197.59 99.619 1179.05 157.442L1215.36 177Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M215.248 177C209.094 102.17 227.185 62.4875 237 52C231.253 63.0544 221.349 99.619 227.716 157.442L215.248 177Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M19.7516 214C25.9058 139.17 7.81477 99.4875 -2 89C3.74739 100.054 13.6506 136.619 7.28424 194.442L19.7516 214Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1215.25 177C1209.09 102.17 1227.19 62.4875 1237 52C1231.25 63.0544 1221.35 99.619 1227.72 157.442L1215.25 177Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M723.845 183C714.748 108.17 741.491 68.4875 756 58C747.504 69.0544 732.864 105.619 742.275 163.442L723.845 183Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M317.845 183C308.748 108.17 335.491 68.4875 350 58C341.504 69.0544 326.864 105.619 336.275 163.442L317.845 183Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1723.85 183C1714.75 108.17 1741.49 68.4875 1756 58C1747.5 69.0544 1732.86 105.619 1742.28 163.442L1723.85 183Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M283.035 164.73C335.436 216.724 398.04 129.698 422.792 79.6851C404.198 107.726 357.545 151.603 319.68 102.781L283.035 164.73Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1283.03 164.731C1335.43 216.725 1398.04 129.698 1422.79 79.6855C1404.2 107.727 1357.54 151.604 1319.68 102.782L1283.03 164.731Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2196.25 165.731C2143.85 217.725 2081.25 130.698 2056.49 80.6855C2075.09 108.727 2121.74 152.604 2159.61 103.782L2196.25 165.731Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M823.034 164.731C875.435 216.725 938.039 129.698 962.791 79.6855C944.197 107.727 897.544 151.604 859.679 102.782L823.034 164.731Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1823.03 164.731C1875.43 216.725 1938.04 129.698 1962.79 79.6855C1944.2 107.727 1897.54 151.604 1859.68 102.782L1823.03 164.731Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M339.507 192.717C381.204 165.39 352.789 114.612 333.369 92.6387C342.959 107.987 353.132 143.277 317.1 161.657L339.507 192.717Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1339.51 192.717C1381.2 165.39 1352.79 114.612 1333.37 92.6387C1342.96 107.987 1353.13 143.277 1317.1 161.657L1339.51 192.717Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M879.507 192.717C921.204 165.39 892.789 114.612 873.369 92.6387C882.959 107.987 893.132 143.277 857.1 161.657L879.507 192.717Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1879.51 192.717C1921.2 165.39 1892.79 114.612 1873.37 92.6387C1882.96 107.987 1893.13 143.277 1857.1 161.657L1879.51 192.717Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M543.368 192.45C590.32 161.679 558.324 104.502 536.457 79.7598C547.256 97.0419 558.71 136.78 518.138 157.475L543.368 192.45Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M137.368 192.45C184.32 161.679 152.324 104.502 130.457 79.7598C141.256 97.0419 152.71 136.78 112.138 157.475L137.368 192.45Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1543.37 192.45C1590.32 161.679 1558.32 104.502 1536.46 79.7598C1547.26 97.0419 1558.71 136.78 1518.14 157.475L1543.37 192.45Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1083.37 192.45C1130.32 161.679 1098.32 104.502 1076.46 79.7598C1087.26 97.0419 1098.71 136.78 1058.14 157.475L1083.37 192.45Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2083.37 192.45C2130.32 161.679 2098.32 104.502 2076.46 79.7598C2087.26 97.0419 2098.71 136.78 2058.14 157.475L2083.37 192.45Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M234.619 187.165C289.14 183.364 293.61 124.145 289.029 95.0107C288.594 113.403 276.731 149.844 232.758 148.466L234.619 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1234.62 187.165C1289.14 183.364 1293.61 124.145 1289.03 95.0107C1288.59 113.403 1276.73 149.844 1232.76 148.466L1234.62 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M774.619 187.165C829.14 183.364 833.61 124.145 829.029 95.0107C828.594 113.403 816.731 149.844 772.758 148.466L774.619 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1774.62 187.165C1829.14 183.364 1833.61 124.145 1829.03 95.0107C1828.59 113.403 1816.73 149.844 1772.76 148.466L1774.62 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M425.265 186.198C486.656 181.918 491.689 115.237 486.531 82.4307C486.041 103.141 472.683 144.174 423.168 142.622L425.265 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M19.2648 186.198C80.6557 181.918 85.6887 115.237 80.5312 82.4307C80.0414 103.141 66.6831 144.174 17.1685 142.622L19.2648 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1425.26 186.198C1486.66 181.918 1491.69 115.237 1486.53 82.4307C1486.04 103.141 1472.68 144.174 1423.17 142.622L1425.26 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M965.265 186.198C1026.66 181.918 1031.69 115.237 1026.53 82.4307C1026.04 103.141 1012.68 144.174 963.168 142.622L965.265 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1965.26 186.198C2026.66 181.918 2031.69 115.237 2026.53 82.4307C2026.04 103.141 2012.68 144.174 1963.17 142.622L1965.26 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M375.619 187.165C430.14 183.364 434.61 124.145 430.029 95.0107C429.594 113.403 417.731 149.844 373.758 148.466L375.619 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1375.62 187.165C1430.14 183.364 1434.61 124.145 1430.03 95.0107C1429.59 113.403 1417.73 149.844 1373.76 148.466L1375.62 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M915.619 187.165C970.14 183.364 974.61 124.145 970.029 95.0107C969.594 113.403 957.731 149.844 913.758 148.466L915.619 187.165Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M584.032 186.198C645.423 181.918 650.456 115.237 645.299 82.4307C644.809 103.141 631.451 144.174 581.936 142.622L584.032 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M178.032 186.198C239.423 181.918 244.456 115.237 239.299 82.4307C238.809 103.141 225.451 144.174 175.936 142.622L178.032 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1584.03 186.198C1645.42 181.918 1650.46 115.237 1645.3 82.4307C1644.81 103.141 1631.45 144.174 1581.94 142.622L1584.03 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1124.03 186.198C1185.42 181.918 1190.46 115.237 1185.3 82.4307C1184.81 103.141 1171.45 144.174 1121.94 142.622L1124.03 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2124.03 186.198C2185.42 181.918 2190.46 115.237 2185.3 82.4307C2184.81 103.141 2171.45 144.174 2121.94 142.622L2124.03 186.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2159.91 182.198C2098.52 177.918 2093.49 111.237 2098.64 78.4307C2099.13 99.1411 2112.49 140.174 2162.01 138.622L2159.91 182.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2073.25 178.198C2011.86 173.918 2006.83 107.237 2011.99 74.4307C2012.48 95.1411 2025.83 136.174 2075.35 134.622L2073.25 178.198Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M102.649 171.176C157.169 167.375 161.639 108.156 157.059 79.0215C156.624 97.4141 144.76 133.855 100.787 132.477L102.649 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1102.65 171.176C1157.17 167.375 1161.64 108.156 1157.06 79.0215C1156.62 97.4141 1144.76 133.855 1100.79 132.477L1102.65 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M642.649 171.176C697.169 167.375 701.639 108.156 697.059 79.0215C696.624 97.4141 684.76 133.855 640.787 132.477L642.649 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M236.649 171.176C291.169 167.375 295.639 108.156 291.059 79.0215C290.624 97.4141 278.76 133.855 234.787 132.477L236.649 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1642.65 171.176C1697.17 167.375 1701.64 108.156 1697.06 79.0215C1696.62 97.4141 1684.76 133.855 1640.79 132.477L1642.65 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M502.649 171.176C557.169 167.375 561.639 108.156 557.059 79.0215C556.624 97.4141 544.76 133.855 500.787 132.477L502.649 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M96.6487 171.176C151.169 167.375 155.639 108.156 151.059 79.0215C150.624 97.4141 138.76 133.855 94.7869 132.477L96.6487 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1502.65 171.176C1557.17 167.375 1561.64 108.156 1557.06 79.0215C1556.62 97.4141 1544.76 133.855 1500.79 132.477L1502.65 171.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M276.663 168.194C338.054 163.914 343.087 97.2326 337.93 64.4268C337.44 85.1372 324.082 126.17 274.567 124.618L276.663 168.194Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1276.66 168.194C1338.05 163.914 1343.09 97.2326 1337.93 64.4268C1337.44 85.1372 1324.08 126.17 1274.57 124.618L1276.66 168.194Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2202.62 169.194C2141.23 164.914 2136.2 98.2326 2141.36 65.4268C2141.85 86.1372 2155.2 127.17 2204.72 125.618L2202.62 169.194Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M816.663 168.194C878.054 163.914 883.087 97.2326 877.93 64.4268C877.44 85.1372 864.082 126.17 814.567 124.618L816.663 168.194Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M253.649 174.176C308.169 170.375 312.639 111.156 308.059 82.0215C307.624 100.414 295.76 136.855 251.787 135.477L253.649 174.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1253.65 174.176C1308.17 170.375 1312.64 111.156 1308.06 82.0215C1307.62 100.414 1295.76 136.855 1251.79 135.477L1253.65 174.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2225.64 175.176C2171.12 171.375 2166.65 112.156 2171.23 83.0215C2171.66 101.414 2183.53 137.855 2227.5 136.477L2225.64 175.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M793.649 174.176C848.169 170.375 852.639 111.156 848.059 82.0215C847.624 100.414 835.76 136.855 791.787 135.477L793.649 174.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1771.65 157.176C1826.17 153.375 1830.64 94.1559 1826.06 65.0215C1825.62 83.4141 1813.76 119.855 1769.79 118.477L1771.65 157.176Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M446.691 171.572C508.082 167.292 513.114 100.611 507.957 67.8047C507.467 88.5151 494.109 129.548 444.594 127.996L446.691 171.572Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M40.6906 171.572C102.082 167.292 107.114 100.611 101.957 67.8047C101.467 88.5151 88.1088 129.548 38.5942 127.996L40.6906 171.572Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1446.69 171.572C1508.08 167.292 1513.11 100.611 1507.96 67.8047C1507.47 88.5151 1494.11 129.548 1444.59 127.996L1446.69 171.572Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M986.691 171.572C1048.08 167.292 1053.11 100.611 1047.96 67.8047C1047.47 88.5151 1034.11 129.548 984.594 127.996L986.691 171.572Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1986.69 171.572C2048.08 167.292 2053.11 100.611 2047.96 67.8047C2047.47 88.5151 2034.11 129.548 1984.59 127.996L1986.69 171.572Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1695.04 178.93C1751.96 155.531 1735.73 90.6581 1720.5 61.1465C1726.56 80.9571 1726.81 124.109 1679.32 138.232L1695.04 178.93Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M192.718 145.928C189.709 102.829 146.555 96.1784 125.354 98.2408C138.753 99.5562 165.348 110.819 164.538 145.346L192.718 145.928Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1192.72 145.929C1189.71 102.829 1146.55 96.1788 1125.35 98.2412C1138.75 99.5567 1165.35 110.819 1164.54 145.346L1192.72 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M732.717 145.929C729.708 102.829 686.554 96.1788 665.354 98.2412C678.752 99.5567 705.347 110.819 704.538 145.346L732.717 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M326.717 145.929C323.708 102.829 280.554 96.1788 259.354 98.2412C272.752 99.5567 299.347 110.819 298.538 145.346L326.717 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1738.36 134.142C1735.35 91.0423 1692.2 84.3921 1671 86.4545C1684.4 87.77 1710.99 99.0326 1710.18 133.559L1738.36 134.142Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M378.081 139.765C374.693 91.2343 326.101 83.7461 302.229 86.0684C317.316 87.5496 347.262 100.231 346.351 139.109L378.081 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1378.08 139.765C1374.69 91.2343 1326.1 83.7461 1302.23 86.0684C1317.32 87.5496 1347.26 100.231 1346.35 139.109L1378.08 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2101.2 140.765C2104.59 92.2343 2153.18 84.7461 2177.06 87.0684C2161.97 88.5496 2132.02 101.231 2132.93 140.109L2101.2 140.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M918.081 139.765C914.693 91.2343 866.101 83.7461 842.229 86.0684C857.316 87.5496 887.262 100.231 886.351 139.109L918.081 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1860.56 155.554C1910.71 137.546 1906.86 86.1154 1898.66 62.6514C1900.74 78.3664 1894.65 112.486 1853.6 123.242L1860.56 155.554Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1551.64 152.614C1594.34 129.306 1580.9 82.0152 1568.84 61.2832C1573.78 75.6148 1574.73 108.122 1539.01 123.498L1551.64 152.614Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M333.717 145.929C330.708 102.829 287.554 96.1788 266.354 98.2412C279.752 99.5567 306.347 110.819 305.538 145.346L333.717 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1333.72 145.929C1330.71 102.829 1287.55 96.1788 1266.35 98.2412C1279.75 99.5567 1306.35 110.819 1305.54 145.346L1333.72 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2145.57 146.929C2148.58 103.829 2191.73 97.1788 2212.93 99.2412C2199.53 100.557 2172.94 111.819 2173.75 146.346L2145.57 146.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M873.717 145.929C870.708 102.829 827.554 96.1788 806.354 98.2412C819.752 99.5567 846.347 110.819 845.538 145.346L873.717 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1873.72 145.929C1870.71 102.829 1827.55 96.1788 1806.35 98.2412C1819.75 99.5567 1846.35 110.819 1845.54 145.346L1873.72 145.929Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M536.849 139.765C533.46 91.2343 484.868 83.7461 460.996 86.0684C476.083 87.5496 506.03 100.231 505.118 139.109L536.849 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M130.849 139.765C127.46 91.2343 78.8685 83.7461 54.9961 86.0684C70.0833 87.5496 100.03 100.231 99.1184 139.109L130.849 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1465.1 134.02C1511.18 118.394 1506.05 69.4957 1497.73 47C1500.14 61.9675 1495.49 94.1554 1457.66 103.167L1465.1 134.02Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2014.18 135.02C1968.11 119.394 1973.23 70.4957 1981.55 48C1979.15 62.9675 1983.79 95.1554 2021.62 104.167L2014.18 135.02Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1076.85 139.765C1073.46 91.2343 1024.87 83.7461 1001 86.0684C1016.08 87.5496 1046.03 100.231 1045.12 139.109L1076.85 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M2076.85 139.765C2073.46 91.2343 2024.87 83.7461 2001 86.0684C2016.08 87.5496 2046.03 100.231 2045.12 139.109L2076.85 139.765Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M586.885 167.996C625.322 138.174 604.503 93.6342 589.289 75.0918C596.454 88.4515 602.582 120.39 569.773 141.268L586.885 167.996Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M180.885 167.996C219.322 138.174 198.503 93.6342 183.289 75.0918C190.454 88.4515 196.582 120.39 163.773 141.268L180.885 167.996Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1586.88 167.996C1625.32 138.174 1604.5 93.6342 1589.29 75.0918C1596.45 88.4515 1602.58 120.39 1569.77 141.268L1586.88 167.996Z" fill="' . $this->args['backgroundcolor'] . '"/>
													<path d="M1516.88 167.996C1555.32 138.174 1534.5 93.6342 1519.29 75.0918C1526.45 88.4515 1532.58 120.39 1499.77 141.268L1516.88 167.996Z" fill="' . $this->args['backgroundcolor'] . '"/>
													</g>
													<defs>
													<clipPath id="clip0">
													<rect width="100%" height="195" fill="white"/>
													</clipPath>
													</defs>';
					$this->args['svg_element'] .= '</svg>';
					$candy                      = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'splash' === $this->args['divider_type'] ) {
					$this->args['default_divider_height'] = '65px';

					$this->args['svg_element']  = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" viewBox="0 0 330 65.34" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
					$this->args['svg_element'] .= '<path d="M275.42,351.54c1.21-.89,1.91-.67,2.09.66-1.22.89-1.92.67-2.09-.66Z" transform="translate(-31 -351)"/><path d="M249.07,361.49a15,15,0,0,1,6.42-1.17c-1.51,2.59-4.81,2.23-7.33,2.82l.91-1.65Z" transform="translate(-31 -351)"/><path d="M282.66,364.9c1.2-2.87,4.9-1.91,7.35-2-1.41,2.63-4.9,1.75-7.35,2Z" transform="translate(-31 -351)"/><path d="M207.69,370.55c.38-4.18,5.48-5.1,8.81-4.13-2.05,2.82-5.67,3.31-8.81,4.13Z" transform="translate(-31 -351)"/><path d="M266.83,371.61a5.8,5.8,0,0,1,4.92.15c-2.68,1.71-5.86,2.14-8.95,2.36.18-2.09,2.5-2,4-2.51Z" transform="translate(-31 -351)"/><path d="M196,374.49c2.92-1.63.26,3.28,0,0Z" transform="translate(-31 -351)"/><path d="M241.12,378.52a11.35,11.35,0,0,1,8.46-3.81,42,42,0,0,1-3.76,3.17c.42.6.85,1.19,1.28,1.79l-1.55,2.76a26.22,26.22,0,0,0,8-1.13c-.07.43-.23,1.28-.3,1.71l2.57.84A168,168,0,0,1,240,390.66c-.55,1.52.95,2.09,2.09,2.55,3.26-.71,6.62-.16,9.82-1.22v-2.65l3.13-.16c0,.53,0,1.59-.07,2.13,3.13-1.13,6.44-1.66,9.58-2.74,2.92-1.16,6.49-.84,8.79-3.27-.07.71-.19,2.13-.25,2.84,1.27.14,2.55.25,3.83.32.05-1.25.11-2.49.18-3.74,1.36.31,2.73.63,4.12.91l2.79-.54-1.28,1c-1.32.4-2.65.77-4,1.08.41,1.11.86,2.21,1.34,3.3l-2.07-.2.08,3.09c3.1,0,6.19-.06,9.28-.2l-.4,1.64c1.78.55,2.55,2.27,3.53,3.69,3.83-1.64,7.63.1,11.57-.07,0,1.72,0,3.43,0,5.15,2.13-.47,4.24-1.05,6.36-1.58a4.39,4.39,0,0,1,3-2.09c.95-.69,1.8-1.72,3.1-1.62.29.55.89,1.66,1.19,2.21a14.11,14.11,0,0,1,6,2.29c1.62-1.63,3.72-1.34,5.77-.93.36.35,1.06,1,1.41,1.41,1.8,0,3.6,0,5.4,0,2.18-2.06,4.92,0,7.28.56l.83,2.72,2.76.56,3.49-1.66-.63,1.23a6,6,0,0,1,3.79.47l1.56-1.66,1.84,2.37c1.07-1.19,2.08-2.43,3.08-3.69l2.73.25v11.93H31v-8.93l4.18-1.64c-.06.62-.17,1.87-.23,2.49l3.48.05c.57-1,1.16-2,1.75-3,.66.83,1.32,1.66,2,2.48,1-1.09,2-2.22,3-3.37,1.49-.18,3-.39,4.46-.64.51-2.17,1.29-4.31,1.17-6.57,1-.43,2.07-.85,3.11-1.26l.24,2.75c1.37-.79,2.7-1.64,4-2.47.32.53,1,1.59,1.31,2.12A47.42,47.42,0,0,0,67,396.05a16.74,16.74,0,0,0-1-4.68l1.52-.52a21.5,21.5,0,0,0,2.52,4.79c3.87-2.26,8.57-3.75,11-7.78,0,.69-.15,2.08-.19,2.77,1.42-.25,2.85-.45,4.29-.57-2.39,1.71-5.72.37-8,2.3a2,2,0,0,0,0,3.68c-.08,1.07-.15,2.15-.23,3.23l2.37-.65L79,400.79c3.17-1.38,5.34-4.75,9-4.84,0-.6-.15-1.8-.21-2.4a26.51,26.51,0,0,0,7.78-4.36c.44,2,1,4,1.55,6,1.73,0,3.45.07,5.18.1a3.46,3.46,0,0,0-2.3-3.09l2.69-.86c-.9-2.29,1.19-2.78,2.86-3.37.49.69,1.49,2.06,2,2.75q2-1,3.9-2.18c1.39-.47,2.77-.94,4.17-1.39.12-3.77,4.26-2.28,6.67-2.41-1.68.19-3.36.32-5,.46.14,3,2.73,1.78,4.41,1.91l.18.34c-1.17.11-2.35.22-3.52.35a7.93,7.93,0,0,1-2.17,5.21c-.07-.67-.22-2-.29-2.67-2,.05-3.93.09-5.9.1v2.12l-3-.11c.05.53.17,1.58.22,2.11l2.39,1.79,2.14-.6c1.74.65,5.41-.52,4.51,2.6h4.33c.23-2.9,3.32-2.26,5.35-2,.14-1.5.31-3,.51-4.48,1.6.37,3.21.73,4.82,1.08l-3.15.43c-.48,1.31-1,2.63-1.38,4,5.42,0,10.85-.63,16.24.09l.12-2.89,1.49,2.94c1.88-1.32,3.9-2.44,6.28-2.31.55.87,1.11,1.74,1.67,2.6-1.49-.15-3-.28-4.49-.42,0,.68,0,2,0,2.72,1.24.33,2.48.72,3.7,1.16l1.51,0a16.26,16.26,0,0,1,4.86-1.56c-.14-1.51-.3-3-.52-4.52a10.34,10.34,0,0,1,3.33-2.2c.05-1.46.12-2.91.21-4.36,1.41.38,2.84.76,4.23,1.24l-3.85-.2c.74,1,1.53,2,2.34,3l3.63-1.26-2.73,1.76c.91,1.82,2,3.57,2.81,5.44a12.57,12.57,0,0,1,7.49-.76c.56-1.27,1.12-1,1.53.54l4.41.07.13-1.58,2.24.44c1.43-.69,2.92-1.27,4.4-1.87.09.73.28,2.19.37,2.91l2.4.84c-.16.81-.49,2.43-.66,3.24H194c0-.59-.12-1.76-.17-2.34l3.62,1,1.8-2.43c-.26,1.79-.62,3.56-1,5.32q1.71-1.17,3.33-2.49l2.35-.26c0-.78,0-2.32.06-3.1l-1.57,1,.31-1.19L199.2,396l3.87,1-.08-1.51-3.31-.33a9,9,0,0,1,6.85-1.7c.7-1.72-.53-3.07-1.65-4.22,1.67.85,3.23,1.9,4.88,2.81a4.11,4.11,0,0,0-3.08,4.3c1.87-.09,3.75-.21,5.64-.23,1.19,2.82,3.15-.1,4.65-.69q-.3-1.38-.51-2.76a62,62,0,0,1,7,2.81,11.28,11.28,0,0,0,4-3.68c2.8-2.21,5.53-4.54,8.15-7l-2.53-.26c.95-.5,1.9-1,2.86-1.49,0-.62-.08-1.85-.1-2.46,1.74-.7,3.48-1.4,5.24-2.06Z" transform="translate(-31 -351)"/><path d="M224.78,378.53a20.92,20.92,0,0,1,5-2.88l.51,1.49a24.44,24.44,0,0,1-5.49,1.39Z" transform="translate(-31 -351)"/><path d="M263.84,378.63c3.08-.55,5.68-2.38,8.53-3.57-.1,1.74-.27,3.47-.24,5.21l-1.16.22c0-.57,0-1.71,0-2.28-2.65-.47-4.87,1.38-3.91,4.13l-1.31,0c.07-.5.2-1.51.27-2-2,.1-4,.17-6,.08a9.17,9.17,0,0,1,3.81-1.79Z" transform="translate(-31 -351)"/><path d="M99.18,376.36c3-1.3-.22,3.36,0,0Z" transform="translate(-31 -351)"/><path d="M116.72,380c-.11-1.3-.45-3.65,1.32-3.95,2.52.55.22,3.78-1.32,3.95Z" transform="translate(-31 -351)"/><path d="M104.08,381.55c-2.44.41-3.4-2.62-.89-3.26l2,2.83-.61,1.37c1.75.21,4,0,3.75-2.32,2.22-.68,4.47-1.24,6.7-1.88-.83,1-1.66,2.08-2.43,3.16l-2,.26c-2,1.6-4.67,1.33-7,2l.57-2.15Z" transform="translate(-31 -351)"/><path d="M324.12,380.12c.37-1.23,1.19-1.42,2.47-.57-.4,1.22-1.22,1.41-2.47.57Z" transform="translate(-31 -351)"/><path d="M82.11,382.19c-.34-1.3,1.77-2.24,2.76-1.65.3,1.24-1.78,2.25-2.76,1.65Z" transform="translate(-31 -351)"/><polygon points="107.19 29.5 107.86 29.52 107.85 30.18 107.18 30.18 107.19 29.5 107.19 29.5"/><path d="M190.11,380.45c2.84-1.78.43,3.29,0,0Z" transform="translate(-31 -351)"/><path d="M225.11,382c1.71,0,2.49.9,2.08,2.63,1.8-.44,3.24-1.65,4.93-2.37-.72,3.61-4.73,4.33-7.44,5.94.19-2.06.32-4.13.43-6.2Z" transform="translate(-31 -351)"/><path d="M74.59,387.76c.67-1.58,1.24-3.62,3.27-3.83-.69,2-.94,4.29-2.44,5.94-1.36-.06-3,.17-3.94-1.1l3.11-1Z" transform="translate(-31 -351)"/><path d="M124.11,384.49c2.84,1.06,6,.78,8.78,1.85l2.56-.52c-.18.78-.36,1.56-.53,2.34l-3.43.82a32.41,32.41,0,0,0-1.15-3.17c-2.21.24-4.5.24-6.23-1.32Z" transform="translate(-31 -351)"/><path d="M306.29,386.17c1.11-.57,2.26-1.08,3.39-1.62l-1,2.75,3.23,0,.2,2.09-4.4-.07c.29.9.59,1.79.91,2.68l-2.94.36c1.36-2.94-1.53-4.63-4.12-3.71a10.68,10.68,0,0,1-4.17,1c1-1.44,2.21-2.66,4.06-2.71,1.44-1.13,3.07-1.78,4.8-.76Z" transform="translate(-31 -351)"/><path d="M109.24,385.53c1.17-.9,1.87-.69,2.1.62-1.19.93-1.88.73-2.1-.62Z" transform="translate(-31 -351)"/><path d="M164.68,387.68c.54-1.46,2.95-.62,3.91.07l-.06.35c-1.08.71-3.16.92-3.85-.42Z" transform="translate(-31 -351)"/><path d="M67.1,388.8c-.08-1.48,2.43-2.38,3.55-1.54,0,1.53-2.41,1.85-3.55,1.54Z" transform="translate(-31 -351)"/><path d="M169.24,389.42c1.51-.32,3.27.55,4.51,1.38-.44,1.85-5.18.54-4.51-1.38Z" transform="translate(-31 -351)"/><path d="M216.48,389.45c3-1.56.24,3.23,0,0Z" transform="translate(-31 -351)"/><path d="M296.92,392.93c.79-1.75,2.82-1.48,4.41-1.55-.73,1.95-2.82,1.46-4.41,1.55Z" transform="translate(-31 -351)"/><path d="M309.91,393a4.73,4.73,0,0,1,5.36,0,6.08,6.08,0,0,1-5.36,0Z" transform="translate(-31 -351)"/><path d="M190.82,393.34H192v1.44a47.93,47.93,0,0,1,5.17,1.08,12.81,12.81,0,0,1-5.26.47c-.37-1-.73-2-1.09-3Z" transform="translate(-31 -351)"/><path d="M302.67,397.29c-.09-3.19,3.24.39,2.94,1.75a2.69,2.69,0,0,1-2.65-.94l-.29-.81Z" transform="translate(-31 -351)"/><path d="M322,397.55c4.56-2.18,8.87.88,13.31,1.65-2,2.41-5.52.93-7.78-.33-1.87-.35-3.71-.8-5.53-1.32Z" transform="translate(-31 -351)"/><path d="M343.24,397.31c2.13-.07,4.57-.12,6.2,1.5-2.14,0-4.56.19-6.2-1.5Z" transform="translate(-31 -351)"/>';
					$this->args['svg_element'] .= '</svg>';

					$candy = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';
				} elseif ( 'custom' === $this->args['divider_type'] && '' !== $this->args['custom_svg'] ) {

					$custom_svg_data                      = fusion_get_svg_from_file( $this->args['custom_svg'], [ 'background-color' => $this->args['backgroundcolor'] ] );
					$this->args['default_divider_height'] = ! empty( $custom_svg_data['height'] ) ? $custom_svg_data['height'] . 'px' : '65px';

					$this->args['svg_element'] = ! empty( $custom_svg_data['svg'] ) ? $custom_svg_data['svg'] : '';
					$candy                     = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg-bg-image' ) . '></div>';

				}

				$helper = $helper_close = '';
				if ( ! empty( $fusion_fwc_type ) && isset( $fusion_col_type['type'] ) ) {

					// 100% width template && 1/1 column.
					if ( $fusion_fwc_type['width_100_percent'] && '1_1' === $fusion_col_type['type'] ) {
						if ( 'boxed' === fusion_get_option( 'layout' ) ) {
							$helper       = '<div ' . FusionBuilder::attributes( 'fusion-section-separator-svg-wrapper' ) . '>';
							$helper_close = '</div>';
						}
					}
				}

				$html          = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>';
					$html     .= $helper . '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-svg-wrapper' ) . '>' . $candy . '</div>' . $helper_close;
					$html     .= '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-spacer' ) . '>';
						$html .= '<div class="fusion-section-separator-spacer-height"></div>';
					$html     .= '</div>';
				$html         .= '</div>';

				$this->element_counter++;

				$this->on_render();

				return apply_filters( 'fusion_element_section_separator_content', $html, $args );
			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {

				$attr = fusion_builder_visibility_atts(
					$this->args['hide_on_mobile'],
					[
						'class' => 'fusion-section-separator section-separator ' . esc_attr( str_replace( '_', '-', $this->args['divider_type'] ) ) . ' fusion-section-separator-' . $this->element_counter,
						'style' => $this->get_style_vars(),
					]
				);

				if ( 'rounded-split' === $this->args['divider_type'] ) {
					$attr['class'] .= ' rounded-split-separator';
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				if ( $this->args['id'] ) {
					$attr['id'] = $this->args['id'];
				}

				if ( class_exists( 'Avada_Studio' ) && false !== strpos( $this->args['backgroundcolor'], '--' ) ) {
					$attr['data-var']   = $this->args['backgroundcolor'];
					$attr['data-color'] = Fusion_Color::new_color( $this->args['backgroundcolor'] )->toCss( 'rgba' );
				}
				return $attr;
			}

			/**
			 * Get the style css vars.
			 *
			 * @since 3.9
			 * @return string
			 */
			private function get_style_vars() {
				global $fusion_fwc_type, $fusion_col_type;
				$fusion_settings = awb_get_fusion_settings();
				$custom_css_vars = [];

				// Border.
				if ( 'triangle' === $this->args['divider_type'] ) {
					if ( $this->args['bordercolor'] ) {
						if ( 'bottom' === $this->args['divider_candy'] || 'top' === $this->args['divider_candy'] ) {
							$custom_css_vars[ 'border_' . $this->args['divider_candy'] ] = $this->args['bordersize'] . ' solid ' . $this->args['bordercolor'];
						} elseif ( false !== strpos( $this->args['divider_candy'], 'top' ) && false !== strpos( $this->args['divider_candy'], 'bottom' ) ) {
							$custom_css_vars['border'] = $this->args['bordersize'] . ' solid ' . $this->args['bordercolor'];
						}
					}
				}

				// Spacer height/padding-top.
				$hundred_px_separators = [ 'slant', 'bigtriangle', 'curved', 'big-half-circle', 'clouds' ];
				if ( in_array( $this->args['divider_type'], $hundred_px_separators, true ) ) {
					$custom_css_vars['spacer-height'] = '99px';
				} elseif ( 'triangle' === $this->args['divider_type'] ) {
					if ( $this->args['bordercolor'] ) {
						if ( 'bottom' === $this->args['divider_candy'] || 'top' === $this->args['divider_candy'] ) {
							$custom_css_vars['spacer-height'] = $this->args['bordersize'];
						} elseif ( false !== strpos( $this->args['divider_candy'], 'top' ) && false !== strpos( $this->args['divider_candy'], 'bottom' ) ) {
							$custom_css_vars['spacer-height'] = 'calc( ' . $this->args['bordersize'] . ' * 2 )';
						}
					}
				} elseif ( 'rounded-split' === $this->args['divider_type'] ) {
					$custom_css_vars['spacer-height'] = '71px';
				} elseif ( 'hills_opacity' === $this->args['divider_type'] ) {
					$custom_css_vars['spacer-padding-top'] = ( 182 / 1024 * 100 ) . '%';
				} elseif ( 'hills' === $this->args['divider_type'] ) {
					$custom_css_vars['spacer-padding-top'] = ( 107 / 1024 * 100 ) . '%';
				} elseif ( 'horizon' === $this->args['divider_type'] ) {
					$custom_css_vars['spacer-padding-top'] = ( 178 / 1024 * 100 ) . '%';
				} elseif ( 'waves_opacity' === $this->args['divider_type'] ) {
					$custom_css_vars['spacer-padding-top'] = ( 216 / 1024 * 100 ) . '%';
				} elseif ( 'waves' === $this->args['divider_type'] ) {
					$custom_css_vars['spacer-padding-top'] = ( 162 / 1024 * 100 ) . '%';
				} elseif ( in_array( $this->args['divider_type'], $this->bg_image_separators, true ) && isset( $this->args['default_divider_height'] ) ) {
					$height                           = '' === $this->args['divider_height'] && 1 < $this->args['divider_repeat'] ? ( intval( $this->args['default_divider_height'] ) / $this->args['divider_repeat'] ) . 'px' : $this->args['default_divider_height']; // Aspect ratio height.
					$custom_css_vars['spacer-height'] = $height;
				}

				// Hide spacer if 100% width template && 1/1 column.
				if ( ! empty( $fusion_fwc_type ) && isset( $fusion_col_type['type'] ) && $fusion_fwc_type['width_100_percent'] && '1_1' === $fusion_col_type['type'] && 'wide' !== fusion_get_option( 'layout' ) ) {
					$custom_css_vars['spacer-display'] = 'none';
				}

				// Decrease margin if needed for the container, to make section go full width.
				if ( ! empty( $fusion_fwc_type ) && isset( $fusion_col_type['type'] ) ) {
					if ( $fusion_fwc_type['width_100_percent'] && '1_1' === $fusion_col_type['type'] ) {
						if ( 'boxed' === fusion_get_option( 'layout' ) ) {
							$custom_css_vars['section-separator-pos'] = 'relative';
							foreach ( $fusion_fwc_type['padding_flex'] as $size => $paddings ) {
								if ( ! empty( $fusion_fwc_type['padding_flex'][ $size ]['left'] ) || ! empty( $fusion_fwc_type['padding_flex'][ $size ]['right'] ) ) {
									$var_size = ( 'large' === $size ? '' : '-' . $size );

									if ( ! empty( $fusion_fwc_type['padding_flex'][ $size ]['left'] ) && ! empty( $fusion_fwc_type['padding_flex'][ $size ]['right'] ) && false !== strpos( $fusion_fwc_type['padding_flex'][ $size ]['left'], '%' ) && false !== strpos( $fusion_fwc_type['padding_flex'][ $size ]['right'], '%' ) ) {
										$margin                                = (float) $fusion_fwc_type['padding_flex'][ $size ]['left'] + (float) $fusion_fwc_type['padding_flex'][ $size ]['right'];
										$scale                                 = ( 100 - $margin ) / 100;
										$padding                               = $fusion_settings->get( 'hundredp_padding' );
										$custom_css_vars['svg-wrapper-margin'] = '0 ' . $padding;

										$custom_css_vars[ 'svg-margin-left' . $var_size ]  = 'calc(-' . ( (float) $fusion_fwc_type['padding_flex'][ $size ]['left'] / $scale ) . '% - ' . $padding . ')';
										$custom_css_vars[ 'svg-margin-right' . $var_size ] = 'calc(-' . ( (float) $fusion_fwc_type['padding_flex'][ $size ]['right'] / $scale ) . '% - ' . $padding . ')';
									} else {
										if ( ! empty( $fusion_fwc_type['padding_flex'][ $size ]['left'] ) ) {
											$custom_css_vars[ 'svg-margin-left' . $var_size ] = '-' . $fusion_fwc_type['padding_flex'][ $size ]['left'];
										}

										if ( ! empty( $fusion_fwc_type['padding_flex'][ $size ]['right'] ) ) {
											$custom_css_vars[ 'svg-margin-right' . $var_size ] = '-' . $fusion_fwc_type['padding_flex'][ $size ]['right'];
										}
									}
								}
							}
						}
					} else {
						// Flex container.
						if ( isset( $fusion_col_type['margin'] ) ) {
							foreach ( $fusion_col_type['margin'] as $size => $margins ) {
								if ( ! empty( $fusion_col_type['margin'][ $size ]['left'] ) || ! empty( $fusion_col_type['margin'][ $size ]['right'] ) ) {
									$var_size = ( 'large' === $size ? '' : '-' . $size );

									if ( ! empty( $fusion_col_type['margin'][ $size ]['left'] ) ) {
										$custom_css_vars[ 'svg-margin-left' . $var_size ] = $fusion_col_type['margin'][ $size ]['left'];
									}

									if ( ! empty( $fusion_col_type['margin'][ $size ]['right'] ) ) {
										$custom_css_vars[ 'svg-margin-right' . $var_size ] = $fusion_col_type['margin'][ $size ]['right'];
									}
								}
							}
						} else {
							$custom_css_vars['svg-margin-left']  = '0';
							$custom_css_vars['svg-margin-right'] = '0';
						}
					}
				}

				// Check for custom height.
				$is_flex_container  = fusion_element_rendering_is_flex();
				$divider_height_arr = [];
				foreach ( [ 'large', 'medium', 'small' ] as $responsive_size ) {
					$var_size = ( 'large' === $responsive_size ? '' : '_' . $responsive_size );
					$key      = 'divider_height' . $var_size;

					// Skip for specific type.
					if ( 'triangle' === $this->args['divider_type'] || 'rounded-split' === $this->args['divider_type'] ) {
						continue;
					}
					// Check for flex.
					if ( ! $is_flex_container && 'large' !== $responsive_size ) {
						continue;
					}

					$divider_height = $this->args[ $key ];

					if ( empty( $divider_height ) && in_array( $this->args['divider_type'], $hundred_px_separators, true ) && 'large' === $responsive_size ) {
						$divider_height = '99px';
					}

					// Check for empty value.
					if ( '' === $divider_height ) {
						continue;
					}

					$divider_height_arr[ $key ]                     = $divider_height;
					$custom_css_vars[ $key ]                        = $divider_height;
					$custom_css_vars[ 'spacer-height' . $var_size ] = $divider_height;
					$custom_css_vars['spacer-padding-top']          = 'inherit';
				}

				foreach ( [ 'large', 'medium', 'small' ] as $responsive_size ) {
					$var_prefix    = ( 'large' === $responsive_size ? '' : '_' . $responsive_size );
					$key           = 'divider_repeat' . $var_prefix;
					$key_divider_h = 'divider_height' . $var_prefix;

					// Only allow for SVG Background type.
					if ( ! in_array( $this->args['divider_type'], $this->bg_image_separators, true ) ) {
						continue;
					}

					// Check for flex.
					if ( ! $is_flex_container && 'large' !== $responsive_size ) {
						continue;
					}

					// Check for empty value.
					if ( '' === $this->args[ $key ] ) {
						continue;
					}

					if ( isset( $this->args['svg_element'] ) ) {
						$height = '' !== $this->args[ $key_divider_h ] ? $this->args[ $key_divider_h ] : $this->get_divider_height_responsive( $key_divider_h, $divider_height_arr );
						$height = '' === $this->args[ $key_divider_h ] && 1 < $this->args[ $key ] ? ( intval( $height ) / $this->args[ $key ] ) . 'px' : $height; // Aspect ratio height.

						if ( 0 < strpos( $height, '%' ) ) {
							$custom_css_vars[ 'bg-size' . $var_prefix ] = floatval( 100 / $this->args[ $key ] ) . '% 100%';
						} else {
							$height                                     = 0 < intval( $height ) ? $height : '100%';
							$value                                      = floatval( 100 / $this->args[ $key ] ) . '% ' . $height;
							$custom_css_vars[ 'bg-size' . $var_prefix ] = $value;
						}
					}
				}

				$margin_vars = Fusion_Builder_Margin_Helper::get_margin_vars( $this->args );

				if ( 'bigtriangle' === $this->args['divider_type'] || 'slant' === $this->args['divider_type'] || 'big-half-circle' === $this->args['divider_type'] || 'clouds' === $this->args['divider_type'] || 'curved' === $this->args['divider_type'] ) {
					$custom_css_vars['sep-padding'] = '0';
					$custom_css_vars['svg-padding'] = '0';
				} elseif ( 'horizon' === $this->args['divider_type'] || 'waves' === $this->args['divider_type'] || 'waves_opacity' === $this->args['divider_type'] || 'hills' === $this->args['divider_type'] || 'hills_opacity' === $this->args['divider_type'] ) {
					$custom_css_vars['sep-font-size']   = '0';
					$custom_css_vars['sep-line-height'] = '0';
				}

				if ( 'slant' === $this->args['divider_type'] && 'bottom' === $this->args['divider_candy'] ) {
					$custom_css_vars['svg-tag-margin-bottom'] = '-3px';
					$custom_css_vars['sep-svg-display']       = 'block';
				}

				if ( 'triangle' === $this->args['divider_type'] ) {
					$custom_css_vars['icon_color'] = $this->args['icon_color'];

					if ( FusionBuilder::strip_unit( $this->args['bordersize'] ) > 1 ) {
						if ( 'bottom' === $this->args['divider_candy'] ) {
							$custom_css_vars['icon-top']    = 'auto';
							$custom_css_vars['icon-bottom'] = '-' . ( FusionBuilder::strip_unit( $this->args['bordersize'] ) + 10 ) . 'px';
						} elseif ( 'top' === $this->args['divider_candy'] ) {
							$custom_css_vars['icon-top'] = '-' . ( FusionBuilder::strip_unit( $this->args['bordersize'] ) + 10 ) . 'px';
						}
					}
				}

				return $this->get_custom_css_vars( $custom_css_vars ) . $margin_vars;
			}

			/**
			 * Builds the attributes array for the svg wrapper.
			 *
			 * @access public
			 * @since 3.0
			 * @return array
			 */
			public function svg_wrapper_attr() {
				global $fusion_fwc_type, $fusion_col_type;

				$attr = [
					'class' => 'fusion-section-separator-svg',
				];

				// 100% width template && 1/1 column.
				if ( ! empty( $fusion_fwc_type ) && isset( $fusion_col_type['type'] ) && $fusion_fwc_type['width_100_percent'] && '1_1' === $fusion_col_type['type'] && 'boxed' !== fusion_get_option( 'layout' ) ) {
					$attr['class'] .= ' fusion-section-separator-fullwidth';
				}

				return $attr;
			}

			/**
			 * Builds the attributes array for the spacer.
			 *
			 * @access public
			 * @since 3.0
			 * @return array
			 */
			public function spacer_attr() {
				global $fusion_fwc_type, $fusion_col_type;

				$attr['class'] = 'fusion-section-separator-spacer';

				// 100% width template && 1/1 column.
				if ( ! empty( $fusion_fwc_type ) && isset( $fusion_col_type['type'] ) && $fusion_fwc_type['width_100_percent'] && '1_1' === $fusion_col_type['type'] && 'wide' === fusion_get_option( 'layout' ) ) {
					$attr['class'] .= ' fusion-section-separator-fullwidth';
				}

				return $attr;
			}

			/**
			 * Builds the rounded split attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function divider_svg_attr() {
				$attr         = [];
				$attr['fill'] = Fusion_Color::new_color( $this->args['backgroundcolor'] )->toCss( 'rgba' );
				return $attr;
			}

			/**
			 * Builds the rounded split attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function divider_rounded_split_attr() {
				return [
					'class' => 'rounded-split ' . $this->args['divider_candy'],
					'style' => 'background-color:' . $this->args['backgroundcolor'] . ';',
				];
			}

			/**
			 * Builds the icon attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function icon_attr() {
				$attr = [
					'class'       => 'section-separator-icon icon ' . fusion_font_awesome_name_handler( $this->args['icon'] ),
					'aria-hidden' => 'true',
				];

				return $attr;
			}

			/**
			 * Builds the divider attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function divider_candy_attr( $args ) {
				$attr = [
					'class' => 'divider-candy',
				];

				$divider_candy = ( $args ) ? $args['divider_candy'] : $this->args['divider_candy'];

				if ( 'bottom' === $divider_candy ) {
					$attr['class'] .= ' bottom';
					$attr['style']  = 'bottom:-' . ( FusionBuilder::strip_unit( $this->args['bordersize'] ) + 20 ) . 'px;border-bottom:1px solid ' . $this->args['bordercolor'] . ';border-left:1px solid ' . $this->args['bordercolor'] . ';';
				} elseif ( 'top' === $divider_candy ) {
					$attr['class'] .= ' top';
					$attr['style']  = 'top:-' . ( FusionBuilder::strip_unit( $this->args['bordersize'] ) + 20 ) . 'px;border-bottom:1px solid ' . $this->args['bordercolor'] . ';border-left:1px solid ' . $this->args['bordercolor'] . ';';
					// Modern setup, that won't work in IE8.
				} elseif ( false !== strpos( $this->args['divider_candy'], 'top' ) && false !== strpos( $this->args['divider_candy'], 'bottom' ) ) {
					$attr['class'] .= ' both';
					$attr['style']  = 'background-color:' . $this->args['backgroundcolor'] . ';border:1px solid ' . $this->args['bordercolor'] . ';';
				}

				return $attr;
			}

			/**
			 * Builds the divider-arrow attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @param array $args The arguments array.
			 * @return array
			 */
			public function divider_candy_arrow_attr( $args ) {

				$attr = [
					'class' => 'divider-candy-arrow',
				];

				$divider_candy = ( $args ) ? $args['divider_candy'] : $this->args['divider_candy'];

				// For borders of size 1, we need to hide the border line on the arrow, thus we set it to 0.
				$arrow_position = FusionBuilder::strip_unit( $this->args['bordersize'] );
				if ( '1' == $arrow_position ) { // phpcs:ignore Universal.Operators.StrictComparisons
					$arrow_position = 0;
				}

				if ( 'bottom' === $divider_candy ) {
					$attr['class'] .= ' bottom';
					$attr['style']  = 'top:' . $arrow_position . 'px;border-top-color: ' . $this->args['backgroundcolor'] . ';';
				} elseif ( 'top' === $divider_candy ) {
					$attr['class'] .= ' top';
					$attr['style']  = 'bottom:' . $arrow_position . 'px;border-bottom-color: ' . $this->args['backgroundcolor'] . ';';
				}

				return $attr;
			}

			/**
			 * Builds the background image SVG attributes array.
			 *
			 * @access public
			 * @since 3.2
			 * @return array
			 */
			public function divider_svg_bg_image_attr() {
				$attr      = [
					'class' => 'fusion-' . $this->args['divider_type'] . '-candy-sep fusion-section-separator-svg-bg',
				];
				$transform = [];

				if ( $this->args['svg_element'] ) {
					$attr['style'] = sprintf( 'background-image:url( data:image/svg+xml;utf8,%s );', rawurlencode( $this->args['svg_element'] ) );

					if ( '' === $this->args['divider_height'] ) {
						$height = $this->args['default_divider_height'];

						if ( 1 < $this->args['divider_repeat'] ) {
							$height = ( intval( $this->args['default_divider_height'] ) / $this->args['divider_repeat'] ) . 'px';
						}

						$attr['style'] .= sprintf( 'height:%s;', $height );
					}

					if ( 'right' === $this->args['divider_position'] ) {
						$transform[] = 'rotateY(180deg)';
					}

					if ( 'bottom' === $this->args['divider_candy'] ) {
						$transform[] = 'rotateX(180deg)';
					}

					if ( 0 < count( $transform ) ) {
						$attr['style'] .= sprintf( 'transform: %s;', implode( ' ', $transform ) );
					}
					if ( 'custom' === $this->args['divider_type'] ) {
						if ( '' === $this->args['divider_height'] ) {
							$height         = '' === $this->args['divider_height'] && 1 < $this->args['divider_repeat'] ? ( intval( $this->args['default_divider_height'] ) / $this->args['divider_repeat'] ) . 'px' : $this->args['default_divider_height']; // Aspect ratio height.
							$attr['style'] .= sprintf( 'height:%s;', $height );
						}
						if ( 1 >= $this->args['divider_repeat'] ) {
							$attr['style'] .= 'background-size:cover;';
						}
					}
				}

				return $attr;
			}

			/**
			 * Load base CSS.
			 *
			 * @access public
			 * @since 3.0
			 * @return void
			 */
			public function add_css_files() {
				FusionBuilder()->add_element_css( FUSION_BUILDER_PLUGIN_DIR . 'assets/css/shortcodes/section-separator.min.css' );

				Fusion_Media_Query_Scripts::$media_query_assets[] = [
					'avada-section-separator-md',
					FUSION_BUILDER_PLUGIN_DIR . 'assets/css/media/section-separator-md.min.css',
					[],
					FUSION_BUILDER_VERSION,
					Fusion_Media_Query_Scripts::get_media_query_from_key( 'fusion-max-medium' ),
				];
				Fusion_Media_Query_Scripts::$media_query_assets[] = [
					'avada-section-separator-sm',
					FUSION_BUILDER_PLUGIN_DIR . 'assets/css/media/section-separator-sm.min.css',
					[],
					FUSION_BUILDER_VERSION,
					Fusion_Media_Query_Scripts::get_media_query_from_key( 'fusion-max-small' ),
				];
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1
			 * @return array $sections Section Separator settings.
			 */
			public function add_options() {

				return [
					'section_separator_shortcode_section' => [
						'label'       => esc_html__( 'Section Separator', 'fusion-builder' ),
						'description' => '',
						'id'          => 'section_separator_shortcode_section',
						'type'        => 'accordion',
						'icon'        => 'fusiona-ellipsis',
						'fields'      => [
							'section_sep_border_size'  => [
								'label'       => esc_html__( 'Section Separator Border Size', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border size of the section separator.', 'fusion-builder' ),
								'id'          => 'section_sep_border_size',
								'default'     => '1',
								'type'        => 'slider',
								'transport'   => 'postMessage',
								'choices'     => [
									'min'  => '0',
									'max'  => '50',
									'step' => '1',
								],
							],
							'section_sep_bg'           => [
								'label'       => esc_html__( 'Section Separator Background Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the background color of the section separator style.', 'fusion-builder' ),
								'id'          => 'section_sep_bg',
								'default'     => 'var(--awb-color2)',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
							],
							'section_sep_border_color' => [
								'label'       => esc_html__( 'Section Separator Border Color', 'fusion-builder' ),
								'description' => esc_html__( 'Controls the border color of the separator.', 'fusion-builder' ),
								'id'          => 'section_sep_border_color',
								'default'     => 'var(--awb-color3)',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
							],
						],
					],
				];
			}

			/**
			 * Get divider height responsive.
			 *
			 * @access public
			 * @param string $key  string array key.
			 * @param array  $hash array  height array.
			 * @since 3.2
			 * @return array $height.
			 */
			public function get_divider_height_responsive( $key, $hash = [] ) {
				$keys        = array_keys( $hash );
				$found_index = array_search( $key, $keys, true );
				if ( false === $found_index || 0 === $found_index ) {
					return '';
				}
				return $keys[ $found_index - 1 ];
			}
		}
	}

	new FusionSC_SectionSeparator();

}

/**
 * Map shortcode to Avada Builder.
 *
 * @since 1.0
 */
function fusion_element_section_separator() {

	$fusion_settings = awb_get_fusion_settings();

	fusion_builder_map(
		fusion_builder_frontend_data(
			'FusionSC_SectionSeparator',
			[
				'name'       => esc_attr__( 'Section Separator', 'fusion-builder' ),
				'shortcode'  => 'fusion_section_separator',
				'icon'       => 'fusiona-ellipsis',
				'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'inc/templates/previews/fusion-section-separator-preview.php',
				'preview_id' => 'fusion-builder-block-module-section-separator-preview-template',
				'help_url'   => 'https://avada.com/documentation/section-separator-element/',
				'params'     => [
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Style', 'fusion-builder' ),
						'description' => esc_attr__( 'Select the type of the separator', 'fusion-builder' ),
						'param_name'  => 'divider_type',
						'value'       => [
							'triangle'        => esc_attr__( 'Triangle', 'fusion-builder' ),
							'slant'           => esc_attr__( 'Slant', 'fusion-builder' ),
							'bigtriangle'     => esc_attr__( 'Big Triangle', 'fusion-builder' ),
							'rounded-split'   => esc_attr__( 'Rounded Split', 'fusion-builder' ),
							'curved'          => esc_attr__( 'Curved', 'fusion-builder' ),
							'big-half-circle' => esc_attr__( 'Big Half Circle', 'fusion-builder' ),
							'clouds'          => esc_attr__( 'Clouds', 'fusion-builder' ),
							'horizon'         => esc_attr__( 'Horizon', 'fusion-builder' ),
							'waves'           => esc_attr__( 'Waves', 'fusion-builder' ),
							'waves_opacity'   => esc_attr__( 'Waves Opacity', 'fusion-builder' ),
							'waves_brush'     => esc_attr__( 'Waves Brush', 'fusion-builder' ),
							'hills'           => esc_attr__( 'Hills', 'fusion-builder' ),
							'hills_opacity'   => esc_attr__( 'Hills Opacity', 'fusion-builder' ),
							'grunge'          => esc_attr__( 'Grunge', 'fusion-builder' ),
							'music'           => esc_attr__( 'Music', 'fusion-builder' ),
							'paper'           => esc_attr__( 'Paper', 'fusion-builder' ),
							'squares'         => esc_attr__( 'Squares', 'fusion-builder' ),
							'circles'         => esc_attr__( 'Circles', 'fusion-builder' ),
							'paint'           => esc_attr__( 'Paint', 'fusion-builder' ),
							'grass'           => esc_attr__( 'Grass', 'fusion-builder' ),
							'splash'          => esc_attr__( 'Splash', 'fusion-builder' ),
							'custom'          => esc_attr__( 'Custom', 'fusion-builder' ),
						],
						'default'     => 'triangle',
					],
					[
						'type'        => 'upload',
						'heading'     => esc_attr__( 'Custom SVG File', 'fusion-builder' ),
						'description' => esc_attr__( 'Upload your custom SVG separator. SVG file should include attribute preserveAspectRatio="none" for best work in combination with custom height or repeat option.', 'fusion-builder' ),
						'param_name'  => 'custom_svg',
						'value'       => '',
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'custom',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'Height', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the height of the separator. Enter value including any valid CSS unit, ex: 200px. Leave empty for auto.', 'fusion-builder' ),
						'param_name'  => 'divider_height',
						'value'       => '',
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'rounded-split',
								'operator' => '!=',
							],
						],
						'responsive'  => [
							'state' => 'large',
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Repeat', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose how many times the Separator should repeat horizontally.', 'fusion-builder' ),
						'param_name'  => 'divider_repeat',
						'value'       => '1',
						'min'         => '1',
						'max'         => '20',
						'step'        => '1',
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'slant',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'bigtriangle',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'rounded-split',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'curved',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'big-half-circle',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'clouds',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'horizon',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'waves',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'waves_opacity',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'hills',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'hills_opacity',
								'operator' => '!=',
							],
						],
						'responsive'  => [
							'state' => 'large',
						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Horizontal Position', 'fusion-builder' ),
						'description' => esc_attr__( 'Select the horizontal position of the separator.', 'fusion-builder' ),
						'param_name'  => 'divider_position',
						'value'       => [
							'left'   => esc_attr__( 'Left', 'fusion-builder' ),
							'center' => esc_attr__( 'Center', 'fusion-builder' ),
							'right'  => esc_attr__( 'Right', 'fusion-builder' ),
						],
						'default'     => 'center',
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'rounded-split',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'big-half-circle',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'clouds',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'horizon',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'hills',
								'operator' => '!=',
							],
							[
								'element'  => 'divider_type',
								'value'    => 'hills_opacity',
								'operator' => '!=',
							],

						],
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Vertical Position', 'fusion-builder' ),
						'description' => esc_attr__( 'Select the vertical position of the separator.', 'fusion-builder' ),
						'param_name'  => 'divider_candy',
						'value'       => [
							'top'        => esc_attr__( 'Top', 'fusion-builder' ),
							'bottom'     => esc_attr__( 'Bottom', 'fusion-builder' ),
							'bottom,top' => esc_attr__( 'Top and Bottom', 'fusion-builder' ),
						],
						'default'     => 'top',
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'clouds',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'iconpicker',
						'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
						'param_name'  => 'icon',
						'value'       => '',
						'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Icon Color', 'fusion-builder' ),
						'description' => '',
						'param_name'  => 'icon_color',
						'value'       => '',
						'default'     => $fusion_settings->get( 'icon_color' ),
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '==',
							],
							[
								'element'  => 'icon',
								'value'    => '',
								'operator' => '!=',
							],
						],
					],
					[
						'type'        => 'range',
						'heading'     => esc_attr__( 'Border', 'fusion-builder' ),
						'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
						'param_name'  => 'bordersize',
						'value'       => '',
						'min'         => '0',
						'max'         => '50',
						'step'        => '1',
						'default'     => $fusion_settings->get( 'section_sep_border_size' ),
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '==',
							],
						],
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the border color. ', 'fusion-builder' ),
						'param_name'  => 'bordercolor',
						'value'       => '',
						'default'     => $fusion_settings->get( 'section_sep_border_color' ),
						'dependency'  => [
							[
								'element'  => 'divider_type',
								'value'    => 'triangle',
								'operator' => '==',
							],
							[
								'element'  => 'bordersize',
								'value'    => '0',
								'operator' => '!=',
							],
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
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
						'description' => esc_attr__( 'Controls the background color of the separator style.', 'fusion-builder' ),
						'param_name'  => 'backgroundcolor',
						'value'       => '',
						'default'     => $fusion_settings->get( 'section_sep_bg' ),
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
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
				],
			]
		)
	);
}
add_action( 'fusion_builder_before_init', 'fusion_element_section_separator' );
