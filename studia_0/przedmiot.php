<?php
class Przedmiot{
    private $numer;
    private $nazwa;
    private $godziny;

    function __construct($id, $imie, $nazwisko) {
        $this->numer = $id;
        $this->nazwa = $imie;
        $this->godziny = $nazwisko;
    }

    
    public static function getPrzedmiot($id)
    {
        $baza = Studia::otworzPolaczenie();
        $sql = 'SELECT * FROM przedmioty WHERE numer = :id';
        $zapytanie = $baza->prepare($sql);
        $zapytanie->bindParam(':id', $id, PDO::PARAM_INT);
        $zapytanie->execute();
        $row = $zapytanie->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Przedmiot($row['numer'], $row['nazwa'], $row['godzin']);
        } else {
            return new Przedmiot(-1,'','');
        }
    }

    function edytuj()
    {
        echo " <form method=POST action=''> 
        <table border=0> <tr> <td>Nazwa</td><td colspan=2> 
        <input type=text name='nazwa' value='$this->nazwa' size=15 style='text-align: left'>
        </td> </tr> <tr> <td>Godziny</td><td colspan=2> 
        <input type=text name='godziny' value='$this->godziny' size=15 style='text-align: left'></td> </tr> <tr>
         <td colspan=3> 
         <input type=submit name='przycisk[$this->numer]' value='Zapisz' style='width:200px'></td> </tr> 
         </table></form>";
    }

    function zapisz(){
        $this->nazwa = $_POST['nazwa'];
        $this->godziny = $_POST['godziny'];

        $baza = Studia::otworzPolaczenie();

        if ($this->numer == -1) {
            $sql = 'INSERT INTO przedmioty SET numer = :numer, nazwa = :nazwa, godzin = :godziny';
            $this->numer = null;
        } else {
            $sql = 'UPDATE przedmioty SET nazwa = :nazwa, godzin = :godziny WHERE numer = :numer';
        }

        $zapytanie = $baza->prepare($sql);
        $zapytanie->bindParam(':numer', $this->numer, PDO::PARAM_INT);
        $zapytanie->bindParam(':nazwa', $this->nazwa, PDO::PARAM_STR);
        $zapytanie->bindParam(':godziny', $this->godziny, PDO::PARAM_INT);
        $zapytanie->execute();
    }
    public static function selectPrzedmiot(){
        $baza = Studia::otworzPolaczenie();
        $rozkaz = "select * from przedmioty;";
        # $studenci = mysqli_query($baza, $rozkaz);
        $wynik = $baza->query($rozkaz);
        $selectPrzedmiot = "<select name=nr_przed>";
        while($rekord = $wynik->fetch(PDO::FETCH_NUM))
            $selectPrzedmiot .= 
                "<option value=$rekord[0]>$rekord[1] $rekord[2]</option>";
        $selectPrzedmiot .= "</select>";
        return $selectPrzedmiot;
    }

    function usun(){
        $baza = Studia::otworzPolaczenie();


        $sql = 'DELETE FROM przedmioty WHERE numer = :id';
        

        $zapytanie = $baza->prepare($sql);
        $zapytanie->execute([':id'=>$this->numer]);
    }

    public static function wypisz_wszystkie()
    {
        // zmienna przechowująca uchwyt do bazy
        // uzyskany jako wynik mysql_connect()
        $polaczenie = Studia::otworzPolaczenie();

        $zapytanie = "select * from przedmioty";
        $wynik = $polaczenie->query($zapytanie);
        // gdy zapytanie nie wykona się poprawnie funkcja jest przerywana 
        if (!$wynik)
            return;

        // generowanie formularza, nagłówków tabeli i przycisku dodawania nowego rekordu (studenta)
        $naglowki = array("Nazwa", "Godzin");
        print("<form method='POST'>");
        print("<b>STUDENCI</b><br>");
        print("<table border = 1><tr>");
        foreach ($naglowki as $naglowek)
            print("<td><b>$naglowek</b></td>");
        // zapis name='przycisk[]' oznacza że po wysłaniu formularza
        // w tablicy danych przesyłanych metodą POST
        // w podtablicy o nazwie 'przycisk', pod pierwszym wolnym indeksem
        // zapisze się wartość 'Dodaj nowego' jeśli ten właśnie przycisk został wciśnięty
        print("<td align='center'><b><input type='submit' name='przycisk[-1]' value='Dodaj nowego'></b></td>");
        print("</tr>");
        // generowanie pozostałych wierszy tabeli zawierających dane studentów
        // oraz przyciski do wykonania operacji na każdym z nich  
        while($wiersz = $wynik->fetch(PDO::FETCH_NUM)){		
            print("<tr>");
            foreach ($wiersz as $p => $pole)
                if ($p != 0)
                    print("<td>$pole</td>");
            // wciśnięcie przycisku ustawi odpowiednią nazwę operacji do wykonania
            // jako wartość elementu 'przycisk[id]', gdzie id jest kluczem głównym z tabeli studetów
            print("<td align='center'><input type='submit' name='przycisk[$wiersz[0]]' value='Edytuj'>
                                              <input type='submit' name='przycisk[$wiersz[0]]' value='Usuń'></td>");
            print("</tr>");
        }
        print("</table>");
        print("</form>");
        $wynik->closeCursor();
    }
}

?>