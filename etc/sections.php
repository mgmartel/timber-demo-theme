<?php

$sections = array();

$sections[] = array(
    'title'      => __( 'Example Options', 'timber-theme' ),
    'header'     => __( 'Example Theme Options', 'timber-theme' ),
    'desc'       => __( 'Use the options below in your theme like this: <code>{{options.my_option_id}}</code>', 'timber-theme' ),
    'icon_class' => 'icon-large',
    'icon'       => 'wrench',
    'fields'     => array(
         array(
            'id'       => 'timber_example_field',
            'type'     => 'editor',
            'title'    => __('Timber Example Field', 'timber-theme'),
            'subtitle' => __( 'Checkout index.twig for a demo on how this value shows up in your theme.', 'timber-theme' ),
            'default'  => 'I am a theme option. Change me in wp-admin!',
        )
    )
);

return $sections;
