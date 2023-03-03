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
        	fid = $(this).attr('data-id');
			cur = $(this).attr('data-score');
			if(cur == '1' && score == '1'){
				score = 0;
			}
			$.post('/pages/manageFormula.php',
				{ update_rating: 1, fid: fid, score: score }
			);
			//reload_formulas_data();
        }
    });
}