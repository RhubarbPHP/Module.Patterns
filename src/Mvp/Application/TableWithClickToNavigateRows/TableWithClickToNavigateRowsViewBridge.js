var bridge = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.TableViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.TableViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
    window.rhubarb.viewBridgeClasses.TableViewBridge.prototype.attachEvents.apply(this, arguments);

    this.attachClientEventHandler("RowClicked", function (tr) {

        // See if the row has an <a> tag with the class names "btn default"
        $("a.default:first", tr).each(function () {
            // This convoluted code is required because the method for simulating the actual event
            // of clicking an a tag is different between browsers and epochs!
            if (this.click) {
                this.click()
            }
            else {
                if (document.createEvent) {
                    if (event.target !== this) {
                        var evt = document.createEvent("MouseEvents");

                        evt.initMouseEvent("click", true, true, window,
                            0, 0, 0, 0, 0, false, false, false, false, 0, null);

                        var allowDefault = this.dispatchEvent(evt);
                    }
                }
            }
        });

        return false;
    });
};

window.rhubarb.viewBridgeClasses.TableWithClickToNavigateRowsViewBridge = bridge;