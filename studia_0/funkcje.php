<?php

function otworz_polaczenie(){
	global $polaczenie;
	$serwer = "127.0.0.1";
	$uzytkownik = "root";
	$haslo = "";
	$baza = "studia";

	$polaczenie = mysqli_connect($serwer, $uzytkownik, $haslo) 
		or exit("Nieudane połączenie z serwerem");	
	if(!mysqli_select_db($polaczenie, $baza)) {
		if (mysqli_errno($polaczenie) == 1049) return false;
		else echo("Nieudane połączenie z bazą $baza<br>");
	}
	mysqli_set_charset($polaczenie, "utf8");
	return true;
}

function zamknij_polaczenie(){
	global $polaczenie;
	mysqli_close($polaczenie);
}

function laduj_baze() {
	// sprawdza czy jest baza, jesli nie tworzy cala strukture i dane testowe
	if(!otworz_polaczenie()) {
		utworz_baze();
		otworz_polaczenie();
		utworz_tabele();
		wstaw_dane_testowe();		
		header("location: studia.php");
	}
	zamknij_polaczenie();
}

function utworz_baze() {
	$polaczenie = mysqli_connect("127.0.0.1", "root", "") or exit("Nieudane połączenie z serwerem");
	$baza = 'studia';
	
	echo "Tworzę bazę danych '$baza' ... <br>";
	mysqli_query($polaczenie, "CREATE DATABASE `$baza` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;") 
	or exit("Błąd w zapytaniu tworzącym bazę");
}

function utworz_tabele() {
	global $polaczenie;

	$rozkaz = 	"create table przedmioty " .
				"(numer int NOT NULL AUTO_INCREMENT ," .
				"nazwa varchar(32), " .	
				"godzin int, PRIMARY KEY (`numer`))";
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: $rozkaz");
	
	$rozkaz = 	"create table studenci " .
				"(numer int NOT NULL AUTO_INCREMENT ," .
				"imie varchar(32), " .	
				"nazwisko varchar(32), " .	
				"PRIMARY KEY (`numer`))";
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: $rozkaz");
	
	$rozkaz = 	"create table oceny " .
				"(nr_stud int NOT NULL, " .
				"nr_przed int NOT NULL, " .	
				"ocena float " .	
				")";
	mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: $rozkaz");
	//echo $rozkaz;
}

function wstaw_dane_testowe() {
	global $polaczenie;
	$rozkazy = array("insert into przedmioty values(null, 'Programowanie', 30);",
					 "insert into przedmioty values(null, 'Szydełkowanie', 20);",
				     "insert into przedmioty values(null, 'Pływanie', 50);");	
	foreach($rozkazy as $rozkaz)
		mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);		   
	
	$rozkazy = array("insert into studenci values(null, 'Jan', 'Smith');",
					 "insert into studenci values(null, 'Agnieszka', 'Bond');",
					 "insert into studenci values(null, 'Monika', 'Ratownik');");
	foreach($rozkazy as $rozkaz)				 
		mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
	
	$rozkazy = array("insert into oceny values(1, 1, 4.0);",
				     "insert into oceny values(1, 2, 5.5);",
					 "insert into oceny values(3, 3, 5.0);");			   
	foreach($rozkazy as $rozkaz)				 
		mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
}

?>