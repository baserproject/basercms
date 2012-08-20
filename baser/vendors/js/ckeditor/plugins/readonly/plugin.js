/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.plugins.add( 'readonly',
{
        requires : [ 'editingblock' ],
        init : function( editor )
        {
                editor.addCommand( 'readonly',
                {
                        modes : { wysiwyg : 1, source : 1 },
                        editorFocus : false,
                        canUndo : false,
                        exec : function ( editor )
                        {
                                editor.readOnly( this.state == CKEDITOR.TRISTATE_OFF );
                                this.toggleState();
                        }
                });

                editor.ui.addButton( 'ReadOnly',
                        {
                                label : editor.lang.readOnly,
                                command : 'readonly'
                        });
        }
});

( function()
{
         var cancelEvent = function( evt )
         {
                evt.cancel();
         };

        function cancelKeyListener( evt )
        {

			// >>> CUSTOMIZE MODIFY ryuring 2010/11/30 日本語変換に対応
            /*    var keystroke = evt.data.keyCode;
                if ( keystroke != 9 && keystroke != CKEDITOR.SHIFT + 9 )
                  cancelEvent( evt );*/
			// ---
			var tmp = evt.editor.getData();
			var keystroke = evt.data.keyCode;
			if ( keystroke != 9 && keystroke != CKEDITOR.SHIFT + 9 ) {
				cancelEvent( evt );
				evt.editor.setData(tmp);
			}
			// <<<
        }
		
   // List of commands that are read-only.
        CKEDITOR.plugins.readyOnly =
   {
           readyOnlyCommands : { readonly:1,maximize:1,showblocks:1,showborders:1,preview:1,save:1,copy:1,print:1,selectAll:1,about:1 }
   };

        CKEDITOR.editor.prototype.readOnly = function( isReadOnly )
   {
                // Disable all commands in wysiwyg mode.
                var command,
                        commands = this._.commands,
                        mode = this.mode;

                for ( var name in commands )
                {
                        if ( name in CKEDITOR.plugins.readyOnly.readyOnlyCommands )
                                continue;

                        command = commands[ name ];
                        isReadOnly ? command.disable() : command[ command.modes[ mode ] ? 'enable' : 'disable' ]();
                        this[ isReadOnly ? 'on' : 'removeListener' ]( 'state', cancelEvent, null, null, 0 );
                }

                if ( this.mode == 'wysiwyg' )
                {
                        // Turn off contentEditable.
                        this.document.$.body.contentEditable = !isReadOnly;

                        // Prevent key handling.
                        this[ isReadOnly ? 'on' : 'removeListener' ]( 'key', cancelKeyListener, null, null, 0 );
                        this[ isReadOnly ? 'on' : 'removeListener' ]( 'selectionChange', cancelEvent, null, null, 0 );

                        // Deal with some toolbar items that don't get associated with commands.
                        var i, j, k;
                        var toolbars = this.toolbox.toolbars;
                        for ( i = 0; i < toolbars.length; i++ )
                        {
                                var toolbarItems = toolbars[ i ].items;
                                for ( j = 0; j < toolbarItems.length; j++ )
                                {
                                         // Combos and panel buttons.
                                        var combo = toolbarItems[ j ].combo;
                                        if ( combo )
                                                combo.setState( isReadOnly ? CKEDITOR.TRISTATE_DISABLED : CKEDITOR.TRISTATE_OFF );

                                        var button = toolbarItems[ j ].button;
                                        if ( button instanceof CKEDITOR.ui.panelButton )
                                                button.setState( isReadOnly ? CKEDITOR.TRISTATE_DISABLED : CKEDITOR.TRISTATE_OFF );
                                }
                        }
                }
                else if ( this.mode == 'source' )
                {
                        if ( isReadOnly )
                                this.textarea.setAttribute( 'disabled', true );
                        else
                                this.textarea.removeAttribute( 'disabled' );
                }
   }

} )();