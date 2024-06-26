<?php
/**
 * Avada Form Builder Table.
 *
 * @package Fusion-Builder
 * @subpackage Options
 * @since 2.2
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

// WP_List_Table is not loaded automatically so we need to load it in our application.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Create a new table class that will extend the WP_List_Table.
 */
class Fusion_Form_Builder_Table extends WP_List_Table {

	/**
	 * Data columns.
	 *
	 * @since 1.0
	 * @var array
	 */
	public $columns = [];

	/**
	 * Number of total table items.
	 *
	 * @since 3.6
	 * @var int
	 */
	public $total_items = -1;

	/**
	 * Class constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct(
			[
				'singular' => esc_html__( 'Form', 'fusion-builder' ), // Singular name of the listed records.
				'plural'   => esc_html__( 'Forms', 'fusion-builder' ), // Plural name of the listed records.
				'ajax'     => false, // This table doesn't support ajax.
				'class'    => 'fusion-form-builder-table',
			]
		);

		$this->columns = $this->get_columns();
	}

	/**
	 * Set the custom classes for table.
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function get_table_classes() {
		return [ 'widefat', 'fixed', 'striped', 'fusion-form-builder-table' ];
	}

	/**
	 * Prepare the items for the table to process.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function prepare_items() {
		$columns      = $this->columns;
		$per_page     = 15;
		$current_page = $this->get_pagenum();
		$data         = $this->table_data( $per_page, $current_page );
		$hidden       = $this->get_hidden_columns();
		$sortable     = $this->get_sortable_columns();

		$this->set_pagination_args(
			[
				'total_items' => -1 !== $this->total_items ? $this->total_items : count( $this->table_data() ),
				'per_page'    => $per_page,
			]
		);

		$this->_column_headers = [ $columns, $hidden, $sortable ];
		$this->items           = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table.
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function get_columns() {
		$columns = [
			'cb'          => '<input type="checkbox" />',
			'title'       => esc_html__( 'Title', 'fusion-builder' ),
			'views'       => __( 'Views', 'fusion-builder' ),
			'entries'     => __( 'Database Entries', 'fusion-builder' ),
			'submissions' => __( 'Submissions', 'fusion-builder' ),
			'conversions' => __( 'Conversion Rate', 'fusion-builder' ),
		];

		return apply_filters( 'manage_fusion_form_posts_columns', $columns );
	}

	/**
	 * Define which columns are hidden
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function get_hidden_columns() {
		return [];
	}

	/**
	 * Define the sortable columns
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'title'       => [ 'title', true ],
			'views'       => [ 'views', true ],
			'submissions' => [ 'submissions', true ],
		];
	}

	/**
	 * Get the table data.
	 *
	 * @since 1.0
	 * @access public
	 * @param  number $per_page     Posts per page.
	 * @param  number $current_page - Current page number.
	 * @return array
	 */
	private function table_data( $per_page = -1, $current_page = 0 ) {
		$data          = [];
		$library_query = [];
		$status        = [ 'publish', 'draft', 'future', 'pending', 'private' ];

		// Make sure current-page and per-page are integers.
		$per_page     = (int) $per_page;
		$current_page = (int) $current_page;

		// phpcs:disable WordPress.Security.NonceVerification
		if ( isset( $_GET['status'] ) ) {
			$status = sanitize_text_field( wp_unslash( $_GET['status'] ) );
		}

		$args = [
			'post_type'      => [ 'fusion_form' ],
			'posts_per_page' => $per_page,
			'post_status'    => $status,
			'offset'         => ( $current_page - 1 ) * $per_page,
		];

		// Add sorting.
		if ( isset( $_GET['orderby'] ) ) {
			$args['orderby'] = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			$args['order']   = ( isset( $_GET['order'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'ASC';
		}

		add_filter( 'posts_join', __CLASS__ . '::join_needed_forms_for_table', 10, 2 );
		add_filter( 'posts_orderby', __CLASS__ . '::orderby_needed_forms_for_table', 10, 2 );
		$library_query = new WP_Query( $args );
		remove_filter( 'posts_join', __CLASS__ . '::join_needed_forms_for_table', 10 );
		remove_filter( 'posts_orderby', __CLASS__ . '::orderby_needed_forms_for_table', 10 );
		$fusion_forms       = new Fusion_Form_DB_Forms();
		$fusion_submissions = new Fusion_Form_DB_Submissions();

		// Check if there are items available.
		if ( $library_query->have_posts() ) {

			$this->total_items = $library_query->found_posts;

			// The loop.
			while ( $library_query->have_posts() ) :
				$library_query->the_post();
				$element_post_id = get_the_ID();

				$terms         = get_the_terms( $element_post_id, 'fusion_tb_category' );
				$display_terms = '';

				$form       = $fusion_forms->get( [ 'where' => [ 'form_id' => $element_post_id ] ] );
				$form_stats = [
					'id'                => '',
					'submissions_count' => '',
					'views'             => '',
					'conversions'       => '',
				];

				if ( ! empty( $form ) && isset( $form[0] ) ) {
					$form                            = $form[0];
					$form_stats['submissions_count'] = isset( $form->submissions_count ) ? $form->submissions_count : '';
					$form_stats['views']             = $form->views;
					$form_stats['conversions']       = isset( $form->submissions_count ) ? round( ( $form->submissions_count * 100 / max( $form->views, 1 ) ), 2 ) . '%' : '-';
					$form_stats['id']                = (int) $form->id;
				}

				$entries = $fusion_submissions->count_form_database_entries( $element_post_id );

				$element_post = [
					'title'       => get_the_title(),
					'id'          => $element_post_id,
					'date'        => get_the_date( 'm/d/Y' ),
					'time'        => get_the_date( 'm/d/Y g:i:s A' ),
					'status'      => get_post_status(),
					'submissions' => $form_stats['submissions_count'],
					'entries'     => $entries,
					'views'       => $form_stats['views'],
					'conversions' => $form_stats['conversions'],
					'form_id'     => $form_stats['id'],
				];

				$data[] = $element_post;
			endwhile;
		}

		// Restore original Post Data.
		wp_reset_postdata();

		return $data;
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Filter function used to add join clause to SQL to order forms table.
	 *
	 * @param string $join Join SQL clause.
	 * @param Object $query Query object.
	 * @return string
	 */
	public static function join_needed_forms_for_table( $join, $query ) {
		global $wpdb;

		if ( is_array( $query->query ) && isset( $query->query['orderby'] ) && ( 'views' === $query->query['orderby'] || 'submissions' === $query->query['orderby'] ) ) {
			$fusion_forms_table_name = $wpdb->prefix . 'fusion_forms';
			if ( strpos( $fusion_forms_table_name, ' ' ) !== false ) {
				$fusion_forms_table_name = '`' . $fusion_forms_table_name . '`';
			}

			$join .= "LEFT JOIN $fusion_forms_table_name ON $wpdb->posts.ID = $fusion_forms_table_name.form_id ";
		}

		return $join;
	}

	/**
	 * Filter function used to add orderby clause to SQL to order forms table.
	 *
	 * @param string $orderby Orderby SQL clause.
	 * @param Object $query Query object.
	 * @return string
	 */
	public static function orderby_needed_forms_for_table( $orderby, $query ) {
		global $wpdb;

		if ( is_array( $query->query ) && isset( $query->query['orderby'] ) && ( 'views' === $query->query['orderby'] || 'submissions' === $query->query['orderby'] ) ) {
			$fusion_forms_table_name = $wpdb->prefix . 'fusion_forms';
			if ( strpos( $fusion_forms_table_name, ' ' ) !== false ) {
				$fusion_forms_table_name = '`' . $fusion_forms_table_name . '`';
			}

			if ( isset( $query->query['order'] ) && ( 'asc' === $query->query['order'] || 'ASC' === $query->query['order'] ) ) {
				$order = 'ASC';
			} else {
				$order = 'DESC';
			}

			if ( 'views' === $query->query['orderby'] ) {
				return $fusion_forms_table_name . '.views ' . $order;
			} elseif ( 'submissions' === $query->query['orderby'] ) {
				return $fusion_forms_table_name . '.submissions_count ' . $order;
			}
		}

		return $orderby;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @since 1.0
	 * @access public
	 * @param  array  $item        Data.
	 * @param  string $column_id - Current column id.
	 * @return string
	 */
	public function column_default( $item, $column_id ) {
		do_action( 'manage_fusion_form_custom_column', $column_id, $item );

		if ( isset( $item[ $column_id ] ) ) {
			return $item[ $column_id ];
		}
		return '';
	}

	/**
	 * Set row actions for title column.
	 *
	 * @since 1.0
	 * @access public
	 * @param  array $item Data.
	 * @return string
	 */
	public function column_title( $item ) {
		$wpnonce = wp_create_nonce( 'fusion-form-builder' );
		$actions = [];

		if ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$actions['restore'] = sprintf( '<a href="?_wpnonce=%s&action=%s&post=%s">' . esc_html__( 'Restore', 'fusion-builder' ) . '</a>', esc_attr( $wpnonce ), 'fusion_restore_element', esc_attr( $item['id'] ) );
			$actions['delete']  = sprintf( '<a href="?_wpnonce=%s&action=%s&post=%s">' . esc_html__( 'Delete Permanently', 'fusion-builder' ) . '</a>', esc_attr( $wpnonce ), 'fusion_delete_element', esc_attr( $item['id'] ) );
		} else {
			$actions = awb_get_list_table_edit_links( $actions, $item );

			if ( current_user_can( 'edit_others_posts' ) ) {
				$actions['clone_section'] = '<a href="' . $this->get_section_clone_link( $item['id'] ) . '" title="' . esc_attr( __( 'Clone this form', 'fusion-builder' ) ) . '">' . __( 'Clone', 'fusion-builder' ) . '</a>';
				$actions['reset']         = sprintf( '<a href="?_awb_reset_form=%s&action=%s&post=%s">' . esc_html__( 'Reset Stats', 'fusion-builder' ) . '</a>', esc_attr( wp_create_nonce( 'reset_form' ) ), 'awb_reset_form', esc_attr( $item['id'] ) );
			}

			if ( current_user_can( 'delete_post', $item['id'] ) ) {
				$actions['trash'] = sprintf( '<a href="?_wpnonce=%s&action=%s&post=%s">' . esc_html__( 'Trash', 'fusion-builder' ) . '</a>', esc_attr( $wpnonce ), 'fusion_trash_element', esc_attr( $item['id'] ) );
			}
		}

		return awb_get_list_table_title( $item ) . ' ' . $this->row_actions( $actions );
	}


	/**
	 * Set bulk actions dropdown.
	 *
	 * @since 1.0
	 * @access public
	 * @return array
	 */
	public function get_bulk_actions() {
		if ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$actions = [
				'fusion_bulk_restore_element' => esc_html__( 'Restore', 'fusion-builder' ),
				'fusion_bulk_delete_element'  => esc_html__( 'Delete Permanently', 'fusion-builder' ),
			];
		} else {
			$actions = [
				'fusion_bulk_trash_element' => esc_html__( 'Move to Trash', 'fusion-builder' ),
			];
		}

		$actions['awb_bulk_reset_forms'] = esc_html__( 'Reset Stats', 'fusion-builder' );

		return $actions;
	}

	/**
	 * The entries column.
	 *
	 * @since 2.3
	 * @access public
	 * @param  array $item Data.
	 * @return string
	 */
	public function column_entries( $item ) {

		if ( 1 <= $item['entries'] ) {
			$url  = admin_url( 'admin.php?page=avada-form-entries&form_id=' . $item['form_id'] );
			$html = '<span class="counter">' . $item['entries'] . '</span>';

			if ( current_user_can( apply_filters( 'awb_role_manager_access_capability', 'moderate_comments', 'fusion_form', 'submissions_access' ) ) ) {
				$html .= '<a class="view-submissions" href="' . esc_url( $url ) . '">' . esc_html__( 'View Entries', 'fusion-builder' ) . '</a>';
			}

			return $html;
		}
		return '-';
	}

	/**
	 * Gets the link to clone a form.
	 *
	 * @access public
	 * @since 3.0
	 * @param int $id The item-id.
	 * @return string
	 */
	public function get_section_clone_link( $id ) {

		$args = [
			'_fusion_form_clone_nonce' => wp_create_nonce( 'clone_form' ),
			'item'                     => $id,
			'action'                   => 'clone_form',
		];

		$url = add_query_arg( $args, admin_url( 'admin.php' ) );

		return $url;
	}

	/**
	 * Set checkbox for bulk selection and actions.
	 *
	 * @since 1.0
	 * @access public
	 * @param  array $item Data.
	 * @return string
	 */
	public function column_cb( $item ) {
		if ( current_user_can( 'delete_post', $item['id'] ) || current_user_can( 'edit_post', $item['id'] ) ) {
			return "<input type='checkbox' name='post[]' value='{$item['id']}' />";
		}

		return '';
	}

	/**
	 * Display custom text if form builder is empty.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function no_items() {
		if ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			esc_attr_e( 'No forms found in Trash.', 'fusion-builder' );
		} else {
			esc_attr_e( 'No forms have been created yet.', 'fusion-builder' );
		}
	}

	/**
	 * Display status count with link.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function get_status_links() {
		$post_status        = [];
		$count_posts        = wp_count_posts( 'fusion_form' );
		$count_posts        = (array) $count_posts;
		$post_status['all'] = $count_posts['publish'] + $count_posts['draft'] + $count_posts['pending'];

		if ( isset( $count_posts['publish'] ) && $count_posts['publish'] ) {
			$post_status['publish'] = $count_posts['publish'];
		}

		if ( isset( $count_posts['draft'] ) && $count_posts['draft'] ) {
			$post_status['draft'] = $count_posts['draft'];
		}

		if ( isset( $count_posts['trash'] ) && $count_posts['trash'] ) {
			$post_status['trash'] = $count_posts['trash'];
		}

		if ( isset( $count_posts['pending'] ) && $count_posts['pending'] ) {
			$post_status['pending'] = $count_posts['pending'];
		}
		?>
		<ul class="subsubsub">
			<?php $i = 0; ?>
			<?php foreach ( $post_status as $status => $count ) : ?>
				<?php
				$i++;
				$current_status = ( isset( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification
				$status_attr    = ( 'all' !== $status ) ? '&status=' . $status : '';
				$status_title   = ( 'publish' === $status ) ? __( 'Published', 'fusion-builder' ) : $status;
				?>
				<li class="<?php echo esc_attr( $status ); ?>">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=avada-forms' ) . $status_attr ); ?>"<?php echo ( $status === $current_status ) ? ' class="current" ' : ''; ?>>
						<?php
						printf(
							/* Translators: 1: Status. 2: Count. */
							esc_html__( '%1$s (%2$s)', 'fusion-builder' ),
							esc_html( ucwords( $status_title ) ),
							esc_html( $count )
						);
						?>
					</a>
				</li>

				<?php
				// Add separator if needed.
				if ( $i < count( $post_status ) ) {
					echo ' | ';
				}
				?>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
