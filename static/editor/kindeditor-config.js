KindEditor.ready(function(K) {
    var options = {
        autoHeightMode : true,
        afterCreate : function() {
            if (this.isEmpty()) {
                this.html('<p><br/></p>');
            };
            this.focus();
            this.loadPlugin('autoheight');
        },
        resizeType: 1,
        items : [
        'source', 'preview', '|', 'formatblock', 'fontsize', 'bold', 'italic', 'underline',
        'strikethrough', 'hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
        'insertunorderedlist', '|', 'emoticons', 'image', 'link', 'unlink', '|', 'fullscreen']
    };
    window.editor = K.create('#content', options);
});