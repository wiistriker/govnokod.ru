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
    });
})(jQuery);