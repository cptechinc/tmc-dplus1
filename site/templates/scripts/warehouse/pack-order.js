$(function() {
	var itemtype_serialized = 'S';
	var itemtype_lotted = 'L';

	var input_barcode = $('input[name=barcode]');
	var input_qty     = $('input[name=qty]');
	var input_linenbr = $('input[name=linenbr]');
	var form_barcode  = $('form[id=barcode-form]');

	$("body").on("click", ".exit-order", function(e) {
		e.preventDefault();
		var button = $(this);

		swal({
			title: 'Are you sure?',
			text: "You are trying to leave this order",
			type: 'warning',
			showCancelButton: true,
			confirmButtonClass: 'btn btn-success',
			cancelButtonClass: 'btn btn-danger',
			buttonsStyling: false,
			confirmButtonText: 'Yes!'
		}).then(function (result) {
			if (result) {
				window.location.href = button.attr('href');
			}
		}).catch(swal.noop);
	});

	$("body").on("click", ".choose-item", function(e) {
		e.preventDefault();
		var button = $(this);
		var itemID = button.data('itemid');
		var linenbr = button.data('linenbr');
		var itemtype = button.data('itemtype');
		var lotserial = button.data('lotserial');

		input_linenbr.val(linenbr);

		if (itemtype == itemtype_serialized) {
			input_barcode.val('').focus();
		} else if (itemtype == itemtype_lotted) {
			input_barcode.val(lotserial);
			input_barcode.focus();
		} else {
			input_barcode.val(itemID);
		}
	});
});
