<?php

namespace classes;

class Bloco
{
    private $linhas = array();

    public function __construct()
    {
        for ($i =0; $i<4; $i++)
        {
            $this->linhas[$i] = new LinhaMP();
            $this->linhas[$i]->setConteudo(str_pad(dechex(rand(0,255)), 2, '0', STR_PAD_LEFT));
        }
    }

    public function getLinha($deslocamento)
    {
        return $this->linhas[$deslocamento];
    }
}