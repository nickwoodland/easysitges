<form action="" method="POST" class="submit_form main_form">
  <h5><?php _e('Thank You','colabsthemes');?></h5>
  <p><?php _e('Your property is ready to be submitted, please confirm the details are correct and click the "Confirm" button to submit your property.','colabsthemes');?></p>
  
  <br>
  <div class="form-builder-input input-submit">
    <input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>"/>
		<input type="hidden" name="ID" value="<?php echo esc_attr( $property_id ); ?>">
    <input type="submit" name="goback" class="button button-bold button-uppercase button-grey button-wide" value="<?php _e( 'Go Back', 'colabsthemes' ) ?>"  />
    <input type="submit" class="button button-bold button-uppercase button-green button-wide" name="property_confirm" value="Confirm">
  </div>
</form>  