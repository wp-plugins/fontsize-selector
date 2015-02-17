<?php
/*
Plugin Name: Fontsize Selector Widget
Plugin URI: http://themes.tradesouthwest.com/plugins/Fontsize-Selector-Form/
Description: Font size selection form Plugin by Tradesouthwest uses a widget to add form to theme which provides a drop-down selector to select font sizes. Plugin will create the widget and put a widget option on the admin widgets page.
Version: 0.3
Author: Larry Judd Oliver
Author URI: http://tradesouthwest.com
*/

/**
 * set params and vars for fontsize values
 */
    $fontsize_selector_options = array(
    '12' => '12',
    '14' => '14',
    '16' => '16',
    '18' => '18'
    );	
    add_option( 'fontsize_selector_options', $fontsize_selector_options );
    global $fontsize_selector_options;	


// Make plugin available for translation, change /languages/ to your .mo-files folder name
	load_plugin_textdomain( 'fontsize-selector', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Now we make a Widget to display the selector form
 * and titles etc in widget
 *
 */

function fontsize_selector_register_widget() {
       register_widget( 'Fontsize_Selector' );
}


function fontsize_selector_styles() {
    wp_enqueue_script(
        'fontsize-select', plugins_url( '/fontsize-selector-style.css' , __FILE__ )
    );
}
add_action( 'wp_enqueue_scripts', 'fontsize_selector_styles' );

class Fontsize_Selector extends WP_Widget {
    function Fontsize_Selector() {

        /* Widget settings. */
	$widget_ops = array( 'classname' => 'fontsize_selector_widget', 'description' => 'Changes size of page fonts' );

        /* Widget control settings. */
	$control_ops = array( 'id_base' => 'fontsize_selector_widget' );

        /* Create the widget. */
        $this->WP_Widget( 'fontsize_selector_widget', 'Font Size Widget', $widget_ops, $control_ops );
    }


    function widget( $args, $instance ) {

        extract( $args );
        $title     = apply_filters( 'widget_title', $instance['title'] );
        //$font_size = $instance['font_size']; 

// Before widget //
       echo $before_widget;
// Title of widget //
       if ( $title ) { echo $before_title . $title . $after_title; } 

// ouput widget content 
    ?>

   
    <form name="fontsize-selector-form" class="fontsize-selector-form" method="POST">
    <fieldset class="fontsize-fieldset>
   <?php 
    // create a nonce field
    wp_nonce_field( 'new_fontsize_selector_options_nonce', 'fontsize_selector_options_nonce' );
    
    $options_selected = get_option( 'fontsize_selector_options' );
    echo '<p><select name="fontsize_selector_options[fontsize]" id="fontsize_select">';
    ?>
<option value="12" <?php if( $options_selected['fontsize'] == '12' ) echo 'selected="selected"'; ?>><?php _e( 'Small', 'fontsize-selector' ); ?></option>
<option value="14" <?php if( $options_selected['fontsize'] == '14' ) echo 'selected="selected"'; ?>><?php _e( 'Medium', 'fontsize-selector' ); ?></option>  
<option value="16" <?php if( $options_selected['fontsize'] == '16' ) echo 'selected="selected"'; ?>><?php _e( 'Medium Large', 'fontsize-selector' ); ?></option>  
<option value="18" <?php if( $options_selected['fontsize'] == '18' ) echo 'selected="selected"'; ?>><?php _e( 'Large', 'fontsize-selector' ); ?></option>  
          </select>
 
    <input type="submit" name="fontsize-submit" id="submit" value="Save Changes" /></p></fieldset></form>

<?php 
// After widget 
echo $after_widget;
}

// Update Settings 

    function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    /* Strip tags (if needed) and update the widget settings. */
    $instance['title']     = strip_tags( $new_instance['title'] );
    //$instance['font_size'] = strip_tags( $new_instance['font_size']);
    return $instance;
    }

    function form( $instance ) {
	/* Set up some default widget settings. */
        $defaults = array( 'title' => 'Select Font Size' );
        $instance = wp_parse_args( (array) $instance, $defaults ); 

// Widget Control Panel 
?>
    <p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
    <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" /></p>
<?php
    }
} // ends widget class

add_action( 'widgets_init', 'fontsize_selector_register_widget' );


/**
 * get each value from form and post
 */
$fontSizes = $_POST;
if ( !empty( $fontSizes ) ) {
 foreach($fontSizes as $vKey => $fontSizes) {
        update_option($vKey, $fontSizes);
  }
}

global $fontsize_selector_options;	

if( isset( $_POST['font_size'] ) )
		update_option( $fontsize_selector_options, 'font_size', esc_attr( $_POST['font_size'] ) );

/**
 * valid values only pass
 */
function fontsize_selector_validate_options( $input ) {
    $valid_options = array( 12, 14, 16, 18, 1, 0, '' );
        if( in_array($input['font_size'], $valid_options) )
            return $input;
        else
            return false;
}


/**
 * Add specific CSS class by filter
 * @uses body_class()
 *
 * @param array $classes existing body classes
 * @return array modified body classes
 */

add_filter( 'body_class', 'fontsize_selector_class_name' );

function fontsize_selector_class_name( $classes ) {

$options = get_option( 'fontsize_selector_options' );

    if( $options['fontsize'] == '12' ) {
        $classes[] = 'fontsize-selector-12'; }

    elseif( $options['fontsize'] == '14' ) {
        $classes[] = 'fontsize-selector-14'; }

    elseif( $options['fontsize'] == '16' ) {
        $classes[] = 'fontsize-selector-16'; }

    elseif( $options['fontsize'] == '18' ) {
        $classes[] = 'fontsize-selector-18'; }
    
    else { $classes[] = 'fontsize-selector-16'; }
	// add 'fontsize-selector' to the $classes array
	// and return the $classes array

	return $classes;
}


/**
 * put selected value to work
 * @wp_enqueue inline styles
 */
function fontsize_selector_styles_method() {
    wp_enqueue_style( 
        'fontsize-style', plugins_url( '/fontsize-selector-style.css', __FILE__ ) 
    );

               $custom_css = "
            body.fontsize-selector-12 > div { font-size: 12px !important; }";

        $custom_css .= "
            body.fontsize-selector-14 > div { font-size: 14px !important; }";

        $custom_css .= "
            body.fontsize-selector-16 > div { font-size: 16px !important; }";

        $custom_css .= "
            body.fontsize-selector-18 > div { font-size: 18px !important; }";   

    wp_add_inline_style( 'fontsize-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'fontsize_selector_styles_method' );
?>
