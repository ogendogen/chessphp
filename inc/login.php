<?php
if (isset($_GET['logout'])) {
	unset($_SESSION['userid']);
	unset($_SESSION['username']);
	session_destroy();
	modal("Powodzenie", "Zostałeś wylogowany!", "success");
	echo "<meta http-equiv=\"refresh\" content=\"1;url=http://".$site."\"/>";
	die();
}
else if (isset($_SESSION['userid'])) {
	echo("<form method=\"post\" action=\"?tab=login\"><input type=\"submit\" name=\"logout\" value=\"Wyloguj\"></form>");
} else {
	if (!isset($_POST["nick"]) || !isset($_POST["pass"]) || $_POST["nick"] == "" || $_POST["pass"] == "") {
		if ($_POST["nick"] === "" || $_POST["pass"] === "") {
			modal("Uwaga!", "Wypełnij wszystkie pola!", "warning");
		}
?>
<!-- HTML've been here -->
<?php
	} else {
		$result = $db->exec("SELECT User_ID, Name, Pass, Salt, Auth FROM users WHERE Name = ?", [$_POST["nick"]]);
		if (count($result) == 0) {
			modal("Błąd!", "Użytkownik o podanym nicku nie istnieje.", "danger");
		} else if ($result[0]["Pass"] != sha1(sha1($_POST["pass"]) . md5($result[0]["Salt"]))) {
			modal("Błąd!", "Podano niepoprawne hasło.", "danger");
		} else if ($result[0]["Auth"] != "") {
			modal("Uwaga!", "Użytkownik nie został aktywowany. Sprawdź swoją skrzynkę pocztową.", "warning");
		} else {
			$_SESSION["userid"] = $result[0]["User_ID"];
			$_SESSION['username'] = $result[0]["Name"];
			modal("Powodzenie", "Zostałeś zalogowany.", "success");
			echo "<meta http-equiv=\"refresh\" content=\"1;url=http://".$site."\"/>";
			die();
		}
	}
}
?>

<div class="container">
	<div class="row text-center" style="margin-top: 50px;">

		<div class="col-md-8 col-md-offset-2">
			<form role="form" method="post" action="?tab=login" alt="formularz logowania">

				<legend class="text-center">Logowanie się</legend>

				<fieldset>

					<div class="form-group col-md-6">
						<label for="first_name">Nick</label>
						<input type="text" class="form-control" name="nick" id="first_name" placeholder="Twój nick" alt="twoj nick">
					</div>
					
					<div class="form-group col-md-6">
						<label for="password">Hasło</label>
						<input type="password" class="form-control" name="pass" id="password" placeholder="Twoje hasło" alt="twoje haslo">
					</div>


				</fieldset>
				
				<div class="form-group">
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-primary" alt="przycisk logowania">
							Zaloguj się
						</button>
					</div>
				</div>
				
			</form>
		</div>
	</div>
</div>