function session_checking() {
    $.post("/core/ajax-session.php", function(data) {
		const response = JSON.parse(data);

       	if (response.session_status === false) {
            location.reload();
        }
		
    });
}
var validateSession = setInterval(session_checking, 5000);
