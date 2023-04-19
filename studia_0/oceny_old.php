<?php
require_once('funkcje.php');

function wypisz_oceny(){
	global $polaczenie;

	$zapytanie = "select studenci.numer, przedmioty.numer, imie, nazwisko, nazwa, ocena from studenci,przedmioty,oceny 
                      where studenci.numer=oceny.nr_stud and przedmioty.numer=oceny.nr_przed;";
	$wynik = mysqli_query($polaczenie, $zapytanie);
	//if(!$wynik) return;

	$naglowki = array("Imię", "Nazwisko", "Przedmiot", "Ocena");
	print("<form method='POST'>");
	print("<b>Oceny studentów</b><br>");
	print("<table border = 1><tr>");
	foreach($naglowki as $naglowek)
		print("<td><b>$naglowek</b></td>");
	print("<td align='center'><b><input type='submit' name='przycisk[][]' value='Nowa ocena'></b></td>");	
	print("</tr>");
	
	while($wiersz = mysqli_fetch_row($wynik)){		
			print("<tr>");
			foreach($wiersz as $p=>$pole)
				if($p > 1) print("<td>" . $pole . "</td>");
			print("<td align='center'><input type='submit' name='przycisk[$wiersz[0]][$wiersz[1]]' value='Edytuj'>
									  <input type='submit' name='przycisk[$wiersz[0]][$wiersz[1]]' value='Usuń'></td>");	
			print("</tr>");		
	}
	print("</table>");
    print("</form>");
	mysqli_free_result($wynik);
}

function nowa_ocena() {
	global $polaczenie;	
	
	// generuje liste wyboru studenta
	$rozkaz = "select * from studenci;";
	$studenci = mysqli_query($polaczenie, $rozkaz);
	$selectStudent = "<select name=nr_stud>";
	while($rekord = mysqli_fetch_row($studenci))
		$selectStudent .= 
			"<option value=$rekord[0]>$rekord[1] $rekord[2]</option>";
	$selectStudent .= "</select>";
	
	// generuje liste wyboru przedmiotu
	$rozkaz = "select * from przedmioty;";
	$przedmioty = mysqli_query($polaczenie, $rozkaz);
	$selectPrzedmiot = "<select name=nr_przed>";
	while($rekord = mysqli_fetch_row($przedmioty))
		$selectPrzedmiot .= 
			"<option value=$rekord[0]>$rekord[1]</option>";
	$selectPrzedmiot .= "</select>";
	
	// generuje przyciski wyboru oceny
	$oceny = [1=>'ndst', 'mierny', 'dst', 'db', 'bdb', 'cel'];
	$selectOcena = "";
	foreach($oceny as $o=>$ocena)
		$selectOcena .= 
			"<input type='radio' name='ocena' value=$o>$ocena";
		
	  // generuje formularz do edycji 
echo " 
	<form method=POST action=''> 
	<table border=0>
	<tr>
	<td>Student: </td>
	<td colspan=2>"
	//<input type=text name='nr_stud' value='' size=15 
	//       style='text-align: center; width:50px'></td>
	.$selectStudent
	."</td></tr>	
	<tr>
	<td>Przedmiot: </td>
	<td colspan=2>"
	//<input type=text name='nr_przed' value='' size=15 
	//       style='text-align: center; width:50px'></td>
	.$selectPrzedmiot
	."</td></tr>	
	<tr>
	<td>Ocena: </td><td colspan=2>"
	//<input type=text name='ocena' value='' size=15 
	//       style='text-align: center; width:50px'></td>
	.$selectOcena
	."</td></tr>	
	<tr>
	<td colspan=3>
	<input type=submit name='przycisk[-1][-1]' value='Zapisz' 
			style='width:200px'></td>
	</tr>
	</table></form>";
}

function edytuj_ocene($nr_stud, $nr_przed) {
	global $polaczenie;	
	
  // poniższy fragment ustawia wartości zmiennych imie i nazwisko
  // wyciągając z bazy dla studenta o podanym w parametrze numerze
	
	$rozkaz = "select nr_stud, nr_przed, ocena, imie, nazwisko, nazwa from studenci,przedmioty,oceny 
                      where nr_stud=$nr_stud and nr_przed=$nr_przed and
					  studenci.numer=nr_stud and przedmioty.numer=nr_przed;";
	
	//$rozkaz = "select * from oceny where nr_stud=$nr_stud and nr_przed=$nr_przed;";
	$rekord = mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
	
	$ocena = mysqli_fetch_row($rekord);
    $wartosc = $ocena[2];	
	
  // generuje formularz do edycji 
echo " 
	<form method=POST action=''> 
	<table border=0>
	<tr>
	<td>Student: </td><td colspan=2>$ocena[3] $ocena[4]</td>
	</tr>
	
	<tr>
	<td>Przedmiot: </td><td colspan=2>$ocena[5]</td>
	</tr>
	
	<tr>
	<td>Ocena: </td><td colspan=2>
	<input type=text name='ocena' value='$wartosc' size=15 
	       style='text-align: center; width:50px'></td>
	</tr>
	
	<tr>
	<td colspan=3>
	<input type=submit name='przycisk[$nr_stud][$nr_przed]' value='Zapisz' 
			style='width:200px'></td>
	</tr>
	</table></form>";
}

function zapisz_ocene($nr_stud, $nr_przed) {
	global $polaczenie;
	$wartosc = $_POST['ocena'];
	// gdy klucze obce ida z formularza biore je do zmiennych
	// wpp wartosci ida z parametrow
	if(isset($_POST['nr_stud'])) $nr_stud = $_POST['nr_stud'];
	if(isset($_POST['nr_stud'])) $nr_przed = $_POST['nr_przed'];
	
	// test czy w bazie jest juz ocena oparta o podane klucze obce
	$rozkaz = "select * from oceny where nr_stud=$nr_stud 
								     and nr_przed=$nr_przed;";
	$wynik = mysqli_query($polaczenie, $rozkaz);	
	
	if(mysqli_num_rows($wynik))
		$rozkaz = "update oceny set ocena=$wartosc 
						where nr_stud=$nr_stud and nr_przed=$nr_przed;";
	else {		
		$rozkaz = "insert into oceny values($nr_stud, $nr_przed, $wartosc);";		
	}
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);	
	mysqli_free_result($wynik);
}

function usun_ocene($nr_stud, $nr_przed) {
	global $polaczenie;
	
	$rozkaz = "delete from oceny where nr_stud=$nr_stud and nr_przed=$nr_przed;";		
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
<hr><center>

<?php
//print_r($_POST);

$polecenie = '';
if(isset($_POST['przycisk'])) {	
	$nrStudenta = key($_POST['przycisk']);			// nr studenta
	$nrPrzedmiotu = key($_POST['przycisk'][$nrStudenta]);	// nr przedmiotu
	$polecenie = $_POST['przycisk'][$nrStudenta][$nrPrzedmiotu]; // jaka operacja
}

otworz_polaczenie();

switch($polecenie) {
	case 'Nowa ocena': nowa_ocena(); break;
	case 'Edytuj': edytuj_ocene($nrStudenta, $nrPrzedmiotu); break;
	case 'Usuń': usun_ocene($nrStudenta, $nrPrzedmiotu); break;
	case 'Zapisz': zapisz_ocene($nrStudenta, $nrPrzedmiotu); break;	
}

wypisz_oceny();
zamknij_polaczenie();
?>

</center>
</body>
</html>
