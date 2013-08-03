var SITE_PATH = '';

var commentsPreloader = new Image();
commentsPreloader.src = SITE_PATH + '/images/commentsload.gif';

var codePreloader = new Image();
codePreloader.src = SITE_PATH + '/images/codeload.gif';

var votePreloader = new Image();
votePreloader.src = SITE_PATH + '/images/govnovote.gif';

var commentVotePreloader = new Image();
commentVotePreloader.src = SITE_PATH + '/images/commentvote.gif';

var comments;
var code;
(function($) {
    comments = {
        load: function (aElemTrigger) {
            var commentsLoadUrl = aElemTrigger.attr('href');

            var commentsHolder = aElemTrigger.parent();
            aElemTrigger.replaceWith(document.createTextNode(aElemTrigger.text()));

            var preloader = $('<img src="' + commentsPreloader.src + '" alt="Загрузка" title="Загрузка списка комментариев…" />');
            commentsHolder.append(preloader);

            $.ajax({
                url: commentsLoadUrl,
                data: {onlyComments: true},
                success: function(msg){
                    commentsHolder.fadeOut(0);
                    commentsHolder.html(msg);
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
        },

        moveForm: function(commentId, folderId, aElemTrigger) {
            if ($(aElemTrigger).hasClass('selected')) {
                return false;
            }

            var formElem = $('#commentForm_' + folderId);
            var nowHolder = $('#answerForm_' + folderId + '_' + commentId);

            if (formElem && nowHolder) {
                var errors = formElem.find('dl.errors');
                if (errors) {
                    errors.remove();
                }

                nowHolder.append(formElem);

                $(aElemTrigger).closest('div.entry-comments').find('a.selected').removeClass('selected');

                $(aElemTrigger).addClass('selected');

                formElem.show();
                formElem.attr('action', aElemTrigger.href);
                formElem.find('textarea').focus();

                $.scrollTo(formElem, 500, {offset: -200});
                //$("html:not(:animated),body:not(:animated)").animate({scrollTop: formElem.offset().top - 200}, 500);
            }
        },

        postForm: function(formElem) {
            formElem = $(formElem);
            var formParent = formElem.parent();
            var reg = /^answerForm_(\d+)_(\d+)$/;
            var matches = formParent.attr('id').match(reg);
            if (matches) {
                var formUrl = formElem.attr('action');
                var formData = formElem.serialize();

                formElem.find('input, textarea, select').attr('disabled', 'disabled');
                var folderId = matches[1];
                var replyTo = matches[2];

                var baseHolder = (replyTo == 0) ? $('#comments_' + folderId) : formElem.closest('.hcomment');
                $.ajax({
                    url: formUrl,
                    type: "POST",
                    data: formData + '&ajax=true',
                    success: function(msg) {
                        if (baseHolder) {
                            formParent.empty();
                            if (msg.match(/<li class="hcomment new">/)) {
                                baseHolder.closest('div.entry-comments').find('a.selected').removeClass('selected');

                                if (replyTo != 0) {
                                    baseHolder.append($('<ul/>').append(msg));
                                } else {
                                    baseHolder.append(msg);
                                }

                                var newComment = baseHolder.find('li.new');
                                if (newComment) {
                                    newComment.removeClass('new');
                                    $.scrollTo(newComment, 500, {offset: -100});
                                    //$("html:not(:animated),body:not(:animated)").animate({scrollTop: newComment.offset().top - 100}, 500);

                                    var commentsCountHolder = $(newComment).closest('div.entry-comments').find('span.enrty-comments-count');
                                    if (commentsCountHolder.length) {
                                        var count_re = /^\((\d+)\)$/;
                                        var comments_count_match = commentsCountHolder.text().match(count_re);
                                        if (comments_count_match) {
                                            var comments_count = comments_count_match[1];
                                            commentsCountHolder.text('(' + ++comments_count + ')');
                                        }
                                    }
                                }
                            } else {
                                formParent.html(msg);
                            }
                        }
                    }
                });
            }
        },

        vote: function(aElemTrigger) {
            var holder = aElemTrigger.closest('span.comment-vote');
            if (holder) {
                var voteUrl = aElemTrigger.attr('href');
                var preloader = $('<img src="' + commentVotePreloader.src + '" class="preloader" alt="Загрузка" title="Идёт учет голоса…" />');
                holder.html(preloader);

                $.ajax({
                    url: voteUrl,
                    data: {isAjax: true},
                    success: function(msg) {
                        holder.html(msg);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        preloader.remove();
                        alert("Ошибка учета голоса!\nОбновите страницу и попытайтесь еще раз");
                    }
                });
            }
        },

        toggleBBCodeBlock: function(aElemTrigger) {
            $(aElemTrigger).parent().find('.bbcodes').toggle();
        },

        handleCtrEnter: function(event, formElem) {
            if ((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
                $(formElem).find('input[name=commentSubmit]').trigger('click');
            }
        }
    }

    code = {
        unfold: function(aElemTrigger) {
            var codeLoadUrl = aElemTrigger.attr('href');
            var entryHolder = aElemTrigger.parent('.entry-content');
            if (entryHolder) {
                var preloader = $('<img src="' + codePreloader.src + '" class="preloader" alt="Загрузка" title="Загрузка кода…" />');
                aElemTrigger.replaceWith(preloader);

                $.ajax({
                    url: codeLoadUrl,
                    data: {format: 'ajax'},
                    success: function(msg) {
                        var currentHeight = entryHolder.height();
                        entryHolder.html(msg);
                        var nowHeight = entryHolder.height();
                        entryHolder.css({overflow: 'hidden', height: currentHeight});
                        entryHolder.animate({height: nowHeight}, 400, function() { $(this).removeAttr('style'); });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        preloader.remove();
                        alert("Ошибка загрузки кода!\nОбновите страницу и попытайтесь еще раз");
                    }
                });
            }
        },

        vote: function(aElemTrigger) {
            var holder = aElemTrigger.closest('p.vote');
            if (holder) {
                var voteUrl = aElemTrigger.attr('href');
                var preloader = $('<img src="' + votePreloader.src + '" class="preloader" alt="Загрузка" title="Идёт учет голоса…" />');
                holder.html(preloader);

                $.ajax({
                    url: voteUrl,
                    data: {isAjax: true},
                    success: function(msg) {
                        holder.html(msg);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        preloader.remove();
                        alert("Ошибка учета голоса!\nОбновите страницу и попытайтесь еще раз");
                    }
                });
            }
        }
    }

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
                            formParent.html(newNode);
                        } else {
                            formParent.empty();
                            var parentCommentNode = formParent.closest('li.hcomment');
                            parentCommentNode.append($('<ul />').append(newNode));
                        }
                        
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
        
        $('a.entry-comments-load').click(function() {
            comments.load($(this));
            return false;
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