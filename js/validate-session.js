function session_checking() {
    $.post("/core/ajax-session.php", function(data) {
        if(data == "-1"){
            //alert("Your session has been expired!");
            location.reload();
        }
    });
}
var validateSession = setInterval(session_checking, 5000);