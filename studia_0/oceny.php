<?php
require_once("class.php");
require_once("ocena.php");
Studia::naglowek("Obsługa ocen");
Studia::menu(); 
?>
<center>
	<?php
	
	$polecenie = '';
	if (isset($_POST['przycisk'])) {
		echo "<br>";
		echo "main";
		echo "<br>";
		var_dump($_POST);
		echo "<br>";
		$nrStudenta = key($_POST['przycisk']);// nr studenta 
		$nrPrzedmiotu = key($_POST['przycisk'][$nrStudenta]);// nr przedmiotu 
		echo $nrStudenta;
		echo $nrPrzedmiotu;
		#$wartosc = key($_POST['przycisk'][$nrStudenta][$nrPrzedmiotu]);
		// echo "<br>";
		// echo $wartosc;
		// echo "<br>";
		$polecenie = $_POST['przycisk'][$nrStudenta][$nrPrzedmiotu];// jaka operacja
		echo $polecenie;
		// echo "<br>";
		// //var_dump($polecenie);
		// echo "<br>";
		// echo $nrStudenta;
		// echo $nrPrzedmiotu;
		// echo "<br>";
		$ocena = Ocena::getOcena($nrStudenta, $nrPrzedmiotu); 	
	
		switch ($polecenie) {
			case 'Nowa ocena':
				Ocena::nowa();
				break;
			case 'Edytuj':
				$ocena->edytuj_ocene();
				break;
			case 'Usuń':
				echo "case usun";
				$ocena->usun_ocene();
				break;
			case 'Zapisz':
				$ocena->zapisz_ocene();
				break;
		}
	}
	Ocena::wypiszWszystkie();
?>
</center>
<?php
Studia::stopka();
?>