var bridge = function (presenterPath) {
    window.rhubarb.viewBridgeClasses.TableViewBridge.apply(this, arguments);

    this.selectedRow = false;
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.TableViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
    var self = this;

    $(this.element).on('click', 'tbody tr', function () {
        self.selectRow($(this));
    });
};

bridge.prototype.clearRows = function () {
    $("tbody tr", this.element).remove();
};

bridge.prototype.selectRow = function (tr) {
    this.selectedRow = tr;
    this.raiseClientEvent("RowSelected", $(tr).data('row-data'));
};

bridge.prototype.removeRow = function (tr) {
    tr = $(tr);

    var data = tr.data('row-data');

    this.raiseClientEvent("RowRemoved", data);

    this.selectedRow = false;

    tr.remove();
};

bridge.prototype.addRow = function (id, rowData) {
    var tr = $("<tr></tr>");
    tr.data('row-id', id);
    tr.data('row-data', rowData);

    // Add the same number of cells from the table head.
    var headCells = $('thead tr:last td, thead tr:last th', this.element);
    var headCount = headCells.length;

    for (var i = 0; i < headCount; i++) {
        var newCell = $('<td></td>');

        if (headCells[i].className) {
            newCell[0].className = headCells[i].className;
        }

        tr.append(newCell);
    }


    $('tbody', this.element).append(tr);

    this.populateCells(tr);

    return tr;
};

bridge.prototype.updateSelectedRow = function (rowData) {
    this.selectedRow.data('row-data', rowData);

    this.populateCells(this.selectedRow);
};

/**
 * This can be overriden to populate the cells in the table row from the row data or you
 * can leave this alone and attache the client side event handler "PopulateCells" instead
 *
 * @param tr
 */
bridge.prototype.populateCells = function (tr) {
    this.raiseClientEvent("PopulateCells", tr);
};

window.rhubarb.viewBridgeClasses.TableWithEditableRowsViewBridge = bridge;