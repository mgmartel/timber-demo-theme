<?php

/**
 * Use Redux Framework with Timber
 *
 * Use this class like you would Redux Framework. All options are automatically
 * made available in your Timber template context (by default as 'options').
 *
 * Extra optional arguments in the $args array:
 * - 'timber_context' (string) Name of the variable in template context.
 * - 'wpml_context'   (string) Name of the context to use in the WPML String
 *                             Translations Module
 *
 * @link https://github.com/jarednova/timber
 * @link https://github.com/ReduxFramework/ReduxFramework
 */
class TimberRedux
{
    private $_setup_data;
    private $_wpml_context = false;
    private $_timber_context = 'options';

    /**
     * @var \ReduxFramework
     */
    private $_data = false;

    public function __get( $name ) {
        return $this->get( $name );
    }

    public function __isset( $name ) {
        return !is_null( $this->get( $name ) );
    }

    public function get( $name, $default = null ) {
        if ( !is_a( $this->_data, '\ReduxFramework' ) )
            return null;

        $ret = $this->_data->get( $name, $default );

        // Maybe return WPML
        if ( $this->_wpml_context && function_exists( 'icl_t' ) ) {
            $found = false;
            $translated = \icl_t( $this->_wpml_context, $name, $ret, $found );

            if ( $found )
                return $translated;
        }

        return $ret;
    }

    public function __construct( $sections = array(), $args = array(), $extra_tabs = array() ) {
        $this->_setup_data = compact( 'sections', 'args', 'extra_tabs' );

        if ( !\did_action( 'plugins_loaded' ) ) {
            \add_action( 'plugins_loaded', array( &$this, '_setup_redux' ), 11 );
        } else {
            $this->_setup_redux();
        }

        if ( isset( $args['timber_context'] ) && $args['timber_context'] )
            $this->_timber_context = $args['timber_context'];

        \add_filter( 'timber_context', array( &$this, '_add_to_twig' ) );
    }

    public function _setup_redux() {
        if ( !class_exists( 'ReduxFramework' ) )
            return;

        extract( $this->_setup_data );

        $this->_data = new \ReduxFramework( $sections, $args, $extra_tabs );

        if ( isset( $args['wpml_context'] ) && $args['wpml_context'] && function_exists( 'icl_register_string' ) ) {
            $this->_register_sections_wpml( $sections, $args['wpml_context'] );
            $this->_wpml_context = $args['wpml_context'];
        }

        $this->_setup_data = null;
    }

        private function _register_sections_wpml( $sections, $context ) {
            foreach( $sections as $section ) {
                foreach( $section['fields'] as $field ) {
                    $id = $field['id'];

                    $data = $this->get( $id, $field['default'] );
                    \icl_register_string( $context, $id, $data );
                }
            }

        }

    public function _add_to_twig( $twig ) {
        $twig[$this->_timber_context] = $this;

        return $twig;
    }
}