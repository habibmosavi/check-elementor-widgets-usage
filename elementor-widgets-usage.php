/**
 * Elementor widgets usage.
 */
 $current_user = wp_get_current_user();

if ( defined('ELEMENTOR_VERSION') && user_can( $current_user, 'administrator' ) ) {

    // Function to display Elementor widgets used on the current page
    function show_elementor_widgets_used() {
        if ( is_singular() ) { 
            $page_id = get_the_ID();
            
            if ( $page_id ) {
                $widgets = get_elementor_widgets_used($page_id);

                if ( !empty($widgets) ) {
                    echo "<h2>Widgets used on this page:</h2><ul>";
                    foreach ($widgets as $widget) {
                        echo "<li>" . esc_html($widget) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No Elementor widgets found on this page.</p>";
                }
            }
        }
    }

    // Function to get Elementor widgets used by page ID
    function get_elementor_widgets_used($post_id) {
        $widget_list = [];
        $elementor_data = get_post_meta($post_id, '_elementor_data', true);

        if ($elementor_data) {
            $content_data = json_decode($elementor_data, true);

            function extract_widgets($elements, &$widget_list) {
                foreach ($elements as $element) {
                    if (isset($element['elType']) && $element['elType'] === 'widget') {
                        $widget_list[] = $element['widgetType'];
                    }
                    if (isset($element['elements']) && !empty($element['elements'])) {
                        extract_widgets($element['elements'], $widget_list);
                    }
                }
            }

            extract_widgets($content_data, $widget_list);
            $widget_list = array_unique($widget_list);
        }

        return $widget_list;
    }

    add_action( 'wp', 'show_elementor_widgets_used' );

}