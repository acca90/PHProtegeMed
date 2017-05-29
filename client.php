<?php

$retorno = "";
function curl($tipo="",$meta="",$GET=false) {
    $curl = curl_init("http://localhost/PHProtegeMed/index.php/get_all_detalhes");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, null);
    $curl_response = curl_exec($curl);
    curl_close($curl);
    return $curl_response;
}
if ($_POST) {
    $retorno = curl();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>

<br>

<form action="client.php" method="POST">
    <center>
        <table>
            <tr>
                <td valign="TOP">
                    <input type="hidden" id='met' name='metodo'>
                    <input onclick="document.getElementById('met').value='POST'" style="width:80px" type="submit" value="POST"><br>
                </td>
                <td valign="TOP">
                    <textarea placeholder="Retorno" style="width:600px; height:200px;"><?php echo $retorno; ?></textarea>
                </td>
            </tr>
        </table>
    </center>
</form>




</body>
</html>