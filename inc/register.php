<?php
if (isset($_SESSION['userid'])) {
	modal("Uwaga!", "Rejestracja jest możliwa tylko dla niezalogowanych użytkowników.", "warning");
} else if (isset($_GET["auth"]) && $_GET["auth"] !== "" && isset($_GET["user"]) && $_GET["user"] !== "") {
	//Auth thing
	//$result = $db->exec("SELECT auth FROM users WHERE name LIKE \"" . $_GET["user"] . "\"");
	$result = $db->exec("SELECT auth FROM users WHERE name LIKE \"" . $_GET["user"] . "\"")[0]["auth"];
	if ($result === $_GET["auth"]) {
		$db->exec("UPDATE users SET auth = \"\" WHERE name LIKE \"" . $_GET["user"] . "\"");
		modal("Powodzenie", "Użytkownik " . $_GET["user"] . " został pomyślnie aktywowany.", "success");
	} else {
		modal("Błąd", "Nieznany bądź już aktywowany użytkownik.", "danger");
	}
} else {
	if (!isset($_POST["nick"]) || !isset($_POST["pass"]) || !isset($_POST["mail"]) || $_POST["nick"] == "" || $_POST["pass"] == "" || $_POST["mail"] == "") {
		if ($_POST["nick"] === "" || $_POST["pass"] === "" || $_POST["mail"] === "") {
			modal("Uwaga!", "Wypełnij wszystkie pola.", "warning");
		}
?>

<div class="container">
	<div class="row" style="margin-top: 50px;">

		<div class="col-md-12">
			<form role="form" method="post" action="?tab=register">

				<legend class="text-center">Rejestracja</legend>

				<fieldset class="text-center">

					<div class="form-group col-xs-12 col-md-6 col-md-offset-3">
						<label for="first_name">Nick</label>
						<input type="text" class="form-control" name="nick" id="first_name" placeholder="Twój nick">
					</div>
					
					<div class="form-group col-xs-12 col-md-6 col-md-offset-3">
						<label for="password">Hasło</label>
						<input type="password" class="form-control" name="pass" id="password" placeholder="Twoje hasło" onblur="validatePasswords()">
					</div>
					
					<div class="form-group col-xs-12 col-md-6 col-md-offset-3">
						<label for="password">Potwierdź hasło</label>
						<input type="password" class="form-control" name="pass2" id="password2" placeholder="Potwierdź hasło" onblur="validatePasswords()">
					</div>
					
					<div class="form-group col-xs-12 col-md-6 col-md-offset-3">
						<label for="password">Email</label>
						<input type="text" class="form-control" name="mail" id="email" placeholder="Twój email" onblur="validateEmail()">
					</div>

				</fieldset>
				
				<div class="form-group text-center">
					<div class="col-md-12">
						<button type="submit" id="btn" class="btn btn-primary">
							Zarejestruj się
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="js/validations.js"></script>

<?php
	} else {
		$salt = sha1(rand(0,9999999999));
		$auth = md5(rand(0,9999999999));
		$db->exec("INSERT INTO users (Name, Pass, Salt, Mail, Auth) VALUES (?, ?, ?, ?, ?)", array($_POST["nick"], sha1(sha1($_POST["pass"]) . md5($salt)), $salt, $_POST["mail"], $auth));
		$msg = "Witaj, " . $_POST["nick"] . ".\r\nŻeby móc grać w naszym serwisie, należy aktywować swoje konto. Możesz to zrobić klikając w ten link:\r\n";
		$msg .= $site . "/index.php?tab=register&user=" . $_POST["nick"] . "&auth=" . $auth;
		mail($_POST["mail"],"Rejestracja na stronie Szachy Online",$msg);
		modal("Uwaga!", "Do pełnego dostępu do serwisu potrzebna jest aktywacja konta - sprawdź skrzynkę pocztową.", "warning");
	}
}
?>