$(document).ready(function(){
	fitFooter();
	if (!is_logged_in) {
		for (var i=0; i<4; i++)
			if ($("#page"+i).hasClass("active")) $("#page"+i).removeClass("active");
		
		var page = getUrlParameter("tab");
		switch(page) {
			case "browse":
				$("#page1").addClass("active");
				break;
			case "play":
				$("#page1").addClass("active");
				break;
			case "login":
				$("#page2").addClass("active");
				break;
			case "register":
				$("#page3").addClass("active");
				break;
			default:
				$("#page0").addClass("active");
				break;
		}
	} else {
		for (var i=0; i<4; i++)
		if ($("#page"+i).hasClass("active")) $("#page"+i).removeClass("active");
		
		var page = getUrlParameter("tab");
		switch(page) {
			case "browse":
				$("#page1").addClass("active");
				break;
			case "play":
				$("#page1").addClass("active");
				break;
			case "login":
				$("#page2").addClass("active");
				break;
			case "register":
				$("#page3").addClass("active");
				break;
			default:
				$("#page0").addClass("active");
				break;
		}
	}
	
	$("[data-toggle='popover']").popover({
		trigger:"hover",
		animation: true,
		placement: "auto"
	}).on("click", function() { $(this).popover("toggle") });
	
	if ($(".field").length){
		for (var i=0; i<8; i++) {
			for (var j=0; j<8; j++) {
				if (i+j % 2 == 0 || (i == 0 && j == 0)) {
					$("#field"+i+j).css("background-color", "#D2691E");
				}
				else {
					$("#field"+i+j).css("background-color", "#FFF8DC");
				}
			}
		}
	}
	$("footer").css("opacity", "1.00");
});

var getUrlParameter = function getUrlParameter(sParam) {
var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	sURLVariables = sPageURL.split("&"),
	sParameterName,
	i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split("=");

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
};

function showPromotion(is_white, ret_func) // true = white, false = black
{
	var color = (is_white ? "w" : "b");
	
	$("#promotion_picking .modal-dialog .modal-body #promobtn_bishop").html("<img src=svg/" + color + "bishop.svg></img>");
	$("#promotion_picking .modal-dialog .modal-body #promobtn_knight").html("<img src=svg/" + color + "knight.svg></img>");
	$("#promotion_picking .modal-dialog .modal-body #promobtn_rook").html("<img src=svg/" + color + "rook.svg></img>");
	$("#promotion_picking .modal-dialog .modal-body #promobtn_queen").html("<img src=svg/" + color + "queen.svg></img>");
	
	$("#promotion_picking .modal-dialog .modal-body #promobtn_bishop").click({piece:4},ret_func);
	$("#promotion_picking .modal-dialog .modal-body #promobtn_knight").click({piece:3},ret_func);
	$("#promotion_picking .modal-dialog .modal-body #promobtn_rook").click({piece:2},ret_func);
	$("#promotion_picking .modal-dialog .modal-body #promobtn_queen").click({piece:5},ret_func);
	
	$('#promotion_picking').modal({backdrop: 'static', keyboard: false});
}

function fitFooter()
{
	var nav_h = document.getElementById("nav").clientHeight;
	var main_h = document.getElementsByClassName("container")[1].clientHeight; // second div with container class, so it's main window div
	var footer_h = document.getElementsByTagName("footer")[0].clientHeight;
	var window_h = window.innerHeight;
	
	var pusher_h = window_h - nav_h - main_h - footer_h;
	$("#pusher").css("height", pusher_h);
}

window.onresize = function(event) { /* Resize event to fit footer */
	fitFooter();
};