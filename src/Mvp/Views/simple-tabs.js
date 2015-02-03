	$.fn.simpleTabs = function( options )
	{
		var defaults =
		{
			initialTab:          '',
			onTabSelected:       function()
			{
			},
			setInputFocus:       false,
			appendTabToLocation: false
		}

		if( location.hash != '' && !window.initialTab )
		{
			window.initialTab = location.hash.replace( "#", "" ).replace( "-tab", "" );
		}

		var options = $.extend( defaults, options );

		return this.each( function()
		{
			var tabUl = $( this );
			var tabPanels;

			tabUl.addClass( 'simple-tab-container' );

			var SelectByIndex = function( index )
			{
				var lis = tabUl.find( 'li' );

				SelectByLi( $( lis[ index ] ) );
			}

			var SelectByName = function( name, targetTabsUl )
			{
				this.loadingByName = true;

				if( !targetTabsUl )
				{
					targetTabsUl = tabUl;
				}

				var lis = targetTabsUl.find( 'li:has(a[href*="#' + name + '"])' );

				if( lis.length == 1 )
				{
					SelectByLi( $( lis[ 0 ] ), targetTabsUl );

					this.loadingByName = false;
					return true;
				}

				this.loadingByName = false;
				return false;
			}

			var SelectByLi = function( li, targetTabsUl )
			{
				if( !targetTabsUl )
				{
					targetTabsUl = tabUl;
				}

				var panelName = li.find( 'a' )[0].hash.replace( "#", "" );

				var panel = $( "#" + panelName );

				panel.parent().children( '.simple-tab-panel' ).hide();
				panel.show();

				targetTabsUl.find( 'li' ).removeClass( 'selected' );
				targetTabsUl.find( 'li:has(a[href="#' + panelName + '"])' ).addClass( 'selected' );

				if( options.appendTabToLocation )
				{
					location.hash = "#" + panelName + "-tab";
				}

				options.onTabSelected( panelName );

				if( options.setInputFocus )
				{
					$( document.getElementById( panelName ) ).find( 'input, select, textarea' ).first().focus();
				}

				if( this.loadingByName )
				{
					// Make sure any parent tabs are open aswell.
					li.parents( '.simple-tab-panel:first' ).each( function()
					{
						// If we don't have a simpleTabsUl property on the discovered panel, it means it's simple tabs
						// plugin hasn't been initiated yet. Make sure the code to initiate the tabs occurs in the order
						// of outermost tab set first.
						if( $( this )[0].simpleTabsUl )
						{
							var id = $( this )[0].id;

							SelectByName( id, $( this )[0].simpleTabsUl );
						}
					} );
				}
			}

			tabUl.find( 'li a' ).each( function()
			{
				var panel = this.hash.replace( '#', '' );
				var link = this;

				$( this ).parent().addClass( 'simple-tab' );

				$( '#' + panel ).addClass( 'simple-tab-panel' );
				$( '#' + panel )[0].simpleTabsUl = tabUl;

				tabPanels = $( '#' + panel ).parent();

				$( this ).click( function( event )
				{
					event.preventDefault();
				} );

				$( this ).parent().click( function( event )
				{
					SelectByLi( $( this ) );
				} );

			} );

			tabUl.addClass( 'simple-tabs' );

			var tabSelected = false;

			if ( window.initialTab )
			{
				tabSelected = SelectByName( window.initialTab );
			}

			if( !tabSelected && options.initialTab != '' )
			{
				tabSelected = SelectByName( options.initialTab );
			}

			if( !tabSelected )
			{
				SelectByIndex( 0 );
			}
		} );
	};

function ResetFavicon()
{
	jQuery( 'link[rel=icon],link[rel=shortcut\\\ icon]' ).each( function()
	{
		var el = jQuery( this );
		el.remove();
		jQuery( '<link rel="' + el.attr( 'rel' ) + '" type="' + el.attr( 'type' ) + '" href="' + el.attr( 'href' ) + '" />' ).appendTo( 'head' );
	} );
}