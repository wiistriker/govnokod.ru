(function($) {
    $(function() {
        var codeMirror = CodeMirror.fromTextArea($('#form_body')[0], {
            indentUnit: 4,
            tabMode: 'spaces',
            lineNumbers: true,
            autofocus: true,
            highlightSelectionMatches: true
        }); 
    });
})(jQuery);