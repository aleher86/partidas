<?php


$db = new mysqli("localhost", "root", "", "partidas");



$fede = new Fede($db);
$fede->corregirParcelas();




class Fede {

    /**
     * @var mysqli
     */
    private $db;

    /**
     * Fede constructor.
     * @param mysqli $db
     */
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function corregirParcelas()
    {
        $db = $this->db;
        $this->recorrerFilas(function ($row) use ($db) {

            $parcela = $row['parcela'];
            $parcela = trim($parcela);
            $parcelaOriginal = $row['parcela'];
            $parcela = str_replace('.', '', $parcela);

            $letra3 = substr($parcela, -3);
            $letra2 = substr($parcela, -2);
            $letra = substr($parcela, -1);

            $parteNumerica = "";

            if(ctype_alpha($letra3)){
                $parteNumerica = (($letra3) ? $letra3 : '000');
                $parcela = rtrim($parcela,$letra3);
            }elseif(ctype_alpha($letra2)){
                $parteNumerica = '0' . (($letra2) ? $letra2 : '00');
                $parcela = rtrim($parcela,$letra2);
            }else{
                if (is_numeric($letra)) $letra = false;

                $parteNumerica = '00' . (($letra) ? $letra : '0');
                if ($letra)
                    $parcela = rtrim($parcela,$letra);
            }



            $parcela = substr($parcela,-4);
            if (strlen($parcela) < 4)
                $parcela = str_pad($parcela, 4, '0', STR_PAD_LEFT);


            $stmt = $db->prepare("UPDATE partidas SET parcela2 = ? WHERE id = ?");
            $newParcela = $parcela . $parteNumerica;
            $stmt->bind_param('ss', $newParcela, $row['id']);
            $stmt->execute();

            echo $parcelaOriginal . ' => ' . $parcela . $parteNumerica;
            echo "\n";

        });
    }

    public function corregirQuinta()
    {
        $db = $this->db;
        $this->recorrerFilas(function ($row) use ($db) {

            $quinta = $row['quinta'];
            $quinta = trim($quinta);
            $quintaOriginal = $row['quinta'];
            $quinta = str_replace('.', '', $quinta);
            $letra = substr($quinta, -1);
            if (is_numeric($letra)) $letra = false;

            $parteNumerica = '00' . (($letra) ? $letra : '0');
            if ($letra)
                $quinta = rtrim($quinta,$letra);

            $quinta = substr($quinta,-4);
            if (strlen($quinta) < 4)
                $quinta = str_pad($quinta, 4, '0', STR_PAD_LEFT);


            $stmt = $db->prepare("UPDATE partidas SET quinta2 = ? WHERE id = ?");
            $newQuinta = $quinta . $parteNumerica;
            $stmt->bind_param('ss', $newQuinta, $row['id']);
            $stmt->execute();

            echo $quintaOriginal . ' => ' . $quinta . $parteNumerica;
            echo "\n";

        });
    }

    public function corregirManzanas()
    {

        $db = $this->db;

        $this->recorrerFilas(function ($row) use ($db) {
            $manzana = $row['manzana'];
            $manzana = trim($manzana);
            $manzanaOriginal = $row['manzana'];
            $letra = substr($manzana, -1);
            if (is_numeric($letra)) $letra = false;

            $parteNumerica = '00' . (($letra) ? $letra : '0');
            if ($letra)
                $manzana = rtrim($manzana,$letra);

            $manzana = substr($manzana,-4);
            if (strlen($manzana) < 4)
                $manzana = str_pad($manzana, 4, '0', STR_PAD_LEFT);


            $stmt = $db->prepare("UPDATE partidas SET manzana2 = ? WHERE id = ?");
            $newManzana = $manzana . $parteNumerica;
            $stmt->bind_param('ss', $newManzana, $row['id']);
            $stmt->execute();

            echo $manzanaOriginal . ' => ' . $manzana . $parteNumerica;
            echo "\n";
        });

    }

    /**
     * @param $funcion
     */
    private function recorrerFilas($funcion)
    {
        $db = $this->db;
        $result = $db->query("SELECT * FROM partidas;");

        for ($i = 0; $i < $result->num_rows; $i++)
        {
            $result->data_seek($i);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $funcion($row);
        }
        echo "Filas procesadas: " . $result->num_rows;
        $result->free();
        $db->close();
    }

}