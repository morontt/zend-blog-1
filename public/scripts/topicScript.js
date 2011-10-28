jQuery(document).ready(function() {
    if (formHide) {
        jQuery('#comment_add').hide();
    }
    
    jQuery('#toggle_comment_add').click(function() {
        jQuery('#comment_add').slideToggle('slow');
    });
    
    jQuery('#submit').click(function() {
        var dataForm = jQuery('#CommentForm').serialize();
        jQuery.ajax({
            url: '/index/ajaxcomment',
            data: dataForm,
            dataType: 'json',
            type: 'POST',
            success: function(data) {
                var divComment = '<div class="comment_item"><div class="comment_title">';
                if (data.link == 'none') {
                    divComment += '<b>' + data.name + '</b>';
                } else {
                    if (data.reg) {
                        divComment += '<a href="' + data.link + '">' + data.name + '</a>';
                    } else {
                        divComment += '<a class="notregistered" href="' + data.link + '">' + data.name + '</a>';
                    }
                }
                divComment += ' : <div class="comment_datetime">' + data.time + '</div></div>';
                divComment += '<div class="comment_text">' + data.text + '</div>';
                divComment += '</div></div>';
                jQuery('#comment_add').slideToggle('slow');
                jQuery(divComment).appendTo('#commentList');
                jQuery('#captcha-element').load(linkCommentBlock);
            },
            error: function(data) {
                jQuery('#CommentForm').html(data.responseText);
            }
        });
        return false;
    });
});