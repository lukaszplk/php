<?php
require_once('class.php');
require_once('przedmiot.php');
class Ocena
{
    private $nr_stud;
    private $nr_przed;
    private $wartosc;

    function __construct($nr_stud, $nr_przed, $wartoc) {
        $this->nr_stud = $nr_stud;
        $this->nr_przed = $nr_przed;
        $this->wartosc = $wartoc;
    }
    public static function wypiszWszystkie()
    {
        // zmienna przechowująca uchwyt do bazy 
// uzyskany jako wynik mysql_connect() 
        $polaczenie = Studia::otworzPolaczenie();
        $zapytanie = "select studenci.numer, przedmioty.numer, imie, nazwisko, nazwa, ocena from studenci,przedmioty,oceny where studenci.numer=oceny.nr_stud and przedmioty.numer=oceny.nr_przed;";
        $wynik = $polaczenie->query($zapytanie);
        // gdy zapytanie nie wykona się poprawnie funkcja jest przerywana 
        if (!$wynik){
            
            return;}
        // generowanie formularza, nagłówków tabeli i przycisku dodawania nowego rekordu (studenta) 
        $naglowki = array("Imię", "Nazwisko", "Przedmiot", "Ocena");
        print("<form method='POST'>");
        print("<b>Oceny studentów</b><br>");
        print("<table border = 1><tr>");
        foreach ($naglowki as $naglowek)
            print("<td><b>$naglowek</b></td>");
        print("<td align='center'><b><input type='submit' name='przycisk[][]' value='Nowa ocena'></b></td>");
        print("</tr>");
        while ($wiersz = $wynik->fetch(PDO::FETCH_NUM)) {
            var_dump($wiersz);
            echo "<br>";
            echo "<br>";
            echo "wiersz";
            echo "<br>";
            echo $wiersz[0];
            echo "wiersz end";
            echo "<br>";
            print("<tr>");
            foreach ($wiersz as $p => $pole){
                print("<td>" . $pole . "</td>");}
            print("<td align='center'>.
            <input type='submit' name='przycisk[$wiersz[0]][$wiersz[1]][$wiersz[5]]' value='Edytuj'>.
            <input type='submit' name='przycisk[$wiersz[0]][$wiersz[1]]' value='Usuń'></td>");
            print("</tr>");
        }
        
        print("</table>");
        print("</form>");
        $wynik->closeCursor();
    }


    public static function nowa() {
        //global $polaczenie;	
        
        // generuje liste wyboru studenta
        
        // $selectStudent = Student::selectStudent();
        
        // // generuje liste wyboru przedmiotu
        // // $rozkaz = "select * from przedmioty;";
        // // $przedmioty = mysqli_query($polaczenie, $rozkaz);
        // // $selectPrzedmiot = "<select name=nr_przed>";
        // // while($rekord = mysqli_fetch_row($przedmioty))
        // //     $selectPrzedmiot .= 
        // //         "<option value=$rekord[0]>$rekord[1]</option>";
        // //$selectPrzedmiot .= "</select>";
        // $selectPrzedmiot = Przedmiot::selectPrzedmiot();
        
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
        .Student::selectStudent()
        ."</td></tr>	
        <tr>
        <td>Przedmiot: </td>
        <td colspan=2>"
        //<input type=text name='nr_przed' value='' size=15 
        //       style='text-align: center; width:50px'></td>
        .Przedmiot::selectPrzedmiot()
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

    public static function getOcena($stud, $przed)
    {
        echo "get ocena";
        echo "".$stud;
        echo "".$przed;
        #echo "".$ocena;
        $baza = Studia::otworzPolaczenie();
        $sql = 'select * from oceny where nr_stud=:nr_stud and nr_przed=:nr_przed;';
        $zapytanie = $baza->prepare($sql);
        $zapytanie->bindParam(':nr_stud', $stud, PDO::PARAM_INT);
        $zapytanie->bindParam(':nr_przed', $przed, PDO::PARAM_INT);
        //$zapytanie->bindParam(':ocena', $ocena, PDO::PARAM_INT);
        $zapytanie->execute();
        $row = $zapytanie->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "baza";
            return new Ocena($row['nr_stud'], $row['nr_przed'], $row['ocena']);
        } else {
            echo "new";
            return new Ocena(-1,'','');
        }
    }
    public function usun_ocene(){
        echo "usun ocene";
        echo $this->nr_stud;
        echo $this->nr_przed;
        echo $this->wartosc;
        $baza = Studia::otworzPolaczenie();
        $sql = 'DELETE FROM oceny WHERE nr_stud=:nr_stud and nr_przed=:nr_przed;';
        $zapytanie = $baza->prepare($sql);
        $zapytanie->execute([':nr_stud'=>$this->nr_stud,':nr_przed'=>$this->nr_przed]);
    }

    public function edytuj_ocene(){
        echo " <form method=POST action=''> 
        <table border=0> <tr> <td>nr stud</td><td colspan=2> 
        <input type=text name='nr_stud' value='$this->nr_stud' size=15 style='text-align: left'>
        </td> </tr>
        <tr> <td>nr stud</td><td colspan=2> 
        <input type=text name='nr_stud' value='$this->nr_stud' size=15 style='text-align: left'>
        </td> </tr>
         <tr> <td>OCena</td><td colspan=2> 
        <input type=text name='nr_przed' value='$this->wartosc' size=15 style='text-align: left'></td> </tr> <tr>
         <td colspan=3> 
         <input type=submit name='przycisk[$this->wartosc]' value='Zapisz' style='width:200px'></td> </tr> 
         </table></form>";
    }

    public function zapisz_ocene() {
        // global $polaczenie;
        // $wartosc = $_POST['ocena'];
        // // gdy klucze obce ida z formularza biore je do zmiennych
        // // wpp wartosci ida z parametrow
        // if(isset($_POST['nr_stud'])) $nr_stud = $_POST['nr_stud'];
        // if(isset($_POST['nr_stud'])) $nr_przed = $_POST['nr_przed'];
        
        // // test czy w bazie jest juz ocena oparta o podane klucze obce
        // $rozkaz = "select * from oceny where nr_stud=$nr_stud 
        //                                  and nr_przed=$nr_przed;";
        // $wynik = mysqli_query($polaczenie, $rozkaz);	
        
        // if(mysqli_num_rows($wynik))
        //     $rozkaz = "update oceny set ocena=$wartosc 
        //                     where nr_stud=$nr_stud and nr_przed=$nr_przed;";
        // else {		
        //     $rozkaz = "insert into oceny values($nr_stud, $nr_przed, $wartosc);";		
        // }
        // mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);	
        // mysqli_free_result($wynik);
        
    

        $this->nr_stud = $_POST['nr_stud'];
        $this->nr_przed = $_POST['nr_przed'];
        $this->wartosc = $_POST['ocena'];


        $baza = Studia::otworzPolaczenie();

        // if ($this->nr_stud == -1) {
        //     $sql = 'INSERT INTO przedmioty SET numer = :numer, nazwa = :nazwa, godzin = :godziny';
        //     $this->numer = null;
        // } else {
        //     $sql = 'UPDATE przedmioty SET nazwa = :nazwa, godzin = :godziny WHERE numer = :numer';
        // }
        $sql = 'INSERT INTO oceny SET nr_stud = :nr_stud, nr_przed = :nr_przed, ocena = :wartosc';
        $zapytanie = $baza->prepare($sql);
        $zapytanie->bindParam(':nr_stud', $this->nr_stud, PDO::PARAM_INT);
        $zapytanie->bindParam(':nr_przed', $this->nr_przed, PDO::PARAM_INT);
        $zapytanie->bindParam(':wartosc', $this->wartosc, PDO::PARAM_INT);
        $zapytanie->execute();
    }
} ?>