var bridge = function (leafPath) {
    window.rhubarb.viewBridgeClasses.TableViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.TableViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
    var self = this;

    $('tr', this.element).each(function () {
        $(this).data('editing-mode', 'not-editing');
    });

    $(".row-controls").html('<a class="edit" href="#">Edit</a><a class="save" href="#">Save</a> <a class="cancel" href="#">Cancel</a><span class="rpc-status"> </span>');
    $(".row-controls .save,.row-controls .cancel", this.element).hide();

    $(".row-controls .edit", this.element).click(function () {
        var tr = $(this).parents('tr:first');

        $(this).hide();
        $('.save,.cancel', tr).show();

        tr.addClass('editing');

        var index = -1;
        var focused = false;

        $("td", tr).each(function () {
            index++;

            // See if we have settings for this td
            if (self.model.SpawnSettings[index]) {
                var value = JSON.parse($(this).data('value'));

                // Spawn the interface
                var control = window.rhubarb.spawn(self.model.SpawnSettings[index], tr.data("row-id"));

                if (control) {
                    control.viewBridge.setValue(value);

                    $(this).data('label', $(this).html());
                    $(this).empty().append(control);

                    if (!focused) {
                        // Set focus to the first of the new controls
                        control.viewBridge.setFocus();
                        focused = true;
                    }

                    // Intercept the enter key for all inputs to trigger the save action.
                    $("input, select, textarea", control).add(control).keypress(function (event) {
                        if (event.keyCode == 13) {
                            $(".save", tr).triggerHandler("click");
                            return false;
                        }

                        return true;
                    });
                }
            }
        });

        return false;
    });

    $(".row-controls .cancel", this.element).click(function () {
        var tr = $(this).parents('tr:first');

        tr.removeClass('editing');

        $('.save,.cancel', tr).hide();
        $('.edit', tr).show();

        $("td", tr).each(function () {
            if ($(this).data('label') !== undefined) {
                $(this).html($(this).data('label'));
            }
        });

        $(".edit", tr).focus();

        return false;
    });

    $(".row-controls .save", this.element).click(function () {
        $('.save,.cancel', tr).hide();

        var tr = $(this).parents('tr:first');

        tr.addClass('saving');

        // Get all the row data into one array
        var rowData = {};

        $("*", tr).each(function () {
            if (this.viewBridge) {
                rowData[this.viewBridge.leafName] = this.viewBridge.getValue();
            }
        });

        self.raiseServerEvent("RowSaved", tr.data('row-id'), rowData, function (response) {
            tr.removeClass('saving');

            if (response) {
                tr.removeClass('editing');
                tr.addClass('updated');

                $("td", tr).each(function () {
                    var td = $(this);

                    if ($(this).data('value') !== undefined) {
                        $("*", td).each(function () {
                            if (this.viewBridge) {
                                td.html(this.viewBridge.getDisplayView());
                                td.data('value', JSON.stringify(this.viewBridge.getValue()));
                            }
                        });
                    }
                });

                $('.edit', tr).show();

                // Focus the next edit link
                $(".edit", tr.next()).focus();
            }
            else {
                alert("An error occurred");

                $('.cancel', tr).triggerHandler('click');
            }
        });

        return false;
    });
};

bridge.prototype.onSubLeafValueChanged = function (viewBridge, value) {
    $(viewBridge.viewNode).parents('tr:first').addClass('updating');

    this.raiseServerEvent('SubPresenterValueChanged', viewBridge.leafPath, value, function () {
        $(viewBridge.viewNode).parents('tr:first').removeClass('updating').addClass('updated');
    });
};

window.rhubarb.viewBridgeClasses.TableWithEditableCellsViewBridge = bridge;

