var bridge = function( presenterPath )
{
    window.gcd.core.mvp.viewBridgeClasses.SearchControl.apply( this, arguments );
};

bridge.prototype = new window.gcd.core.mvp.viewBridgeClasses.SearchControl();
bridge.prototype.constructor = bridge;

bridge.prototype.createDom = function()
{
	window.gcd.core.mvp.viewBridgeClasses.SearchControl.prototype.createDom.apply( this );
};

bridge.prototype.attachEvents = function()
{
	window.gcd.core.mvp.viewBridgeClasses.SearchControl.prototype.attachEvents.apply( this );

	if ( this.model.HasAddPresenter && !this.addButton )
	{
		this.addButton = $( '<input type="button" value="Add" />' );
		this.buttonsContainer.append( this.addButton );

		var self = this;

		this.waitForPresenters( [ "Add" ], function ( addPresenter ) {
			addPresenter.attachClientEventHandler( "ItemAdded", function ( item ) {
				self.setSelectedItems( [ item ] );
			} );
		} );

		this.addButton.click( function () {
			self.findChildViewBridge( "Add" ).clearAndShow();
		} );
	}
};

bridge.prototype.updateUiState = function() {
	window.gcd.core.mvp.viewBridgeClasses.SearchControl.prototype.updateUiState.apply( this );

	if ( this.model.HasAddPresenter )
	{
		this.addButton.hide();

		switch( this._state )
		{
			case "unselected":
				this.addButton.show();
				break;
			case "searching":
				this.addButton.show();
				break;
			case "searched":
				this.addButton.show();
				break;
			case "selected":
				this.addButton.hide();
				break;
		}
	}
};

window.gcd.core.mvp.viewBridgeClasses.ModelSearchOrAddViewBridge = bridge;