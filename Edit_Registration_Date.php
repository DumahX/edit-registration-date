<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'Edit_Registration_Date' ) ) {
  final class Edit_Registration_Date {
    private static $instance = null;
    private $errors = array();

    /**
     * Constructor
     * 
     * Associates methods with the proper hooks.
     * 
     * @access private
     * @return void
     */
    private function __construct() {
      add_action( 'edit_user_profile', array( $this, 'registration_date_field' ) );
      add_action( 'show_user_profile', array( $this, 'registration_date_field' ) );
      add_action( 'edit_user_profile_update', array( $this, 'save_registration_date_field' ) );
      add_action( 'personal_options_update', array( $this, 'save_registration_date_field' ) );
      add_action( 'user_profile_update_errors', array( $this, 'maybe_show_errors' ), 10, 1 );
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Adds a new field to the Edit User screen so the registration date
     * can be edited.
     * 
     * @access public
     * @param object $user Current user being edited.
     * @return void
     */
    public function registration_date_field( $user ) {
      if ( ! $this->can_user_edit() ) {
        return;
      }

      ?>
      <table class="form-table">
        <tbody>
          <tr id="erd_registration_date_row">
            <th>
              <label for="erd_registration_date"><?php esc_html_e( 'Registration Date', 'edit-registration-date' ); ?></label>
            </th>
            <td>
              <input type="text" name="erd_registration_date" id="erd_registration_date" value="<?php echo $user->user_registered; ?>">
            </td>
          </tr>
        </tbody>
      </table>
      <?php
    }

    /**
     * Saves the registration date field when the user has been edited.
     * 
     * @access public
     * @param int $user_id ID of the user being edited.
     * @return void
     */
    public function save_registration_date_field( $user_id ) {
      if ( ! current_user_can( 'edit_user', $user_id ) || ! $this->can_user_edit() ) {
        return;
      }

      $registration_date = isset( $_POST['erd_registration_date'] ) ? trim( $_POST['erd_registration_date'] ) : '';

      if ( empty( $registration_date ) ) {
        $this->errors[ 'registration_date_empty' ] = __( 'Registration date cannot be empty.', 'edit-registration-date' );
      }

      if ( ! is_string( $registration_date ) || ! strtotime( $registration_date ) ) {
        $this->errors[ 'registration_date_type' ] = __( 'Registration date must be in a valid date format.', 'edit-registration-date' );
      }

      if ( count( $this->errors ) > 0 ) {
        return;
      }

      if ( is_wp_error( wp_update_user( array( 'ID' => $user_id, 'user_registered' => $registration_date ) ) ) ) {
        $this->errors[ 'registration_date_save' ] = __( 'There was an issue with saving the registration date.', 'edit-registration-date' );
      }
    }

    /**
     * Shows errors on the Edit User screen if there was an issue with the registration field or
     * saving the user.
     * 
     * @access public
     * @param object $errors WP_Error object
     * @return void
     */
    public function maybe_show_errors( $errors ) {
      if ( is_countable( $this->errors ) && count( $this->errors ) > 0 ) {
        foreach ( $this->errors as $k => $v ) {
          $errors->add( $k, $v );
        }
      }
    }

    /**
     * Registers and enqueues scripts on the Edit User screen.
     * 
     * @access public
     * @param string $hook The current admin page.
     * @return void
     */
    public function enqueue_scripts( $hook ) {
      if ( 'user-edit.php' !== $hook && 'profile.php' !== $hook ) {
        return;
      }

      wp_register_script(
        'edit-registration-date',
        EDIT_REGISTRATION_DATE_PLUGIN_URL . 'js/edit-registration-date.js',
        array( 'jquery' )
      );

      wp_register_script(
        'edit-registration-date-jquery-ui-timepicker',
        EDIT_REGISTRATION_DATE_PLUGIN_URL . 'js/jquery-ui-timepicker.min.js',
        array( 'jquery', 'jquery-ui-datepicker' )
      );

      wp_register_style(
        'edit-registration-date-jquery-ui-timepicker',
        EDIT_REGISTRATION_DATE_PLUGIN_URL . 'css/jquery-ui-timepicker.min.css'
      );

      wp_enqueue_script( 'edit-registration-date' );
      wp_enqueue_script( 'edit-registration-date-jquery-ui-timepicker' );
      wp_enqueue_style( 'edit-registration-date-jquery-ui-timepicker' );
    }

    /**
     * Allows only one instance of the class to be loaded.
     * 
     * @access public
     * @return object
     */
    public static function instance() {
      if ( null == self::$instance ) {
        self::$instance = new self;
      }

      return self::$instance;
    }

    /**
     * Checks if the user can edit the registration date for their own profile and for other users.
     * 
     * @access private
     * @return bool
     */
    private function can_user_edit() {
      $current_user = wp_get_current_user();

      if (
        ! $current_user ||
        ! array_intersect( apply_filters( 'edit_registration_date_allowed_roles', array( 'administrator' ) ), (array) $current_user->roles )
      ) {
        return false;
      } else {
        return true;
      }
    }
  }
}