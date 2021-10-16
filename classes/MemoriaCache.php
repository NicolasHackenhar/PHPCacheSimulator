<?php

namespace classes;

class MemoriaCache
{
    private $conjuntos = array();
    private $operacao;
    private $conteudo;

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

    public function getConteudo(MemoriaPrincipal $MP, $endereco, $conteudo = null)
    {
        $this->conteudo = $conteudo;
        $endereco = bindec($endereco);
        $numeroBloco = (int)($endereco / 4);
        $deslocamento = $endereco % 4;
        $quadro = $numeroBloco % 4;
        $linhaCache = $this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento);

        if (!$linhaCache)
        {
            return $this->notInCache($MP, $numeroBloco, $quadro, $deslocamento);
        }

        if ($this->operacao == 0)
        {
            return $linhaCache->getConteudo($deslocamento);
        }

        return $this->escreveInCache($linhaCache, $deslocamento);
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
        if ($this->operacao == 0)
        {
            return $this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento)->getConteudo($deslocamento);
        }
        return $this->escreveInCache($this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento), $deslocamento);
    }

    private function escreveInCache($linhaCache, $deslocamento)
    {
        $linhaCache->setConteudo($this->conteudo, $deslocamento);
        $linhaCache->setEscrita(true);
        return $linhaCache->getConteudo($deslocamento);
    }

    public function setOperacao($operacao)
    {
        $this->operacao = $operacao;
    }

}