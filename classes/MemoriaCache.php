<?php

namespace classes;

class MemoriaCache
{
    private $conjuntos = array();
    private $operacao;
    private $conteudo;
    private $sucessosLeitura = 0;
    private $sucessosEscrita = 0;
    private $faltasLeitura = 0;
    private $faltasEscrita = 0;
    private $retornoLeitura = array();


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
        $this->retornoLeitura = [
            'numeroBloco' => $numeroBloco,
            'quadro' => $quadro,
            'deslocamento' => $deslocamento
        ];
        $linhaCache = $this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento);

        if (!$linhaCache)
        {
            $this->retornoLeitura['inCache'] = 'Nao';
            return $this->notInCache($MP, $numeroBloco, $quadro, $deslocamento);
        }

        if ($this->operacao == 0)
        {
            $this->sucessosLeitura++;
            $this->retornoLeitura['inCache'] = 'Sim';
            $this->retornoLeitura['conteudo'] = $linhaCache->getConteudo($deslocamento);

            return $this->retornoLeitura;
        }
        $this->sucessosEscrita++;
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
            $this->retornoLeitura['conteudo'] = $this->conjuntos[$quadro]->getLinha($numeroBloco, $deslocamento)->getConteudo($deslocamento);
            $this->faltasLeitura++;
            return $this->retornoLeitura;
        }
        $this->faltasEscrita++;
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

    public function getEstatisticas()
    {
        return [
            'Sucessos' => [
                'Leitura' => $this->sucessosLeitura,
                'Escrita' => $this->sucessosEscrita,
                'Total' =>  $this->sucessosLeitura + $this->sucessosEscrita,
            ],
            'Faltas' => [
                'Leitura' => $this->faltasLeitura,
                'Escrita' => $this->faltasEscrita,
                'Total' => $this->faltasLeitura + $this->faltasEscrita
            ],
            'Geral' =>
                [
                    'Sucessos' => $this->sucessosLeitura + $this->sucessosEscrita,
                    'Faltas' => $this->faltasLeitura + $this->faltasEscrita,
                    'Total' => $this->sucessosLeitura + $this->sucessosEscrita+$this->faltasLeitura + $this->faltasEscrita
                ]
        ];
    }

}