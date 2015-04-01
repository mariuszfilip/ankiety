/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
    config.height= '100%';
    config.contentsCss = url+'/public/css/print.css';
    config.allowedContent = true;
    config.extraPlugins = 'youtube';
    config.youtube_width = '640';
    config.youtube_height = '480';
    config.youtube_related = true;
    config.youtube_older = false;
    config.youtube_privacy = false;

   /* config.toolbar =
        [
//            { name: 'document',    items : [ 'Source','-','Save','PageBreak','NewPage','DocProps','Preview','Print','-','Maximize' ] },
            { name: 'document',    items : [ 'Print','ImageButton','mediaembed'] },
//            { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
//            { name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
//            { name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
//            '/',
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
//            { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
//            { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
            { name: 'insert',      items : [ 'Image','Flash','Smiley','SpecialChar'] },
//            { name: 'insert',      items : ['Image' ] },
//            '/',
            { name: 'styles',      items : [ 'Styles','Format','Font','FontSize','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] }
//            { name: 'colors',      items : [ 'TextColor','BGColor' ] },
//            { name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','About' ] }
        ];*/

};

