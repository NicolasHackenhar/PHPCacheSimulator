<?php

namespace classes;

class Conjunto
{
    private $linhas = array();
    private $contador = 0;
    private $cheio = false;

    public function __construct()
    {
        for ($i = 0; $i < 2; $i++) {
            $this->linhas[$i] = new LinhaCache();
        }
    }

    public function getLinhas()
    {
        return $this->linhas;
    }

    public function getLinha($rotulo)
    {
        foreach ($this->linhas as $linhaCache)
        {
            if ($linhaCache->getRotulo() == $rotulo && $linhaCache->isValid())
            {
                return $linhaCache;
            }
        }

        return false;
    }

    public function insertBloco(Bloco $bloco, $numeroBloco)
    {
        $this->linhas[$this->contador] = new LinhaCache();
        $this->linhas[$this->contador]->setRotulo($numeroBloco);
        for ($i = 0; $i < 4; $i++) {
            $linhaMP = $bloco->getLinha($i);
            $this->linhas[$this->contador]->setConteudo(
                $linhaMP->getConteudo(),  $i
            );
        }
        $this->linhas[$this->contador]->setValid(true);
        $this->contador++;
        if ($this->contador > 1)
        {
            $this->contador = 0;
        }
    }

    public function isCheio()
    {
        $this->cheio = true;
        foreach ($this->linhas as $linhaCache)
        {
            if (empty($linhaCache->getRotulo()) && !$linhaCache->isValid())
            {
                $this->cheio = false;
            }
        }

        return $this->cheio;
    }

    public function removeLinha()
    {
        return $this->linhas[$this->contador];
    }

    public function getContador()
    {
        return $this->contador;
    }
}