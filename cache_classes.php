<?php

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

class LinhaMP
{
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
            $this->linhas[$i]->setConteudo(str_pad(dechex(rand(0,255)), 2, '0', STR_PAD_LEFT));
        }
    }

    public function getLinha($deslocamento)
    {
        return $this->linhas[$deslocamento];
    }
}

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

    public function getLinha($rotulo, $deslocamento)
    {
        foreach ($this->linhas as $linhaCache)
        {
            if ($linhaCache->getRotulo() == $rotulo && $linhaCache->isValid())
            {
                return $linhaCache->getConteudo($deslocamento);
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
        $this->linhas[$this->contador]->setIndice($this->contador);
        if ($this->cheio)
        {
            $this->linhas[$this->contador]->setIndice(1);
        }
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
        $linhaRetorno = false;
        foreach ($this->linhas as $indiceLinha => $linhaCache)
        {
            if ($linhaCache->getIndice() == 0)
            {
                $this->contador = $indiceLinha;
                $linhaRetorno =  $linhaCache;
            }elseif ($linhaCache->getIndice() == 1)
            {
                $linhaCache->setIndice(0);
            }
        }
        return $linhaRetorno;
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

    public function getConjuntos()
    {
        return $this->conjuntos;
    }

    public function getConteudo(MemoriaPrincipal $MP, $endereco)
    {
        $endereco = bindec($endereco);
        $numeroBloco = (int)($endereco / 4);
        $deslocamento = $endereco % 4;
        $quadro = $numeroBloco % 4;
        $linhaCache = $this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento);

        if (!$linhaCache)
        {
            return $this->notInCache($MP, $numeroBloco, $quadro, $deslocamento);
        }

        return $linhaCache;
    }

    private function notInCache(MemoriaPrincipal $MP, $numeroBloco, $quadro, $deslocamento)
    {
        if ($this->conjuntos[$quadro]->isCheio())
        {
            return  $this->removeLinhaCache($MP, $numeroBloco, $deslocamento, $quadro);
        }
        return $this->insereBloco($MP, $numeroBloco, $quadro, $deslocamento);
    }

    private function removeLinhaCache(MemoriaPrincipal $MP, $numeroBloco, $deslocamento, $quadro)
    {
        $linhaRemovida = $this->conjuntos[$quadro]->removeLinha();
        if ($linhaRemovida->isEscrita())
        {
            $MP->setBloco($linhaRemovida);
        }
        return $this->insereBloco($MP, $numeroBloco, $quadro, $deslocamento);
    }

    private function insereBloco(MemoriaPrincipal $MP, $numeroBloco, $quadro, $deslocamento)
    {
        $blocoMP = $MP->getBloco($numeroBloco);
        $this->conjuntos[$quadro]->insertBloco($blocoMP, $numeroBloco);
        return $this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento);
    }

}
