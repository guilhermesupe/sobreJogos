<?php
require_once("lib/raelgc/view/Template.php");
include 'config.php';
use raelgc\view\Template;

 //Classe para utlizar o template para retornar ao html o resultado dos links do youtube 

class Busca{

        
function __construct(){
       $this->tpl = new Template("main.html");  
    }

        function updateView($retorno){
          
                      if(!empty($retorno)){
                      $this->tpl->CAMPO = $retorno;
                      //Caso existam links para retornar
                       $this->tpl->block("BLOCK_FIELDS");
                       
                      $this->tpl->show();
                    }
                    else{
                      $this->tpl->CAMPO = $retorno;
                      //Retorna que a busca não teve resultados
                       $this->tpl->block("BLOCK_VAZIO");
                       
                      $this->tpl->show();
                    }
                
        }
              
    }          
      

?>