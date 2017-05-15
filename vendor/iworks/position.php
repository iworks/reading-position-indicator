<?php

class iworks_position
{
	private $base;
	private $capability;
	private $options;
	private $root;
	private $version = 'PLUGIN_VERSION';
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
		add_filter( 'the_content', array( $this, 'the_content' ) );
		add_action( 'iworks_rate_css', array( $this, 'iworks_rate_css' ) );
	}

	public function wp_head() {
		if ( ! is_singular() ) {
			return;
		}
		$data = $this->options->get_all_options();
		if ( ! isset( $data['style'] ) ) {
			return;
        }
        $color1 = $data['color1'];
        if ( isset( $data['color1_opacity'] ) && 100 != $data['color1_opacity'] ) {
            $color1 = $this->options->hex2rgb( $color1 );
            $color1[] = $data['color1_opacity'] / 100;
            $color1 = sprintf( 'rgba(%s)', implode( ',', $color1 ) );
        }
        $color2 = $data['color2'];
        if ( isset( $data['color2_opacity'] ) && 100 != $data['color2_opacity'] ) {
            $color2 = $this->options->hex2rgb( $color2 );
            $color2[] = $data['color2_opacity'] / 100;
            $color2 = sprintf( 'rgba(%s)', implode( ',', $color2 ) );
        }
        $background = $data['background'];
        if ( isset( $data['background_opacity'] ) && 100 != $data['background_opacity']  ) {
            $background = $this->options->hex2rgb( $background );
            $background[] = $data['background_opacity'] / 100;
            $background = sprintf( 'rgba(%s)', implode( ',', $background ) );
        }
?>
<style type="text/css" media="handheld, projection, screen">
<?php
if ( isset( $data['radius'] ) && 0 < $data['radius'] ) {
?>
#reading-position-indicator::-moz-progress-bar {
    border-radius: <?php echo $data['radius']; ?>px;
}
#reading-position-indicator::-webkit-progress-value {
    border-radius: <?php echo $data['radius']; ?>px;
}
#reading-position-indicator[role] {
    border-radius: <?php echo $data['radius']; ?>px;
}
<?php
}
?>
<?php
if ( isset( $data['height'] ) ) {
?>
#reading-position-indicator{height:<?php echo $data['height']; ?>px}
<?php
}
?>
#reading-position-indicator{ background: <?php echo $background; ?>}
#reading-position-indicator::-webkit-progress-bar{background-color: <?php echo $background; ?>}
<?php
switch ( $data['style'] ) {
	case 'solid':
        if ( isset( $data['color1'] ) ) {
	?>
#reading-position-indicator {
    color: <?php echo $color1; ?>;
    background: <?php echo $background; ?>;
}
#reading-position-indicator::-webkit-progress-value {
    background-color: <?php echo $color1; ?>;
}
#reading-position-indicator::-moz-progress-bar {
    background-color: <?php echo $color1; ?>;
}
#reading-position-indicator::[aria-valuenow]:before {
    background-color: <?php echo $color1; ?>;
}
.progress-bar  {
    background-color: <?php echo $color1; ?>; ;
}
<?php
		}
        break;
    case 'indeter':
?>
#reading-position-indicator[value]::-webkit-progress-value {
		background-image:
		-webkit-linear-gradient(-45deg, transparent 33%, rgba(0, 0, 0, .1) 33%, rgba(0,0, 0, .1) 66%, transparent 66%),
		-webkit-linear-gradient(top, rgba(255, 255, 255, .25), rgba(0, 0, 0, .25)),
		-webkit-linear-gradient(right, <?php echo $color1; ?>, <?php echo $color2; ?>);
		background-size: 35px 20px, 100% 100%, 100% 100%;
	}
<?php

	case 'transparent':
    case 'gradient':
        if ( 'transparent' == $data['style'] ) {
            $color2 = 'transparent';
        }
?>
#reading-position-indicator::-webkit-progress-value {
    background: linear-gradient(to right, <?php echo $color2; ?>, <?php echo $color1; ?>);
}
#reading-position-indicator::-moz-progress-bar {
    background: linear-gradient(to right, <?php echo $color2; ?>, <?php echo $color1; ?>);
}
#reading-position-indicator[role][aria-valuenow] {
    background: linear-gradient(to right, <?php echo $color2; ?>, <?php echo $color1; ?>) !important;
}
<?php
/*
    background: -webkit-linear-gradient(left, <?php echo $color2; ?>, <?php echo $color1; ?>); /* For Safari 5.1 to 6.0 * 
    background: -o-linear-gradient(right, <?php echo $color2; ?>, <?php echo $color1; ?>); /* For Opera 11.1 to 12.0 * 
    background: -moz-linear-gradient(right, <?php echo $color2; ?>, <?php echo $color1; ?>); /* For Firefox 3.6 to 15 * 
*/
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
        /**
         * select2
         */
        $file = 'assets/external/select2/select2.min.js';
        wp_enqueue_script( 'select2', plugins_url( $file, $this->base ), array( 'jquery' ), '4.0.3' );
        $file = 'assets/external/select2/select2.min.css';
        wp_enqueue_style( 'select2', plugins_url( $file, $this->base ), array(), '4.0.3' );
        /**
         * jQuery UI Slider
         */
        wp_enqueue_script( 'jquery-ui-slider' );
        $file = 'assets/external/jquery-ui/jquery-ui-slider.min.css';
        wp_enqueue_style( 'jquery-ui-slider', plugins_url( $file, $this->base ), array(), '1.12.1' );
        /**
         * plugin file
         */
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

	/**
	 * Add marker to content.
	 *
	 * @since 1.0.2
	 */
	public function the_content( $content ) {
		if ( is_singular() ) {
			$content .= '<div class="reading-position-indicator-end"></div>';
		}
		return $content;
	}
	/**
	 * Change image for rate message.
	 *
	 * @since 1.0.2
	 */
	public function iworks_rate_css() {
		$logo = plugin_dir_url( dirname( dirname( __FILE__ ) ) ).'assets/images/icon.svg';
		echo '<style type="text/css">';
		printf( '.iworks-notice-reading-position-indicator .iworks-notice-logo{background-image:url(%s);}', esc_url( $logo ) );
		echo '</style>';
	}
}
