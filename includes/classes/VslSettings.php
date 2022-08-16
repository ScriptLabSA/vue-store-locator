<?php

/**
 * VslSettings class
 * @author : Script Lab
 * Date: 2022/07/29
 * Time: 10:50 AM
 */
defined( 'ABSPATH' ) || exit();

class VslSettings
{
	private static $_instance = null; //phpcs:ignore
	public $parent = null;
	public $base = '';
	public $token = '';

	public $settings = array();

	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'vsl_';
		$this->token = 'vsl_store';

		// Initialise settings.
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename( VSL_PLUGIN_FILE ),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of plugin settings page. See readme for implementation.
		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {

		$args = $this->menu_settings();

		// Do nothing if wrong location key is set.
		if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
			switch ( $args['location'] ) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
					break;
				default:
					return;
			}
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
		}
	}

	/**
	 * Prepare default settings page arguments
	 *
	 * @return mixed|void
	 */
	private function menu_settings() {
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'VSL Settings', VSL_PLUGIN_NAME ),
				'menu_title'  => __( 'VSL Settings', VSL_PLUGIN_NAME ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public function configure_settings( $settings = array() ) {
		return $settings;
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets() {
		wp_register_script( $this->token . '-sortable-js', VSL_PLUGIN_URL . '/assets/js/jquery-sortable-min.js', array( 'jquery' ), '0.9.13', true );
		wp_enqueue_script( $this->token . '-sortable-js' );

		wp_register_script( $this->token . '-clonedata-js', VSL_PLUGIN_URL . '/assets/js/cloneData.js', array( 'jquery' ), '1.0.1', true );
		wp_enqueue_script( $this->token . '-clonedata-js' );

		wp_register_script( $this->token . '-settings-js', VSL_PLUGIN_URL . '/assets/js/settings.js', array( 'jquery', $this->token . '-clonedata-js', $this->token . '-sortable-js' ), '1.0.5', true );
		wp_enqueue_script( $this->token . '-settings-js' );
		wp_localize_script( $this->token . '-settings-js', 'vsl_settings_vars', array(
			'removeConfirmMessage' => __( 'Are you sure want to delete?', VSL_PLUGIN_NAME ),
			'labelMessage' => __( 'Options', VSL_PLUGIN_NAME ),
			'helpMessage' => __( 'Add Comma-separated text values for select options', VSL_PLUGIN_NAME )
		));

		wp_register_style( $this->token . '-settings-css', VSL_PLUGIN_URL . '/assets/css/admin/settings.css', array(), '1.0.4' );
        wp_enqueue_style( $this->token . '-settings-css' );

		wp_enqueue_style( 'bootstrap_grid', VSL_PLUGIN_URL . '/assets/css/bootstrap-grid.min.css', array(), null );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->token . '_settings">' . __( 'Settings', VSL_PLUGIN_NAME ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {

		$settings['vsl-general-settings'] = array(
			'title'       => __( 'General', VSL_PLUGIN_NAME ),
			'description' => __( 'Plugin general settings.', VSL_PLUGIN_NAME ),
			'fields'      => array(
				// array(
				// 	'id'          => 'store_custom_fields',
				// 	'label'       => __( 'Store custom fields', VSL_PLUGIN_NAME ),
				// 	'description' => '',
				// 	'type'        => 'custom_fields',
				// 	'default'     => VslConstants::DEFAULT_SETTINGS
				// )
				// array(
				// 	'id'          => 'colour_picker',
				// 	'label'       => __( 'Pick a colour', VSL_PLUGIN_NAME ),
				// 	'description' => __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', VSL_PLUGIN_NAME ),
				// 	'type'        => 'color',
				// 	'default'     => '#21759B',
				// ),
				// array(
				// 	'id'          => 'colour_picker_2',
				// 	'label'       => __( 'Pick a colour', VSL_PLUGIN_NAME ),
				// 	'description' => __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', VSL_PLUGIN_NAME ),
				// 	'type'        => 'color_2',
				// 	'default'     => '#21759B',
				// ),
				// array(
				// 	'id'          => 'an_image',
				// 	'label'       => __( 'An Image', VSL_PLUGIN_NAME ),
				// 	'description' => __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', VSL_PLUGIN_NAME ),
				// 	'type'        => 'image',
				// 	'default'     => '',
				// 	'placeholder' => ''
				// ),
				// array(
				// 	'id'          => 'multi_select_box',
				// 	'label'       => __( 'A Multi-Select Box', VSL_PLUGIN_NAME ),
				// 	'description' => __( 'A standard multi-select box - the saved data is stored as an array.', VSL_PLUGIN_NAME ),
				// 	'type'        => 'select_multi',
				// 	'options'     => array(
				// 		'linux'   => 'Linux',
				// 		'mac'     => 'Mac',
				// 		'windows' => 'Windows'
				// 	),
				// 	'default'     => array( 'linux' )
				// )
			)
		);

		$settings['vsl-store-settings'] = array(
			'title'       => __( 'Store', VSL_PLUGIN_NAME ),
			'description' => __( 'Plugin store settings.', VSL_PLUGIN_NAME ),
			'fields'      => array(
				array(
					'id'          => 'store_custom_fields',
					'label'       => __( 'Store custom fields', VSL_PLUGIN_NAME ),
					'description' => '',
					'type'        => 'custom_fields',
					'default'     => VslConstants::DEFAULT_SETTINGS
				)
			)
		);

		$settings = apply_filters( $this->token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab.
			//phpcs:disable
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}
			//phpcs:enable

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section !== $section ) {
					continue;
				}

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field.
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting( $this->token . '_settings', $option_name, $validation );

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array( $this, 'display_field' ),
						$this->token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html; //phpcs:ignore
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML.
		$html = '<div class="wrap vsl-settings" id="' . $this->token . '_settings">' . "\n";
		$html .= '<h2>' . __( 'Plugin Settings', VSL_PLUGIN_NAME ) . '</h2>' . "\n";

		$tab = '';
		//phpcs:disable
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= $_GET['tab'];
		}
		//phpcs:enable

		// Show page tabs.
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class.
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) { //phpcs:ignore
					if ( 0 === $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) { //phpcs:ignore
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) { //phpcs:ignore
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields.
				ob_start();
				settings_fields( $this->token . '_settings' );
				do_settings_sections( $this->token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
				$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
				$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', VSL_PLUGIN_NAME ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html     .= '</form>' . "\n";
		$html         .= '</div>' . "\n";

		echo $html; //phpcs:ignore
	}

	public function display_field( $data = array(), $post = null, $echo = true ) {

		// Get field info.
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name.
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data.
		$data = false;
		if ( $post ) {

			// Get saved field data.
			$option_name .= $field['id'];
			if( ! isset( $field['show_only'] ) ) {
				$option = get_post_meta( $post->ID, $field['id'], true );
			} else if( isset( $field['data'] ) ) {
				$option = $field['data'];
			}

			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		} elseif( isset( $field['tax'] ) && ( boolean ) $field['tax'] ) {

			// Get saved option.
			$option_name .= $field['id'];

			if( isset( $field['term_id'] ) ) {
				$option = get_term_meta( $field['term_id'], $option_name, true ); 
			} else {
				$option = false;
			}

			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		} else {
			// Get saved option.
			$option_name .= $field['id'];
			$option = get_option( $option_name );
			
			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		}

		// Show default data if no option saved and default is supplied.
		if ( false === $data && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( false === $data ) {
			$data = '';
		}

		$html = '';
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';

		switch ( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
				break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
				break;

			case 'date':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" data-toggle="datepicker" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $data ) . '" readonly />' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="" />' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $placeholder ) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' === $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( strval( $k ), (array) $data, true ) ) {
						$checked = true;
					}
					$html .= '<p><label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label></p> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k === $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( strval( $k ) === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, (array) $data, true ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'dropdown_users':
				$html .= wp_dropdown_users( array( 'id' => esc_attr( $field['id'] ), 'name' => esc_attr( $option_name ), 'selected' => $data, 'echo' => false ) );
				break;

			case 'dropdown_taxonomy':
				$html .= wp_dropdown_categories( array( 'id' => esc_attr( $field['id'] ), 'name' => esc_attr( $option_name ), 'selected' => $data, 'echo' => false, 'taxonomy' => esc_attr( $field['taxonomy'] ) ) );
				break;

			case 'image':
				$upload_text = isset( $field['options']['upload_bt_text'] ) && ! empty( $field['options']['upload_bt_text'] ) ? $field['options']['upload_bt_text'] : __( 'Upload new image', VSL_PLUGIN_NAME );
				$remove_text = isset( $field['options']['remove_bt_text'] ) && ! empty( $field['options']['remove_bt_text'] ) ? $field['options']['remove_bt_text'] : __( 'Remove image', VSL_PLUGIN_NAME );

				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image', VSL_PLUGIN_NAME ) . '" data-uploader_button_text="' . __( 'Use image', VSL_PLUGIN_NAME ) . '" class="image_upload_button button" value="' . $upload_text . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="' . $remove_text . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
				break;

			case 'color':
				//phpcs:disable
				?><div class="color-picker" style="position:relative;">
					<input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color"
						value="<?php esc_attr_e( $data ); ?>" />
					<div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
				</div>
				<?php
				//phpcs:enable
				break;

			case 'color_2':
				$html .= '<input name="' . esc_attr( $option_name ) . '" type="text" class="color-wp-field" value="' . esc_attr( $data ) . '" />';
				break;

			case 'editor':
				wp_editor(
					$data,
					$option_name,
					array(
						'textarea_name' => $option_name,
					)
				);
				break;

			case 'multiple_image':
				$is_dragable = isset( $field['options']['dragable'] ) && ! empty( $field['options']['dragable'] ) ? ( boolean ) $field['options']['dragable'] : false;

				$html .= '<div class="msl-image-list" data-dragable="' . ( $is_dragable ? 'true' : 'false' ) . '">' . "\n";
				$html .= '<div class="gallery-image-list-options"><div>';
				
				if ( isset( $field['options']['editable'] ) && $field['options']['editable'] ) {
					$html .= '<input id="' . esc_attr( $option_name ) . '_button" data-option_name="' . esc_attr( $option_name ) . '" type="button" data-uploader_title="' . __( 'Upload an image', VSL_PLUGIN_NAME ) . '" data-uploader_button_text="' . __( 'Use image', VSL_PLUGIN_NAME ) . '" class="gallery-upload-button button" value="' . __( 'Upload images', VSL_PLUGIN_NAME ) . '" />' . "\n";
				}

				$html .= '</div><div class="gallery-image-list-summary"><span class="summary-total">' . ( is_array( $data ) ? count( $data ): '0' ) . '</span> ' . __( 'picture(s)', VSL_PLUGIN_NAME ) . '</div></div>';
				$html .= '<div class="grid-media">' . "\n";

				foreach ( ( array ) $data as $value) {
					$media_src = wp_get_attachment_image_src( ( int ) $value, 'thumbnail');
					if ( is_array( $media_src ) ) {
						$html .= '<div class="item-media" data-id="' . $value . '"><div class="item-media-content"><input type="hidden" name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $value ) . '" class="image_attachment_id"><div class="grid-card"><div class="grid-card-handle" style="touch-action: none; user-select: none;"><img class="grid-image-preview" src="' . $media_src[0] . '" style="" /></div>' . "\n";
						
						if ( isset( $field['options']['editable'] ) &&  $field['options']['editable'] ) {
							$html .= '<i class="grid-card-remove dashicons dashicons-no-alt"></i>' . "\n";
						}

						$html .= '</div></div></div>' . "\n";
					}
				}
				$html .= '</div></div>' . "\n";
				break;

			case 'multiple_image_2':
				$html .= '<div class="board-container"><div class="board-cards">';
				$count = 0;
				foreach ( ( array ) $data as $value ) {
					if( is_array( $value ) ) {
						if( isset( $value['id'] ) && isset( $value['date'] ) ) {
							$media_src = wp_get_attachment_image_src( ( int ) $value['id'], 'thumbnail');
							if ( is_array( $media_src ) ) {
								$html .= '<div class="board-card-column" data-id="' . $value['id'] . '"><input type="hidden" name="' . esc_attr( $option_name ) . '[' . $count . '][id]" value="' . esc_attr( $value['id'] ) . '" class="image_attachment_id"><input type="hidden" name="' . esc_attr( $option_name ) . '[' . $count . '][date]" value="' . esc_attr( $value['date'] ) . '" class="image_attachment_id"><div class="board-card-title">' . esc_attr( $value['date'] ) . '</div><img class="board-image-preview" src="' . $media_src[0] . '" style="" /></div>' . "\n";
							}
						}
					} else {
						$media_src = wp_get_attachment_image_src( ( int ) $value, 'thumbnail');
						if ( is_array( $media_src ) ) {
							$html .= '<div class="board-card-column" data-id="' . $value . '"><input type="hidden" name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $value ) . '" class="image_attachment_id"><img class="board-image-preview" src="' . $media_src[0] . '" style="" /></div>' . "\n";
						}
					}

					$count ++;
				}
				$html .= '</div></div>';
				break;

			case 'custom_fields':
				$html .= '<div class="custom-fields-main">';
				$html .=    '<div class="container custom-fields-container" id="main-container">';

				if ( is_array( $data ) && count( $data ) > 0 ) {
					$iterator = 0;
					foreach( $data as $custom_field ) {
						$html .= '<div class="card container-item" data-index="' . $iterator . '" data-name="' . esc_attr( $option_name ) . '">';
						$html .=    '<i class="grid-card-move dashicons dashicons-move"></i>';
						$html .=    '<div class="row g-2">';
						// $html .=       '<span class="close">&times;</span>';
						$html .=       '<div class="col-4 mb-space"><label class="form-label" for="' . esc_attr( $option_name ) . '_name_' . $iterator . '">' . __( 'Name', VSL_PLUGIN_NAME ) . '</label><input class="form-control" type="text" id="' . esc_attr( $option_name ) . '_name_' . $iterator . '" name="' . esc_attr( $option_name ) . '[' . $iterator . '][name]" value="' . esc_attr( $custom_field['name'] ) . '" required="required"></div>';
						$html .=       '<div class="col-4 mb-space"><label class="form-label" for="' . esc_attr( $option_name ) . '_label_' . $iterator . '">' . __( 'Label', VSL_PLUGIN_NAME ) . '</label><input class="form-control" type="text" id="' . esc_attr( $option_name ) . '_label_' . $iterator . '" name="' . esc_attr( $option_name ) . '[' . $iterator . '][label]" value="' . esc_attr( $custom_field['label'] ) . '" required="required"></div>';
						$html .=       '<div class="col-4 mb-space"><label class="form-label" for="' . esc_attr( $option_name ) . '_type_' . $iterator . '">' . __( 'Type', VSL_PLUGIN_NAME ) . '</label>' . $this->get_select_html( $option_name, $custom_field['type'], $iterator ) . '</div>';
						
						switch ( $custom_field['type'] ) {
							case 'select':
								$html .= '<div class="col-12 mb-space exclude"><label class="form-label" for="' . esc_attr( $option_name ) . '_options_' . $iterator . '">' . __( 'Options', VSL_PLUGIN_NAME ) . '</label><textarea class="form-control" type="text" id="' . esc_attr( $option_name ) . '_options_' . $iterator . '" name="' . esc_attr( $option_name ) . '[' . $iterator . '][options]" required="required">' . esc_textarea( $custom_field['options'] ) . '</textarea><small class="text-muted">' . __( 'Add Comma-separated text values for select options', VSL_PLUGIN_NAME ) . '</small></div>';
								break;
						}
						
						$html .=       '<div class="col-12 remove-container">';
						$html .=          '<a href="javascript:;" class="remove-item button">' . __( 'Remove', VSL_PLUGIN_NAME ) . '</a>';
						$html .=       '</div>';
						$html .=    '</div>';
						$html .= '</div>';

						$iterator++;
					}
				} else {
					$html .= '<div class="card container-item" data-index="0" data-name="' . esc_attr( $option_name ) . '">';
					$html .=    '<div class="row g-2">';
					$html .=       '<div class="col-4 mb-space"><label class="form-label" for="' . esc_attr( $option_name ) . '_name_0">' . __( 'Name', VSL_PLUGIN_NAME ) . '</label><input class="form-control" type="text" id="' . esc_attr( $option_name ) . '_name_0" name="' . esc_attr( $option_name ) . '[0][name]" required="required"></div>';
					$html .=       '<div class="col-4 mb-space"><label class="form-label" for="' . esc_attr( $option_name ) . '_label_0">' . __( 'Label', VSL_PLUGIN_NAME ) . '</label><input class="form-control" type="text" id="' . esc_attr( $option_name ) . '_label_0" name="' . esc_attr( $option_name ) . '[0][label]" required="required"></div>';
					$html .=       '<div class="col-4 mb-space"><label class="form-label" for="' . esc_attr( $option_name ) . '_type_0">' . __( 'Type', VSL_PLUGIN_NAME ) . '</label>' . $this->get_select_html( $option_name, 'text', 0 ) . '</div>';
					$html .=       '<div class="col-12 remove-container">';
					$html .=          '<a href="javascript:;" class="remove-item button">' . __( 'Remove', VSL_PLUGIN_NAME ) . '</a>';
					$html .=       '</div>';
					$html .=    '</div>';
					$html .= '</div>';
				}

				$html .=    '</div>';
				$html .=    '<div>';
				$html .=       '<div class="row add-more-custom">';
				$html .=          '<div class="col">';
				$html .=             '<a id="add-more-custom" href="javascript:;" class="add-item button">' . __( 'Add More Custom Fields', VSL_PLUGIN_NAME ) . '</a>';
				$html .=          '</div>';
				$html .=       '</div>';
				$html .=    '</div>';
				$html .= '</div>';
				break;
		}

		switch ( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
				break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}

				if( isset( $field['description'] ) && ! empty($field['description'] ) ) {
					$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";
				}
				
				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
				break;
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html; //phpcs:ignore

	}

	public function get_select_html( $option_name, $value = 'number', $iterator = 0 ) {
		$select = '<select class="form-select select-custom-type" aria-label="' . __( 'Select an option', VSL_PLUGIN_NAME ) . '" id="' . esc_attr( $option_name ) . '_type_' . $iterator . '" name="' . esc_attr( $option_name ) . '[' . $iterator . '][type]">';
		foreach( VslConstants::SETTINGS_TYPE as $type ) {
			$select .= '<option value="' . $type['key'] . '" ' . selected( $type['key'], $value, false ) . '>' . $type['label'] . '</option>';
		}
		$select .= '</select>';

		return $select;
	}

	/**
	 * Validate form field
	 *
	 * @param  string $data Submitted value.
	 * @param  string $type Type of field to validate.
	 * @return string       Validated value
	 */
	public function validate_field( $data = '', $type = 'text' ) {

		switch ( $type ) {
			case 'text':
				$data = esc_attr( $data );
				break;
			case 'url':
				$data = esc_url( $data );
				break;
			case 'email':
				$data = is_email( $data );
				break;
			case 'date':
				$data = $this->is_date_format( $data );
				break;
		}

		return $data;
	}

	/**
	 * Validate date
	 *
	 * @param  string $date
	 * @param  string $format
	 * 
	 * @return string|boolean Validated value
	 */
	public function is_date_format( $date, $format = 'm/d/Y' ) {
		$d = DateTime::createFromFormat( $format, $date );
		return $d && $d->format( $format ) == $date ? $date : false;
	}

	public function get_store_custom_fields() {
		$settings = $this->get_settings_field_value( $this->base . 'store_custom_fields', VslConstants::DEFAULT_SETTINGS );
		
		for( $i = 0; $i < count( $settings ); $i++ ) {
			if( isset( $settings[$i]['type'] ) && 'select' === $settings[$i]['type'] && isset( $settings[$i]['options'] ) && ! empty( $settings[$i]['options'] ) ) {
				$settings[$i]['options'] = array_map( 'trim', str_getcsv( $settings[$i]['options'] ) );
			}
		}

		return $settings;
	}

	private function get_settings_field_value( $option_name, $default = false ) {
		return get_option( $option_name, $default );
	}

	/**
	 * Main Instance
	 *
	 * Ensures only one instance of Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @param object $parent Object instance.
	 * @return object instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of VslSettings is forbidden.' ) ), esc_attr( $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of VslSettings is forbidden.' ) ), esc_attr( $this->parent->_version ) );
	} // End __wakeup()
}
