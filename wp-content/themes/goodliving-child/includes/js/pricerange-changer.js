
/*
Added by RF
When User chnages Property Type when searching, repopulate min and max price dropdowns
Note: uses dropkick() in plugins.js
------------------------------------------------------------------- */
(function($){

	"use strict";

	$(document).ready(function(){

		$('#property_types').change(function(e){

			var buyOptions = ["143", "9", "31", "10", "7"],
			rentOptions = ["142", "23", "22"];

			// -- chosen a buyOption
			if (buyOptions.indexOf($(this).val()) !== -1) {
				buildOptions('sale', 'min');
				buildOptions('sale', 'max');
			}
			else if (rentOptions.indexOf($(this).val()) !== -1) {
				buildOptions('rent', 'min');
				buildOptions('rent', 'max');
			}
		});
	});

	function buildOptions(type, mode) {

		var optionText, optionValue, prices = {
			lowest: "1",
			highest: "999999999",
		};

		// -- get the new options --
		var newOptions = getOptionArray(type, mode, prices);

		// console.log('newOptions', newOptions);
		// var selectedOption = 'all';

		var selector = (mode == 'min') ? '#price_min' : '#price_max';
		var select = $(selector);

		if(select.prop) {
			var options = select.prop('options');
		}
		else {
			var options = select.attr('options');
		}
		// -- remove all others --
		$('option', select).remove();
		// -- rebuild with new  --
		$.each(newOptions, function(val, text) {

			optionValue = text;

			if (mode == 'max' && text == prices.highest) {
				optionText = (type == 'sale') ? 'Max Price' : 'Max Rent';
				// - set default selected option --
				options[options.length] = new Option(optionText, optionValue, true, true);
			}
			else if (mode == 'min' && text == prices.lowest) {
				optionText = (type == 'sale') ? 'Min Price' : 'Min Rent';
				// - set default selected option --
				options[options.length] = new Option(optionText, optionValue, true, true);
			}
			else {
				optionText = '&euro;' + commaSeparateNumber(text);
				options[options.length] = new Option(optionText, optionValue);
			}
		});

		// console.log('newOptions', options);

		// -- reinitialise dropkick --
		$(selector).dropkick({
			theme: 'metro',
			change: function(value, label) {
				$(this).trigger('change');
			}
		});

		// console.log('select, options', select, options);
	}


	function getOptionArray(type, mode, prices) {

		// console.log('getOptionArray', type, mode, prices);

		/*
		keep this in sync with php arrays
		// -- ranges for price drop downs -- set up in:
		/wp-content/themes/goodliving-child/includes/forms/property-search.php
		*/

		var
		min_sale_price_options = ["50000","100000","200000","300000","400000","500000","600000","700000","800000","900000","1000000","2000000","3000000","4000000","5000000"],
		max_sale_price_options = ["100000","200000","300000","400000","500000","600000","700000","800000","900000","1000000","2000000","3000000","4000000","5000000","10000000","50000000","100000000"],

		min_longterm_rent_monthly_options = ["250","500","750","1000","1250","1500","2000","2500","3000","4000","5000"],
		max_longterm_rent_monthly_options = ["500","750","1000","1250","1500","2000","2500","3000","4000","5000","10000"],

		min_holiday_rent_daily_options = ["50","100","200","300","400","500","600","700","800","900"],
		max_holiday_rent_daily_options = ["100","200","300","400","500","600","700","800","900","1000"]
		;

		if (type == 'sale') {
			if (mode == 'min') {
				min_sale_price_options.unshift(prices.lowest);
				return min_sale_price_options;
			}
			else {
				max_sale_price_options.push(prices.highest);
				return max_sale_price_options;
			}
		}
		else if (type == 'rent') {
			if (mode == 'min') {
				min_longterm_rent_monthly_options.unshift(prices.lowest);
				return min_longterm_rent_monthly_options;
			}
			else {
				max_longterm_rent_monthly_options.push(prices.highest);
				return max_longterm_rent_monthly_options;
			}
		}
	}

	function commaSeparateNumber(val){
		while (/(\d+)(\d{3})/.test(val.toString())){
			val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
		}
		return val;
	}


})(jQuery);
