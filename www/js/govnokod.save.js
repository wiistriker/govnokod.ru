(function($) {
    var linesCountLast;
    $(function() {
        var textareaObj = $('textarea.code').attr('wrap', 'off');
        var textareaLines = $("<ol/>").addClass('linenums');

        textareaObj.before(textareaLines);
        textareaObj.keyup(function() {
            var lines = $(this).val().split('\n');
            var linesCount = lines.length;

            if (linesCount > 100) {
                lines = lines.slice(0, 100);
                $(this).val(lines.join("\n"));
                linesCount = 100;
            }

            if (linesCountLast != linesCount) {
                linesCountLast = linesCount
                textareaLines.empty();
                var liString = '';
                for (var i = 1; i <= linesCount; i++) {
                    liString += '<li>' + i + '.</li>';
                }
                textareaLines.html(liString);

                $(this).css('height', textareaLines.height() + 16);
            }
        });
        textareaObj.keyup();
    });
})(jQuery);