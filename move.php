<?php

require_once("inc/utility.php");

header("Content-Type: application/json");

$db = new Database();

function isPossible($b,$x1,$y1,$x2,$y2) {
	$piece = abs($b[$x1][$y1]);
	if ($piece == 0) return false;
	$color = $b[$x1][$y1]/$piece; //-1 - black, 1 - white
	if ($x1 == $x2 && $y1 == $y2) return false;
	if ($b[$x2][$y2] != 0 && $color == ($b[$x2][$y2]/abs($b[$x2][$y2]))) return false;
	
	switch ($piece) {
		
		case 1: //Pawn
			if ($y2 == $y1 - $color) {
				if ($x1 == $x2 && $b[$x2][$y2] == 0) return true;
				elseif (($x1 == $x2+1 || $x1 == $x2-1) && $b[$x2][$y2] != 0) return true;
				return false;
			} elseif ($x1 == $x2 && $y2 == $y1 - $color*2) {
				if ($b[$x1][$y1 - $color] == 0 && $b[$x1][$y2] == 0 && $y1 == (1 + ($color+1)/2*5)) return true;
				return false;
			} else return false;
			
		case 2: //Rook
			if ($x1 == $x2) {
				for ($i=min($y1,$y2)+1; $i<max($y1,$y2); $i++) {
					if ($b[$x1][$i] != 0) return false;
				}
				return true;
			} elseif ($y1 == $y2) {
				for ($i=min($x1,$x2)+1; $i<max($x1,$x2); $i++) {
					if ($b[$i][$y1] != 0) return false;
				}
				return true;
			} else return false;
			
		case 3: //Knight
			if (($x2 == $x1-1 || $x2 == $x1+1) && ($y2 == $y1-2 || $y2 == $y1+2)) return true;
			elseif (($x2 == $x1-2 || $x2 == $x1+2) && ($y2 == $y1-1 || $y2 == $y1+1)) return true;
			else return false;
			
		case 4: //Bishop
			if (max($x2,$x1) - min($x2,$x1) == max($y2,$y1) - min($y2,$y1)) {
				if (($x1 < $x2 && $y1 < $y2) || ($x1 > $x2 && $y1 > $y2)) {
					for ($i = 1; $i < max($x2,$x1) - min($x2,$x1); $i++) {
						if ($b[min($x1,$x2)+$i][min($y1,$y2)+$i] != 0) return false;
					}
				} else {
					for ($i = 1; $i < max($x2,$x1) - min($x2,$x1); $i++) {
						if ($b[min($x1,$x2)+$i][max($y1,$y2)-$i] != 0) return false;
					}
				}
				return true;
			} else return false;
			
		case 5: //Queen
			$state = false;
			$b[$x1][$y1] = 2*$color; $state = $state || isPossible($b,$x1,$y1,$x2,$y2);
			$b[$x1][$y1] = 4*$color; $state = $state || isPossible($b,$x1,$y1,$x2,$y2);
			$b[$x1][$y1] = 5; return $state;
			
		case 6: //King (regular move)
			if ($x2 >= $x1-1 && $x2 <= $x1+1 && $y2 >= $y1-1 && $y2 <= $y1+1) {
				if ($color*$b[$x2][$y2] > 0) {
					return false;
				}
				return true;
			} else return false;
			
	}
	return false;
}

session_start();

function answer($value) {
	echo(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
	die();
}
if (isset($_GET["game"]) && $_GET["game"] != "") {
	$game = intval($_GET["game"]);
	$data = $db->exec("SELECT
				games.player_white AS white,
				games.player_black AS black,
				u1.name AS whitenick,
				u2.name AS blacknick,
				games.moves AS moves,
				games.finished AS end,
				algebraic AS algstr
			FROM games
				LEFT JOIN users AS u1 ON games.player_white = u1.user_id
				LEFT JOIN users AS u2 ON games.player_black = u2.user_id
			WHERE game_id = ?", [$game]);
	if (count($data)) $data = $data[0];
	else answer([false, "nogame"]);
	
	//1 - pawn, 2 - rook, 3 - knight, 4 - bishop, 5 - queen, 6 - king
	//Positive - white
	//Negative - black
	$board = [
		[-2,-1, 0, 0, 0, 0, 1, 2],
		[-3,-1, 0, 0, 0, 0, 1, 3],
		[-4,-1, 0, 0, 0, 0, 1, 4],
		[-5,-1, 0, 0, 0, 0, 1, 5],
		[-6,-1, 0, 0, 0, 0, 1, 6],
		[-4,-1, 0, 0, 0, 0, 1, 4],
		[-3,-1, 0, 0, 0, 0, 1, 3],
		[-2,-1, 0, 0, 0, 0, 1, 2]
	];
	$blackturn = false;
	$x1=0; $y1=0; $x2=0; $y2=0; //From/to positions
	$c = [ //Castling states
		"w1" => false,
		"w2" => false,
		"wk" => false,
		"b1" => false,
		"b2" => false,
		"bk" => false,
	];
	//Determine current chessboard state. This will not check for previous invalid moves
	for ($i=0; $i<strlen($data["moves"]); $i+=2) {
		$movecode = ord($data["moves"][$i]);
		if ($movecode >= 8 && $movecode < 15) {
			$movecode -= 8;
			if (!$blackturn) $movecode *= -1;
			$board[$x2][$y2] = $movecode;
			$i--;
			continue;
		}
		if ($movecode == 15) {
			$i++;
			$blackturn = !$blackturn;
		}
		$x1 = (ord($data["moves"][$i]) & 0xF0) >> 4;
		$y1 = ord($data["moves"][$i]) & 0x0F;
		$x2 = (ord($data["moves"][$i+1]) & 0xF0) >> 4;
		$y2 = ord($data["moves"][$i+1]) & 0x0F;
		if (($x1 == 0 && $y1 == 0) || ($x2 == 0 && $y2 == 0)) { $c["b1"] = true; };
		if (($x1 == 7 && $y1 == 0) || ($x2 == 7 && $y2 == 0)) { $c["b2"] = true; };
		if (($x1 == 4 && $y1 == 0) || ($x2 == 4 && $y2 == 0)) { $c["bk"] = true; };
		if (($x1 == 0 && $y1 == 7) || ($x2 == 0 && $y2 == 7)) { $c["w1"] = true; };
		if (($x1 == 7 && $y1 == 7) || ($x2 == 7 && $y2 == 7)) { $c["w2"] = true; };
		if (($x1 == 4 && $y1 == 7) || ($x2 == 4 && $y2 == 7)) { $c["wk"] = true; };
		$board[$x2][$y2] = $board[$x1][$y1];
		$board[$x1][$y1] = 0;
		$blackturn = !$blackturn;
	}
	
	if (isset($_GET["from"]) && $_GET["from"] != "" && isset($_GET["to"]) && $_GET["to"] != "") {
		$from = strval($_GET["from"]);
		$to = strval($_GET["to"]);
		
		//Get column and row values
		$fromx = intval($from[0]);
		$fromy = intval($from[1]);
		$tox = intval($to[0]);
		$toy = intval($to[1]);
		
		$kingmove = false;
		//Check if move is valid
		//- Game is finished already
		if ($data["finished"]) {
			answer([false, "game is finished"]);
		}
		//- FROM and TO values are valid
		if ($fromx < 0 || $fromx >= 8 || $fromy < 0 || $fromy >= 8 || $tox < 0 || $tox >= 8 || $toy < 0 || $toy >= 8) {
			answer([false, "out of bounds"]);
		}
		//- Move done by valid player
		if (!isset($_SESSION["userid"]) || ($blackturn?$data["black"]:$data["white"]) != $_SESSION["userid"]) {
			answer([false, "wrong user"]);
		}
		//- Anything can be moved from selected tile
		if (($blackturn?-$board[$fromx][$fromy]:$board[$fromx][$fromy]) <= 0) {
			answer([false, "no piece to move"]);
		}
		//- FROM same as TO
		if ($fromx == $tox && $fromy == $toy) {
			answer([false, "zero length move"]);
		}
		//- Destination isn't the same player's piece
		if (($blackturn?-$board[$tox][$toy]:$board[$tox][$toy]) > 0) {
			answer([false, "can't kill your own pieces"]);
		}
		//- Check if there are 2 kings on board (this should ALWAYS be the case)
		$wkingx = -1; $bkingx = -1;
		$wkingy = -1; $bkingy = -1;
		for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
			if ($board[$i][$j] == -6) {
				if ($bkingx != -1) answer([false, "more than 1 black king"]);
				$bkingx = $i;
				$bkingy = $j;
			} elseif ($board[$i][$j] == 6) {
				if ($wkingx != -1) answer([false, "more than 1 white king"]);
				$wkingx = $i;
				$wkingy = $j;
			}
		}
		if ($wkingx == -1) answer([false, "no white king"]);
		if ($bkingx == -1) answer([false, "no black king"]);
		//- Check if for some reason there's checkmate/stalemate already (this should NEVER be the case)
		$canmove = false;
		for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
			for ($k=0;$k<8;$k++) for ($l=0;$l<8;$l++) {
				if ($blackturn && $board[$i][$j] <= 0) continue;
				if (!$blackturn && $board[$i][$j] >= 0) continue;
				if (isPossible($board,$i,$j,$k,$l)) {
					$canmove = true;
					goto nobadend;
				}
			}
		}
		nobadend:
		if (!$canmove) {
			answer([false, "player has no valid move"]);
		}
		//- Piece has a valid path
		$piece = abs($board[$fromx][$fromy]);
		$ctry = ""; //Player tries to castle
		switch ($piece) {
			case 1: if (!isPossible($board,$fromx,$fromy,$tox,$toy)) answer([false, "wrong pawn move"]); break;
			case 2: if (!isPossible($board,$fromx,$fromy,$tox,$toy)) answer([false, "wrong rook move"]); break;
			case 3: if (!isPossible($board,$fromx,$fromy,$tox,$toy)) answer([false, "wrong knight move"]); break;
			case 4: if (!isPossible($board,$fromx,$fromy,$tox,$toy)) answer([false, "wrong bishop move"]); break;
			case 5: if (!isPossible($board,$fromx,$fromy,$tox,$toy)) answer([false, "wrong queen move"]); break;
			case 6: //King
				if (!isPossible($board,$fromx,$fromy,$tox,$toy)) {
					//Possibility of castling
					$cleft;$cright;
					if ($blackturn) {
						$cleft = !($c["bk"] || $c["b1"]);
						$cright = !($c["bk"] || $c["b2"]);
					} else {
						$cleft = !($c["wk"] || $c["w1"]);
						$cright = !($c["wk"] || $c["w2"]);
					}
					if ($toy != $fromy) answer([false, "wrong king move"]);
					if ($tox == $fromx - 2 && $cleft && $board[$fromx-1][$toy]==0 && $board[$fromx-2][$toy]==0 && $board[$fromx-3][$toy]==0) {
						for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
							if (($blackturn?$board[$i][$j]:-$board[$i][$j])<=0) {
								continue;
							}
							if (isPossible($board,$i,$j,$fromx,$fromy) ||
								isPossible($board,$i,$j,$fromx-1,$fromy) ||
								isPossible($board,$i,$j,$fromx-2,$fromy)) {
								answer([false, "wrong king move"]);
							}
						}
						//Queenside castling
						$ctry .= chr($blackturn?0:7);
						$ctry .= chr(($blackturn?0:7) | 48);
						if ($blackturn) {
							$board[0][0] = 0;
							$board[3][0] = -2;
						} else {
							$board[0][7] = 0;
							$board[3][7] = 2;
						}
						$kingmove = true;
					} elseif ($tox == $fromx + 2 && $cright && $board[$fromx+1][$toy]==0 && $board[$fromx+2][$toy]==0) {
						for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
							if (($blackturn?$board[$i][$j]:-$board[$i][$j])<=0) continue;
							if (isPossible($board,$i,$j,$fromx,$fromy) ||
								isPossible($board,$i,$j,$fromx+1,$fromy) ||
								isPossible($board,$i,$j,$fromx+2,$fromy)) {
								answer([false, "wrong king move"]);
							}
						}
						//Kingside castling
						$ctry .= chr(($blackturn?0:7) | 112);
						$ctry .= chr(($blackturn?0:7) | 80);
						if ($blackturn) {
							$board[7][0] = 0;
							$board[5][0] = -2;
						} else {
							$board[7][7] = 0;
							$board[5][7] = 2;
						}
						$kingmove = true;
					} else answer([false, "wrong king move"]);
				} else $kingmove = true;
		}
		//- Move the piece on board to initiate further tests
		$killblow = ($board[$tox][$toy] != 0);
		$board[$tox][$toy] = $board[$fromx][$fromy];
		$board[$fromx][$fromy] = 0;
		//- If they can get our king after the move, then it is invalid
		if ($kingmove && $blackturn) { $bkingx = $tox; $bkingy = $toy; }
		if ($kingmove && !$blackturn) { $wkingx = $tox; $wkingy = $toy; }
		for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
			if ($blackturn && isPossible($board,$i,$j,$bkingx,$bkingy)) {
				answer([false, "king uncovered to enemy"]);
			}
			if (!$blackturn && isPossible($board,$i,$j,$wkingx,$wkingy)) {
				answer([false, "king uncovered to enemy"]);
			}
		}
		//- If it's a pawn promotion then a piece has to be selected
		$prom = "";
		if ($blackturn && $toy == 7 && $board[$tox][$toy] == -1) {
			if ($_GET["pro"] == 2) { //Rook promotion
				$prom .= chr(10);
				$board[$tox][$toy] = -2;
			} elseif ($_GET["pro"] == 3) { //Knight promotion
				$prom .= chr(11);
				$board[$tox][$toy] = -3;
			} elseif ($_GET["pro"] == 4) { //Bishop promotion
				$prom .= chr(12);
				$board[$tox][$toy] = -4;
			} elseif ($_GET["pro"] == 5) { //Queen promotion
				$prom .= chr(13);
				$board[$tox][$toy] = -5;
			} else { //Unknown promotion
				answer([false, "unknown promotion"]);
			}
		}
		elseif (!$blackturn && $toy == 0 && $board[$tox][$toy] == 1) {
			if ($_GET["pro"] == 2) { //Rook promotion
				$prom .= chr(10);
				$board[$tox][$toy] = 2;
			} elseif ($_GET["pro"] == 3) { //Knight promotion
				$prom .= chr(11);
				$board[$tox][$toy] = 3;
			} elseif ($_GET["pro"] == 4) { //Bishop promotion
				$prom .= chr(12);
				$board[$tox][$toy] = 4;
			} elseif ($_GET["pro"] == 5) { //Queen promotion
				$prom .= chr(13);
				$board[$tox][$toy] = 5;
			} else { //Unknown promotion
				answer([false, "unknown promotion"]);
			}
		}
		
		$goodtype = "game continues";
		//Check for check
		for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
			if (!$blackturn && isPossible($board,$i,$j,$bkingx,$bkingy)) {
				$goodtype = "check";
			}
			if ($blackturn && isPossible($board,$i,$j,$wkingx,$wkingy)) {
				$goodtype = "check";
			}
		}
		//Check for endgame
		$canmove = false;
		$ckingx = $blackturn ? $wkingx : $bkingx;
		$ckingy = $blackturn ? $wkingy : $bkingy;
		for ($i=0;$i<8;$i++) for ($j=0;$j<8;$j++) {
			for ($k=0;$k<8;$k++) for ($l=0;$l<8;$l++) {
				if ($blackturn && $board[$i][$j] <= 0) continue;
				if (!$blackturn && $board[$i][$j] >= 0) continue;
				if (isPossible($board,$i,$j,$k,$l)) {
					$subboard = $board;
					$subboard[$k][$l] = $subboard[$i][$j];
					$subboard[$i][$j] = 0;
					$currentkingx = $ckingx;
					$currentkingy = $ckingy;
					if ($subboard[$k][$l] == ($blackturn ? 6 : -6)) {
						$currentkingx = $k;
						$currentkingy = $l;
					}
					$cangetking = false;
					
					for ($m=0;$m<8;$m++) for ($n=0;$n<8;$n++) {
						if (($blackturn?-$subboard[$m][$n]:$subboard[$m][$n])>0 && isPossible($subboard,$m,$n,$currentkingx,$currentkingy)) {
							$cangetking = true;
							break;
						}
					}
					if (!$cangetking) {
						$canmove = true;
						goto noend;
					}
				}
			}
		}
		noend:
		if (!$canmove) {
			$goodtype = ($goodtype=="check" ? "checkmate" : "stalemate");
		}
		
		//Add move to binary string
		$data["moves"] .= chr(($fromx << 4) | $fromy);
		$data["moves"] .= chr(($tox << 4) | $toy);
		if (strlen($ctry) > 0) { //If castling - we have to move the other piece too
			$data["moves"] .= chr(15); //Player turn override code
			$data["moves"] .= $ctry;
		}
		$data["moves"] .= $prom;
		//Check if game should be finished
		$gameends = 0;
		if ($goodtype == "checkmate") {
			$gameends = $blackturn?2:1;
		} elseif ($goodtype == "stalemate") {
			$gameends = 3;
		}
		//Add an algebraic notation move
		if (strlen($ctry) > 0) {
			$data["algstr"].="O-O";
			if ($tox == 2) $data["algstr"].="-O";
		} else {
			$thispiece = abs($board[$tox][$toy]);
			if (strlen($prom) > 0) $thispiece = 1;
			switch ($thispiece) {
				case 2: $data["algstr"].="W"; break;
				case 3: $data["algstr"].="S"; break;
				case 4: $data["algstr"].="G"; break;
				case 5: $data["algstr"].="H"; break;
				case 6: $data["algstr"].="K"; break;
			}
			$data["algstr"].=chr(ord('a') + $fromx);
			$data["algstr"].=chr(ord('0') + (8 - $fromy));
			$data["algstr"].=($killblow?":":"-");
			$data["algstr"].=chr(ord('a') + $tox);
			$data["algstr"].=chr(ord('0') + (8 - $toy));
			if (strlen($prom) > 0) {
				if (ord($prom) == 10) $data["algstr"].="W";
				elseif (ord($prom) == 11) $data["algstr"].="S";
				elseif (ord($prom) == 12) $data["algstr"].="G";
				elseif (ord($prom) == 13) $data["algstr"].="H";
			}
		}
		if ($goodtype == "check") $data["algstr"] .= "+";
		if ($goodtype == "checkmate") $data["algstr"] .= "#";
		$data["algstr"] .= " ";
		switch ($gameends) {
			case 1: $data["algstr"] .= "1-0"; break;
			case 2: $data["algstr"] .= "0-1"; break;
			case 3: $data["algstr"] .= "0.5-0.5"; break;
		}
		//Push that binary string to database
		$db->exec("UPDATE games SET moves = ?, finished = ?, algebraic = ? WHERE game_id = ?", [$data["moves"], $gameends, $data["algstr"], $game]);
		
		//Send good message
		answer([true, $goodtype]);
		
		
		
	} elseif (!isset($_GET["from"]) && !isset($_GET["to"])) {
		if (!isset($_GET["slot"])) {
			$algformat = "";
			$algarr = explode(" ", $data["algstr"]);
			$turnnum = 0;
			for ($i=0; $i<count($algarr); $i++) {
				$t = $algarr[$i];
				if ($t == "") continue;
				if ($t == "1-0" || $t == "0-1" || $t == "0.5-0.5") {
					$algformat .= "<br>" . $t;
					break;
				}
				if ($i%2==0) {
					$turnnum++;
					if ($turnnum != 1) $algformat .= "<br>";
					$algformat .= "<b>" . $turnnum . ".</b>";
				}
				$algformat .= " ";
				$algformat .= $t;
			}
			//Output current game as json
			$result = [
				"white" => $data["white"],
				"black" => $data["black"],
				"whitenick" => $data["whitenick"],
				"blacknick" => $data["blacknick"],
				"finished" => $data["end"],
				"board" => $board,
				"turn" => ($blackturn?"black":"white"),
				"moves" => $algformat
			];
			answer([true, $result]);
		} elseif (isset($_GET["slot"]) && $_GET["slot"] !== "") {
			$slotnum = intval($_GET["slot"]);
			$playernum = intval($_SESSION["userid"]);
			if ($slotnum == 0 && $data["white"] !== null) answer([false, "slot is full"]);
			if ($slotnum == 1 && $data["black"] !== null) answer([false, "slot is full"]);
			if ($data["white"] == $playernum || $data["black"] == $playernum) answer([false, "player already ingame"]);
			if ($slotnum == 0) {
				$db->exec("UPDATE games SET player_white = ? WHERE game_id = ?", [ $playernum, $game ]);
			} else {
				$db->exec("UPDATE games SET player_black = ? WHERE game_id = ?", [ $playernum, $game ]);
			}
		} else {
			answer([false, "unknown slot/player arguments"]);
		}
	} else {
		answer([false, "unknown from/to arguments"]);
	}
}

answer([false, "unknown action"]);

?>