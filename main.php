<?php
require_once "classes/includes.php";

use classes\MemoriaCache;
use classes\MemoriaPrincipal;

ini_set("display_errors",1);
ini_set("display_startup_erros",1);
error_reporting(E_ALL);

const MENU = PHP_EOL.'->Menu:'.PHP_EOL.'0 - Encerrar'.PHP_EOL.'1 - Ler Endereco'.PHP_EOL.'2 - Escrever no Endereco'.PHP_EOL.'3 - Estatisticas'.PHP_EOL.PHP_EOL.'-> ';
const OPCAO_INVALIDA = PHP_EOL."***** OPCAO INVALIDA ***** ".PHP_EOL;
const CABECALHO_MP = PHP_EOL." | End. Dec. | End Bin. | Conteudo | Bloco | Deslocamento | Quadro |".PHP_EOL;
const CABECALHO_CACHE = PHP_EOL." | Rotulo | Celula 0 | Celula 1 | Celula 2 | Celula 3 | Valid | Escrita |".PHP_EOL;
const ESCRITA = 1;
const LEITURA = 0;


$MP = new MemoriaPrincipal(128);
$Cache = new MemoriaCache(4);
imprimeMP($MP);
imprimeCache($Cache);
$option = 1;

main($MP, $Cache);

function main(MemoriaPrincipal $MP, MemoriaCache $Cache)
{
    echo MENU;
    $option = str_replace(PHP_EOL, '', fgets(STDIN));
    if (strlen($option) != 1 || !preg_match('/[01234]/', $option))
    {
        echo PHP_EOL.'****** Informe uma opcao valida ******'.PHP_EOL;
        main($MP, $Cache);
    }

    while ($option != 0)
    {
        switch ($option)
        {
            case 1:
                lerEndereco($MP, $Cache);
                break;
            case 2:
                escreverNoEndereco($MP, $Cache);
                break;
            case 3:
                estatisticas($MP, $Cache);
                break;
            default:
                echo OPCAO_INVALIDA;
                main($MP, $Cache);
                break;
        }
    }
}

function imprimeMP(MemoriaPrincipal $MP)
{
    echo ' '.str_pad('MEMORIA PRINCIPAL', 67, '-', STR_PAD_BOTH);
    echo  CABECALHO_MP;

    for ($i = 0; $i <= 127; $i++)
    {
        $deslocamento = $i%4;
        $bloco = (int) ($i/4);
        $content = $MP->getBloco($bloco)->getLinha($deslocamento)->getConteudo();
        $address = str_pad(decbin($i),7, "0", STR_PAD_LEFT);
        $quadro = $bloco%4;

        echo' | '.str_pad($i,9, ' ', STR_PAD_BOTH).
            ' | '.str_pad($address,8, ' ', STR_PAD_LEFT).
            ' | '.str_pad($content,8, ' ', STR_PAD_BOTH).
            ' | '.str_pad($bloco,5, ' ', STR_PAD_BOTH).
            ' | '.str_pad($deslocamento,12, ' ', STR_PAD_BOTH).
            ' | '.str_pad($quadro,6, ' ', STR_PAD_BOTH).' |'.PHP_EOL;
    }
    echo str_pad(' ', 68, '-').PHP_EOL;
}

function imprimeCache(MemoriaCache $Cache)
{
    echo ' '.str_pad('MEMORIA CACHE', 72, '-', STR_PAD_BOTH);
    echo  CABECALHO_CACHE;

    $conjuntos = $Cache->getConjuntos();
    foreach ($conjuntos as $conjunto)
    {
        $linhas = $conjunto->getLinhas();
        foreach ($linhas as $linha)
        {
            $rotulo = str_pad(decbin($linha->getRotulo()),5, '0', STR_PAD_LEFT);
            echo' | '.str_pad($rotulo,6, ' ', STR_PAD_LEFT).
                ' | '.str_pad($linha->getConteudo(0),8, ' ', STR_PAD_BOTH).
                ' | '.str_pad($linha->getConteudo(1),8, ' ', STR_PAD_BOTH).
                ' | '.str_pad($linha->getConteudo(2),8, ' ', STR_PAD_BOTH).
                ' | '.str_pad($linha->getConteudo(3),8, ' ', STR_PAD_BOTH).
                ' | '.str_pad((int)$linha->isValid(),5, ' ', STR_PAD_BOTH).
                ' | '.str_pad((int)$linha->isEscrita(),7, ' ', STR_PAD_BOTH).' |'.PHP_EOL;
        }
    }
    echo str_pad(' ', 73, '-').PHP_EOL;
}

function lerEndereco(MemoriaPrincipal $MP, MemoriaCache $Cache)
{
    $enderecoBusca = getEndereco();
    $Cache->setOperacao(LEITURA);
    $content = $Cache->getConteudo($MP, $enderecoBusca);

    imprimeMP($MP);
    imprimeCache($Cache);

    echo PHP_EOL.'->Conteudo: '.$content.PHP_EOL;

    main($MP, $Cache);
}

function getEndereco()
{
    echo PHP_EOL.'->Informe um endereco:'.PHP_EOL.'-> ';
    $endereco = str_replace(PHP_EOL, '', fgets(STDIN));
    if (strlen($endereco) != 7 || !preg_match('/[0,1]{7}/', $endereco))
    {
        echo PHP_EOL.'****** Informe um endereco binario com 7 bits ******'.PHP_EOL;
        getEndereco();
    }
    return $endereco;
}

function escreverNoEndereco(MemoriaPrincipal $MP, MemoriaCache $Cache)
{
    $enderecoBusca = getEndereco();
    echo PHP_EOL.'->Informe o conteudo:'.PHP_EOL.'-> ';
    $conteudo = str_replace(PHP_EOL, '', fgets(STDIN));
    $Cache->setOperacao(ESCRITA);
    $content = $Cache->getConteudo($MP, $enderecoBusca, $conteudo);

    imprimeMP($MP);
    imprimeCache($Cache);

    echo PHP_EOL.'->Conteudo: '.$content.PHP_EOL;

    main($MP, $Cache);
}

function estatisticas(MemoriaPrincipal $MP, MemoriaCache $Cache)
{
    main($MP, $Cache);
}