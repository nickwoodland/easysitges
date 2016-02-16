(function($){

"use strict";

var form_builder = {};
form_builder.file_frame = {};
form_builder.el = {
  $body: $('body')
};

/* WP Media Uploader
------------------------------------------------------------------- */
form_builder.wp_media_uploader = function( $field, options, callback ) {
  this.file_frame = form_builder.file_frame;
  this.el = {
    $field: $field
  };
  this.options = options;
  this.callback = callback;

  this.file_type = {
    image: /(^.*\.jpg|jpeg|png|gif|ico*)/gi,
    document: /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi,
    audio: /(^.*\.mp3|m4a|ogg|wav*)/gi,
    video: /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi
  };

  this.event_binding = function() {
    form_builder.el.$body.on('click', this.el.$field.find('.upload_button').selector, $.proxy(this.clicked, this));
  };

  this.clicked = function(e) {
    e.preventDefault();
    this.create_uploader( $(e.currentTarget) );
  };

  this.create_uploader = function( $invoker ) {
    var _this = this,
        $field_input = $invoker.parent().find('input, textarea'),
        field_name = $field_input.attr('name');

    // If the media frame already exists, reopen it.
    if( this.file_frame[ field_name ] ) {
      this.file_frame[ field_name ].open();
      return;
    }

    // Create the media frame.
    var media_options = $.extend({
      title: formbuilder_string.media_upload.title,
      button: {
        text: formbuilder_string.media_upload.button_text
      },
      library: {
        type: 'image'
      },
      multiple: false
    }, this.options);

    this.file_frame[ field_name ] = wp.media.frames[ field_name ] = wp.media( media_options );

    // When file is selected, run a callback
    this.file_frame[ field_name ].on( 'select', function() {

      // If callback is defined
      if( typeof _this.callback == 'function' ) {
        _this.callback.apply( _this, [_this.file_frame[ field_name ], $field_input] );

      } else {
        var attachment = _this.file_frame[ field_name ].state().get('selection').first().toJSON();
        $field_input.val( attachment.id );

        // If image, append the preview
        if( attachment.url.match( _this.file_type.image ) ) {
          $field_input.parent().find('.image-preview').remove();
          $('<div class="image-preview"><img src="'+ attachment.url +'"><a href="#" class="delete" title="Delete image">&times;</a></div>').appendTo( $field_input.parent() );

          // If multiple is false, hide the upload button
          if( media_options.multiple == false ) {
            _this.el.$field.find('.upload_button').hide();
          }
        }

        // If audio
        else if( attachment.url.match( _this.file_type.audio ) || attachment.url.match( _this.file_type.video ) ) {
          var attachment = _this.file_frame[ field_name ].state().get('selection').first().toJSON();
          $field_input.val( attachment.url );
          $field_input.show();
        }
      }
    });

    // Open the modal
    this.file_frame[ field_name ].open();
  }

  this.event_binding();
}

/* Upload Fields
------------------------------------------------------------------- */
form_builder.upload_fields = function( el ) {
  if( !el ) return;

  this.$el = $(el);

  this.event_binding = function() {
    this.$el.on('click', '.delete', $.proxy(this.remove_upload, this));
  };

  this.remove_upload = function(e) {
    e.preventDefault();
    var $delete = $(e.currentTarget),
        $imagePreview = $delete.parent('.image-preview'),
        $input = $imagePreview.siblings('input');

    $input.val('');
    $imagePreview.siblings('.upload_button').show();
    $imagePreview.remove();
  };

  this.hide_or_show_input = function() {
    this.$el.each(function(){
      // Check if number of not
      if( isNaN($(this).find('input').val() * 1) ) {
        $(this).find('input').show();
      }
    });
  };

  this.init = function() {
    var uploader = new form_builder.wp_media_uploader( this.$el );
    this.event_binding();
    this.hide_or_show_input();
  };

  this.init();
};

/* Gallery Fields
------------------------------------------------------------------- */
form_builder.gallery_fields = function( el ) {
  if( !el ) return;

  this.$el = $(el);

  this.options = {
    title: formbuilder_string.gallery_upload.title,
    library: {
      type: 'image'
    },
    button: {
      text: formbuilder_string.gallery_upload.button_text
    },
    multiple: true
  };

  this.callback = function( frame, $field_input ) {
    var selection = frame.state().get('selection'),
        attachment_ids = $field_input.val();

    selection.map( function( attachment ) {
      attachment = attachment.toJSON();

      if ( attachment.id ) {
        attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;

        $field_input.siblings('.gallery-holder').append('\
          <li class="image" data-attachment_id="' + attachment.id + '">\
            <img src="' + attachment.url + '" />\
            <a href="#" class="delete" title="Delete image">&times;</a>\
          </li>');
      }
    } );

    $field_input.val( attachment_ids );
  };

  this.event_binding = function() {
    this.$el.on('click', '.delete', $.proxy(this.remove_image, this));
  };

  this.remove_image = function(e) {
    $(e.currentTarget).closest('li.image').remove();

    var attachment_ids = '';

    this.$el.find('li.image').css('cursor','default').each(function() {
      var attachment_id = $(this).attr( 'data-attachment_id' );
      attachment_ids = attachment_ids + attachment_id + ',';
    });

    this.$el.find(':hidden').val( attachment_ids );

    return false;
  };

  this.init = function() {
    var gallery = new form_builder.wp_media_uploader( this.$el, this.options, this.callback );
    this.event_binding();
  }

  this.init();
};

/* Multiple File Fields
------------------------------------------------------------------- */
form_builder.file_fields = function( el ) {
  if( !el ) return;

  this.$el = $(el);

  this.options = {
    title: formbuilder_string.file_upload.title,
    button: {
      text: formbuilder_string.file_upload.button_text
    },
    multiple: true
  };

  this.callback = function( frame, $field_input ) {
    var selection = frame.state().get('selection'),
        attachment_val = $field_input.val();

    selection.map( function( attachment ) {
      attachment = attachment.toJSON();

      if ( attachment.url ) {
        attachment_val = attachment_val ? attachment_val + "\n" + attachment.url : attachment.url;
      }
    } );

    $field_input.val( attachment_val );
  };

  this.init = function() {
    var files = new form_builder.wp_media_uploader( this.$el, this.options, this.callback );
  };

  this.init();
};

/* Custom Fields
------------------------------------------------------------------- */
form_builder.custom_fields = function( el ) {
  if( !el ) return;

  this.$el = $(el);

  this.event_binding = function() {
    this.$el.on('click', '.add_custom_fields', $.proxy(this.clone_input, this));
    this.$el.on('click', '.remove-item', $.proxy(this.remove_item, this));
  };

  this.setup_input_name = function( $placeholder ) {
    var index = $placeholder.siblings().length;

    $placeholder.find('[name]').each(function(){
      $(this).attr('name', $(this).attr('name') + '['+ index +']');
    });
  };

  this.clone_input = function(e) {
    e.preventDefault();

    var $input_placeholder = $(e.currentTarget).parent().find('.custom-fields-placeholder');
    var $fields_list = $(e.currentTarget).parent().find('.custom-fields-list');
    var $clone = $input_placeholder.clone();

    $clone.appendTo( $fields_list ).show().removeClass('custom-fields-placeholder').addClass('custom-fields-item');
    this.setup_input_name( $clone );
  };

  this.remove_item = function(e) {
    e.preventDefault();
    var $item = $(e.currentTarget).parent();

    // Find the upload or gallery field and remove file frame reference
    $item.find('.input-upload, .input-gallery').each(function(){
      var input_name = $(this).find('input').attr('name');

      if( form_builder.file_frame[ input_name ] ) {
        delete form_builder.file_frame[ input_name ];
      }
    });

    $item.remove();
  };

  this.event_binding();
};

/* Form Submit
------------------------------------------------------------------- */
form_builder.form_submit = {
  el: {},
  is_validated: false,

  is_number: function( value ) {
    return $.isNumeric( $.trim( value ) );
  },

  is_email: function( value ) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( $.trim( value ) );
  },

  validate: function() {
    var _this = this,
        validatedFields = [];

    // Remove all error box
    this.el.$form.find('.alert.alert-error').remove();

    this.el.$form.find('[name]').each(function(){
      var $input = $(this);

      // Check for required field
      if( $input.hasClass('required') ) {
        if( $.trim( $input.val() ) == '' || $input.val() === null ) {
          validatedFields.push(false);
          _this.append_error( $input );
        } else {
          validatedFields.push(true);
        }
      }

      // Check for number input
      if( $input.parent('.form-builder-input').hasClass('field-number') ) {
        if( $.trim( $input.val() ) != '' ) {
          if( _this.is_number( $input.val() ) ) {
            validatedFields.push(true);
          } else {
            validatedFields.push(false);
            _this.append_error( $input, formbuilder_string.validator.number );
          }
        }
      }

      // Check for email input
      if( $input.parent('.form-builder-input').hasClass('field-email') ) {
        if( $.trim( $input.val() ) != '' ) {
          if( _this.is_email( $input.val() ) ) {
            validatedFields.push(true);
          } else {
            validatedFields.push(false);
            _this.append_error( $input, formbuilder_string.validator.email );
          }
        }
      }
    });

    // Check if all fields are validated
    if( $.inArray( false, validatedFields ) != -1 ) {
      _this.is_validated = false;
    }

    // All field validated
    else {
      _this.is_validated = true;
    }
  },

  append_error: function( $input, message ) {
    if( typeof $input == 'undefined' ) return;

    if( message == '' || typeof message == 'undefined' ) {
      message = formbuilder_string.validator.required;      
    }

    $('<div class="alert alert-error validation">'+ message +'</div>').appendTo( $input.parent() );
  },

  event_binding: function() {
    // this.el.$submit_button.on('click', $.proxy(this.submit_form, this));
    this.el.$form.on('submit', $.proxy(this.submit_form, this));
    this.el.$form.on('change', '#product-type', $.proxy(this.input_change, this));
    this.el.$form.on('change', '[name="_downloadable"]', $.proxy(this.input_change, this));
  },

  submit_form: function(event) {
    event.preventDefault();

    // Prevent submitting the form when user press enter key on
    // no-submit field
    if( !this.el.$form.find(document.activeElement).hasClass('no-submit') ) {

      this.el.$form.trigger('beforeValidate', [this]);

      this.validate();

      if( this.is_validated ) {
        // Remove all custom-fields-placeholder for prevent duplication
        this.el.$form.find('.custom-fields-placeholder').remove();

        // Submit the form
        // $('<input type="hidden" name="submit_product_form" value="Submit">').appendTo( this.el.$form );
        // $('<input type="hidden" name="edit_product_form" value="Submit">').appendTo( this.el.$form );

        // Bug on firefox, name of input type submit not sent to server
        if( $('body').hasClass('gecko') || $('body').hasClass('ie') ) {
          var $submit = this.el.$form.find(':submit');
          $('<input type="hidden" name="'+ $submit.attr('name') +'" value="'+ $submit.val() +'">').appendTo( this.el.$form );
        }

        this.el.$form.trigger('beforeSubmit');

        this.el.$form.off('submit');
        this.el.$form.submit();
        return false;
      } 

      // Scroll to error
      else {
        this.el.$html.animate({
          scrollTop: this.el.$form.find('.alert-error:first').offset().top - 50
        });
      }

    }

  },

  input_change: function(event) {
    var input = event.currentTarget,
        value = input.value;

    switch ( input.type ) {
      case 'select-one':
        this.change_product_type( value );
        break;

      case 'checkbox':
        this.change_downloadable( input.checked );
        break;
    }
  },

  change_product_type: function( product_type ) {
    if( product_type == '' ) return;

    this.el.$form.find('[class*="is_"]').hide();
    this.el.$form.find('.is_' + product_type).show();
    this.change_downloadable( this.el.$form.find('[name="_downloadable"]').prop('checked') );
  },

  change_downloadable: function( checked ) {
    if( checked ) {
      this.el.$form.find('.is_downloadable').show();
    } else {
      this.el.$form.find('.is_downloadable').hide();
    }
  },

  init: function( form ) {
    if( typeof form == 'undefined' ) return;

    this.el.$form = $(form);
    this.el.$html = $('html,body');
    this.el.$submit_button = $('#submit_product_form, #edit_product_form');

    this.event_binding();

    this.change_product_type( this.el.$form.find('#product-type').val() );
    this.change_downloadable( this.el.$form.find('[name="_downloadable"]').prop('checked') );
  }
};

/* Initiate
------------------------------------------------------------------- */
form_builder.init = function() {
  new form_builder.upload_fields('.input-upload');
  new form_builder.gallery_fields('.input-gallery');
  new form_builder.custom_fields('.input-custom_fields');
  new form_builder.file_fields('.input-file');
  this.form_submit.init('#submit_form');
}

$(document).ready(function(){
  form_builder.init();
});

})(jQuery);