/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  // Define changes to default configuration here.
  // For complete reference see:
  // http://docs.ckeditor.com/#!/api/CKEDITOR.config

  // The toolbar groups arrangement, optimized for two toolbar rows.
  config.allowedContent= true;
    config.removeFormatAttributes= '';
    config.disableAutoInline= true;
    config.extraPlugins = 'imageuploader,syntaxhighlight';
    config.enterMode = CKEDITOR.ENTER_BR;
    config.protectedSource = [];
    
  config.toolbarGroups = [
    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
    { name: 'links' },
    { name: 'insert' },
    { name: 'forms' },
    { name: 'tools' },
    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
    { name: 'others' },
    '/',
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
    { name: 'styles' },
    { name: 'colors' },
    { name: 'about' },
    { name: 'source' }
  ];
  // Remove some buttons provided by the standard plugins, which are
  // not needed in the Standard(s) toolbar.
  config.removeButtons = 'Underline,Subscript,Superscript';

  // Set the most common block elements.
  config.format_tags = 'p;h1;h2;h3;pre';

  // Simplify the dialog windows.
    config.removeDialogTabs = 'image:advanced;link:advanced';
    config.removeButtons = 'Underline,Subscript,Superscript';


    config.format_tags = 'p;h1;h2;h3;pre';


    config.removeDialogTabs = 'image:advanced;link:advanced';
    config.extraPlugins = 'pastetool';
    config.extraPlugins = 'pastefromword';
    config.extraPlugins = 'clipboard';
    config.extraPlugins = 'notification';
    config.extraPlugins = 'toolbar';
    config.extraPlugins = 'button';
    config.pasteFromWordCleanupFile = 'plugins/pastefromword/filter/default.js';
    config.pasteFromWordNumberedHeadingToList = true;
    config.pasteFromWordPromptCleanup = false;
    config.pasteFromWordRemoveStyles = false;

};
// function cleanUp() {

//     if (!CKEDITOR.cleanWord) {
//         // since the filter is lazily loaded by the pastefromword plugin we need to add it ourselves. 
//         // We use the same function as the callback for when the cleanup filter is loaded. Change the script path to the correct one
//         CKEDITOR.scriptLoader.load("plugins/pastefromword/filter/default.js", cleanUp, null, false, true );
//         // alert('loading script for the first usage');
//     } else { // The cleanWord is available for use

//         // change to the correct editor instance
//         var editor = CKEDITOR.instances.editor1;
//         // perform the clean up
//         var cleanedUpData = CKEDITOR.cleanWord(editor .getData(),  editor );

//         // do something with the clean up
//         alert(cleanedUpData);
//     }
// }

// cleanUp();

