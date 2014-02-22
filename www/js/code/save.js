(function($) {
    $(function() {
        CodeMirror.modeURL = '/codemirror-3.11/mode/%N/%N.js';

        var editor = CodeMirror.fromTextArea($('#form_body')[0], {
            indentUnit: 4,
            tabMode: 'spaces',
            lineNumbers: true,
            autofocus: true,
            highlightSelectionMatches: true,
            matchBrackets: true
        });

        $('#form_category')
            .on('change', function() {
                var id = $(this).val();
                var highlighter = govnokod.highlighters[id];

                if (highlighter) {
                    CodeMirror.requireMode(highlighter.mode, function () {
                        editor.setOption('mode', highlighter.mime);
                    });
                }
            })
            .change();

        var tagsHolder = $('#tags');

        tagsHolder.data('index', tagsHolder.find(':input').length);

        var newTagTrigger = $('<a href="#">add</a>');

        newTagTrigger.click(function(e) {
            var prototype = tagsHolder.data('prototype');
            var index = tagsHolder.data('index');
            var newTag = prototype.replace(/__name__/g, index);

            tagsHolder.data('index', index + 1);

            var newTagLi = $('<li></li>').append(newTag);
            newTagTrigger.before(newTagLi);
            e.preventDefault();
        });

        tagsHolder.append($('<li />').append(newTagTrigger));
    });
})(jQuery);