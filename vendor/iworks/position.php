<?php

class iworks_position
{
	private $base;
	private $capability;
	private $options;
	private $root;
	private $version = 'trunk';
	private $min = '.min';

	public function __construct() {

		/**
		 * static settings
		 */
		$this->base = dirname( dirname( __FILE__ ) );
		$this->root = plugins_url( '', (dirname( dirname( __FILE__ ) )) );
		$this->capability = apply_filters( 'iworks_reading_position_indicator_capability', 'manage_options' );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->min = '';
		}
		/**
		 * options
		 */
		$this->options = get_iworks_reading_position_indicator_options();

		/**
		 * generate
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
	}

	public function wp_head() {

		if ( ! is_singular() ) {
			return;
		}
		$data = $this->options->get_all_options();
		if ( ! isset( $data['style'] ) ) {
			return;
		}
?>
<style type="text/css" media="handheld, projection, screen">
<?php
if ( isset( $data['height'] ) ) {
?>
#reading-position-indicator{height:<?php echo $data['height']; ?>px}
<?php
}
switch ( $data['style'] ) {
	case 'solid':
		if ( isset( $data['color1'] ) ) {
	?>
	#reading-position-indicator { color: <?php echo $data['color1']; ?>; }
#reading-position-indicator::-webkit-progress-value { background-color: <?php echo $data['color1']; ?>; }
#reading-position-indicator::-moz-progress-bar { background-color: <?php echo $data['color1']; ?>; }
.progress-bar { background-color: <?php echo $data['color1']; ?>; }
<?php
		}
	break;
	case 'transparent':
	?>
	#reading-position-indicator.single::-webkit-progress-value { background-image: -webkit-linear-gradient(left, transparent, <?php echo $data['color1']; ?>); }
#reading-position-indicator.single::-moz-progress-bar { background-image: -moz-linear-gradient(left, transparent, <?php echo $data['color1']; ?>); }
<?php
	break;
	case 'gradient':
	?>
	#reading-position-indicator.multiple::-webkit-progress-value { background-image: -webkit-linear-gradient( -45deg, transparent 33%, rgba(0, 0, 0, .1) 33%, rgba(0,0, 0, .1) 66%, transparent 66%), -webkit-linear-gradient(left, <?php echo $data['color2']; ?>, <?php echo $data['color1']; ?>); }
#reading-position-indicator.multiple::-moz-progress-bar { background-image: -moz-linear-gradient(-45deg, transparent 33%, rgba(0, 0, 0, .1) 33%, rgba(0,0, 0, .1) 66%, transparent 66%), -moz-linear-gradient(left, <?php echo $data['color2']; ?>, <?php echo $data['color1']; ?>); }
<?php
	break;
}
?>
</style>
<?php
	}

	public function admin_init() {

		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		/**
		 * options
		 */
		$this->options->options_init();
	}

	public function admin_enqueue_scripts() {

		$screen = get_current_screen();
		if ( 'appearance_page_irpi_index' != $screen->base ) {
			return;
		}
		$file = sprintf( '/assets/scripts/%s.admin%s.js', __CLASS__, $this->min );
		wp_enqueue_script( __CLASS__, plugins_url( $file, $this->base ), array( 'jquery' ), $this->get_version( $file ) );
		wp_enqueue_script( __CLASS__ );
	}

	public function wp_enqueue_scripts() {

		if ( ! is_singular() ) {
			return;
		}

		$file = sprintf( '/assets/styles/%s%s.css', __CLASS__, $this->min );
		wp_register_style(
			__CLASS__,
			plugins_url( $file, $this->base ),
			array(),
			$this->get_version(),
			'handheld, projection, screen'
		);
		wp_enqueue_style( __CLASS__ );

		$file = sprintf( '/assets/scripts/%s%s.js', __CLASS__, $this->min );
		wp_register_script(
			__CLASS__,
			plugins_url( $file, $this->base ),
			array( 'jquery' ),
			$this->version,
			true
		);
		wp_localize_script( __CLASS__, __CLASS__, $this->options->get_all_options() );
		wp_enqueue_script( __CLASS__ );
	}

	private function get_version( $file = null ) {

		if ( defined( 'IWORKS_DEV_MODE' ) && IWORKS_DEV_MODE ) {
			if ( null != $file ) {
				$file = dirname( $this->base ) . $file;
				if ( is_file( $file ) ) {
					return md5_file( $file );
				}
			}
			return rand( 0, 99999 );
		}
		return $this->version;
	}

	public function plugin_row_meta( $links, $file ) {

		if ( ! preg_match( '/reading-position-indicator.php$/', $file ) ) {
			return $links;
		}
		if ( ! is_multisite() && current_user_can( $this->capability ) ) {
			$links[] = sprintf(
				'<a href="%s">%s</a>',
				add_query_arg( 'page', 'irpi_index', admin_url( 'themes.php' ) ),
				__( 'Settings' )
			);
		}
		$links[] = sprintf(
			'<a href="http://iworks.pl/donate/reading-position-indicator.php">%s</a>',
			__( 'Donate' )
		);
		return $links;
	}
}
