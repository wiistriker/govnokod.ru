new Image().src = '/images/commentsload.gif';
new Image().src = '/images/codeload.gif';
new Image().src = '/images/govnovote.gif';
new Image().src = '/images/commentvote.gif';

(function($) {
    $(function() {
        $('time.timeago').timeago();
        
        $('div.entry-comments').on('click', 'a.post-comment', function(e) {
            var commentsHolder = $(this).closest('div.entry-comments');
            var newFormHolder;
            
            var reply_id = $(this).data('reply-to');
            if (reply_id) {
                newFormHolder = $('#reply_form_holder_' + reply_id);
            } else {
                newFormHolder = commentsHolder.find('div.root-comment-form-holder');
            }
            
            if (newFormHolder) {
                var formNode = commentsHolder.find('form');
                
                if (!$(this).hasClass('selected')) {
                    formNode.show();
                    
                    commentsHolder.find('a.selected').removeClass('selected');
                    $(this).addClass('selected');
                    
                    formNode.find('dl.errors').remove();
                    formNode.attr('action', $(this).attr('href'));
                    
                    newFormHolder.append(formNode);
                    
                    formNode.find('textarea').focus();
                    $.scrollTo(formNode, 500, { offset: -200 });
                } else {
                    $(this).removeClass('selected');
                    formNode.hide();
                }
            }
            
            e.preventDefault();
        });
        
        $('div.entry-comments').on('submit', 'form', function(e) {
            var form = $(this);
            var form_data = form.serialize();
            
            var formParent = form.parent();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form_data,
                success: function(response) {
                    var newNode = $(response);
                    
                    if (newNode.is('form')) {
                        formParent.html(newForm);
                    } else if (newNode.is('li.hcomment')) {
                        if (formParent.is('div.root-comment-form-holder')) {
                            var commentsRootNode = formParent.closest('div.entry-comments').find('ul.comments-list');
                            commentsRootNode.append(newNode);
                        } else {
                            var parentCommentNode = formParent.closest('li.hcomment');
                            var parentCommentAnswersList = parentCommentNode.find('ul.answers-list');
                            if (parentCommentAnswersList.count == 0) {
                                parentCommentAnswersList = $('<ul />').addClass('answers-list');
                                parentCommentNode.append(parentCommentAnswersList);
                            }

                            parentCommentAnswersList.append(newNode);
                        }

                        formParent.empty();
                        
                        $.scrollTo(newNode, 500, { offset: -100 });
                        
                        var commentsCountHolder = formParent.closest('div.entry-comments').find('span.entry-comments-count');
                        if (commentsCountHolder.length) {
                            var comments_count = parseInt(commentsCountHolder.text());
                            if (comments_count != NaN) {
                                commentsCountHolder.text(comments_count + 1);
                            }
                        }
                    }
                },
                error: function() {
                    alert('Ошибка при добавлении комментария. Пожалуйста, перезагрузите страницу и повторите попытку');
                }
            });
            e.preventDefault();
        });
        
        $('a.entry-comments-load').click(function(e) {
            var commentsHolder = $(this).parent();
            $(this).replaceWith(document.createTextNode($(this).text()));

            var preloader = $('<img src="/images/commentsload.gif" alt="Загрузка" title="Загрузка списка комментариев…" />');
            commentsHolder.append(preloader);

            $.ajax({
                url: $(this).data('load-url'),
                success: function(response){
                    commentsHolder.fadeOut(0);
                    commentsHolder.html(response);
                    commentsHolder.fadeIn(300);

                    //Если подключен jshighlight, то ищем все [code] теги в комментариях и пробуем их подсветить
                    if (typeof(hljs) == 'object') {
                        commentsHolder.find('pre code').each(function() {
                            hljs.highlightBlock(this);
                        });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    preloader.remove();
                    alert("Ошибка загрузки списка комментариев!\nОбновите страницу и попытайтесь еще раз");
                }
            });
            
            e.preventDefault();
        });

        $('p.vote a').live('click', function() {
            code.vote($(this));
            return false;
        });

        $('span.comment-vote a').live('click', function() {
            comments.vote($(this));
            return false;
        });

        $('div.entry-content a.trigger').click(function(){
            code.unfold($(this));
            return false;
        });

        $('#expand-trigger').click(function() {
            $('#userpane').toggleClass('expanded');
            return false;
        });

        $('span.hidden-text a.ajax').live('click', function() {
            $(this).closest('div.entry-comment-hidden').removeClass('entry-comment-hidden');
            return false;
        });

        $('a.edit-comment-link').live('click', function() {
            var holder = $(this).parent().find('div.entry-comment');

            var edit_url = $(this).attr('href');
            $(this).remove();

            var preloader = $('<img src="' + SITE_PATH + '/images/comments/edit-preload.gif" alt="Загрузка" title="Идёт загрузка формы…" />');
            holder.html(preloader);

            $.ajax({
                url: edit_url,
                data: {ajax: true},
                success: function(msg) {
                    holder.html(msg);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    preloader.remove();
                    alert("Ошибка загрузки формы редактирования!\nВозможно, что отведенное на редактирование время истекло.\nОбновите страницу и попытайтесь еще раз");
                }
            });

            return false;
        });

        $('form.edit-comment-form').live('submit', function() {
            var formElem = $(this);
            var formData = formElem.serialize();
            formElem.find('input, textarea, select').attr('disabled', 'disabled');

            $.ajax({
                url: formElem.attr('action'),
                type: "POST",
                data: formData + '&ajax=true',
                success: function(msg) {
                    formElem.replaceWith(msg);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    alert("Ошибка редактирования!\nВозможно, что отведенное на редактирование время истекло.\nОбновите страницу и попытайтесь еще раз");
                }
            });

            return false;
        });

        //Если вдруг подключили js highlight драйвер
        if (typeof(hljs) == 'object') {
            hljs.initHighlighting();
        }
    });
})(jQuery);