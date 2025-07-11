<?php
/*
Copyright 2015-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 3, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

class iworks_position {
	private $base;
	private $capability;
	private $options;
	private $root;
	private $version = 'PLUGIN_VERSION';
	private $min     = '.min';
	private $check   = false;
	private $data    = null;

	/**
	 * plugin file
	 *
	 * @since 1.0.9
	 */
	private $plugin_file;

	public function __construct() {
		/**
		 * static settings
		 */
		$this->base       = dirname( __DIR__, 1 );
		$this->root       = plugins_url( '', ( dirname( __DIR__, 1 ) ) );
		$this->capability = apply_filters( 'iworks_reading_position_indicator_capability', 'manage_options' );
		/**
		 * plugin ID
		 *
		 * @since 3.3.2
		 */
		$file              = dirname( __DIR__, 2 ) . '/reading-position-indicator.php';
		$this->plugin_file = plugin_basename( $file );
		/**
		 * is debug?
		 */
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->min = '';
		}
		/**
		 * generate
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'action_init_load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'action_init_option_object' ), 11 );
		add_action( 'iworks_rate_css', array( $this, 'iworks_rate_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_head', array( $this, 'set_check_value' ), 0 );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_filter( 'the_content', array( $this, 'the_content' ) );
		/**
		 * iWorks Rate Class
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
		add_filter( 'iworks_rate_settings_page_url_' . 'reading-position-indicator', array( $this, 'filter_get_setting_page_url' ) );
	}

	/**
	 * Initialize iWorks Option object
	 */
	public function action_init_option_object() {
		/**
		 * options
		 */
		$this->check_option_object();
	}

	public function wp_head() {
		if ( ! $this->check ) {
			return;
		}
		$this->check_option_object();
		$data = $this->get_data();
		if ( ! isset( $data['style'] ) ) {
			return;
		}
		$color1 = $data['color1'];
		if ( isset( $data['color1_opacity'] ) && 100 != $data['color1_opacity'] ) {
			$color1   = $this->options->hex2rgb( $color1 );
			$color1[] = $data['color1_opacity'] / 100;
			$color1   = sprintf( 'rgba(%s)', esc_attr( implode( ',', $color1 ) ) );
		}
		$color2 = $data['color2'];
		if ( isset( $data['color2_opacity'] ) && 100 != $data['color2_opacity'] ) {
			$color2   = $this->options->hex2rgb( $color2 );
			$color2[] = $data['color2_opacity'] / 100;
			$color2   = sprintf( 'rgba(%s)', esc_attr( implode( ',', $color2 ) ) );
		}
		$background = $data['background'];
		if ( isset( $data['background_opacity'] ) && 100 != $data['background_opacity'] ) {
			$background   = $this->options->hex2rgb( $background );
			$background[] = $data['background_opacity'] / 100;
			$background   = sprintf( 'rgba(%s)', esc_attr( implode( ',', $background ) ) );
		}
		echo '<style type="text/css" media="handheld, projection, screen">';
		if ( isset( $data['radius'] ) && 0 < $data['radius'] ) {
			$style = sprintf( 'border-radius: %dpx;', esc_attr( $data['radius'] ) );
			printf( '#reading-position-indicator::-moz-progress-bar { %s }', esc_attr( $style ) );
			printf( '#reading-position-indicator::-webkit-progress-value { %s }', esc_attr( $style ) );
			printf( '#reading-position-indicator[role] { %s }', esc_attr( $style ) );
		}
		echo 'body #reading-position-indicator,';
		echo 'body.admin-bar #reading-position-indicator {';
		/**
		 * position
		 */
		if ( isset( $data['position'] ) ) {
			switch ( $data['position'] ) {
				case 'bottom':
					echo 'bottom: 0;';
					echo 'top: inherit;';
					break;
			}
		}
		/**
		 * height
		 */
		$height = 10;
		if ( isset( $data['height'] ) ) {
			$height = $data['height'];
		}
		printf( 'height: %spx;', esc_attr( $height ) );
		printf( 'background: %s;', esc_attr( $background ) );
		echo '}';

		?>
#reading-position-indicator::-webkit-progress-bar{background-color: <?php echo esc_attr( $background ); ?>}
		<?php
		switch ( $data['style'] ) {
			case 'solid':
				if ( isset( $data['color1'] ) ) {
					?>
#reading-position-indicator {
color: <?php echo esc_attr( $color1 ); ?>;
background: <?php echo esc_attr( $background ); ?>;
}
#reading-position-indicator::-webkit-progress-value {
background-color: <?php echo esc_attr( $color1 ); ?>;
}
#reading-position-indicator::-moz-progress-bar {
background-color: <?php echo esc_attr( $color1 ); ?>;
}
#reading-position-indicator::[aria-valuenow]:before {
background-color: <?php echo esc_attr( $color1 ); ?>;
}
.progress-bar  {
background-color: <?php echo esc_attr( $color1 ); ?>;
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
-webkit-linear-gradient(right, <?php echo esc_attr( $color1 ); ?>, <?php echo esc_attr( $color2 ); ?>);
background-size: <?php echo esc_attr( $height * 2 ); ?>px <?php echo esc_attr( $height ); ?>px, 100% 100%, 100% 100%;
}
				<?php

			case 'transparent':
			case 'gradient':
				if ( 'transparent' == $data['style'] ) {
					$color2 = 'transparent';
				}
				?>
#reading-position-indicator::-webkit-progress-value {
background: linear-gradient(to right, <?php echo esc_attr( $color2 ); ?>, <?php echo esc_attr( $color1 ); ?>);
}
#reading-position-indicator::-moz-progress-bar {
background: linear-gradient(to right, <?php echo esc_attr( $color2 ); ?>, <?php echo esc_attr( $color1 ); ?>);
}
#reading-position-indicator[role][aria-valuenow] {
background: linear-gradient(to right, <?php echo esc_attr( $color2 ); ?>, <?php echo esc_attr( $color1 ); ?>) !important;
}
				<?php
				break;
		}
		?>
</style>
		<?php
	}

	public function admin_init() {
		$this->check_option_object();
	}

	public function wp_enqueue_scripts() {
		if ( ! $this->check ) {
			return;
		}
		$this->check_option_object();
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
			array(),
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
			return time();
		}
		return $this->version;
	}

	/**
	 * get settings page url
	 *
	 * @since 1.0.5
	 */
	private function get_setting_page_url() {
		return add_query_arg( 'page', 'irpi_index', admin_url( 'themes.php' ) );
	}

	/**
	 * Add marker to content.
	 *
	 * @since 1.0.2
	 */
	public function the_content( $content ) {
		if ( $this->check ) {
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
		$logo = plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/images/icon.svg';
		echo '<style type="text/css">';
		printf( '.iworks-notice-reading-position-indicator .iworks-notice-logo{background-image:url(%s);}', esc_url( $logo ) );
		echo '</style>';
	}

	/**
	 * Set check value.
	 *
	 * @since 1.0.2
	 */
	public function set_check_value() {
		if ( ! is_singular() ) {
			return;
		}
		$data = $this->get_data();
		if ( ! isset( $data['post_type'] ) ) {
			return;
		}
		if ( empty( $data['post_type'] ) ) {
			return;
		}
		$post_type   = get_post_type();
		$this->check = in_array( $post_type, $data['post_type'] );
	}

	/**
	 * Get data from DB.
	 *
	 * @since 1.0.2
	 */
	private function get_data() {
		$this->check_option_object();
		if ( null === $this->data ) {
			$this->data = $this->options->get_all_options();
		}
		return $this->data;
	}

	/**
	 * Plugin logo for rate messages
	 *
	 * @since 1.0.5
	 *
	 * @param string $logo Logo, can be empty.
	 * @param object $plugin Plugin basic data.
	 */
	public function filter_plugin_logo( $logo, $plugin ) {
		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}
		if ( 'reading-position-indicator' === $plugin['slug'] ) {
			return plugin_dir_url( dirname( __DIR__, 1 ) ) . '/assets/images/icon.svg';
		}
		return $logo;
	}

	public function filter_get_setting_page_url( $url ) {
		return $this->get_setting_page_url();
	}

	/**
	 * register plugin to iWorks Rate Helper
	 *
	 * @since 1.0.9
	 */
	public function action_init_register_iworks_rate() {
		if ( ! class_exists( 'iworks_rate' ) ) {
			include_once __DIR__ . '/rate/rate.php';
		}
		do_action(
			'iworks-register-plugin',
			plugin_basename( $this->plugin_file ),
			__( 'Reading Position Indicator ', 'reading-position-indicator' ),
			'reading-position-indicator'
		);
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 1.0.9
	 */
	public function action_init_load_plugin_textdomain() {
		load_plugin_textdomain(
			'reading-position-indicator',
			false,
			plugin_basename( $this->root ) . '/languages'
		);
	}

	/**
	 * check option object
	 *
	 * @since 1.0.9
	 */
	private function check_option_object() {
		if ( is_a( $this->options, 'iworks_options' ) ) {
			return;
		}
		$this->options = iworks_reading_position_indicator_get_options_object();
	}
}
