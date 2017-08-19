<?php
$db = new Database();
$gamelist = $db->exec("SELECT
			game_id,
			u1.name AS player_white,
			u2.name AS player_black,
			finished
		FROM games
			LEFT JOIN users AS u1 ON games.player_white = u1.user_id
			LEFT JOIN users AS u2 ON games.player_black = u2.user_id
		WHERE finished = 0 ORDER BY game_id DESC");

echo("<div class=\"container\"><a href=\"?tab=play\" class=\"gamerow btn btn-primary img-responsive\"><br>Rozpocznij nową grę<br>&nbsp;</a>");
for ($i=0; $i<count($gamelist); $i++) {
	$waiting = false;
	if ($gamelist[$i]["player_white"] === null) { $gamelist[$i]["player_white"] = "???"; $waiting = true; }
	if ($gamelist[$i]["player_black"] === null) { $gamelist[$i]["player_black"] = "???"; $waiting = true; }
	echo("<a href=\"?tab=play&id=".$gamelist[$i]["game_id"]."\" class=\"gamerow btn btn-success img-responsive\"><b>Gra #".
		$gamelist[$i]["game_id"]."</b><br>".$gamelist[$i]["player_white"]." vs ".$gamelist[$i]["player_black"]."<br>");
	if ($waiting) {
		echo("Czekanie na graczy");
	} elseif ($gamelist[$i]["finished"] == 0) {
		echo("W trakcie");
	}
	echo("</a>");
}
echo("</div>");
?>