(function($){
  $(function(){
    var $wrap = $('.pl-wrap');
    if (!$wrap.length) return;

    function fetchCards(){
      var data = {
        action: 'pl_fetch_prompts',
        nonce: window.plNonce || '',
        category: $('#pl-filter-category').val() || '',
        tag: $('#pl-filter-tag').val() || ''
      };
      $.post($('#pl-cards').data('ajax-url'), data, function(resp){
        var $c = $('#pl-cards').empty();
        if (!resp || !resp.success || !resp.data || !resp.data.cards) return;
        resp.data.cards.forEach(function(card){
          var h = '<div class="pl-card">'
                + '<h3>'+ (card.title || '') +'</h3>'
                + '<p>'+ (card.excerpt || '') +'</p>'
                + '</div>';
          $c.append(h);
        });
      });
    }

    $('#pl-filter-category, #pl-filter-tag').on('change', fetchCards);
    fetchCards();
  });
})(jQuery);
