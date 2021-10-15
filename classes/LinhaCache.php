<?php

namespace classes;

class LinhaCache
{
    private $conteudo;
    private $escrita = false;
    private $valid = false;
    private $rotulo;
    private $indice = 0;

    public function __construct()
    {
        $this->conteudo[0] = null;
        $this->conteudo[1] = null;
        $this->conteudo[2] = null;
        $this->conteudo[3] = null;
    }

    public function setConteudo($conteudo, $deslocamento)
    {
        $this->conteudo[$deslocamento] = $conteudo;
    }

    public function getConteudo($deslocamento)
    {
        return $this->conteudo[$deslocamento];
    }

    public function setEscrita(bool $escrita)
    {
        $this->escrita = $escrita;
    }
    public function isEscrita()
    {
        return $this->escrita;
    }

    public function setValid(bool $valid)
    {
        $this->valid = $valid;
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function setRotulo($rotulo)
    {
        $this->rotulo = $rotulo;
        return $this;
    }

    public function getRotulo()
    {
        return $this->rotulo;
    }

    public function setIndice($indice)
    {
        $this->indice = $indice;
        return $this;
    }

    public function getIndice()
    {
        return $this->indice;
    }
}