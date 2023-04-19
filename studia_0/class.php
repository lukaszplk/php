<?php
include('studia.php');
class Studia
{
    public static function naglowek($tytul)
    {
        echo "<!DOCTYPE html>";
        echo "<html>";
        echo "<head>";
        echo "<meta charset=\"UTF-8\">";
        echo "<title>$tytul</title>";
        echo "</head>";
        echo "<body>";
    }
    public static function menu()
    {
        echo `<body bgcolor=yellow text="#000FFF">
            <!-- 1. przycisk ładujący stronę odpowiedzialną za zarządzanie studentami -->
            <input type=button value=" STUDENCI "
            onClick="window.location='studenci.php'">
            <br><br>
            <!-- 2. formularz ładujący stronę odpowiedzialną za zarządzanie ocenami -->
            <form name=menu action='oceny.php'>
            <input type=submit value=" OCENY ">
            </form>
            <!-- 3. odsyłacz (link) do strony odpowiedzialnej za zarządzanie przedmiotami -->
            <a href='przedmioty.php'> PRZEDMIOTY </a>
            <hr>`;
    }
    public static function otworzPolaczenie(){
        try{
            $baza = new PDO("mysql:host=localhost;dbname=studia", "root", "");
        } catch(PDOException $e)
        {
           echo 'Połączenie nie mogło zostać utworzone: ' . $e->getMessage();
        }
        return $baza;
    }

    public static function stopka()
    {
        echo "</body>";
        echo "</html>";
    }

}

class Student{
    private $id;
    private $imie;
    private $nazwisko;

    function __construct($id, $imie, $nazwisko) {
        $this->id = $id;
        $this->imie = $imie;
        $this->nazwisko = $nazwisko;
    }

    public static function selectStudent(){
        $baza = Studia::otworzPolaczenie();
        $rozkaz = "select * from studenci;";
        # $studenci = mysqli_query($baza, $rozkaz);
        $wynik = $baza->query($rozkaz);
        $selectStudent = "<select name=nr_stud>";
        while($rekord = $wynik->fetch(PDO::FETCH_NUM))
            $selectStudent .= 
                "<option value=$rekord[0]>$rekord[1] $rekord[2]</option>";
        $selectStudent .= "</select>";
        return $selectStudent;
    }

    
    public static function getStudent($id)
    {
        $baza = Studia::otworzPolaczenie();
        $sql = 'SELECT * FROM studenci WHERE numer = :id';
        $zapytanie = $baza->prepare($sql);
        $zapytanie->bindParam(':id', $id, PDO::PARAM_INT);
        $zapytanie->execute();
        $row = $zapytanie->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Student($row['numer'], $row['imie'], $row['nazwisko']);
        } else {
            return new Student(-1,'','');
        }
    }

    function edytuj()
    {
        echo " <form method=POST action=''> 
        <table border=0> <tr> <td>Imię</td><td colspan=2> 
        <input type=text name='imie' value='$this->imie' size=15 style='text-align: left'>
        </td> </tr> <tr> <td>Nazwisko</td><td colspan=2> 
        <input type=text name='nazwisko' value='$this->nazwisko' size=15 style='text-align: left'></td> </tr> <tr>
         <td colspan=3> 
         <input type=submit name='przycisk[$this->id]' value='Zapisz' style='width:200px'></td> </tr> 
         </table></form>";
    }

    function zapisz(){
        $this->imie = $_POST['imie'];
        $this->nazwisko = $_POST['nazwisko'];

        $baza = Studia::otworzPolaczenie();

        if ($this->id == -1) {
            $sql = 'INSERT INTO studenci SET numer = :id, imie = :imie, nazwisko = :nazwisko';
            $this->id=null;
        } else {
            $sql = 'UPDATE studenci SET imie = :imie, nazwisko = :nazwisko WHERE numer = :id';
        }

        $zapytanie = $baza->prepare($sql);
        $zapytanie->bindParam(':id', $this->id, PDO::PARAM_INT);
        $zapytanie->bindParam(':imie', $this->imie, PDO::PARAM_STR);
        $zapytanie->bindParam(':nazwisko', $this->nazwisko, PDO::PARAM_STR);
        $zapytanie->execute();
    }

    function usun(){
        $baza = Studia::otworzPolaczenie();


        $sql = 'DELETE FROM studenci WHERE numer = :id';
        

        $zapytanie = $baza->prepare($sql);
        $zapytanie->execute([':id'=>$this->id]);
    }

    public static function wypisz_wszystkie()
    {
        // zmienna przechowująca uchwyt do bazy
        // uzyskany jako wynik mysql_connect()
        $polaczenie = Studia::otworzPolaczenie();

        $zapytanie = "select * from studenci";
        $wynik = $polaczenie->query($zapytanie);
        // gdy zapytanie nie wykona się poprawnie funkcja jest przerywana 
        if (!$wynik)
            return;

        // generowanie formularza, nagłówków tabeli i przycisku dodawania nowego rekordu (studenta)
        $naglowki = array("Imię", "Nazwisko");
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