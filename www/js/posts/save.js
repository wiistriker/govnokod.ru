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

        var locked_tags = [ 'lol', 'php' ];

        var tagsField = $('#form_tags');
        tagsField.select2({
            width: '60%',
            tags: [],
            /*
            data: function() {
                return { text: 'tag', results: [ { id: 'php', text: 'php', locked: true }, { id: 'java', text: 'java' } ] }
            },
            */
            initSelection : function (element, callback) {
                var data = [];
                $(element.val().split(',')).each(function () {
                    var tag_value = this.toString();

                    data.push({ id: tag_value, text: tag_value, locked: locked_tags.indexOf(tag_value) > -1 });
                });

                callback(data);
            },
            tokenSeparators: [',']
        });
    });
})(jQuery);