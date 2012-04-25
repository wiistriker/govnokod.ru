(function($) {
    $(function() {
        $('div.show-code a.show-code-trigger').click(function() {
            var code_holder = $(this).next('div.code-holder');
            code_holder.toggle();

            if (code_holder.css('display') == 'none') {
                $(this).text('Показать код ▼');
            } else {
                $(this).text('Скрыть код ▲');
            }
            return false;
        });
    });
})(jQuery);