<?php
include('funkcje.php');

function wypisz_studenci(){
	// zmienna przechowująca uchwyt do bazy
	// uzyskany jako wynik mysql_connect()
	global $polaczenie;

	$zapytanie = "select * from studenci";	
	$wynik = mysqli_query($polaczenie, $zapytanie);
	// gdy zapytanie nie wykona się poprawnie funkcja jest przerywana 
	if(!$wynik) return;

  // generowanie formularza, nagłówków tabeli i przycisku dodawania nowego rekordu (studenta)
	$naglowki = array("Imię", "Nazwisko");
	print("<form method='POST'>");
	print("<b>STUDENCI</b><br>");
	print("<table border = 1><tr>");
	foreach($naglowki as $naglowek) print("<td><b>$naglowek</b></td>");
	// zapis name='przycisk[]' oznacza że po wysłaniu formularza
	// w tablicy danych przesyłanych metodą POST
	// w podtablicy o nazwie 'przycisk', pod pierwszym wolnym indeksem
	// zapisze się wartość 'Dodaj nowego' jeśli ten właśnie przycisk został wciśnięty
	print("<td align='center'><b><input type='submit' name='przycisk[-1]' value='Dodaj nowego'></b></td>");	
	print("</tr>");
  // generowanie pozostałych wierszy tabeli zawierających dane studentów
  // oraz przyciski do wykonania operacji na każdym z nich  
	while($wiersz = mysqli_fetch_row($wynik)){		
			print("<tr>");
			foreach($wiersz as $p=>$pole)
				if($p != 0) print("<td>$pole</td>");
		  // wciśnięcie przycisku ustawi odpowiednią nazwę operacji do wykonania
		  // jako wartość elementu 'przycisk[id]', gdzie id jest kluczem głównym z tabeli studetów
			print("<td align='center'><input type='submit' name='przycisk[$wiersz[0]]' value='Edytuj'>
									  <input type='submit' name='przycisk[$wiersz[0]]' value='Usuń'></td>");	
			print("</tr>");		
	}
	print("</table>");
    print("</form>");
	mysqli_free_result($wynik);
}

function edytuj_studenta($nr = -1) {
	global $polaczenie;	
	
  // poniższy fragment ustawia wartości zmiennych imie i nazwisko
  // wyciągając z bazy dla studenta o podanym w parametrze numerze
	if($nr != -1) { // edycja
		$rozkaz = "select imie, nazwisko from studenci where numer=$nr;";
		$rekord = mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
                
        $student = mysqli_fetch_row($rekord);
        $imie = $student[0];
        $nazwisko = $student[1];
                
		//$imie = mysqli_result($rekord, 0, "imie");
		//$nazwisko = mysqli_result($rekord, 0, "nazwisko");
	}
	else { // dodanie nowego
		$imie=''; $nazwisko='';
	}
	
  // generuje formularz do edycji imienia i nazwiska studenta	
echo " 
	<form method=POST action=''> 
	<table border=0>
	<tr>
	<td>Imię</td><td colspan=2>
	<input type=text name='imie' value='$imie' size=15 style='text-align: left'></td>
	</tr>
	<tr>
	<td>Nazwisko</td><td colspan=2>
	<input type=text name='nazwisko' value='$nazwisko' size=15 style='text-align: left'></td>
	</tr>
	<tr>
	<td colspan=3>
	<input type=submit name='przycisk[$nr]' value='Zapisz' style='width:200px'></td>
	</tr>
	</table></form>";
}

function zapisz_studenta($nr) {
	global $polaczenie;
	$imie = $_POST['imie'];
	$nazwisko = $_POST['nazwisko'];
	if($nr != -1)
		$rozkaz = "update studenci set imie='$imie', nazwisko='$nazwisko' where numer=$nr;";
	else $rozkaz = "insert into studenci values(null, '$imie', '$nazwisko');";		
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);	
}

function usun_studenta($nr) {
	global $polaczenie;
	
	$rozkaz = "delete from studenci where numer=$nr;";		
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
}

?>

<html>
<head>
<meta charset="utf-8">
<title>Obsługa studentów</title>
</head>
<body bgcolor=yellow text="#000FFF">

<input type=button value=" STUDENCI " onClick="window.location='studenci.php'">
<br><br>
<form name=menu action='oceny.php'>
<input type=submit value=" OCENY "> 
</form>
<a href='przedmioty.php'> PRZEDMIOTY </a>

<hr>
<center>

<?php
//print_r($_POST);

$polecenie = '';
if(isset($_POST['przycisk'])) {	
	$nr = key($_POST['przycisk']);
	$polecenie = $_POST['przycisk'][$nr];
}

otworz_polaczenie();

switch($polecenie) {
	case 'Edytuj': edytuj_studenta($nr); break;
	case 'Dodaj nowego': edytuj_studenta(); break;
	case 'Usuń': usun_studenta($nr); break;
	case 'Zapisz': zapisz_studenta($nr); break;
}

wypisz_studenci();
zamknij_polaczenie();
?>

</center>
</body>
</html>
