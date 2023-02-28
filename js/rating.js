function initRating(container){
    $('span.rating', container).raty({
        half: false,
		number: 5,
        starOff: '/js/raty/images/star-off.png',
        starOn: '/js/raty/images/star-on.png',
        readOnly: false,
        score: function() {
            return $(this).attr('data-score');
        },
		click: function(score, evt) {
        var target = $(this),
        fid = target.attr('data-id');
			$.post('/pages/manageFormula.php',
				{ update_rating: 1, fid: fid, score: score }
			);
        }
    });
}