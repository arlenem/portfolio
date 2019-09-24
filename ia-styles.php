<?php
/*
Plugin Name: IA Stylesheet
Version: 1.2
Text Domain: ia-styles
Domain Path: /languages
Description: Define global font styles and colors
Author: Ibel Agency
Author URI: https://www.ibelagency.com
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( ! class_exists( 'IA_Styles' ) ) :

class IA_Styles {
	/**
	 * @var IA_Styles The single instance of the class
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * @var version
	 * @since 1.1
	 */
	static $version = '1.1';

	/**
	 * @var plugin options
	 * @since 1.0
	 */
	public $ia_styles_options = array();
	public $options = array();
	public $ia_reg_number = null;

	/**
	 * @var plugin $tabs
	 * @since 1.0
	 */
	public $tabs = array( 'h_styles' => 'Header and Paragraph (h and p) Styles', 'nav_styles' => 'Nav Styles', 'a_styles' => 'Link and Button (a) Styles', 'typeface_code' => 'Typeface Code', 'colors' => 'Color Declarations', 'globals' => 'Global Styles');

	/**
	 * @var plugin $h_fonts
	 * @since 1.0
	 */
	public $h_fonts = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'p1');

	/**
	 * @var plugin $nav_fonts
	 * @since 1.0
	 */
	public $nav_fonts = array('nav', 'nav_hover', 'nav_current', 'subnav', 'subnav_hover', 'subnav_current' );

	/**
	 * @var plugin $a_fonts
	 * @since 1.0
	 */
	public $a_fonts = array('a', 'a_hover', 'a2', 'a2_hover', 'a3', 'a3_hover');

	/**
	 * @var plugin $css_text
	 * @since 1.0
	 */
	public $css_text = array('font-family', 'font-size', 'line-height', 'letter-spacing', 'color');

	/**
	 * @var plugin $button_colors
	 * @since 1.1
	 */
	public $button_colors = array('button1', 'button2', 'button3');

	/**
	 * @var plugin $css_buttons
	 * @since 1.1
	 */
	public $css_buttons = array('background-name', 'hover-background-color', 'text-color', 'hover-text-color');
	
	/**
	 * @var plugin $css_select
	 * @since 1.0
	 */
	public $css_select = array(
		'font-style' => array('normal', 'italic'), 
		'font-weight' => array('normal', '100', '200', '300', '400', '500', '600', '700', '800', '900', 'bold'), 
		'text-transform' => array('none', 'uppercase', 'capitalize', 'lowercase'), 
		'text-decoration' => array('none', 'underline')
	);

	/**
	 * @var plugin $global_styles
	 * @since 1.2
	 */
	public $global_styles = array('article-padding', 'module-title-bottom-margin', 'column-gutter', 'extra-small-margin', 'small-margin', 'medium-margin', 'large-margin', 'extra-large-margin');

	/**
	 * @var plugin $global_buttons
	 * @since 1.1
	 */
	public $global_buttons = array('button-padding');

	/**
	 * @var plugin $css_globals
	 * @since 1.1
	 */
	public $css_globals = array('pixel-amount');

	/**
	 * @var plugin $css_global_button
	 * @since 1.1
	 */
	public $css_global_button = array('padding-top', 'padding-right', 'padding-bottom', 'padding-left');

	/**
	 * @var plugin $h_spacing
	 * @since 1.2
	 */
	public $h_spacing = array(
		'bottom_spacing' => array('extra-small', 'small', 'medium', 'large', 'extra-large')
	);

	/**
	 * @var plugin $colors Stores the color declarations (name and hexcode)
	 * @since 1.0
	 */
	public $colors = array();

	/**
	 * Main IA_Styles Instance
	 *
	 * Ensures only one instance of IA_Styles is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return IA_Styles - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' , 'ia-styles' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' , 'ia-styles' ), '1.0' );
	}

	/**
	 * IA_Styles Constructor.
	 * @access public
	 * @return IA_Styles
	 * @since  1.0
	 */
	public function __construct(){

		$this->options = get_option( 'ia_styles_options', true );

		// Set-up Action and Filter Hooks
		register_uninstall_hook( __FILE__, array( __CLASS__, 'delete_plugin_options' ) );

		// load plugin text domain for translations
		add_action( 'init', array( $this, 'load_text_domain' ) );

		// register admin settings
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		//Get the stored color declarations
		add_action( 'admin_menu', array( $this, 'get_options' ) );

		// add plugin options page
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// add ajax function for add colors button on form
		add_action( 'wp_ajax_ajax_function', 'ajax_function' );

		// Load admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );

		// Load front pages scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'front_script' ) );

		// add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ), 10, 2 );

		//Add setting sections to the IA Stylesheet settings page
		add_action( 'admin_init', array( $this, 'setup_sections' ) );

		//Add fields to the sections to the IA Stylesheet settings page
		add_action( 'admin_init', array( $this, 'setup_fields' ) );

		//Get (or set) random number for stylesheet to avoid conflict in multisite
		//add_action( 'admin_init', array( $this, 'get_registered_number' ) );

		// Create the stylesheet from the options
		add_action( 'update_option_ia_styles_options', array( $this, 'create_stylesheet' ), 10, 2 );
	}


	/**
	 * Delete options table entries ONLY when plugin deactivated AND deleted
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function delete_plugin_options() {
		$options = get_option( 'ia_styles_options', true );
		if( isset( $options['delete'] ) && $options['delete'] ) delete_option( 'ia_styles_options' );
	}

	/**
	 * Make plugin translation-ready
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function load_text_domain() {
		load_plugin_textdomain( "ia-styles", false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	// ------------------------------------------------------------------------------
	// Admin options
	// ------------------------------------------------------------------------------

	/**
	 * Whitelist plugin options
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function admin_init(){
		register_setting( 'ia_styles_options', 'ia_styles_options', array( $this,'validate_options' ) );
	}


	/**
	 * Enqueue Scripts
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function admin_script( $hook ){
		if ( 'settings_page_ia_styles_options' != $hook ) {
	        return;
	    }
		wp_register_style( 'ia-plugin-stylesheet', plugins_url('ia-styles/css/plugin-styles.css' ) );
		wp_enqueue_style( 'ia-plugin-stylesheet' );
	}

	/**
	 * Add ajax for the add color button
	 * @access public
	 * @return void
	 * @since  unused 1.0
	 */
	function ia_ajax_function($num) {
		$ia_styles_options = $this->get_options;
		//var_dump($this->ia_styles_options);

		$fields = array();
		$fields[] = array(
		    'uid' => "ia_styles_options[colors][$num][color_name]",
		    'label' => 'Color Name',
		    'section' => 'colors',
		    'type' => 'text',
		    'options' => false,
		    'placeholder' => false,
		    'helper' => false,
		    'supplemental' => false,
		    'default' => $ia_styles_options['colors'][$num]['color_name']
		);
		$fields[] = array(
		    'uid' => "ia_styles_options[colors][$num][color_hexcode]",
		    'label' => 'Color Hexcode/RGBA',
		    'section' => 'colors',
		    'type' => 'text',
		    'options' => false,
		    'placeholder' => false,
		    'helper' => false,
		    'supplemental' => 'hexcode or rgba',
		    'default' => $ia_styles_options['colors'][$num]['color_name']
		);
		foreach( $fields as $field ){
			add_settings_field( $field['uid'], $field['label'], array( $this,'field_callback' ), 'ia_styles_options', $field['section'], $field );
		}
		register_setting( 'ia_styles_options', 'ia_styles_options', array( $this,'validate_options' ) );
		do_settings_fields('ia_styles_options', $field['section']);
		//wp_die(); 
	}

	/**
	 * Display a Settings link on the main Plugins page
	 * @access public
	 * @param  array $links
	 * @param  string $file
	 * @return array
	 * @since  1.0
	 */
	public function add_action_links( $links, $file ) {

		$plugin_link = '<a href="'.admin_url( 'options-general.php?page=ia_styles_options' ) . '">' . __( 'Settings' , 'ia-styles' ) . '</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $plugin_link );
		
		return $links;
	}


	/**
	 * Add plugin's options page
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function add_options_page() {
		add_options_page(__( 'IA Stylesheet Options Page',"ia-styles" ), __( 'IA Stylesheet', "ia-styles" ), 'manage_options', 'ia_styles_options', array( $this,'render_form' ) );
	}


	/**
	 * Create the CSS Stylesheet from the options
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function create_stylesheet($old, $new) {
		require dirname(__FILE__) . '/inc/stylesheet.php';
		$rules = create_rules();
		$this->ia_reg_number = $this->get_registered_number();
		file_put_contents( dirname(__FILE__). "/css/" . $this->ia_reg_number . "-fontstyles.css", $rules);
	}


	/**
	 * Get stored options
	 * @access public
	 * @return array
	 * @since  1.0
	 */
	public function get_options() {
		$this->ia_styles_options = get_option( 'ia_styles_options', true );
		return $this->ia_styles_options;
	}

	/**
	 * Set Registered Number
	 * @access public
	 * @return string
	 * @since  1.0
	 */
	public function set_registered_number() {
		$t = time();
		$success = update_option('ia_reg_number', $t);
		if ($success){
			return $t;
		} else{
			return false;
		}
	}

	/**
	 * Get Registered Number
	 * @access public
	 * @return string
	 * @since  1.0
	 */
	public function get_registered_number() {
		$this->ia_reg_number = get_option( 'ia_reg_number');
		if (false === $this->ia_reg_number) {
			$this->ia_reg_number = $this->set_registered_number();
		}
		return $this->ia_reg_number;
	}

	/**
	 * Render the Plugin options form
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function render_form(){
		if (isset($_GET['tab'])) { $tab = $_GET['tab']; } else { $tab = 'h_styles'; }
		?>
		<div class="wrap ia-stylesheet">
	        <h2><?php _e( 'IA Stylesheet', 'ia-styles' ); ?></h2>
	        <?php if ( isset ( $_GET['tab'] ) ) { $this->admin_tabs($_GET['tab']); } else { $this->admin_tabs('h_styles'); } ?>

		        <form class="<?php echo $_GET['tab']; ?>-group" method="post" action="options.php">
		        	<?php settings_fields( 'ia_styles_options' );
				    switch ($tab) {
						case 'h_styles':
							//Header and Paragraph Styles
							echo '<h1>Header and Paragraph (h and p) Styles</h1>';
		                	do_settings_sections( 'ia_styles_options' );
							break;

						case 'nav_styles':
							echo '<h1>Nav and Subnav Styles</h1>';
		                	do_settings_sections( 'ia_styles_options' );
							break;

						case 'a_styles':
							echo '<h1>Link and Button (a) Styles</h1>';
		                	do_settings_sections( 'ia_styles_options' );
							break;

						case 'typeface_code':
		                	do_settings_sections( 'ia_styles_options' );
							break;

						case 'colors':
		                	do_settings_sections( 'ia_styles_options' );
							break;

						case 'globals':
							echo '<h1>Global Styles</h1>';
		                	do_settings_sections( 'ia_styles_options' );
							break;
		    		}
		        	?>
		            <?php
		            submit_button();
		            ?>
		        </form>
	        <?php //} ?>
	    </div> 
		<?php
	}


	/**
	 * Create tabs in the setting page
	 * @access public
	 * @param  string $current
	 * @return array
	 * @since  1.1
	 */
	function admin_tabs( $current = 'h_styles' ) {
	    $tabs = array( 'h_styles' => 'Header and Paragraph Styles', 'nav_styles' => 'Nav Styles', 'a_styles' => 'Link and Button Styles', 'typeface_code' => 'Typeface Code', 'colors' => 'Colors', 'globals' => 'Global Styles');
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=ia_styles_options&tab=$tab'>$name</a>";
	    }
	    echo '</h2>';
	}


	/**
	 * Add Sections to the Plugin options form
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function setup_sections() {
		if (isset($_GET['tab'])) { $tab = $_GET['tab']; } else { $tab = 'h_styles'; }
	    if ($tab) {
    		switch ($tab) {
				case 'h_styles':
		    		foreach ($this->h_fonts as $font) {
		    			add_settings_section( $font, $font.' Styles', array( $this, 'section_callback' ), 'ia_styles_options' );
			    	}
					break;

				case 'nav_styles':
					foreach ($this->nav_fonts as $font) {
		    			add_settings_section( $font, $font.' Styles', array( $this, 'section_callback' ), 'ia_styles_options' );
			    	}
					break;

				case 'a_styles':
					foreach ($this->a_fonts as $font) {
		    			add_settings_section( $font, $font.' Styles', array( $this, 'section_callback' ), 'ia_styles_options' );
			    	}
			    	foreach ($this->button_colors as $font) {
		    			add_settings_section( $font, $font.' Colors', array( $this, 'section_callback' ), 'ia_styles_options' );
			    	}
					break;

				case 'typeface_code':
					add_settings_section( 'typeface', 'Typeface Code', array( $this, 'section_callback' ), 'ia_styles_options' );
					break;

				case 'colors':
					add_settings_section( 'colors', 'Colors', array( $this, 'section_callback' ), 'ia_styles_options' );
					break;

				case 'globals':
					foreach ($this->global_styles as $font) {
						add_settings_section( $font, $font.' Declaration', array( $this, 'section_callback' ), 'ia_styles_options' );
					}
					foreach ($this->global_buttons as $font) {
						add_settings_section( $font, $font.' Declaration', array( $this, 'section_callback' ), 'ia_styles_options' );
					}
					break;
	    	}
	    }
	}

	/**
	 * Add Fields to the Plugin options form
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function setup_fields() {
		$fields = array();
		$ia_styles_options = $this->ia_styles_options;
		foreach ($this->h_fonts as $font) {
			foreach ($this->css_text as $text) {
				$fields[] = array(
		            'uid' => "ia_styles_options[$font][$text]",
		            'label' => $text,
		            'section' => $font,
		            'type' => 'text',
		            'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$text]
			    );
			}

			foreach ($this->css_select as $select => $options) {
				$fields[] = array(
			        'uid' => "ia_styles_options[$font][$select]",
			        'label' => $select,
			        'section' => $font,
			        'type' => 'select',
			        'options' => array(),
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$select]
			    );
			    $count = count($fields)-1;
			    foreach ($options as $option) {
			    	$fields[$count]['options'][$option] = $option;
			    }
			}
			foreach ($this->h_spacing as $select => $options) {
				$fields[] = array(
			        'uid' => "ia_styles_options[$font][$select]",
			        'label' => $select,
			        'section' => $font,
			        'type' => 'select',
			        'options' => array(),
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$select]
			    );
			    $count = count($fields)-1;
			    foreach ($options as $option) {
			    	$fields[$count]['options'][$option] = $option;
			    }
			}
		}
		foreach ($this->nav_fonts as $font) {
			foreach ($this->css_text as $text) {
				$fields[] = array(
		            'uid' => "ia_styles_options[$font][$text]",
		            'label' => $text,
		            'section' => $font,
		            'type' => 'text',
		            'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$text]
			    );
			}

			foreach ($this->css_select as $select => $options) {
				$fields[] = array(
			        'uid' => "ia_styles_options[$font][$select]",
			        'label' => $select,
			        'section' => $font,
			        'type' => 'select',
			        'options' => array(),
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$select]
			    );
			    $count = count($fields)-1;
			    foreach ($options as $option) {
			    	$fields[$count]['options'][$option] = $option;
			    }
			}
		}
		foreach ($this->a_fonts as $font) {
			foreach ($this->css_text as $text) {
				$fields[] = array(
		            'uid' => "ia_styles_options[$font][$text]",
		            'label' => $text,
		            'section' => $font,
		            'type' => 'text',
		            'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$text]
			    );
			}

			foreach ($this->css_select as $select => $options) {
				$fields[] = array(
			        'uid' => "ia_styles_options[$font][$select]",
			        'label' => $select,
			        'section' => $font,
			        'type' => 'select',
			        'options' => array(),
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$select]
			    );
			    $count = count($fields)-1;
			    foreach ($options as $option) {
			    	$fields[$count]['options'][$option] = $option;
			    }
			}
		}
		foreach ($this->button_colors as $font) {
			foreach ($this->css_buttons as $text) {
				$fields[] = array(
		            'uid' => "ia_styles_options[$font][$text]",
		            'label' => $text,
		            'section' => $font,
		            'type' => 'text',
		            'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $ia_styles_options[$font][$text]
			    );
			}
		}
		$fields[] = array(
	        'uid' => 'ia_styles_options[typeface_code]',
	        'label' => 'Typeface Code',
	        'section' => 'typeface',
	        'type' => 'textarea',
	        'options' => false,
	        'placeholder' => false,
	        'helper' => false,
	        'supplemental' => false,
	        'default' => $ia_styles_options['typeface_code']
	    );
	    //if ($this->ia_styles_options[colors]) {
	 //    	$count = 0;
	 //    	foreach ($this->options[colors] as $color) {
	 //    		$fields[] = array(
		// 	        'uid' => "ia_styles_options[colors][$count][color_name]",
		// 	        'label' => 'Color Name',
		// 	        'section' => 'colors',
		// 	        'type' => 'text',
		// 	        'options' => false,
		//             'placeholder' => false,
		//             'helper' => false,
		//             'supplemental' => false,
		//             'default' => $color['color_name']
		// 	    );
		// 	    $fields[] = array(
		// 	        'uid' => "ia_styles_options[colors][$count][color_hexcode]",
		// 	        'label' => 'Color Hexcode/RGBA',
		// 	        'section' => 'colors',
		// 	        'type' => 'text',
		// 	        'options' => false,
		//             'placeholder' => false,
		//             'helper' => false,
		//             'supplemental' => 'hexcode or rgba',
		//             'default' => $color['color_hexcode']
		// 	    );
		// 	    $count++;
	 //    	}
		// } else {
			for($i=0; $i<12; $i++){
				$fields[] = array(
			        'uid' => 'ia_styles_options[colors]['.$i.'][color_name]',
			        'label' => 'Color Name',
			        'section' => 'colors',
			        'type' => 'text',
			        'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => false,
		            'default' => $this->options['colors'][$i]['color_name']
			    );
			    $fields[] = array(
			        'uid' => 'ia_styles_options[colors]['.$i.'][color_hexcode]',
			        'label' => 'Color Hexcode/RGBA',
			        'section' => 'colors',
			        'type' => 'text',
			        'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => 'hexcode or rgba',
		            'default' => $this->options['colors'][$i]['color_hexcode']
			    );
			 }
		//}
		//var_dump($fields);
		// foreach( $fields as $field ){
	 //        add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'ia_styles_options', $field['section'], $field );
	 //        register_setting( 'ia_styles_options', $field['uid'] );
	 //    }

		foreach ($this->global_styles as $font) {
			foreach ($this->css_globals as $text) {
				$fields[] = array(
		            'uid' => "ia_styles_options[$font][$text]",
		            'label' => $text,
		            'section' => $font,
		            'type' => 'text',
		            'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => 'Value in pixels',
		            'default' => $ia_styles_options[$font][$text]
			    );
			}
		}
		foreach ($this->global_buttons as $font) {
			foreach ($this->css_global_button as $text) {
				$fields[] = array(
		            'uid' => "ia_styles_options[$font][$text]",
		            'label' => $text,
		            'section' => $font,
		            'type' => 'text',
		            'options' => false,
		            'placeholder' => false,
		            'helper' => false,
		            'supplemental' => 'Value in pixels',
		            'default' => $ia_styles_options[$font][$text]
			    );
			}
		}
		//Go through each $field and add it to the section.
		foreach( $fields as $field ){
	        add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'ia_styles_options', $field['section'], $field );
	    }
	    register_setting( 'ia_styles_options', 'ia_styles_options', array( $this,'validate_options' ) );
	}

	/**
	 * Callback to display fields on the Plugin options form
	 * @access public
	 * @return void
	 * @since  1.1
	 */
	public function section_callback( $arguments ) {
		if ($arguments['id'] === 'a'){
			echo '<p>a is the Inline Link type. It is for clickable text that is part of a paragraph or sentence.</p>';
		}
		if ($arguments['id'] === 'a2'){
			echo '<p>a2 is the Text Link type. It is for clickable (call-out) text that wouldn\'t be part of a paragraph or sentence, and isn\'t a button.</p>';
		}
		if ($arguments['id'] === 'a3'){
			echo '<p>a3 is the Button Link type. It is for clickable (call-out) text that looks like a button.</p>';
		}
		if ($arguments['id'] === 'colors'){
			//echo '<p><a class="add-new-color">Add New Color</a></p>';
			echo '<p>Enter all colors to be used throughout the website. There is no need to enter Black (#000000) or White (#FFFFFF); those are included in the stylesheet.</p>';
		}
		if ($arguments['id'] === 'button1'){
			echo '<p>Enter the color name and hexcode or RGBA for up to 3 button variations.</p>';
			echo '<p>To limit the number of options in the modules, the button background and text colors, as well as the button background and text color on hover, will be grouped.</p>';
			echo '<p>When adding button color options in the module fields, specify the name of the background color to access the group of text and hover colors stored here.</p>';
		}
		if ($arguments['id'] === 'article-padding'){
			echo '<p>This sets the padding on the top and bottom of the modules within the grid. Enter the value in pixels (ie: 10px).</p>';
		}
		if ($arguments['id'] === 'margin-title-bottom-margin'){
			echo '<p>This sets the margin below on the module title in modules that use this global style. Enter the value in pixels (ie: 10px).</p>';
		}
		if ($arguments['id'] === 'column-gutter'){
			echo '<p>This sets the padding between columns in modules that use bootstrap or the class name "gutter". This needs to be an even number.</p>';
			echo '<p>This will add the same amount as margin below each column. This adds the same space below each item as is between each item.</p>';
			echo '<p>Enter the total amount for the space needed between columns and enter the value in pixels (ie: 10px).</p>';
		}
		if ($arguments['id'] === 'extra-small-margin'){
			echo '<p>This sets the margin size for the extra-small-margin-top, extra-small-margin-bottom, extra-small-padding-top, extra-small-padding-bottom options in modules that use this option field.</p><p>This field may also be used in Article Padding, and assigned to Header Styles.</p><p> Enter the value in pixels (ie: 10px).</p>';
		}
		if ($arguments['id'] === 'small-margin'){
			echo '<p>This sets the margin size for the small-margin-top, small-margin-bottom, small-padding-top, small-padding-bottom options in modules that use this option field.</p><p>This field may also be used in Article Padding, and assigned to Header Styles.</p><p> Enter the value in pixels (ie: 10px).</p>';
		}
		if ($arguments['id'] === 'medium-margin'){
			echo '<p>This sets the margin size for the medium-margin-top, medium-margin-bottom, medium-padding-top, medium-padding-bottom options in modules that use this option field.</p><p>This field may also be used in Article Padding, and assigned to Header Styles.</p><p> Enter the value in pixels (ie: 10px).</p>';
		}
		if ($arguments['id'] === 'large-margin'){
			echo '<p>This sets the margin size for the large-margin-top, large-margin-bottom, large-padding-top, large-padding-bottom options in modules that use this option field.</p><p>This field may also be used in Article Padding, and assigned to Header Styles.</p><p> Enter the value in pixels (ie: 10px).</p>';
		}

		if ($arguments['id'] === 'extra-large-margin'){
			echo '<p>This sets the margin size for the extra-large-margin-top, extra-large-margin-bottom, extra-large-padding-top, extra-large-padding-bottom options in modules that use this option field.</p><p>This field may also be used in Article Padding, and assigned to Header Styles.</p><p> Enter the value in pixels (ie: 10px).</p>';
		}
	}

	/**
	 * Callback to display font style fields on the Plugin options form
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function field_callback( $arguments ) {
	    // Check which type of field we want
	    switch( $arguments['type'] ){
	        case 'text': 
	            printf( '<input name="%1$s" id="%1$s" type="%2$s" value="%3$s" />', $arguments['uid'], $arguments['type'], $arguments['default'] );
	            break;
	        case 'select': // If it is a select dropdown
		        if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
		            $options_markup = '';
		            foreach( $arguments['options'] as $key => $label ){
		                $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $arguments['default'], $key, false ), $label );
		            }
		            printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
		        }
		        break;
		    case 'textarea': // If it is a textarea
		        printf( '<textarea name="%1$s" id="%1$s" rows="5" cols="50">%2$s</textarea>', $arguments['uid'], $arguments['default'] );
		        break;
	    }
	}
	

	/**
	 * Sanitize and validate options
	 * @access public
	 * @param  array $input
	 * @return array
	 * @since  1.1
	 */
	public function validate_options( $input ){
		
		$options = get_option('ia_styles_options');
		
		if (is_array($input) && !empty($input)) {
			foreach ($input as $value) {
				foreach ($this->h_fonts as $font) {
					foreach ($this->css_text as $text) {
						if ($input[$font][$text]) {
							$options[$font][$text] = str_replace('"', "'", strip_tags($input[$font][$text]));
						}
					}
					foreach ($this->css_select as $select => $value) {
						if ($input[$font][$select]) {
							$options[$font][$select] = $input[$font][$select];
						}
					}
					foreach ($this->h_spacing as $select => $value) {
						if ($input[$font][$select]) {
							$options[$font][$select] = $input[$font][$select];
						}
					}
				}
				foreach ($this->nav_fonts as $font) {
					foreach ($this->css_text as $text) {
						if ($input[$font][$text]) {
							$options[$font][$text] = str_replace('"', "'", strip_tags($input[$font][$text]));
						}
					}
					foreach ($this->css_select as $select => $value) {
						if ($input[$font][$select]) {
							$options[$font][$select] = $input[$font][$select];
						}
					}
				}
				foreach ($this->a_fonts as $font) {
					foreach ($this->css_text as $text) {
						if ($input[$font][$text]) {
							$options[$font][$text] = str_replace('"', "'", strip_tags($input[$font][$text]));
						}
					}
					foreach ($this->css_select as $select => $value) {
						if ($input[$font][$select]) {
							$options[$font][$select] = $input[$font][$select];
						}
					}
				}
				foreach ($this->button_colors as $font) {
					foreach ($this->css_buttons as $text) {
						if ($text === 'button-background-name' && $input[$font][$text]) {
							$color = htmlspecialchars(strip_tags($input[$font][$text]), ENT_COMPAT);
							$color = strtolower($color);
							$color = str_replace(' ', '-', $color);
							$options[$font][$text] = $color;
						} elseif ($input[$font][$text]) {
							$options[$font][$text] = str_replace('"', "'", strip_tags($input[$font][$text]));
						} 
					}
				}
				if ($input['typeface_code']) {
					$options['typeface_code'] = $input['typeface_code'];
				}
				if (is_array($input['colors']) && !empty($input['colors'])) {
					$count = 0;
					foreach ($input['colors'] as $color) {
						$color = htmlspecialchars(strip_tags($input['colors'][$count]['color_name']), ENT_COMPAT);
						$color = strtolower($color);
						$color = str_replace(' ', '-', $color);
						$options['colors'][$count]['color_name'] = $color;
						//$options['colors'][$count]['color_name'] = $input['colors'][$count]['color_name'];
						$options['colors'][$count]['color_hexcode'] = htmlspecialchars(strip_tags($input['colors'][$count]['color_hexcode']), ENT_COMPAT);
						//$options['colors'][$count]['color_hexcode'] = $input['colors'][$count]['color_hexcode'];

						$count++;
					}
				}
				foreach ($this->global_styles as $font) {
					foreach ($this->css_globals as $text) {
						if ($input[$font][$text]) {
							$options[$font][$text] = str_replace('"', "'", strip_tags($input[$font][$text]));
						}
					}
				}
				foreach ($this->global_buttons as $font) {
					foreach ($this->css_global_button as $text) {
						if ($input[$font][$text]) {
							$options[$font][$text] = str_replace('"', "'", strip_tags($input[$font][$text]));
						}
					}
				}
			}
		}

		return $options;
	}
}//end of class
endif;

/**
 * Add ajax for the add color button
 * @access public
 * @return void
 * @since  unused 1.0
 */
// function iass_ajax_function() {
// 	$num = $_POST['num'];
// 	$plugin = IA_Styles();
// 	return $plugin->ia_ajax_function($num);
	
// 	wp_die(); 
// }

//add ajax function for add colors button on form
//add_action( 'wp_ajax_ajax_function', 'iass_ajax_function' );

/**
 * Enqueue New Stylesheet for Theme
 * @access public
 * @return void
 * @since  1.0
 */
function front_script(){
	$num = get_option( 'ia_reg_number', true );
	wp_enqueue_style('ia-stylesheet', plugin_dir_url( __FILE__ ) . 'css/' . $num . '-fontstyles.css' );
}
// Load front pages scripts
add_filter( 'wp_enqueue_scripts', 'front_script', 30 );

/**
 * Enqueue Font Source for Theme
 * @access public
 * @return void
 * @since  1.0
 */
function ia_font_script(){
	$options = get_option( 'ia_styles_options', true );
	if ($options['typeface_code']) {
		echo $options['typeface_code'];
	}
}
// Load front pages scripts
add_action( 'wp_head', 'ia_font_script' );

/**
 * Launch the whole plugin
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  1.0
 * @return IA_Styles
 */
function IA_Styles() {
	return IA_Styles::instance();
}
if( is_admin() ) {
	IA_Styles();
}