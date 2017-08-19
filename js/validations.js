function validateEmail() {
	var email = $("#email").val();
	var result = email.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
	if (result == null || result[0] == null) {
			$("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">Uwaga!</span>");
			$("#myModal .modal-dialog .modal-body").html("<div class=\"alert alert-warning\"><span style=\"font-weight: bold;\">Podałeś niepoprawny email!</span></div>");
			$("#myModal").modal("show");
			if (!$("#email").hasClass("error")) {
				$("#email").addClass("error");
				$("#btn").attr("disabled", "true");
			}
			return;
	}
	if ($("#email").hasClass("error")) {
		$("#email").removeClass("error");
		$("#email").addClass("success");
		$("#btn").removeAttr("disabled");
	}
}

function validatePasswords() {
	var pw = $("#password").val();
	var pw2 = $("#password2").val();
	if (pw != "" && pw2 != "" && pw != pw2) {
			$("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">Uwaga!</span>");
			$("#myModal .modal-dialog .modal-body").html("<div class=\"alert alert-warning\"><span style=\"font-weight: bold;\">Hasła różnią się!</span></div>");
			$("#myModal").modal("show");
			if (!$("#password").hasClass("error")) {
				$("#password").addClass("error");
				$("#password2").addClass("error");
			}
			$("#btn").attr("disabled", "true");
			return;
	}
	
	if ($("#password").hasClass("error")) {
		$("#password").removeClass("error");
		$("#password2").removeClass("error");
		$("#btn").removeAttr("disabled");
	}
}