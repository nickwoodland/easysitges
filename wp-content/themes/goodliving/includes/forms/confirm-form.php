<?php
if ( colabs_charge_listings() ){
  colabs_load_template('/includes/forms/confirm-form-charge.php', array('order' => $_GET['order'], 'step' => $step));
}else{
  colabs_load_template('/includes/forms/confirm-form-nocharge.php', array('property_id' => $_GET['property_id'], 'step' => $step));
}
