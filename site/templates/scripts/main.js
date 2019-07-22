// Well hello there. Looks like we don't have any Javascript.
// Maybe you could help a friend out and put some in here?
// Or at least, when ready, this might be a good place for it.

var nav = '#yt-menu';

$(function() {
	$('[data-toggle="tooltip"]').tooltip();
	init_datepicker();

	$(window).scroll(function() {
		if ($(this).scrollTop() > 50) {
			$('#back-to-top').fadeIn();
		} else {
			$('#back-to-top').fadeOut();
		}
	});

	// scroll body to 0px on click
	$('#back-to-top').click(function () {
		$('#back-to-top').tooltip('hide');
		$('body,html').animate({ scrollTop: 0 }, 800);
		return false;
	});

	$("body").on('keypress', 'form:not(.allow-enterkey-submit) input', function(e) {
		if (e.which === 13) {
			e.preventDefault();
			var input = $(this);

			if (input.closest('form').attr('tab-inputs') == "true") {
				var $canfocus = $('input:not([type=hidden])');
				var index = $canfocus.index(this) + 1;
				if (index >= $canfocus.length) index = 0;
				$canfocus.eq(index).focus();
			}
		}
	});

	$('.placard').on('accepted.fu.placard', function () {
		var placard = $(this);
		var form = placard.closest('form');
		form.submit();
	});

	$('form[submit-empty="false"]').submit(function () {
		var $empty_fields = $(this).find(':input').filter(function () {
			return $(this).val() === '';
		});
		$empty_fields.prop('disabled', true);
		return true;
	});
});

$.fn.extend({
	loadin: function(href, callback) {
		var parent = $(this);
		parent.html('<div></div>');

		var element = parent.find('div');
		console.log('loading ' + href + " into " +  parent.returnelementdescription());
		element.load(href, function() {
			init_datepicker();
			// init_timepicker();
			callback();
		});
	},
	returnelementdescription: function() {
		var element = $(this);
		var tag = element[0].tagName.toLowerCase();
		var classes = '';
		var id = '';
		if (element.attr('class')) {
			classes = element.attr('class').replace(' ', '.');
		}
		if (element.attr('id')) {
			id = element.attr('id');
		}
		var string = tag;
		if (classes) {
			if (classes.length) {
				string += '.'+classes;
			}
		}
		if (id) {
			if (id.length) {
				string += '#'+id;
			}
		}
		return string;
	},
	hasParent: function(selector) {
		var element = $(this);
		return $(this).closest(selector).length > 0;
	},
	formIsCompleted: function() {
		var form = $(this);
		form.find('.required').each(function() {
			if ($(this).val() === '') {
				return false;
			}
		});
		return true;
	}
});


Number.prototype.formatMoney = function(c, d, t) {
	var n = this,
		c = isNaN(c = Math.abs(c)) ? 2 : c,
		d = d == undefined ? "." : d,
		t = t == undefined ? "," : t,
		s = n < 0 ? "-" : "",
		i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
		j = (j = i.length) > 3 ? j % 3 : 0;
		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };


function toggle_nav() {
	$(nav).toggle();
	$(nav).find('input[name=q]').focus();
}

function init_datepicker() {
	$('.datepicker').each(function(index) {
		$(this).datepicker({
			date: $(this).find('.date-input').val(),
			allowPastDates: true,
		});
	});
}
