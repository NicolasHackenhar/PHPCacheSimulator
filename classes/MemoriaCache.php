<?php

namespace classes;

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