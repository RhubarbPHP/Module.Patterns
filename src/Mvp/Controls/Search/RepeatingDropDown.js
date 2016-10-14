var repeatingDropDown = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.DropDownViewBridge.apply(this, arguments);

    this.element = this.element.find('select');

    this.createDom();

    if (this.model.SelectedItems && this.model.SelectedItems.length > 0) {
        for (var i in this.model.SelectedItems) {
            this.addAnother();

            var item = this.model.SelectedItems[i];

            this.container.find('select:last').val(item);
        }
    }
    else {
        this.addAnother();
    }
};

repeatingDropDown.prototype = new window.rhubarb.viewBridgeClasses.DropDownViewBridge();
repeatingDropDown.prototype.constructor = repeatingDropDown;

repeatingDropDown.prototype.createDom = function () {
    // Create a container for the drop down to make things easier.
    this.container = $('<div></div>');

    this.element.attr('disabled', 'disabled');
    this.element.after(this.container);

    // Shift the id attribute to the container as our approach is to clone the drop down to make the secondary ones.
    var id = this.element.attr("id");
    this.element.removeAttr("id");
    this.container.attr("id", id);

    this.element.hide();
};

repeatingDropDown.prototype.addAnother = function () {
    var clone = this.element.clone();
    clone.removeAttr('disabled');
    clone.show();
    var div = $('<div></div>');
    div.append(clone);

    this.container.append(div);

    this.updateDom();
};

repeatingDropDown.prototype.setCurrentlyAvailableSelectionItems = function (items) {
    window.rhubarb.viewBridgeClasses.DropDownViewBridge.prototype.setCurrentlyAvailableSelectionItems.apply(this, [items]);

    var self = this;

    // Copy the items to our other elements too.
    this.container.find('select').each(function () {
        var oldValue = $(this).val();

        $(this).html(self.element.html());

        $(this).val(oldValue);
    });
};

repeatingDropDown.prototype.updateDom = function () {
    var self = this;

    // Remove add links and re add them
    this.container.find('.add-another').remove();
    this.container.find('.delete-another').remove();

    var addAnother = $("<a href='#' class='add-another'>Add Another</a>");

    addAnother.click(function () {
        self.addAnother();

        return false;
    });

    // Find the last select with a value.
    var lastSelect = this.container.find('select').last().filter(function () {
        return ($(this).val() != "" && $(this).val() != "0");
    });

    if (lastSelect.length) {
        this.container.append(addAnother);
    }

    // Find the last select with a value.
    var removableDropDowns = this.container.find('select');

    if (removableDropDowns.length > 1) {
        for (var x = 0; x < removableDropDowns.length; x++) {
            var deleteAnother = $("<a href='#' class='delete-another'>Remove</a>");

            deleteAnother.click(function () {
                $(this).parents('div:first').remove();

                self.updateDom();

                return false;
            });

            $(removableDropDowns[x]).after(deleteAnother);
        }
    }

    this.container.find('select').unbind('change').change(function () {
        self.updateDom()
    });
};

repeatingDropDown.prototype.getValue = function () {
    // Get the first drop down with a value.
    var value = "";

    $("select", this.container).each(function () {
        if (this.value && this.value != "0" && this.value != "") {
            value = this.value;
        }
    });

    return value;
};

repeatingDropDown.prototype.attachEvents = function () {
};

window.rhubarb.viewBridgeClasses.RepeatingDropDown = repeatingDropDown;