CKEDITOR.config.pasteFromWordCleanupFile = '/pastefromword.js';
...
CKEDITOR.on( 'instanceReady', function( ev ) {
    /**
     * Paste event to apply Paste From Word filtering on all text.
     *
     * The pastefromword plugin will only process text that has tell-tale signs
     * it is from Word. Use this hook to treat all pasted text as if
     * it is coming from Word.
     *
     * This method is a slightly modified version of code found in
     * plugins/pastefromword/plugin.js
     */
    ev.editor.on( 'paste', function( evt ) {    
        var data = evt.data,
            editor = evt.editor,
            content;

        /**
         * "pasteFromWordHappened" is a custom property set in custom
         * pastefromword.js, so that filtering does not happen twice for content
         * actually coming from Word. It's a dirty hack I know.
         */
        if( editor.pasteFromWordHappened ) {
            // Reset property and exit paste event
            editor.pasteFromWordHappened = 0;
            return;
        }

        var loadRules = function( callback ) {
            var isLoaded = CKEDITOR.cleanWord;

            if( isLoaded ) {
                callback();
            }
            else {
                CKEDITOR.scriptLoader.load( CKEDITOR.config.pasteFromWordCleanupFile, callback, null, false, true );
            }

            return !isLoaded;
        };

        content = data['html'];

        // No need to filter text if html tags are not presence, so perform a regex
        // to test for html tags.
        if( content && (/<[^<]+?>/).test(content) ) {
            var isLazyLoad = loadRules( function(){
                if( isLazyLoad ) {
                    editor.fire('paste', data);
                }
                else {
                    data[ 'html' ] = CKEDITOR.cleanWord( content, editor );
                    // Reset property or if user tries to paste again, it won't work
                    editor.pasteFromWordHappened = 0;
                }
            });

            isLazyLoad && evt.cancel();
        }

    });
});