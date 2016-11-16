var bridge = function (leafPath) {
    window.rhubarb.viewBridgeClasses.SearchControl.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.SearchControl();
bridge.prototype.constructor = bridge;

bridge.prototype.createDom = function () {
    window.rhubarb.viewBridgeClasses.SearchControl.prototype.createDom.apply(this);
};

bridge.prototype.attachEvents = function () {
    window.rhubarb.viewBridgeClasses.SearchControl.prototype.attachEvents.apply(this);

    if (this.model.hasAddPresenter && !this.addButton) {
        this.addButton = document.createElement('input');
        this.addButton.type = 'button';
        this.addButton.value = 'Add';

        this.buttonsContainer.appendChild(this.addButton);

        var self = this;

        this.findViewBridge("Add").attachClientEventHandler("ItemAdded", function (item) {
            self.setSelectedItems([item]);
        });

        this.addButton.addEventListener('click',function(){
            self.findChildViewBridge("Add").clearAndShow();
        });
    }
};

bridge.prototype.updateUiState = function () {
    window.rhubarb.viewBridgeClasses.SearchControl.prototype.updateUiState.apply(this);

    if (this.model.hasAddPresenter) {
        this.addButton.style.display = 'none';

        switch (this._state) {
            case "unselected":
                this.addButton.style.display = 'block';
                break;
            case "searching":
                this.addButton.style.display = 'block';
                break;
            case "searched":
                this.addButton.style.display = 'block';
                break;
            case "selected":
                this.addButton.style.display = 'none';
                break;
        }
    }
};

window.rhubarb.viewBridgeClasses.ModelSearchOrAddControlViewBridge = bridge;