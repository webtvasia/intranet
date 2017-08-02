<?php
/**
 * Class Metaboxes
 * Handles the metaboxes with Eonet
 * This class creates a single meta box
 * @since 1.0.0
 */
namespace Eonet\Core;

use Eonet\Core\EonetOptions;

if ( ! defined('ABSPATH') ) die('Forbidden');

class EonetMetaboxes {

    /**
     * Metabox's title
     *
     * @var string
     */
    public $title;

    /**
     * Metabox's post type
     *
     * @var string
     */
    static $type;

    /**
     * Metabox's options
     * It's an array ready for EonetOptions class
     *
     * @var mixed
     */
    public $options;

    /**
     * Construct the function
     *
     * @param $args array
     *      'title' : the metabox title
     *      'type' : the post type where we attach the metabox
     *      'options' : the options displayed within the metabox
     */
    public function __construct($args)
    {

        $this->title = $args['title'];
        self::$type = $args['type'];
        $this->options = $args['options'];

        add_action( 'add_meta_boxes', array($this,'register') );

        add_action( 'save_post', array($this,'saveAction'), 10, 3 );


    }

    /**
     * Set default values [val] to $options array
     * So we handle already saved post metadata
     * Accepting that we only check for post meta
     * whose names are the options' names [name]
     *
     * IMPORTANT, ALL META VALUES ATTACHED
     * ARE PREFIXED WITH: eo_[name]
     *
     * @param $post_id
     * @param $options array The options to parse
     *
     * @return array
     */
    public static function setDefaultValues($post_id, $options) {

	    //We get all the meta of the post, so we save queries and we can check if a field has been saved at least one time
	    $saved_value = get_post_meta($post_id);

        foreach ($options as $key=>$option) {

            if(!isset($saved_value['eo_' . $option['name']]))
                continue;

	        //Get the saved value of the field
            $val = $saved_value['eo_' . $option['name']][0];

            //If the default value is an array, we can suppose that also the saved value is an array
            if(is_array($option['val']))
	            $val = unserialize($val);

            $options[$key]['val'] = $val;


        }

        return $options;

    }

    /**
     * Function used to register the metabox
     */
    public function register() {

        add_meta_box(
            'eonet_'.sanitize_title($this->title),
            $this->title,
            array($this, 'content'),
            self::$type
        );

    }

    /**
     * Meta box display callback
     *
     * @param \WP_Post $post Current post object.
     */
    public function content($post) {

        $options = self::setDefaultValues($post->ID, $this->options);

        $html = '';

        $html .= '<div id="eo_'.sanitize_title($this->title).'" class="eo_metabox">';
        $html .= '<div class="eo_form">';
        // Nonce :
        $html .= wp_nonce_field( 'eonet_metabox_nonce', 'eonet_metabox_nonce', true, false );
        // Options :
        $html .= EonetOptions::renderForm($options);
        $html .= '</div>';
        $html .= '</div>';

        echo $html;

    }

    /**
     * Action to the save post hook
     *
     * @param int $post_id Post ID
     * @return integer
     */
    public function saveAction( $post_id )
    {

        self::save($post_id);
        return $post_id;

    }

    /**
     * Save meta box content.
     *
     * @param int $post_id Post ID
     * @return integer
     */
    public static function save( $post_id )
    {

        /**
         * Let's check a few things before saving our metaboxe's values
         */

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_id;

        if (!isset($_POST['eonet_metabox_nonce']) || !wp_verify_nonce($_POST['eonet_metabox_nonce'], 'eonet_metabox_nonce'))
            return $post_id;


        $post_type = get_post_type($post_id);

	    if(!is_array(self::$type))
		    self::$type = array(self::$type);

	    if (!in_array($post_type, self::$type))
		    return $post_id;

        /**
         * We can now save our values as post meta
         */

        foreach ($_POST as $field_name => $field_value) {

            $option_array = explode('eo_field_', $field_name);

            if (sizeof($option_array) == 2) {

                update_post_meta($post_id, 'eo_' . $option_array[1], $field_value);

            }

        }

        return $post_id;

    }

}