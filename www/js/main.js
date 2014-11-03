new Image().src = '/images/commentsload.gif';
new Image().src = '/images/codeload.gif';
new Image().src = '/images/rating/codevote.gif';
new Image().src = '/images/rating/commentvote.gif';

(function($) {
    $(function() {
        $('time.timeago').timeago();
        //hljs.initHighlighting();

        var comments = $('div.entry-comments');

        comments.on('click', 'a.post-comment', function(e) {
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
                    newFormHolder.show();
                    
                    commentsHolder.find('a.selected').removeClass('selected');
                    $(this).addClass('selected');
                    
                    formNode.find('dl.errors').remove();
                    formNode.attr('action', $(this).attr('href'));
                    
                    newFormHolder.append(formNode);
                    
                    formNode.find('textarea').focus();
                    $.scrollTo(formNode, 500, { offset: -200 });
                } else {
                    $(this).removeClass('selected');
                    newFormHolder.hide();
                }
            }
            
            e.preventDefault();
        });

        comments.on('submit', 'form', function(e) {
            var commentsHolder = $(this).closest('div.entry-comments');

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
                        var newCommentHolder;
                        if (formParent.is('div.root-comment-form-holder')) {
                            newCommentHolder = commentsHolder.find('ul.comments-list');
                        } else {
                            newCommentHolder = formParent.closest('li.hcomment').find('ul');
                        }

                        newCommentHolder.append(newNode);
                        formParent.empty();

                        $.scrollTo(newNode, 500, { offset: -100 });

                        newCommentHolder.find('time.timeago').timeago();
                        commentsHolder.find('a.post-comment.selected').removeClass('selected');

                        var commentsCountHolder = commentsHolder.find('span.entry-comments-count');
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
        }).on('keydown', 'textarea', function(e) {
            if (e.ctrlKey && e.keyCode == 13) {
                $(this).closest('form').trigger('submit');
                e.preventDefault();
            }
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

                    commentsHolder.find('time.timeago').timeago();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    preloader.remove();
                    alert("Ошибка загрузки списка комментариев!\nОбновите страницу и попытайтесь еще раз");
                }
            });
            
            e.preventDefault();
        });

        if (typeof ga != 'undefined') {
            $('.ga-event').live('click', function(e) {
                var ga_event_string = $(this).data('ga-event');
                if (ga_event_string) {
                    var gaEventDataList = ga_event_string.split(';');

                    if (gaEventDataList && gaEventDataList.length > 0) {
                        $.each(gaEventDataList, function(index, ga_event_data_string) {
                            var gaData = ga_event_data_string.split('|');
                            if (gaData && gaData.length) {
                                gaData.unshift('event');
                                gaData.unshift('send');

                                ga.apply(this, gaData);
                            }
                        });
                    }
                }
            });
        }

        $('p.vote a').click(function(e) {
            var self = $(this);
            var holder = self.closest('p.vote');
            if (holder) {
                var preloader = $('<img src="/images/rating/codevote.gif" class="preloader" alt="Загрузка" title="Идёт учет голоса…" />');
                holder.html(preloader);

                setTimeout(function() {
                    preloader.remove();
                }, 2000);

                $.ajax({
                    url: self.attr('href'),
                    type: 'POST',
                    success: function(msg) {
                        holder.html(msg);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        preloader.remove();
                        alert("Ошибка учета голоса!\nОбновите страницу и попытайтесь еще раз");
                    }
                });
            }

            e.preventDefault();
        });

        comments.on('click', 'span.comment-vote a', function(e) {
            var self = $(this);
            var holder = self.closest('span.comment-vote');
            if (holder) {
                var preloader = $('<img src="/images/rating/commentvote.gif" class="preloader" alt="Загрузка" title="Идёт учет голоса…" />');
                holder.html(preloader);

                setTimeout(function() {
                    preloader.remove();
                }, 2000);

                $.ajax({
                    url: self.attr('href'),
                    type: 'POST',
                    success: function(msg) {
                        holder.html(msg);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        preloader.remove();
                        alert("Ошибка учета голоса!\nОбновите страницу и попытайтесь еще раз");
                    }
                });
            }

            e.preventDefault();
        });

        if (typeof document.location.hash != 'undefined') {
            var commentAnchorMatch = document.location.hash.match(/^#comment(\d+)$/);
            if (commentAnchorMatch) {
                var commentNode = $('#comment-' + commentAnchorMatch[1]);
                if (commentNode.length) {
                    commentNode.css('background-color', '#EAF2B8');
                }
            }
        }

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
    });
})(jQuery);