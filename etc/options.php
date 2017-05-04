<?php

function iworks_reading_position_indicator_options()
{
    $options = array();

    /**
     * main settings
     */
    $options['index'] = array(
        'use_tabs' => false,
        'version'  => '0.0',
        'page_title' => __('Progress configuration', 'upprev'),
        'menu_title' => __('Progress', 'upprev'),
        'menu' => 'theme',
        'enqueue_scripts' => array(
            'upprev-admin-js',
        ),
        'enqueue_styles' => array(
            'upprev-admin',
            'upprev',
        ),
        'options'  => array(
            array(
                'name'              => 'style',
                'type'              => 'radio',
                'th'                => __( 'Color style', 'upprev' ),
                'default'           => 'solid',
                'radio'             => array(
                    'solid'         => array( 'label' => __( 'solid', 'upprev' ) ),
                    'transparent'   => array( 'label' => __( 'transparent to color',  'upprev' ) ),
                    'gradient'      => array( 'label' => __( 'gradient',  'upprev' ) ),
                ),
                'sanitize_callback' => 'esc_html'
            ),
            array(
                'name'              => 'height',
                'type'              => 'number',
                'class'             => 'small-text',
                'th'                => __( 'Height', 'upprev' ),
                'label'             => __( 'px', 'upprev' ),
                'default'           => 5,
                'sanitize_callback' => 'absint'
            ),
            array(
                'name'              => 'color1',
                'type'              => 'wpColorPicker',
                'class'             => 'short-text',
                'th'                => __( 'Primary color', 'upprev' ),
                'sanitize_callback' => 'esc_html',
                'default'           => '#f20',
                'use_name_as_id'    => true,
            ),
            array(
                'name'              => 'color2',
                'type'              => 'wpColorPicker',
                'class'             => 'short-text',
                'th'                => __( 'Secoundary color', 'upprev' ),
                'default'           => '#d93',
                'sanitize_callback' => 'esc_html',
                'use_name_as_id'    => true,
            ),
        ),
    );
    return $options;
}

