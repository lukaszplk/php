<?php
require_once('przedmiot.php');
require_once('class.php');
Studia::naglowek('Obsluga przedmiotow');
Studia::menu();
?>
<center>

<?php
//print_r($_POST);

$polecenie = '';
if(isset($_POST['przycisk'])) {	
	$nr = key($_POST['przycisk']);
	$polecenie = $_POST['przycisk'][$nr];
	$przedmiot = Przedmiot::getPrzedmiot($nr);


//otworz_polaczenie();

switch($polecenie) {
	case 'Edytuj': $przedmiot->edytuj(); break;
	case 'Dodaj nowego': $przedmiot->edytuj(); break;
	case 'Usuń':  $przedmiot->usun(); break;
	case 'Zapisz':  $przedmiot->zapisz(); break;
}
}

Przedmiot::wypisz_wszystkie();
//zamknij_polaczenie();
?>

</center>
<?php
Studia::stopka();
?>
