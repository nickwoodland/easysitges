<?php

/**
 * Function for building all form
 */
if (!function_exists('colabs_form_builder')) {
  function colabs_form_builder($fields, $post = false) {
    global $wpdb;
		
    // enqueue form builder js
    wp_enqueue_media();
    wp_enqueue_script( 'colabs_form_builder', get_template_directory_uri() . '/includes/js/custom-form-builder.js', array('jquery'), '', true );
		
    // Build the structures
    foreach ( $fields as $field ) { 

      $field = (object) $field;
      $field = apply_filters( 'colabs_form_builder_field_' . $field->field_type, $field, $post );
      if ( ! $field ) continue;
			$class = isset( $field->field_class ) ? $field->field_class : ''; ?>
      
      <div class="form-builder-input input-<?php echo $field->field_type; ?> <?php echo $class;?>">
        <label><?php echo esc_html($field->field_label); ?></label>
        <?php colabs_form_builder_field( $field, $post ); ?>
				<?php if ( isset($field->field_desc) ) {echo '<span class="description">'.$field->field_desc.'</span>';}?>
      </div>
      <?php
    }
  }
}

/**
 * Function for rendering individual field type
 * 
 * @param  Mixed $field  Object of field options
 */
function colabs_form_builder_field( $field, $post = false ) {
  if( !$field ) return;
	$post_meta_val = ( $post ) ? get_post_meta($post->ID, $field->field_name, true) : false;
  $colabs_taxonomies = array();
  switch ( $field->field_type ) {

    // Text Field
    // ----------
    case 'text':
      
      if ( $field->field_name == 'post_title' && $post ) {
        $value = $post->post_title;
			}else if ( isset($post_meta_val)&& $post_meta_val ) { 
        $value = colabs_string_clean( $post_meta_val );	
      } else {
        $value = colabs_string_clean( $field->field_values );
      }
      ?>
      <input name="<?php echo esc_attr($field->field_name); ?>" id="<?php esc_attr($field->field_name); ?>" type="text" value="<?php echo $value; ?>" class="text <?php if ($field->field_req) echo 'required' ?>" />
      <?php
      break;
    
    // Text Field
    // ----------
    case 'calendar':
      
      if ( isset($post_meta_val)&& $post_meta_val ) { 
        $value = $post_meta_val;	
      } else {
        $value = $field->field_values;
      }
      ?>
      <input name="<?php echo esc_attr($field->field_name); ?>" id="<?php esc_attr($field->field_name); ?>" type="text" value="<?php echo $value; ?>" class="datepicker <?php if ($field->field_req) echo 'required' ?>" />
      <?php
      break;
    
    // Text Field
    // ----------
    case 'time':
      
      if ( isset($post_meta_val)&& $post_meta_val ) { 
        $value = $post_meta_val;	
      } else {
        $value = $field->field_values;
      }
      ?>
      <input name="<?php echo esc_attr($field->field_name); ?>" id="<?php esc_attr($field->field_name); ?>" type="time" value="<?php echo $value; ?>" class="timepicker <?php if ($field->field_req) echo 'required' ?>" />
      <?php
      break;  

    // Hidden Field
    // ------------
    case 'hidden':
      ?>
      <input name="<?php echo esc_attr($field->field_name); ?>" id="<?php esc_attr($field->field_name); ?>" type="hidden" value="<?php echo $field->field_values; ?>" />
      <?php
      break;

    // Select Field
    // ------------
    case 'select':
      ?>
      <select name="<?php echo esc_attr($field->field_name); ?>" id="<?php echo esc_attr($field->field_name); ?>" class="dropdownlist <?php if($field->field_req) echo 'required' ?>">
        <?php
        $options = $field->field_values;                
        foreach ( $options as $option=>$value ) {
          
          $selected = '';
          if ( $option == $post_meta_val ){
						$selected = 'selected="selected"';
					}else if( isset($field->field_std) && $option == $field->field_std ){
						$selected = 'selected="selected"';
					}
					
          ?>
          <option <?php echo $selected;?> value="<?php echo esc_attr($option); ?>"><?php echo esc_attr($value); ?></option>
          <?php
        }
        ?>
      </select>
      <?php
      break;

    // Taxonomy Select
    // ---------------
    case 'select-tax':
      $colabs_taxonomies = array();  

      // If there is extra option field, append the option
      if( isset( $field->field_extra_option ) ) {
        $colabs_taxonomies[''] = __('Select One', 'colabsthemes');
        $colabs_taxonomies[ sanitize_key( $field->field_extra_option ) ] = $field->field_extra_option;
      }

      $colabs_taxonomies_obj = get_categories('taxonomy='.$field->field_values.'&hide_empty=0');
      foreach ($colabs_taxonomies_obj as $colabs_taxonomy) {
      $colabs_taxonomies[$colabs_taxonomy->cat_ID] = $colabs_taxonomy->cat_name;}
      
      if( $post ) {
        $post_tax = wp_get_post_terms( $post->ID, $field->field_values );
        if( $post_tax ) {
          $post_meta_val = $post_tax[0]->term_id;
        }
      }

      $disabled = '';
      if( $post ) {
        if( isset( $field->field_disabled_on_edit ) && $field->field_disabled_on_edit == true ) {
          $disabled = 'disabled';
        }
      }

      ?>
      <select name="<?php echo esc_attr($field->field_name); ?>" <?php echo $disabled; ?> id="<?php echo esc_attr($field->field_name); ?>" class="dropdownlist <?php if($field->field_req) echo 'required' ?>">
        <?php
        $options = $colabs_taxonomies;                
        foreach ( $options as $option=>$value ) {
          
          $selected = '';
          if ( $option == $post_meta_val )$selected = 'selected="selected"';
          ?>
          <option <?php echo $selected;?> value="<?php echo esc_attr($option); ?>"><?php echo esc_attr($value); ?></option>
          <?php
        }
        ?>
      </select>
      <?php
      break;

    // Textarea Field
    // --------------
    case 'textarea':
			$editor_id = 'editpost';
      if ( $field->field_name == 'post_content' && $post ) {
        $value = $post->post_content;
				$editor_id = $field->field_name.'-'.$post->ID;
      }else if ( isset($post_meta_val)&& $post_meta_val ) {
        $value = $post_meta_val;
      }else{
				$value = $field->field_values;
			}
			
      $rte_option = get_option( 'colabs_enable_rich_text_editor' );

      // Check for Rich-text editor option
      if( $rte_option === FALSE ) {
        wp_editor( $value, $editor_id, array('textarea_name'=>$field->field_name) );
      } else {
        if( $rte_option == 'true' ) {
          wp_editor( $value, $editor_id,array('textarea_name'=>$field->field_name) );
        } else {
          echo '<textarea name="'. $field->field_name .'" id="'. $editor_id .'" rows="10">'. $value .'</textarea>';
        }
      }

      break;

    // Radio Field
    // -----------
    case 'radio':
      $options = $field->field_values; ?>
      <ol class="radios">
        <?php if ( !$field->field_req ) { ?>
        <li>
          <input type="radio" name="<?php echo esc_attr($field->field_name); ?>" id="<?php echo esc_attr($field->field_name); ?>" class="radiolist" <?php if ( empty( $post_meta_val ) ) echo 'checked="checked"';?> value=""><?php _e('None', 'colabsthemes'); ?>
        </li> <!-- #radio-button -->
        <?php
        }
        foreach ( $options as $option=>$value ) {
          $checked = '';
          if ( $option == $post_meta_val )$checked = 'checked="checked"';
          ?>
          <li>
            <input type="radio" <?php echo $checked;?> name="<?php echo esc_attr($field->field_name); ?>" id="<?php echo esc_attr($field->field_name); ?>" value="<?php echo esc_attr($option); ?>" class="radiolist <?php if ($field->field_req) echo 'required'; ?>" ><?php echo trim(esc_attr($value)); ?>
          </li> <!-- #radio-button -->
        <?php
        }
        ?>
      </ol> <!-- #radio-wrap -->
      <?php
      break;
		
		// Checkbox Field
    // --------------
    case 'checkbox':
			$checked = '';
			$value = colabs_string_clean( $field->field_values );
      if ( $value == $post_meta_val )$checked = 'checked="checked"';
      ?>
      <input name="<?php echo esc_attr($field->field_name); ?>" <?php echo $checked;?> id="<?php esc_attr($field->field_name); ?>" type="checkbox" value="<?php echo $value; ?>" class="checkboxlist <?php if ($field->field_req) echo 'required' ?>" />
      <?php
      break;
			
    // Multicheck Field
    // --------------
    case 'multicheck':
      $options = $field->field_values;
      $optionCursor = 1;
      ?>
      <ol class="checkboxes">
        <?php
        foreach ( $options as $option=>$value ) {
          $checked = '';
          if ( $option == $post_meta_val )$checked = 'checked="checked"';
          ?>
          <li>
            <input type="checkbox" <?php echo $checked;?> name="<?php echo esc_attr($field->field_name); ?>[]" id="<?php echo esc_attr($field->field_name); echo '_'.$optionCursor++; ?>" value="<?php echo esc_attr($option); ?>" class="checkboxlist <?php if ($field->field_req) echo 'required' ?>" ><?php echo trim(esc_attr($value)); ?>
          </li> <!-- #checkbox -->
        <?php
        }
        ?>
      </ol> <!-- #checkbox-wrap -->
      <?php
      break;
      
    // Multicheck Field
    // --------------
    case 'multicheck-tax':
      if( !$post_meta_val ) {
        $post_meta_val = array();
      }
      // If there is extra option field, append the option
      if( isset( $field->field_extra_option ) ) {
        $colabs_taxonomies[''] = __('Select One', 'colabsthemes');
        $colabs_taxonomies[ sanitize_key( $field->field_extra_option ) ] = $field->field_extra_option;
      }

      $colabs_taxonomies_obj = get_categories('taxonomy='.$field->field_values.'&hide_empty=0');
      foreach ($colabs_taxonomies_obj as $colabs_taxonomy) {
      $colabs_taxonomies[$colabs_taxonomy->cat_ID] = $colabs_taxonomy->cat_name;}
      
      if( $post ) {
        $post_tax = wp_get_post_terms( $post->ID, $field->field_values );
        if( $post_tax ) {
          foreach ($post_tax as $item_tax){
            $post_meta_val[] = $item_tax->term_id;
          }
          
        }
      }

      $disabled = '';
      if( $post ) {
        if( isset( $field->field_disabled_on_edit ) && $field->field_disabled_on_edit == true ) {
          $disabled = 'disabled';
        }
      }
      $options = $colabs_taxonomies; 
      $optionCursor = 1;
      ?>
      <ol class="checkboxes">
        <?php
        foreach ( $options as $option=>$value ) {
          $checked = '';
          if ( in_array($option,$post_meta_val) )$checked = 'checked="checked"';
          ?>
          <li>
            <input type="checkbox" <?php echo $checked;?> name="<?php echo esc_attr($field->field_name); ?>[]" id="<?php echo esc_attr($field->field_name); echo '_'.$optionCursor++; ?>" value="<?php echo esc_attr($option); ?>" class="checkboxlist <?php if ($field->field_req) echo 'required' ?>" ><?php echo trim(esc_attr($value)); ?>
          </li> <!-- #checkbox -->
        <?php
        }
        ?>
      </ol> <!-- #checkbox-wrap -->
      <?php
      break;  

    // Gallery Field
    // -------------
    case 'gallery':
      $value = '';
			if($post_meta_val) $value = $post_meta_val;
      ?>
      <input name="<?php echo esc_attr($field->field_name); ?>" id="<?php esc_attr($field->field_name); ?>" type="hidden" value="<?php echo $value; ?>" class="text <?php if ($field->field_req) echo 'required' ?>">
      <a href="#" class="button button-secondary upload_button btn" title="Upload"><?php _e('Add Gallery','colabsthemes');?></a>

      <ul class="gallery-holder clearfix">
				<?php 
				if($post_meta_val){
				$attachments = array_filter( explode( ',', $post_meta_val ) );
				
				if ( $attachments )
					foreach ( $attachments as $attachment_id ) {
						echo '<li class="image" data-attachment_id="' . $attachment_id . '">
							' . wp_get_attachment_image( $attachment_id, 'medium' ) . '
							<a class="delete" title="Delete image" href="#">&times;</a>
						</li>';
					}
				}?>
			</ul>
      <?php
      break;

    // Upload Field
    // ------------
    case 'upload':
			$value = $field->field_values;
			if($post_meta_val)$value=$post_meta_val;
      ?>
      <input style="display:none" name="<?php echo esc_attr($field->field_name); ?>" id="<?php esc_attr($field->field_name); ?>" type="text" value="<?php echo $value; ?>" class="text <?php if ($field->field_req) echo 'required' ?>">
      <a href="#" class="button button-secondary upload_button btn" title="Upload"><?php _e('Upload','colabsthemes');?></a>
      <?php if($post_meta_val){
			$image_attributes = wp_get_attachment_image_src( $post_meta_val, 'large' );
			?>
			<div class="image-preview">
				<img src="<?php echo $image_attributes[0]; ?>">
				<a title="Delete image" class="delete" href="#">&times;</a>
			</div>
			<?php }
      break;

		// File Field
    // ------------
    case 'file':
			$value = $field->field_values;
			if($post_meta_val){
			if ( is_array( $post_meta_val ) )
				$value = implode( "\n", $post_meta_val );
			}	
      ?>
			<textarea id="<?php echo esc_attr($field->field_name); ?>" class="short file_paths <?php if ($field->field_req) echo 'required' ?>" wrap="off" name="<?php echo esc_attr($field->field_name); ?>" placeholder="<?php _e('File paths/URLs, one per line','colabsthemes');?>" rows="3" cols="20" ><?php echo esc_textarea( $value );?></textarea>
      <a href="#" class="btn btn-uppercase btn-full-color btn-bold upload_button" title="Upload"><?php _e('Choose a file','colabsthemes');?></a>
      <?php
      break;
		
    // Custom Field
    // ------------
    case 'custom_fields':
      ?>
      <a href="#" class="button button-secondary add_custom_fields"><?php echo $field->field_label; ?></a>

      <div class="custom-fields-list">
				<?php if($post_meta_val):?>
				<?php foreach($post_meta_val as $key=>$item):?>
					<div class="custom-fields-item">
					<a href="#" class="remove-item"><?php _e('Remove','colabsthemes');?></a>
					<?php if( isset($field->fields) && sizeof( $field->fields ) > 0 ) : ?>
						<?php foreach( $field->fields as $custom_fields ) : ?>
							<?php $custom_fields = (object) $custom_fields; 
							$custom_fields->field_values = $item[$custom_fields->field_name];
							$custom_fields->field_name = $field->field_name.'_'.$custom_fields->field_name.'['.$key.']';
							?>
							<div class="form-builder-input input-<?php echo $custom_fields->field_type; ?>">
								<label><?php esc_html($custom_fields->field_label); ?></label>
								<?php colabs_form_builder_field( $custom_fields ); ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					</div>
				<?php endforeach;?>	
				<?php endif;?>
			</div>

      <div class="custom-fields-placeholder" style="display:none">
        <a href="#" class="remove-item"><?php _e('Remove','colabsthemes');?></a>
        <?php if( isset($field->fields) && sizeof( $field->fields ) > 0 ) : ?>
          <?php foreach( $field->fields as $custom_fields ) : ?>
            <?php $custom_fields = (object) $custom_fields; ?>
            <div class="form-builder-input input-<?php echo $custom_fields->field_type; ?>">
              <label><?php esc_html($custom_fields->field_label); ?></label>
              <?php colabs_form_builder_field( $custom_fields ); ?>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <?php
      break;
  }
}

function colabs_string_clean($string) {
  $string = stripslashes($string);
  $string = trim($string);
  return $string;
}
?>