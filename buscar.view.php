<?php
include 'busca.php';
//Descomente para visualizar erros do php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once 'class.YouTubeBean.php';
include_once 'class.YouTubeVideoDownloader.php';
//Recebe o que foi digitado na caixa de texto via post
$termos = $_POST['busca'] ;
//Substitui espaços por %20 para os termos serem usados na URL da API
$termos = str_replace(' ', '%20', $termos);
//Executa o cURL para fazer uma query dos termos pesquisados na Collection da aplicação
$rep = shell_exec('curl -X GET -u "fa6ff1f8-d05c-4763-9e75-98ed5033ca59":"M6RwGuP4IRx0" "https://gateway.watsonplatform.net/discovery/api/v1/environments/d55cf9b4-88af-4db6-ba7b-38691f16bf0c/collections/af9d91b0-48c6-450e-84dd-2d953094f208/query?version=2017-11-07&deduplicate=false&highlight=true&passages=true&passages.count=5&natural_language_query='.$termos.'"');
//Recebe o JSON de volta e o transforma em um array
$array = json_decode($rep,true);
//Dentro do JSON pega a propriedade de Metadados para extrair o nome do document da coleção do discovery que a query retornou
//Como anteriormente os nomes dos documents eram os ids dos vídeos do youtube, remonta-se a url do youtube usando o nome do documento retornado na query
if(!empty($array["results"])){
$retorno = $array["results"][0]["extracted_metadata"]["filename"];
$id= explode('.', $retorno);
$retorno = 'https://www.youtube.com/embed/'. $id[0].'?rel=0';
}
else{
	$retorno = '';
}
$resultado = new Busca();
$resultado->updateView($retorno);

?>
