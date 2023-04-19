<?php
require_once('class.php');
Studia::naglowek('Obsluga studentow');
Studia::menu();
?>
<center>

<?php
//print_r($_POST);

$polecenie = '';
if(isset($_POST['przycisk'])) {	
	$nr = key($_POST['przycisk']);
	$polecenie = $_POST['przycisk'][$nr];
	$student = Student::getStudent($nr);


//otworz_polaczenie();

switch($polecenie) {
	case 'Edytuj': $student->edytuj(); break;
	case 'Dodaj nowego': $student->edytuj(); break;
	case 'Usuń':  $student->usun(); break;
	case 'Zapisz':  $student->zapisz(); break;
}
}

Student::wypisz_wszystkie();
//zamknij_polaczenie();
?>

</center>
<?php
Studia::stopka();
?>
