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
                //jQuery('#commentList').load(linkCommentBlock);
                //jQuery('#comment_add').slideToggle('slow');
                console.log(data.result);
                alert('PREVED');
            },
            error: function(data) {
                jQuery('#CommentForm').html(data.responseText);
            }
        });
        return false;
    });
});