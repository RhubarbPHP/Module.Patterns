var bridge = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.SelectionControlViewBridge.apply(this, arguments);

    if (this.model.SelectedItems.length === 0) {
        this.model.SelectedItems = {};
    }
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.SelectionControlViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
    window.rhubarb.viewBridgeClasses.SelectionControlViewBridge.prototype.attachEvents.apply(this, arguments);

    var self = this;

    $("a", this.element).click(function () {
        var item = $(this).data("item");
        var selectedValueKey = self.getSelectedKeyFromValue(item.value);

        if (selectedValueKey == -1) {
            self.model.SelectedItems.push(item);
        }
        else {
            delete self.model.SelectedItems[selectedValueKey];
        }

        self.saveState();
        self.updateSelectedItems();

        self.valueChanged();

        return false;
    });
};

bridge.prototype.updateSelectedItems = function () {
    var self = this;

    $("a", this.element).each(function () {
        var item = $(this).data("item");
        var selected = self.isValueSelected(item.value);
        if (selected == false) {
            $(this).removeClass("-is-selected");
        }
        else {
            $(this).addClass("-is-selected");
        }
    });
};

window.rhubarb.viewBridgeClasses.ItemToggleViewBridge = bridge;