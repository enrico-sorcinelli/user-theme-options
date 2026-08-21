<?php
/**
 * Plugin base class.
 *
 * @package user-theme-options
 * @author Enrico Sorcinelli
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

/**
 * Plugin base class.
 */
class User_Theme_Options {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Handled roles.
	 *
	 * @var array
	 */
	public $managed_roles = array();

	/**
	 * Instance settings.
	 *
	 * @var array
	 */
	private $settings = false;

	/**
	 * User_Theme_Options constructor.
	 *
	 * @param array $args {
	 *     Argument list.
	 *     @type boolean $debug Debug mode.
	 * }
	 */
	public function __construct( $args = array() ) {

		$this->settings = wp_parse_args(
			$args,
			array(
				'debug' => false,
			)
		);

		// Default sumbnenu visibility values.
		$this->settings['default_theme_options'] = array(
			'themes'       => 0,
			'customize'    => 0,
			'widgets'      => 0,
			'nav-menus'    => 0,
			'font-library' => 0,
			'patterns'     => 0,
		);

		// Slug submenu mapping.
		$this->settings['theme_options_slug_map'] = array(
			'themes'       => 'themes.php',
			'customize'    => 'customize.php',
			'widgets'      => 'widgets.php',
			'nav-menus'    => 'nav-menus.php',
			'font-library' => 'font-library.php',
			'patterns'     => 'site-editor.php?p=/pattern',
		);

		// Load plugin text domain.
		load_plugin_textdomain( 'user-theme-options', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );

		// Set handled roles.
		if ( defined( 'USER_THEME_OPTIONS_MANAGED_ROLES' ) && is_array( USER_THEME_OPTIONS_MANAGED_ROLES ) ) {
			$this->managed_roles = USER_THEME_OPTIONS_MANAGED_ROLES;
		}

		// Add setting on user profile.
		if ( current_user_can( 'edit_users' ) ) {
			add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields' ) );

			add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );
		}

		// Only for non admin.
		if ( ! current_user_can( 'administrator' ) ) {
			add_action( 'admin_menu', array( $this, 'fix_edit_themes_menu' ), 10 );
		}
	}

	/**
	 * Get the singleton instance of this class.
	 *
	 * @param array $args Constructor arguments list.
	 *
	 * @return object
	 */
	public static function get_instance( $args = array() ) {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self( $args );
		}
		return self::$instance;
	}

	/**
	 * Add section in user profile page.
	 *
	 * @param \WP_User $user The WP_User object of the user being edited.
	 *
	 * @return void
	 */
	public function extra_user_profile_fields( $user ) {

		// Check for handled roles.
		if ( false === $this->check_role( $user ) ) {
			return;
		}

		$theme_options = wp_parse_args(
			is_network_admin() ?
				get_user_meta( $user->ID, 'user_theme_options_edit_theme_options', true )
				: get_user_option( 'user_theme_options_edit_theme_options', $user->ID ),
			$this->settings['default_theme_options']
		);

		$this->log( __METHOD__ . ':' . __LINE__, $theme_options, is_network_admin() );

		?>
<h3><?php esc_html_e( 'Theme Options', 'user-theme-options' ); ?></h3>
<table class="form-table">
	<tr>
		<th><?php esc_html_e( 'Themes', 'user-theme-options' ); ?></th>
		<td>
			<label>
				<input name="user_theme_options_edit_theme_options[themes]" type="checkbox" value="1" <?php checked( 1, user_can( $user, 'edit_theme_options' ) && $theme_options['themes'] ); ?>">
				<?php esc_html_e( 'Enable Themes', 'user-theme-options' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will allow user to edit themes.', 'user-theme-options' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Customizer', 'user-theme-options' ); ?></th>
		<td>
			<label>
				<input name="user_theme_options_edit_theme_options[customize]" type="checkbox" value="1" <?php checked( 1, user_can( $user, 'edit_theme_options' ) && $theme_options['customize'] ); ?>">
				<?php esc_html_e( 'Enable Customize', 'user-theme-options' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will allow user to open customizer.', 'user-theme-options' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Widgets', 'user-theme-options' ); ?></th>
		<td>
			<label>
				<input name="user_theme_options_edit_theme_options[widgets]" type="checkbox" value="1" <?php checked( 1, user_can( $user, 'edit_theme_options' ) && $theme_options['widgets'] ); ?>">
				<?php esc_html_e( 'Enable widgets', 'user-theme-options' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will allow user to edit widgets.', 'user-theme-options' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Menu', 'user-theme-options' ); ?></th>
		<td>
			<label>
				<input name="user_theme_options_edit_theme_options[nav-menus]" type="checkbox" value="1" <?php checked( 1, user_can( $user, 'edit_theme_options' ) && $theme_options['nav-menus'] ); ?>">
				<?php esc_html_e( 'Enable menus', 'user-theme-options' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will allow user to edit menus.', 'user-theme-options' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Fonts', 'user-theme-options' ); ?></th>
			<td>
			<label>
				<input name="user_theme_options_edit_theme_options[font-library]" type="checkbox" value="1" <?php checked( 1, user_can( $user, 'edit_theme_options' ) && $theme_options['font-library'] ); ?>">
				<?php esc_html_e( 'Enable Fonts', 'user-theme-options' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will allow user to edit fonts.', 'user-theme-options' ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Patterns', 'user-theme-options' ); ?></th>
		<td>
			<label>
				<input name="user_theme_options_edit_theme_options[patterns]" type="checkbox" value="1" <?php checked( 1, user_can( $user, 'edit_theme_options' ) && $theme_options['patterns'] ); ?>">
				<?php esc_html_e( 'Enable Patterns', 'user-theme-options' ); ?>
			</label>
			<p class="description">
				<?php esc_html_e( 'This setting will allow user to edit patterns.', 'user-theme-options' ); ?>
			</p>
		</td>
	</tr>
		<?php
		// Allow to do other things.
		do_action( 'user_theme_options_profile_fields', $user, $theme_options );
		?>
</table>
		<?php
	}

	/**
	 * Update user caps from profile.
	 *
	 * @param integer $user_id The user ID of the user being edited.
	 *
	 * @return void
	 */
	public function save_extra_user_profile_fields( $user_id ) {

		// Only worry about if the user has access.
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		// Get user.
		$editing_user = get_user_by( 'id', $user_id );

		// Check for managed roles.
		if ( false === $this->check_role( $editing_user ) ) {
			return;
		}

		$this->log( __METHOD__ . ':' . __LINE__ );

		$_REQUEST['user_theme_options_edit_theme_options'] = wp_parse_args(
			$_REQUEST['user_theme_options_edit_theme_options'],
			$this->settings['default_theme_options']
		);

		$add_cap = false;
		foreach ( $_REQUEST['user_theme_options_edit_theme_options'] as $key => $val ) {
			if ( ! empty( $val ) ) {
				$add_cap = true;
			}
		}

		if ( true === $add_cap ) {
			$this->log( __METHOD__ . ':' . __LINE__ . ' add cap' );
			$editing_user->add_cap( 'edit_theme_options' );
		} else {
			$this->log( __METHOD__ . ':' . __LINE__ . ' remove cap' );
			$editing_user->remove_cap( 'edit_theme_options' );
		}

		// Update preferences.
		update_user_option( $user_id, 'user_theme_options_edit_theme_options', $_REQUEST['user_theme_options_edit_theme_options'], is_network_admin() );
	}


	/**
	 * Allow non administrator users to see access the Menus page under Appearance but hide other options.
	 * Note that users who know the correct path to the hidden options can still access them
	 *
	 * @return void
	 */
	public function fix_edit_themes_menu() {

		global $submenu;

		// Does nothing for admin users or if user yet don't have edit_theme_options.
		if ( current_user_can( 'administrator' ) || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// Get current user.
		$user = wp_get_current_user();

		// Check for managed roles.
		if ( false === $this->check_role( $user ) ) {
			return;
		}

		$this->log( __METHOD__ . ':' . __LINE__ );

		// Retrieve per-site option.
		$theme_options = wp_parse_args(
			get_user_option( 'user_theme_options_edit_theme_options', $user->ID ),
			$this->settings['default_theme_options']
		);

		// Iterate over each Appareance submenu.
		foreach ( $submenu['themes.php'] as $item_value ) {

			// Check slug.
			if ( empty( $item_value[2] ) ) {
				continue;
			}

			// Iterate over each config.
			foreach ( $theme_options as $key => $value ) {

				// Check for mapping.
				if ( ! isset( $this->settings['theme_options_slug_map'][ $key ] ) ) {
					continue;
				}

				// Match slug.
				if ( preg_match( '#^' . preg_quote( $this->settings['theme_options_slug_map'][ $key ], '#' ) . '(\?|$)#', $item_value[2] ) ) {

					// Remove menu item using complete submenu slug.
					if ( empty( $value ) ) {
						remove_submenu_page( 'themes.php', $item_value[2] );
					}

					break;
				}
			}
		}

		// Allow to do other things.
		do_action( 'user_theme_options_fix_menu', $user, $theme_options );
	}

	/**
	 * Check if current user role is handled.
	 *
	 * @param integer|\WP_User $user WP user or user ID.
	 *
	 * @return bool
	 */
	public function check_role( $user = null ) {

		if ( empty( $user ) ) {
			$user = wp_get_current_user();
		} elseif ( ! $user instanceof \WP_User ) {
			$user = get_user_by( 'id', $user );
		}

		if ( empty( $user ) ) {
			return false;
		}

		$this->log( __METHOD__ . ':' . __LINE__, $user->roles, $this->managed_roles, array_intersect( $user->roles, $this->managed_roles ) );

		// Empty managed roles means all roles (except od administrator)
		if ( empty( $this->managed_roles ) ) {
			if ( in_array( 'administrator', $user->roles ) ) {
				return false;
			}
			return true;
		}

		// Check for managed roles over current user roles.
		if ( empty( array_intersect( $user->roles, $this->managed_roles ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Debugging helper.
	 */
	private function log() {
		if ( false === $this->settings['debug'] ) {
			return;
		}
		error_log( print_r( func_get_args(), true ) );
	}

	/**
	 * Plugin uninstall hook.
	 *
	 * @return void
	 */
	public static function plugin_uninstall() {
	}
}

