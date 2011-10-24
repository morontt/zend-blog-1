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
            url: '/index/addcomment',
            data: dataForm,
            type: 'POST',
            success: function() {
                jQuery('#commentList').load(linkAddComment);
                jQuery('#comment_add').slideToggle('slow');
            }
        });
        return false;
    });
});