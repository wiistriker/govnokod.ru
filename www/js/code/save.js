(function($) {
    $(function() {
        var codeMirror = CodeMirror.fromTextArea($('#form_body')[0], {
            indentUnit: 4,
            lineNumbers: true,
            autofocus: true
        }); 
    });
})(jQuery);