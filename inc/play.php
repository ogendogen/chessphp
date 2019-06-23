<?php

require_once("inc/utility.php");
require_once("inc/constants.php");

$db = new Database();

$game = 0;
if (!isset($_GET["id"]) || $_GET["id"] === "") {
	//Check if there's an empty game to enter
	$game = $db->exec("SELECT game_id FROM games WHERE player_white IS NULL AND player_black IS NULL AND LENGTH(moves) = 0");
	if (count($game) == 0) {
		//Create a game if it wasn't found
		$db->exec("INSERT INTO games () VALUES ()");
		$game = $db->exec("SELECT game_id FROM games ORDER BY game_id DESC LIMIT 1");
	}
} else {
	$game = $db->exec("SELECT game_id FROM games WHERE game_id = ?", [$_GET["id"]]);
}
if (count($game) > 0) $game = $game[0]["game_id"];
else {
	error("Nie znaleziono gry o podanym ID.");
}
?>

<div id="game" class="container" style="padding-bottom: 20px;">

	<!-- Promotion Modal Start -->
	<div id="promotion_picking" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h4 class="modal-title">Wybierz bierkę do awansu</h4>
		  </div>
		  <div class="modal-body alert alert-success text-center">
			<button type="button" class="btn btn-default" id="promobtn_bishop" width="45" height="45" data-dismiss="modal"> </button>
			<button type="button" class="btn btn-default" id="promobtn_knight" width="45" height="45" data-dismiss="modal"> </button>
			<button type="button" class="btn btn-default" id="promobtn_rook" width="45" height="45" data-dismiss="modal"> </button>
			<button type="button" class="btn btn-default" id="promobtn_queen" width="45" height="45" data-dismiss="modal"> </button>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal" disabled="disabled">Zamknij</button>
		  </div>
		</div>

	  </div>
	</div>
	<!-- Promotion Modal End -->
	
	<div class="row">
		<div id="board" class="col-md-offset-2 col-md-6">
		</div>
		
		<div id="info" class="col-md-6">
			<strong>
				<div id="player1" onclick="joinClick(0);" class="row bg-info">
				</div>
			</strong>
			<strong>
				<div id="player2" onclick="joinClick(1);" class="row bg-danger">
				</div>
			</strong>
			
			<div id="match" class="row bg-success pre-scrollable">
				<strong>Przebieg meczu</strong>
				<div id="process">
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var svgitem = {
	item1: '<?php echo(addcslashes(implode(file("svg/bking.svg")),"\"'\r\n")); ?>',
	item2: '<?php echo(addcslashes(implode(file("svg/bqueen.svg")),"\"'\r\n")); ?>',
	item3: '<?php echo(addcslashes(implode(file("svg/bbishop.svg")),"\"'\r\n")); ?>',
	item4: '<?php echo(addcslashes(implode(file("svg/bknight.svg")),"\"'\r\n")); ?>',
	item5: '<?php echo(addcslashes(implode(file("svg/brook.svg")),"\"'\r\n")); ?>',
	item6: '<?php echo(addcslashes(implode(file("svg/bpawn.svg")),"\"'\r\n")); ?>',
	item8: '<?php echo(addcslashes(implode(file("svg/wpawn.svg")),"\"'\r\n")); ?>',
	item9: '<?php echo(addcslashes(implode(file("svg/wrook.svg")),"\"'\r\n")); ?>',
	item10: '<?php echo(addcslashes(implode(file("svg/wknight.svg")),"\"'\r\n")); ?>',
	item11: '<?php echo(addcslashes(implode(file("svg/wbishop.svg")),"\"'\r\n")); ?>',
	item12: '<?php echo(addcslashes(implode(file("svg/wqueen.svg")),"\"'\r\n")); ?>',
	item13: '<?php echo(addcslashes(implode(file("svg/wking.svg")),"\"'\r\n")); ?>',
};
var game_id = <?php echo($game); ?>;
var user_id = <?php echo($_SESSION['userid']?$_SESSION['userid']:"null"); ?>;
var clickedx = -1;
var clickedy = -1;
var currentp = "white";
var p1 = null;
var p2 = null;
var game_board = [[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0]];
function onDataGet(data) {
	if (data instanceof Array && data[0] === true) {
		var stuff = data[1];
		if (stuff.whitenick !== null)
			$("#player1").text(stuff.whitenick);
		else
			$("#player1").text("[kliknij by dołączyć]");
		if (stuff.blacknick !== null)
			$("#player2").text(stuff.blacknick);
		else
			$("#player2").text("[kliknij by dołączyć]");
		$("#process").html(stuff.moves);
		currentp = stuff.turn;
		if (currentp === "white") $("#player1").css("background-color", "#FFAAAA");
		else $("#player1").css("background-color", "#FFFFFF");
		if (currentp === "black") $("#player2").css("background-color", "#FFAAAA");
		else $("#player2").css("background-color", "#FFFFFF");
		player1 = stuff.white;
		player2 = stuff.black;
		refreshBoard(stuff.board);
	}
}
function refreshData() {
	$.getJSON('http://<?php echo($site); ?>/move.php', { game: game_id }, onDataGet);
}
function refreshBoard(board) {
	game_board = board;
	for (var i=0; i<8; i++) {
		for (var j=0; j<8; j++) {
			if (board[i][j] != 0) {
				$("#field"+i+j).html(svgitem["item"+(board[i][j]+7)]);
			} else {
				$("#field"+i+j).html("");
			}
		}
	}
}
var promData = {};
function tileClicked(x,y) {
	if (clickedx == -1) {
		clickedx = x;
		clickedy = y;
		$("#field"+x+y).css("background-color", "#FFF814");
	} else {
		if ((clickedx+clickedy)%2 == 0) {
			$("#field"+clickedx+clickedy).css("background-color", "#FFF8DC");
		} else {
			$("#field"+clickedx+clickedy).css("background-color", "#A52A2A");
		}
		promData = { game: game_id, from: clickedx+""+clickedy, to: x+""+y };
		$.getJSON('http://<?php echo($site); ?>/move.php', promData, function(data){
			if (data instanceof Array) {
				if (data[0] === true) {
					refreshData();
				} else if (data[1] === "unknown promotion") {
					showPromotion(currentp=="white",function(ev){
						promData.pro = ev.data.piece;
						$.getJSON('http://<?php echo($site); ?>/move.php', promData, function(data){
							if (data instanceof Array && data[0] === true) {
								refreshData();
							}
						});
					});
				}
			}
			if (data instanceof Array && data[0] === true) {
				refreshData();
			}
		});
		clickedx = -1;
		clickedy = -1;
	}
}
function joinClick(player) {
	if (user_id != null) {
		$.getJSON('http://<?php echo($site); ?>/move.php', { game: game_id, slot: player, player: user_id }, function(data){
			refreshData();
		});
	}
}

$(document).ready(function(){
	refreshData();
	setInterval(refreshData, 2500);
	for (var x=0; x<8; x++) {
		$("#board").append("<div id=row"+x+" class=row></div>");
		for (var y=0; y<8; y++) {
			
			var z = 0;
			z = x+y;
			
			if (z % 2 == 0) {
				$("#board #row"+x).append("<div id=field"+y+x+" onclick=\"tileClicked("+y+","+x+");\" class=field style='background-color: #FFF8DC;'></div>");
			} else {
				$("#board #row"+x).append("<div id=field"+y+x+" onclick=\"tileClicked("+y+","+x+");\" class=field style='background-color: #A52A2A;'></div>");
			}
		}
	}
});
</script>

<script src="js/speechAPI.js"></script>