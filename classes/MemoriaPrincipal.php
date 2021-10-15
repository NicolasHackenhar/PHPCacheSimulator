<?php

namespace classes;

class MemoriaPrincipal
{
    private $blocos = array();

    public function __construct(int $tamanho)
    {
        for ($i = 0; $i < $tamanho; $i++)
        {
            $this->blocos[$i] = new Bloco();
        }
    }

    public function getBloco($numeroBloco)
    {
        return $this->blocos[$numeroBloco];
    }

    public function setBloco(LinhaCache $linhaCache)
    {
        for ($i = 0; $i < 4; $i++)
        {
            $linhaMP = $this->blocos[$linhaCache->getRotulo()]->getLinha($i);
            $linhaMP->setConteudo($linhaCache->getConteudo($i));
        }

    }

}