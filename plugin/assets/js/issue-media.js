(function($){
  'use strict';
  $(function(){
    $('.docszine-media-button').on('click', function(event){
      event.preventDefault();
      var button = $(this);
      var target = $(button.data('target'));
      var frame = wp.media({
        title: 'Select Issue cover',
        button: { text: 'Use cover' },
        library: { type: 'image' },
        multiple: false
      });
      frame.on('select', function(){
        var item = frame.state().get('selection').first().toJSON();
        target.val(item.id).trigger('change');
        var preview = button.siblings('.docszine-cover-preview');
        if(!preview.length){ preview = $('<img>', { class: 'docszine-cover-preview', alt: '', style: 'display:block;max-width:120px;height:auto;margin-top:8px' }).insertAfter(button); }
        preview.attr('src', item.sizes && item.sizes.thumbnail ? item.sizes.thumbnail.url : item.url);
      });
      frame.open();
    });
  });
})(jQuery);
