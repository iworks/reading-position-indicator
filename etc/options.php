<?php

function iworks_reading_position_indicator_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'use_tabs'        => false,
		'version'         => '0.0',
		'page_title'      => esc_html__( 'Progress configuration', 'reading-position-indicator' ),
		'menu_title'      => esc_html__( 'Progress', 'reading-position-indicator' ),
		'menu'            => 'theme',
		'enqueue_scripts' => array(
			'progress-admin-js',
		),
		'enqueue_styles'  => array(
			'progress-admin',
			'reading-position-indicator',
		),
		'options'         => array(
			array(
				'name'     => 'post_type',
				'type'     => 'select2',
				'th'       => esc_html__( 'Display On', 'reading-position-indicator' ),
				'default'  => array( 'post' ),
				'options'  => iworks_reading_position_indicator_post_types(),
				'multiple' => true,
			),
			array(
				'name'              => 'position',
				'type'              => 'radio',
				'th'                => esc_html__( 'Position', 'reading-position-indicator' ),
				'default'           => 'top',
				'radio'             => array(
					'top'    => array( 'label' => esc_html__( 'top', 'reading-position-indicator' ) ),
					'bottom' => array( 'label' => esc_html__( 'bottom', 'reading-position-indicator' ) ),
				),
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'position_placement',
				'type'              => 'number',
				'class'             => 'small-text slider',
				'th'                => esc_html__( 'Position placement', 'reading-position-indicator' ),
				'label'             => esc_html__( 'px', 'reading-position-indicator' ),
				'default'           => 0,
				'min'               => 0,
				'sanitize_callback' => 'absint',
				'description'       => esc_html__( 'Direction depend on position.', 'reading-position-indicator' ),
			),
			array(
				'name'              => 'style',
				'type'              => 'radio',
				'th'                => esc_html__( 'Color style', 'reading-position-indicator' ),
				'default'           => 'solid',
				'radio'             => array(
					'solid'    => array( 'label' => esc_html__( 'solid', 'reading-position-indicator' ) ),
					'gradient' => array( 'label' => esc_html__( 'gradient', 'reading-position-indicator' ) ),
					'indeter'  => array( 'label' => esc_html__( 'indeterminate', 'reading-position-indicator' ) ),
				),
				'sanitize_callback' => 'esc_html',
			),
			array(
				'name'              => 'height',
				'type'              => 'number',
				'th'                => esc_html__( 'Thickness', 'reading-position-indicator' ),
				'label'             => esc_html__( 'px', 'reading-position-indicator' ),
				'default'           => 5,
				'min'               => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'small-text', 'slider' ),
			),
			array(
				'name'              => 'color1',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => esc_html__( 'Foreground color', 'reading-position-indicator' ),
				'sanitize_callback' => 'esc_html',
				'default'           => '#f20',
				'use_name_as_id'    => true,
				'description'       => esc_html__( 'The part that moves on scroll.', 'reading-position-indicator' ),
			),
			array(
				'name'              => 'color1_opacity',
				'type'              => 'number',
				'class'             => 'small-text slider',
				'th'                => esc_html__( 'Foreground opacity', 'reading-position-indicator' ),
				'label'             => esc_html__( '%', 'reading-position-indicator' ),
				'min'               => 0,
				'max'               => 100,
				'default'           => 100,
				'sanitize_callback' => 'absint',
			),
			array(
				'name'              => 'color2',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => esc_html__( 'Secoundary color', 'reading-position-indicator' ),
				'default'           => '#d93',
				'sanitize_callback' => 'esc_html',
				'use_name_as_id'    => true,
			),
			array(
				'name'              => 'color2_opacity',
				'type'              => 'number',
				'class'             => 'small-text slider',
				'th'                => esc_html__( 'Secoundary color opacity', 'reading-position-indicator' ),
				'label'             => esc_html__( '%', 'reading-position-indicator' ),
				'min'               => 0,
				'max'               => 100,
				'default'           => 100,
				'sanitize_callback' => 'absint',
			),
			array(
				'name'              => 'background',
				'type'              => 'wpColorPicker',
				'class'             => 'short-text',
				'th'                => esc_html__( 'Background color', 'reading-position-indicator' ),
				'sanitize_callback' => 'esc_html',
				'default'           => '#ddd',
				'use_name_as_id'    => true,
				'description'       => esc_html__( 'The part that moves on scroll.', 'reading-position-indicator' ),
			),
			array(
				'name'              => 'background_opacity',
				'type'              => 'number',
				'class'             => 'small-text slider',
				'th'                => esc_html__( 'Background opacity', 'reading-position-indicator' ),
				'label'             => esc_html__( '%', 'reading-position-indicator' ),
				'min'               => 0,
				'max'               => 100,
				'default'           => 5,
				'sanitize_callback' => 'absint',
			),
			array(
				'name'              => 'radius',
				'type'              => 'number',
				'class'             => 'small-text slider',
				'th'                => esc_html__( 'Progress radius', 'reading-position-indicator' ),
				'label'             => esc_html__( 'px', 'reading-position-indicator' ),
				'min'               => 0,
				'default'           => 0,
				'sanitize_callback' => 'absint',
			),
		),
		'metaboxes'       => array(
			'assistance' => array(
				'title'    => esc_html__( 'We are waiting for your message', 'reading-position-indicator' ),
				'callback' => 'iworks_reading_position_indicator_options_need_assistance',
				'context'  => 'side',
				'priority' => 'core',
			),
			'love'       => array(
				'title'    => esc_html__( 'I love what I do!', 'reading-position-indicator' ),
				'callback' => 'iworks_reading_position_indicator_options_loved_this_plugin',
				'context'  => 'side',
				'priority' => 'core',
			),
		),
	);
	return $options;
}

function iworks_reading_position_indicator_post_types() {
	$args       = array(
		'public' => true,
	);
	$p          = array();
	$post_types = get_post_types( $args, 'names' );
	foreach ( $post_types as $post_type ) {
		$a               = get_post_type_object( $post_type );
		$p[ $post_type ] = $a->labels->name;
	}
	return $p;
}

function iworks_reading_position_indicator_options_need_assistance( $iworks_reading_position_indicator ) {
	$content = apply_filters( 'iworks_rate_assistance', '', 'reading-position-indicator' );
	if ( ! empty( $content ) ) {
		echo wp_kses_post( $content );
		return;
	}

	?>
<p><?php esc_html_e( 'We are waiting for your message', 'reading-position-indicator' ); ?></p>
<ul>
	<li><a href="<?php echo esc_html_x( 'https://wordpress.org/support/plugin/sierotki/', 'link to support forum on WordPress.org', 'reading-position-indicator' ); ?>"><?php esc_html_e( 'WordPress Help Forum', 'reading-position-indicator' ); ?></a></li>
</ul>
	<?php
}

function iworks_reading_position_indicator_options_loved_this_plugin( $iworks_reading_position_indicator ) {
	$content = apply_filters( 'iworks_rate_love', '', 'reading-position-indicator' );
	if ( ! empty( $content ) ) {
		echo wp_kses_post( $content );
		return;
	}
	?>
<p><?php esc_html_e( 'Below are some links to help spread this plugin to other users', 'reading-position-indicator' ); ?></p>
<ul>
	<li><a href="https://wordpress.org/support/plugin/sierotki/reviews/#new-post"><?php esc_html_e( 'Give it a five stars on WordPress.org', 'reading-position-indicator' ); ?></a></li>
	<li><a href="<?php echo esc_html_x( 'https://wordpress.org/plugins/sierotki/', 'plugin home page on WordPress.org', 'reading-position-indicator' ); ?>"><?php esc_html_e( 'Link to it so others can easily find it', 'reading-position-indicator' ); ?></a></li>
</ul>
	<?php
}
