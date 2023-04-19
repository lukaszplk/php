<?php
include('funkcje.php');

function wypisz_przedmioty(){
	global $polaczenie;

	$zapytanie = "select * from przedmioty";	
	$wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: ".$rozkaz);	

  // generowanie formularza, nagłówków tabeli i przycisku dodawania nowego rekordu (studenta)
	$naglowki = array("Nazwa", "Liczba godzin");
	print("<form method='POST'>");
	print("<b>PRZEDMIOTY</b><br>");
	print("<table border = 1><tr>");
	foreach($naglowki as $naglowek) print("<td><b>$naglowek</b></td>");
	
	print("<td align='center'><b><input type='submit' name='przycisk[-1]' value='Dodaj nowy'></b></td>");	
	print("</tr>");
	while($wiersz = mysqli_fetch_row($wynik)){		
			print("<tr>");
			foreach($wiersz as $p=>$pole)
				if($p != 0) print("<td>" . $pole . "</td>");		  
			print("<td align='center'><input type='submit' name='przycisk[".$wiersz[0]."]' value='Edytuj'>
									  <input type='submit' name='przycisk[".$wiersz[0]."]' value='Usun'></td>");	
			print("</tr>");		
	}
	print("</table>");
    print("</form>");
	mysqli_free_result($wynik);
}

function edytuj_przedmiot($nr = -1) {
	global $polaczenie;	
	
	if($nr != -1) {
		$rozkaz = "select nazwa, godzin from przedmioty where numer=$nr;";
		$rekord = mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
                
        $przedmiot = mysqli_fetch_row($rekord);
        $nazwa = $przedmiot[0];
        $godziny = $przedmiot[1];
	}
	else {
		$nazwa=''; $godziny='';
	}
	
  // generuje formularz do edycji
	echo " 
	<form method=POST action=''> 
	<table border=0>
	<tr>
	<td>Nazwa</td><td colspan=2>
	<input type=text name='nazwa' value='$nazwa' size=25 style='text-align: left'></td>
	</tr>
	<tr>
	<td>Liczba godzin</td><td colspan=2>
	<input type=text name='godziny' value='$godziny' size=10 style='text-align: left'></td>
	</tr>
	<tr>
	<td colspan=3>
	<input type=submit name='przycisk[$nr]' value='Zapisz' style='width:200'></td>
	</tr>
	</table></form>";
}

function zapisz_przedmiot($nr) {
	global $polaczenie;
	$nazwa = $_POST['nazwa'];
	$godziny = $_POST['godziny'];
	if($nr != -1)
		$rozkaz = "update przedmioty set nazwa='$nazwa', godzin='$godziny' where numer=$nr;";
	else $rozkaz = "insert into przedmioty values(null, '$nazwa', '$godziny');";		
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);	
}

function usun_przedmiot($nr) {
	global $polaczenie;
	
	$rozkaz = "delete from przedmioty where numer=$nr;";		
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
}

?>

<html>
<head>
<meta charset="utf-8">
<title>Obsługa przedmiotów</title>
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
	$polecenie = current($_POST['przycisk']);
	$nr = key($_POST['przycisk']);
}


otworz_polaczenie();

switch($polecenie) {
	case 'Edytuj': edytuj_przedmiot($nr); break;
	case 'Dodaj nowy': edytuj_przedmiot(); break;	
	case 'Usun': usun_przedmiot($nr); break;
	case 'Zapisz': zapisz_przedmiot($nr); break;
}

wypisz_przedmioty();
zamknij_polaczenie();
?>

</center>
</body>
</html>
