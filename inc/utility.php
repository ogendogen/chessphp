<?php
require_once("inc/constants.php");

function modal($header, $message, $type) {
	//if (!is_string($type)) return;
	if ($type == "normal") {
		echo '<script>$(document).ready(function(){ 
			$("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">'.$header.'</span>");
			$("#myModal .modal-dialog .modal-body").text("'.$message.'");
			$("#myModal").modal("show"); 
		});
		</script>';
	}
	else if ($type == "success") {
		echo '<script>$(document).ready(function(){ 
			$("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">'.$header.'</span>");
			$("#myModal .modal-dialog .modal-body").html("<div class=\"alert alert-success\"><span style=\"font-weight: bold;\">'.$message.'</span></div>");
			$("#myModal").modal("show"); 
		});
		</script>';
	}
	else if ($type == "warning") {
		echo '<script>$(document).ready(function(){ 
			$("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">'.$header.'</span>");
			$("#myModal .modal-dialog .modal-body").html("<div class=\"alert alert-warning\"><span style=\"font-weight: bold;\">'.$message.'</span></div>");
			$("#myModal").modal("show"); 
		});
		</script>';
	}
	else if ($type == "danger") {
		echo '<script>$(document).ready(function(){ 
			$("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">'.$header.'</span>");
			$("#myModal .modal-dialog .modal-body").html("<div class=\"alert alert-danger\"><span style=\"font-weight: bold;\">'.$message.'</span></div>");
			$("#myModal").modal("show"); 
		});
		</script>';
	}
}

function error($reason) {
	modal("Błąd krytyczny", $reason, "danger");
	include_once("inc/footer.php");
	die();
}

class Database {
	var $conn;
	function Database() {
		try {
			global $host;
			global $user;
			global $pass;
			global $database;
			$this->conn = new PDO("mysql:host=".$host.";dbname=".$database, $user, $pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			error("Połączenie z bazą nie powiodło się: " . $e->getMessage());
		}
	}
	function exec($query,$params = null) {
		try {
			$retval = null;
			$q = $this->conn->prepare($query);
			if ($q->execute($params)) {
				if (substr($query,0,6) === "SELECT")
					$retval = $q->fetchAll(PDO::FETCH_ASSOC);
				$q->closeCursor();
			}
			return $retval;
		} catch (PDOException $e) {
			error("Błąd bazy: " . $e->getMessage());
		}
		
	}
}

$db = new Database();

?>