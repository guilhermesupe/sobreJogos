<?php
include_once 'class.YouTubeBean.php';
include_once 'class.YouTubeVideoDownloader.php';
//Descomente para visualizar erros no php
//error_reporting(-1);
//ini_set('display_errors', 'On');
//URL recebida do youtube recebido da caixa de texto
$url = $_POST['url'] ;
//Devolve somente o id do link do vídeo do youtube
parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars ); 
//Utiliza a lib open source youtube-dl para baixar o vídeo do youtube no servidor, o nome do arquivo será o ID do vídeo
$l = shell_exec('youtube-dl/2017.11.26/bin/youtube-dl ' . $url  . ' -o '. $my_array_of_vars['v'].  '.mp4 2>&1');
// Utiliza a lib ffmpeg para converter o vídeo em um arquivo de áudio .mp3 com o nome sound.mp3
$l1 = shell_exec('ffmpeg/3.4/bin/ffmpeg -y -i '. $my_array_of_vars['v']. '.mp4 -vn -ar 44100 -ac 2 -ab 192k -f mp3 sound.mp3 2>&1' );
// Aqui é possível deletar o arquivo de vídeo
// shell_exec('delete ' . $my_array_of_vars['v'] . '.mp4');
//----------------------------------------------------------------------------------------------------------------------------------------------------
//Esta parte começa o envio do arquivo .mp3 do áudio do vídeo para o serviço de speech to text da IBM
//Usuário do serviço
$username = '010bc02b-da13-4cb0-bd50-132ca0d70573';
//Senha do usuário do serviço
$password = 'fkw55kF0aX2s';
//endpoint da API do serviço
$url_r = 'https://stream.watsonplatform.net/speech-to-text/api/v1/models/pt-BR_BroadbandModel/recognize';
$file = fopen('sound.mp3', 'r');
$size = filesize('sound.mp3');
$fildata = fread($file,$size);
$headers = array(    "Content-Type: audio/mp3",
                      "Transfer-Encoding: chunked");
                     
 // Inicializa o cURL para enviar o arquivo para a api 
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url_r);
 curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
 curl_setopt($ch, CURLOPT_POST, TRUE);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
 curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $fildata);
 curl_setopt($ch, CURLOPT_INFILE, $file);
 curl_setopt($ch, CURLOPT_INFILESIZE, $size);
 curl_setopt($ch, CURLOPT_VERBOSE, true);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 $executed = curl_exec($ch);
 curl_close($ch);
 // $executed conterá o JSON da resposta da API contendo a transcrição do áudio
 //Para visualizar somente a parte de texto da transcrição toda junta, descomente o trecho abaixo
/*
$total = '';
$array = json_decode($executed,true);
for ($i =0 ; $i< count($array["results"]); $i++){
	$total .= ' ' . $array["results"][$i]["alternatives"][0]["transcript"];
}
echo $total;
*/
//$fp = fopen($my_array_of_vars['v'].'.txt', 'w');
//fwrite($fp, $total);
//fclose($fp);

//Cria o arquivo .JSON da transcrição
$fp = fopen($my_array_of_vars['v'].'.json', 'w');
fwrite($fp, $executed);
fclose($fp);
//Envia para o Watson discovery service como um novo documento o JSON da transcrição, o nome do documento é o ID do vídeo original do youtube
$rep = shell_exec('curl -X POST -u "fa6ff1f8-d05c-4763-9e75-98ed5033ca59":"M6RwGuP4IRx0" -F "file=@'.$my_array_of_vars['v'].'.json" https://gateway.watsonplatform.net/discovery/api/v1/environments/d55cf9b4-88af-4db6-ba7b-38691f16bf0c/collections/af9d91b0-48c6-450e-84dd-2d953094f208/documents?version=2017-11-07 2>&1');
echo $rep;
echo 'Indexado com sucesso!';
//Aqui pode-se exluir o arquivo sound.mp3 e o arquivo JSON gerado caso seja necessário.
echo file_get_contents("indexar.html");
?>