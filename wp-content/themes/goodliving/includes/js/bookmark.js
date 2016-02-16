//handles ajax call add to bookmark
function call_ajax_add_to_bookmark(url) {
	jQuery.ajax({
	 type: 'POST',
	  url: url,
	  data: {post_id: jQuery('.widget_property_bookmark').data('property-id'),user_id: jQuery('.widget_property_bookmark').data('user-id')},
	  success: function( response ) {
					var msg = jQuery('.messagebookmark');
					jQuery('.messagebookmark').html(response);
					msg.fadeIn();
					jQuery('.bookmarkadded').css('display','none');
					jQuery('.bookmarkaddedbrowse').css('display','block');
		}

	});
}
//handles ajax request on removing product from wishlist
function remove_item_from_bookmark(url,bookmark_id, rowid) {

	jQuery.ajax({
	 type: 'POST',
	  url: url,
		data: {bookmark_id:bookmark_id },
		beforeSend: function() {
			jQuery('#bookmark-row-' + rowid).fadeTo(300, 0.5).addClass('loading');
		},
		success: function( response ) {
			jQuery('#bookmark-row-' + rowid).animate({
				opacity: 0
			}, 300, function(){
				jQuery(this).slideUp()
			});
		}
	  /* success: function( response ) {

			jQuery("#"+rowid).remove();	
			arr = response.split('##');
			jQuery('#dvin_messages').html(arr[0]);
			
			//if div exists reflect the total count
			if(jQuery('#dvin-wishlist-count').length>0) {
				jQuery('#dvin-wishlist-count').html(arr[1]);
			}

			//display no products message, if exists
			if(arr[2]!='') {
				jQuery('.cart').append('<tr><td colspan="6"><center>'+arr[2]+'</center></td></tr>');	
			}
		} */

	});
}