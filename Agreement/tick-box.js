var terms_accepted="";

checkbox_1 = false;

function termsAccepted() {
	if ( checkbox_1 == false ) {
		terms_accepted='accepted';
		checkbox_1 = true;
	} else {
		terms_accepted='';
		checkbox_1 = false;
	}
}


function checkForm() {
	if (checkboxFunc()) {
		window.location.href = 'accepted.php';
   }
}


function checkboxFunc() {
	if (terms_accepted=="") {
		alert("\You must accept the Terms of Service before you can enter!");
	} else {
		return true;
   }
}
