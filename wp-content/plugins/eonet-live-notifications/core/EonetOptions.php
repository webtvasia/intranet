<?php
/**
 * Class Components
 * Handles the admin tabs and main actions
 * @since 1.0.0
 */
namespace Eonet\Core;

if ( ! defined('ABSPATH') ) die('Forbidden');

use Exception;

class EonetOptions {

    /**
     * Helper to get an array of post types
     * Used in many settings
     * @return array
     */
    static public function getPostTypesArray()
    {
        // Get post types :
        $post_types_fetched = get_post_types(array(
            'exclude_from_search' => false
        ),'objects');
        $post_types = array();
        // We loop through them :
        foreach ($post_types_fetched as $post_type){
            $post_types[$post_type->name] = $post_type->label;
        }
        // Return the array :
        return $post_types;
    }

    /**
     * Helper to get an array of the members roles
     * Used in many settings
     * @return array
     */
    static public function getRolesArray()
    {
        // Get roles :
        global $wp_roles;

        $roles_array = array();

        // We go through all the roles :
        foreach ($wp_roles->roles as $key=>$value){
            $roles_array[$key] = $value['name'];
        }
        return $roles_array;
    }

	/**
	 * Helper to get the WP editor settings array
	 * @link https://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * @param $name : name of the textarea
	 * @param array $editor_settings
	 *
	 * @return array : settings
	 */
    static public function getEditorSettings($name, $editor_settings = array() )
    {
        $default_settings = array(
            'wpautop' => true, // use wpautop?
            'media_buttons' => true, // show insert/upload button(s)
            'textarea_name' => $name, // set the textarea name to something different, square brackets [] can be used here
            'textarea_rows' => 8, // rows="..."
            'tabindex' => '',
            'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
            'editor_class' => '', // add extra class(es) to the editor textarea
            'teeny' => false, // output the minimal editor config used in Press This
            'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
            'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
            'quicktags' => false // load Quicktags, can be used to pass settings directly to Quicktags using an array()
        );

	    $editor_settings = wp_parse_args( $editor_settings, $default_settings );

        return $editor_settings;
    }

	/**
	 * Take an array of settings (settings.php) and render an HTML markup.
	 * @param $settings array : Settings array to render
	 * @return string
	 */
	static public function renderForm($settings = array()) {

		// This variable will contain all the HTML returned;
		$html = '';

		// We go through all settings :
		foreach ($settings as $option) {

			$html .= self::renderField($option);

		}

		return $html;

	}

    /**
     * Render a single field
     * @param $option array : Option array
     * @return string
     */
    static function renderField($option) {

        // Hidden input:
        if ($option['type'] == 'hidden') {
            return self::renderFieldInput($option);
        }

	    $class_name = $option['name'];

	    $pieces = explode('[', $option['name']);

	    // If the name of the field is something like option_group[id][option_name] (such as in taks forms),
	    // then create an addition generical class in this format: eo_form_field_OPTION_GOUP_OPTION_NAME
	    if(count($pieces) == 3)
	        $class_name = 'eo_form_field_' . $option['name'] . ' eo_form_field_' . $pieces[0] . '_' . rtrim($pieces[2], ']');

	    $class_name .= ' eo_form_field_type_' . $option['type'];

        // Container :
        $html = '<div id="eo_form_field_'.$option['name'].'" class="eo_form_field ' . $class_name . '">';

        $html .= '<div class="eo_field_wrapper">';

        // Label :
        if(isset($option['label']) && $option['type'] != 'heading') {
            $html .= '<label for="eo_field_'.$option['name'].'" class="eo_field_label">';
            $html .= $option['label'];
            $html .= '</label>';
        }

        $html .= ($option['type'] != 'heading') ? '<div class="eo_field_container">' : '';

	    //Render the input field
	    $html .= self::renderFieldInput($option);

        // Render the description :
	    $html .= self::renderFieldDescription($option);


        $html .= '</div><!-- .eo_field_container -->';

        $html .= ($option['type'] != 'heading') ? '</div>' : '';

        // End container :
        $html .= '</div> <!-- .eo_form_field -->';

        return $html;

    }

	/**
	 * Create a submit button
	 * @param $type string : delete, new, save
	 * @param $label string : extra label on the button
	 * @return string
	 */
	static public function getSubmit($type = 'new', $label = '') {

		if($type == 'delete') {
			$icon = 'fa-trash-o';
			$text = esc_html__('Delete', 'eonet-live-notifications');
		}
		elseif ($type == 'new') {
			$icon = 'fa-pencil-square-o';
			$text = esc_html__('New', 'eonet-live-notifications');
		} else {
			$icon = 'fa-check-circle';
			$text = esc_html__('Save', 'eonet-live-notifications');
		}

		$html = '<button type="submit" value="submit" class="eo_btn eo_btn_default"><i class="fa '.$icon.'"></i> '.$text.' '.$label.'</button>';

		return $html;
	}

	/**
	 * Render the input part of the field
	 * (such as the input text, the textarea, etc)
	 *
	 * @param array $option
	 * @throws Exception
	 * @return string
	 */
	static protected function renderFieldInput($option) {

		$default_page = ( !empty($active_extensions) ) ? 'settings' : 'extensions';
		$default_page = apply_filters( 'eonet_admins_pages_default_page', $default_page);

		//Check if have to get the option from plugin options or from theme options
		if( (isset( $_GET['eo_tab'] ) && $_GET['eo_tab'] == 'theme-settings')
		    || ( !isset($_GET['eo_tab']) && $default_page == 'theme-settings') )
			$option_saved = eo_get_theme_option($option['name']);
		else
			$option_saved = eonet_get_option($option['name']);

		switch($option['type']) {

			case 'text':
				return self::renderFieldTypeText( $option , $option_saved);

            case 'hidden':
                return self::renderFieldTypeHidden( $option , $option_saved);

			case 'switch':
				return self::renderFieldTypeSwitch( $option, $option_saved );

			case 'textarea':
				return self::renderFieldTypeTextarea( $option, $option_saved );

            case 'editor':
                return self::renderFieldTypeEditor( $option, $option_saved );

            case 'html':
                return self::renderFieldTypeHTML( $option );

			case 'heading':
				return self::renderFieldTypeHeading( $option );

            case 'upload':
                return self::renderFieldTypeUpload( $option, $option_saved );

            case 'tag':
                return self::renderFieldTypeTag( $option, $option_saved );

			case ('select' == $option['type'] && isset($option['choices']) ):
				return self::renderFieldTypeSelect( $option, $option_saved );

			case 'datepicker':
				return self::renderFieldTypeDatePicker( $option, $option_saved );

			case 'colorpicker':
				return self::renderFieldTypeColorPicker( $option, $option_saved );

			case 'iconpicker':
				return self::renderFieldTypeIconPicker( $option, $option_saved );

			case 'image_select':
				return self::renderFieldTypeImageSelect( $option, $option_saved );

			case 'typography':
				return self::renderFieldTypeTypography( $option, $option_saved );

			case 'slider':
				return self::renderFieldTypeSlider( $option, $option_saved );

			default:
				throw new Exception("Impossible to render the option field: Unknown option type.");
				
		}
	}

	/**
	 * If exists, render the description of the field
	 *
	 * @param array $option
	 *
	 * @return string
	 */
	static protected function renderFieldDescription($option) {
		$html_description = '';

		if(isset($option['desc'])) {
			$html_description .= '<div for="eo_desc_'.$option['name'].'" class="eo_field_desc">';
			$html_description .= '<p><i class="fa fa-question-circle"></i> '.$option['desc'].'</p>';
			$html_description .= '</div>';
		}

		return $html_description;
	}

	/**
	 * Render the input text
	 *
	 * @param array $option
	 * @param string $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeText( $option, $option_saved ) {
		$html = '';

		if ( $option_saved == '' ) {
			$val = ( isset( $option['val'] ) && $option['val'] != '' ) ? 'value="' . $option['val'] . '"' : '';
		} else {
			$val = 'value="' . $option_saved . '"';
		}
		$html .= '<input id="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . '" type="text" class="eo_field eo_field_text" ' . $val . '>';

		return $html;
	}

    /**
     * Render the input hidden
     *
     * @param array $option
     * @param string $option_saved
     *
     * @return string
     */
    static protected function renderFieldTypeHidden( $option, $option_saved ) {
        $html = '';

        if ( $option_saved == '' ) {
            $val = ( isset( $option['val'] ) && $option['val'] != '' ) ? 'value="' . $option['val'] . '"' : '';
        } else {
            $val = 'value="' . $option_saved . '"';
        }
        $html .= '<input id="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . '" type="hidden">';

        return $html;
    }

	/**
	 * Render the input datepicker
	 *
	 * @param $option
	 * @param $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeDatePicker( $option, $option_saved ) {
		$html = '';

		if ( $option_saved == '' ) {
			$val = ( isset( $option['val'] ) && $option['val'] != '' ) ? 'value="' . $option['val'] . '"' : '';
		} else {
			$val = 'value="' . $option_saved . '"';
		}
		$html .= '<input id="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . '" type="text" class="eo_field eo_field_datepicker" ' . $val . '>';

		return $html;
	}

	/**
	 * Render the input colorpicker
	 *
	 * @param $option
	 * @param $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeColorPicker( $option, $option_saved ) {
		
		$html = '';

		if ( $option_saved == '' ) {
			$val = ( isset( $option['val'] ) && $option['val'] != '' ) ? 'value="' . $option['val'] . '"' : '';
		} else {
			$val = 'value="' . $option_saved . '"';
		}

		$transparent = (isset($option['transparent']) && $option['transparent']) ? 'true' : 'false' ;
		$transparent = 'data-eo-transparent="' . $transparent . '"' ;

		$html .= '<div id="eo_field_' . $option['name'].'_wrapper" '.$transparent.' class="eo_field_colorpicker_wrapper">';
		$html .= '<input 
			id="eo_field_' . $option['name'] . '" 
			name="eo_field_' . $option['name'] . '" 
			type="text" 
			class="eo_field eo_field_colorpicker" 
			'.$val.'/>';
		$html .= '<span class="eo_colorpicker_input"><i></i></span>';
		$html .= '</div>';

		return $html;
		
	}

	/**
	 * Render the input switch
	 *
	 * @param array $option
	 * @param string $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeSwitch( $option, $option_saved ) {

		if($option_saved == '')
			$option_saved = $option['val'];

		// Cast the strings '1' and '' to bool
		$option_saved = ($option_saved != 'true' && $option_saved != 'false') ? (bool)$option_saved : $option_saved;

		$html    = '';
        if(
            ( ($option_saved == 'true' || $option_saved === true) )
        ) {
            $checked = 'checked';
        } else {
            $checked = '';
        }
		$html .= '<div class="eo_field_switch_wrapper">';
        $html .= '<input id="eo_field_' . $option['name'] . '_hidden" name="eo_field_' . $option['name'] . '" type="hidden" value="0">';
        $html .= '<input id="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . '" class="eo_field eo_field_toggle" value="1" type="checkbox" ' . $checked . '>';
		$html .= '<label for="eo_field_' . $option['name'] . '" class="eo_field_toggle_span"></label>';
        $html .= '</div>';

		return $html;
	}

	/**
	 * Render the icon picker field
	 *
	 * @param $option
	 * @param $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeIconPicker( $option, $option_saved ) {

		$icons_array = eonet_get_font_awesome_icons();

		$icon_value = (empty($option_saved) && !empty($option['val'])) ? $option['val'] : $option_saved;

		ob_start();
		?>
		<div id="eo_field_<?php echo sanitize_html_class($option['name']); ?>" class="eo_field_iconpicker_wrapper">

			<div class="eo_field_iconpicker_icon_selected_wrapper">
				<div class="eo_field_iconpicker_icon_selected"><i class="<?php echo eo_sanitize_multiple_html_classes($icon_value); ?>"></i></div>
				<a href="javascript:void(0)" class="eo_field_iconpicker_details_trigger"><i class="fa fa-caret-down"></i></a>
			</div>
			
			<div class="eo_field_iconpicker_icons_details">
				<label class="eo_label_for_select">
					<span><?php esc_html_e('Icon groups:', 'eonet-live-notifications'); ?></span>
					<select class="eo_field_iconpicker_group_select">
	
						<option value="all" ><?php echo esc_html_x( 'All icons', 'Select option in the icon picker field', 'eonet-live-notifications' ) ?></option>
	
						<?php
						foreach ( $icons_array['groups'] as $group_key => $group_name ) {
							echo '<option value="' . esc_attr( $group_key ) . '" >' . esc_html( $group_name ) . '</option>' . "\n";
						}
						?>
					</select>
				</label>
	
				<!-- Grid of icons-->
				<div class="eo_field_iconpicker_icons_grid_container">
					<?php $c = 0; ?>
					<?php foreach ( $icons_array['icons'] as $group_key => $group ) : ?>
						<div class="eo_field_iconpicker_icons_grid_panel " data-eo-icons-group="<?php echo esc_attr($group_key); ?>">
							<div class="eo_field_iconpicker_icons_grid_panel_title"><?php echo $icons_array['groups'][$group_key]; ?></div>
							<div class="eo_field_iconpicker_icons_grid_panel_icons eo_clearfix">
								<?php foreach ( $group as $icon_class ): ?>
									<?php
									if($c++ == 0) {
										$selected = (empty($icon_value)) ? 'selected': '';
										echo '<a href="javascript:void(0)" class="eo_field_iconpicker_icons_grid_element eo_empty_icon '.$selected.'"></a>';
									}
									?>
									<a href="javascript:void(0)" class="eo_field_iconpicker_icons_grid_element"><i class="<?php echo eo_sanitize_multiple_html_classes($icon_class); ?>"></i></a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			
			<!-- Select of the icons (hidden in the options panel, used only to store the value or in case that something is wrong with js) -->
			<select name="eo_field_<?php echo esc_attr($option['name']); ?>" class="eo_field_iconpicker_select">
				<option value="" <?php ( strcmp( '', $icon_value ) === 0 ? 'selected' : '' )?>><?php esc_html_e( 'No icon', 'eonet-live-notifications' ); ?></option>
				<?php
				foreach ( $icons_array['icons'] as $group => $icons ) {
					echo '<optgroup label="' . esc_attr( $group ) . '">' . "\n";
					foreach ( $icons as $key => $label ) {
						$class_key = $label;
						echo '<option value="' . esc_attr( $class_key ) . '" ' . ( strcmp( $class_key, $icon_value ) === 0 ? 'selected' : '' ) . '>' . esc_html( $label ) . '</option>' . "\n";
					}
					echo '</optgroup>' . "\n";
				}
				?>
			</select>

		</div>

		<?php
		return ob_get_clean();

	}

	/**
	 * @param $option
	 * @param $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeImageSelect( $option, $option_saved ) {

		$image_selected = (empty($option_saved) && !empty($option['val'])) ? $option['val'] : $option_saved;

		$html = '';
		$html .= '<div id="eo_field_' . $option['name'] . '" class="eo_field_image_select_wrapper">';

			foreach ($option['choices'] as $key => $image) {
				$alt = (isset($image['alt'])) ? $image['alt'] : '';
				$selected = ($key == $image_selected) ? 'selected' : '';
				$html .= '<a data-eo-value="'.$key.'" class="eo_image_select_element '.$selected.'" href="javascript:void(0)"><img src="'.$image['src'].'" alt="'.$alt.'"/></a>';
			}

			$html .= '<input type="hidden" class="eo_field_image_select_selected_image" name="eo_field_'.$option['name'].'" value="'.$image_selected.'" />';

		$html .= '</div>';

		return $html;

	}

	/**
	 * Render the input textarea
	 *
	 * @param array $option
	 * @param string $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeTextarea( $option, $option_saved ) {
		$html = '';
		$html .= '<textarea id="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . '" class="eo_field eo_field_textarea" rows="4" cols="50">';
		if ( $option_saved == '' ) {
			$val = ( isset( $option['val'] ) && $option['val'] != '' ) ? $option['val'] : '';
		} else {
			$val = $option_saved;
		}
		$html .= $val;
		$html .= '</textarea>';

		return $html;
	}

    /**
     * Render the input editor
     * REQUIRES specific enqueues about CSS & JS from WP in order to render correctly
     * @link https://codex.wordpress.org/Function_Reference/wp_editor
     * @param array $option
     * @param string $option_saved
     * @return string
     */
    static protected function renderFieldTypeEditor( $option, $option_saved ) {

        $name = 'eo_field_'.$option['name'];
        $option['val'] = (isset($option['val'])) ? $option['val'] : '';
        $val = (!empty($option_saved)) ? $option_saved : $option['val'];

		$editor_settings = ( isset($option['settings']) ) ? $option['settings'] : array();

        ob_start();
        wp_editor( $val, $name, self::getEditorSettings($name, $editor_settings) );
        $editor_html = ob_get_contents();
        ob_end_clean();

        $html = $editor_html;

        return $html;
    }

    /**
     * Render the HTML option
     *
     * @param array $option
     * @return string
     */
    static protected function renderFieldTypeHTML( $option ) {

        $html = '';
        $html .= '<div id="eo_field_' . $option['name'] . '" class="eo_field_html_wrapper">';
        $html .= (isset($option['content'])) ? $option['content'] : '';
        $html .= '</div>';

        return $html;
    }

	/**
	 * Render an heading to separate sections in the panel tabs
	 *
	 * @param $option
	 *
	 * @return string
	 */
	static protected function renderFieldTypeHeading( $option ) {

		$html = '';
		$html .= '<div id="eo_field_' . $option['name'] . '" class="eo_field_heading_wrapper">';
			$html .= '<div class="eo_field_heading">';
				$html .= (isset($option['label'])) ? $option['label'] : '';
			$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

    /**
     * Render the Upload option
     * It's only for WP attachments so far
     * @param array $option
     * @param int $option_saved attachment ID
     * @return string
     */
    static protected function renderFieldTypeUpload( $option, $option_saved ) {

        $html = '';
        $html .= '<div class="eo_field_upload_wrapper">';
            $value = (!empty($option_saved)) ? $option_saved : $option['val'];
            // If there is already an image to fetch :
            if(!empty($option['val']) || !empty($option_saved)) {
                $value = (!empty($option_saved)) ? $option_saved : $option['val'];
                $featured_url = wp_get_attachment_image_src( $value, 'full' );
                $html .= '<div class="eo_field_upload_preview">';
                    $html .= '<div class="eo_field_upload_single_file">';
                        // Close button :
                        $html .= '<a href="javascript:void(0);" class="eo_field_upload_single_delete"><i class="fa fa-times"></i></a>';
                        // Image :
                        $html .= '<img src="'.$featured_url[0].'">';
                    $html .= '</div>';
                $html .= '</div>';
            }
            // File ID in WP, if it matches a WP attachment :
            $html .= '<input type="hidden" name="eo_field_' . $option['name'] . '" id="eo_field_' . $option['name'] . '" value="'.$value.'">';
            // Button :
            $html .= '<button data-eo-name="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . '_trigger" class="eo_btn_upload_wp eo_btn eo_btn_default"><i class="fa fa-picture-o"></i> '.esc_html__('Choose an Image', 'eonet-live-notifications').'</button>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render the Tag option in any form
     * @param array $option
     * @param $option_saved array
     * @return string, null
     */
    static protected function renderFieldTypeTag( $option, $option_saved = array()) {

        if(!isset($option['taxonomy']) || empty($option['taxonomy'])){
            return;
        }

        // Set data
        if ( !empty($option['val']) && is_array($option['val'])) {
            $values = $option['val'];
        } else {
            $values = $option_saved;
        }

        /**
         * We create a JS ready array of all terms
         * So we can look for it and get the data easily from jQuery
         * We don't use JSON because it's more easier to loop through an array
         */
        $terms = get_terms($option['taxonomy'], array('hide_empty' => false));
        $data_array = '[';
        $nbr_terms = count($terms);
        $counter_terms = 0;
        foreach ($terms as $term){
            $counter_terms++;
            $ending = ($counter_terms == $nbr_terms && $nbr_terms != 1) ? ', ' : '';
            $data_array .= '"'.$term->name.'"'.$ending;
        }
        $data_array .= ']';

        $html = '';
        $html .= '<div class="eo_field_tags_wrapper" data-eo-data=\''.$data_array.'\' data-eo-input-name="'.$option['name'].'">';

        $html .= '<ul class="eo_tags_list">';
            // The data :
            if(!empty($values)) {
                foreach ($values as $val) :
                    $html .= '<li class="eo_single_tag">';
                    $html .= '<input type="hidden" name="eo_field_' . $option['name'] . '[]" value="' . $val . '">';
                    $html .= $val;
                    $html .= '<span class="eo_single_tag_close"><i class="fa fa-times"></i></span>';
                    $html .= '</li>';
                endforeach;
            }
            // The input :
            $html .= '<li><input type="text" class="eo_field_tags_typer"></li>';
        $html .= '</ul>';

        $html .= '</div>';

        return $html;

    }

	/**
	 * Render the input select
	 *
	 * @param array $option
	 * @param array|string $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeSelect( $option, $option_saved ) {
		$html         = '';
		$multiple     = ( isset( $option['multiple'] ) && $option['multiple'] == true ) ? 'multiple' : '';
		$multiple_obj = ( isset( $option['multiple'] ) && $option['multiple'] == true ) ? '[]' : '';
		$html .= '<div class="eo_field_select_wrapper ' . $multiple . '">';
		$html .= '<select id="eo_field_' . $option['name'] . '" name="eo_field_' . $option['name'] . $multiple_obj . '" class="eo_field_select" ' . $multiple . '>';
        foreach ( $option['choices'] as $choice_id => $choice_label ) {
			if ( $option_saved == '' ) {
			    if(
                    (isset( $option['val']) && !is_array($option['val']) && $option['val'] == $choice_id)
                    ||
                    (isset($option['val']) && is_array($option['val']) && in_array($choice_label, $option['val']))
                    ||
                    (isset($option['val']) && is_array($option['val']) && in_array($choice_id, $option['val']))
                ) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
			} else {
				if ( is_array( $option_saved ) && in_array( $choice_id, $option_saved ) ) {
					$selected = 'selected';
				} elseif ( ! is_array( $option_saved ) && $choice_id == $option_saved ) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
			}
			$html .= '<option value="' . $choice_id . '" ' . $selected . '>' . $choice_label . '</option>';
		}
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render the typography field
	 *
	 * Example:
	 *
	 * array(
	 *  'name'      => 'field_id',
	 *  'type'      => 'typography',
	 *  'label'     => esc_html__('Select a font', 'eonet'),
	 *  'desc'      => esc_html__('Your desc here', 'eonet'),
	 *  'font-family' => true,
	 *  'font-size => true,
	 *  'val'       => array(
     *   'font-family' => 'Font name',
	 *   'font-size' => '14',
	 *  )
	 * );
	 *
	 * @param array $option
	 * @param array|string $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeTypography( $option, $option_saved ) {

		//Set the default parameters
		$parameters = array();
		$parameters['font-family'] = (isset($option['font-family'])) ? $option['font-family'] : true;
		$parameters['font-size'] = (isset($option['font-size'])) ? $option['font-size'] : true;

		$html         = '';
		$html .= '<div id="eo_field_' . $option['name'] . '_wrapper" class="eo_field_typography_wrapper">';

		// Render the font family select
		if( $parameters['font-family'] ) {
			$default_val = (isset($option['val']['font-family']) && !empty($option['val']['font-family'])) ? $option['val']['font-family'] : '';
			$val = (isset($option_saved['font-family']) && !empty($option_saved['font-family'])) ? $option_saved['font-family'] : $default_val;
			$available_values = EonetGoogleFontLoader::getFontsNames();

			$html .= '<div class="eo_input_group eo_clearfix">';
				$html .= '<label for="eo_field_' . $option['name'] . '_font-family">' . esc_html__( 'Font Family:', 'eonet' ) . '</label>';
				$html .= '<select 
					id="eo_field_' . $option['name'] . '_font-family" 
					name="eo_field_' . $option['name'] . '[font-family]" 
					class="eo_field eo_field_typography_font_family" >';

				$html .= '<option value="" >' . esc_html__('Select a font..', 'eonet') . '</option>';
				foreach ( $available_values as $font ) {
					$html .= '<option value="' . $font . '" ' . selected($font, $val, false) . '>' . $font . '</option>';
				}
				$html .= '</select>';
			$html .= '</div>';
		}

		//Render the font size input box
		if( $parameters['font-size'] ) {
			$default_val = (isset($option['val']['font-size']) && !empty($option['val']['font-size'])) ? $option['val']['font-size'] : '';
			$val = (isset($option_saved['font-size']) && !empty($option_saved['font-size'])) ? $option_saved['font-size'] : $default_val;
			$html .= '<div class="eo_input_group eo_clearfix">';
				$html .= '<label for="eo_field_' . $option['name'] . '_font-size">' . esc_html__( 'Font Size:', 'eonet' ) . '</label>';
				$html .= '<input 
					name="eo_field_' . $option['name'] . '[font-size]" 
					type="number" 
					class="eo_field eo_field_typography_font_size" 
					value="'.$val.'">';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Render the input select
	 *
	 * @param $option
	 * @param $option_saved
	 *
	 * @return string
	 */
	static protected function renderFieldTypeSlider( $option, $option_saved ) {

		//Set the default parameters
		$parameters = array();
		$parameters['min'] = (isset($option['min'])) ? $option['min'] : 0;
		$parameters['max'] = (isset($option['max'])) ? $option['max'] : 10;
		$parameters['step'] = (isset($option['step'])) ? $option['step'] : 1;

		$val = (!empty($option['val']) && $option_saved == '') ? $option['val'] : $option_saved;

		$html = '';
		$html .= '<div id="eo_field_' . $option['name'] . '_wrapper" class="eo_field_slider_wrapper">';
			$html .= '<input class="eo_field_slider_value eo_field" type="text" disabled="disabled" value="'.$val.'"/>';
			$html .= '<input 
				id="eo_field_' . $option['name'].'" 
				class="eo_field_slider" 
				type="text" 
				name="eo_field_' . $option['name'] . '"
				data-provide="slider" 
				data-slider-id="eo_field_' . $option['name'] . '"
				data-slider-tooltip="hide" 
				data-slider-min="' . $parameters['min'] . '" 
				data-slider-max="' . $parameters['max'] . '" 
				data-slider-step="' . $parameters['step'] . '" 
				data-slider-value="' . $val . '" />';
		$html .= '</div>';

		return $html;

	}

}