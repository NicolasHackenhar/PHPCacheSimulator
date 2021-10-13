<?php

class LinhaCache
{
    public static $tamanho = 8;
    private $conteudo;
    private $escrita = false;
    private $valid = false;
    private $rotulo = null;

    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
    }

    public function getConteudo()
    {
        return $this->conteudo;
    }

    public function setEscrita(bool $escrita)
    {
        $this->escrita = $escrita;
        return $this;
    }
    public function isEscrita(): bool
    {
        return $this->escrita;
    }

    public function setValid(bool $valid)
    {
        $this->valid = $valid;
        return $this;
    }

    public function isValid(): bool
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
}

class LinhaMP
{
    public static $tamanho = 8;
    private $conteudo;

    public function setConteudo($conteudo)
    {
        $this->conteudo = $conteudo;
    }

    public function getConteudo()
    {
        return $this->conteudo;
    }
}

class Bloco
{
    private $linhas = array();

    public function __construct()
    {
        for ($i =0; $i<4; $i++)
        {
            $this->linhas[$i] = new LinhaMP();
            $this->linhas[$i]->setConteudo(dechex(rand(0,255)));
        }
    }
}

class Conjunto
{
    private $linhas = array();

    public function __construct()
    {
        for ($i =0; $i<2; $i++)
        {
            $this->linhas[$i] = new LinhaCache();
        }
    }
}

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

}

class MemoriaCache
{
    private $conjuntos = array();

    public function __construct(int $conjuntos)
    {
        for ($i = 0; $i < $conjuntos; $i++)
        {
            $this->conjuntos[$i] = new Conjunto();
        }
    }
}
