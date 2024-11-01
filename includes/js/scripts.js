(function($) {

	console.log("WCIO client scripts loaded");

	if (typeof _wcio == 'undefined' || _wcio === null) {
		_wcio = {};
	}

	// Create browser compatible event handler.
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

	// Listen for a message from the iframe.
	eventer(messageEvent, function(e) {
		var height;
		var message = e.data;
		if (message.type == 'HEIGHT_CHANGED' && Number.isInteger(message.data)) {
			height = message.data + 150; // +150 for good measures
			console.log('worldclass-embed height: ' + height + 'px');
			document.getElementById('worldclass-embed').style.height = height + 'px';
		}
		else if (message.type == 'SCROLL_TO_TARGET') {
			var iframeOffset = $("#worldclass-embed").offset().top;
			var headerHeight = $('header').length > 0 ? $('header').height() : 0;
			var wcBaseOffset = 50;
			$('html, body').animate({
				scrollTop: iframeOffset - headerHeight - wcBaseOffset
			}, 500);
		}
	}, false);

})(jQuery);
