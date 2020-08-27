<?php

namespace elzobrito;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Upload
 *
 * @author ELZOBRITODOSSANTOSFI
 */
class ClassUpload
{

    //put your code here

    private $pasta;
    private $tamanho;
    private $extensoes;
    // O nome original do arquivo no computador do usuário
    private $arqName;
    private $arqNameCripto;
    // O tipo mime do arquivo. Um exemplo pode ser "image/gif"
    private $arqType;
    // O tamanho, em bytes, do arquivo
    private $arqSize;
    // O nome temporário do arquivo, como foi guardado no servidor
    private $arqTemp;
    // O código de erro associado a este upload de arquivo
    private $arqError;

    //Altera a permissão da pasta
    private $permissao;
    private $msgError = array();

    function getNumeroErro()
    {
        return $this->arqError;
    }

    public function getError()
    {
        return $this->msgError[$this->arqError];
    }

    private function mover()
    {
        if ($this->arqError == 0) {
            if ($this->arqNameCripto != "") {
                return move_uploaded_file($this->arqTemp, $this->pasta .  DIRECTORY_SEPARATOR . $this->arqNameCripto);
            } else {
                return move_uploaded_file($this->arqTemp, $this->pasta . $this->arqName);
            }
        }
    }

    function getCaminho()
    {
        if ($this->arqNameCripto != "") {
            return "." . DIRECTORY_SEPARATOR . $this->pasta .  DIRECTORY_SEPARATOR . $this->arqNameCripto;
        } else {
            return "." . DIRECTORY_SEPARATOR . $this->pasta .  DIRECTORY_SEPARATOR . $this->arqName;
        }
    }

    function getArqNameCripto()
    {
        return $this->arqNameCripto;
    }

    function getArqName()
    {
        return $this->arqName;
    }

    private function renomear($param)
    {
        if ($param == true) {
            $nome = $this->arqName;
            $ext = explode('.', $nome);
            $ext = strtolower(end($ext));
            $this->arqNameCripto = sha1($this->arqName . "" . time()) . "." . $ext;
        }
    }

    private function valida()
    {
        if ($this->arqError == 0) {
            // Verifica o tipo de arquivo enviado
            if (array_search(strtoupper($this->arqType), $this->extensoes) === false) {
                $this->arqError = 1;
                //echo 'O tipo de arquivo enviado é inválido!';
                // Verifica o tamanho do arquivo enviado
            } else if ($this->arqSize > (1024 * 1024 * $this->tamanho)) {
                $this->arqError = 2;
                //echo 'O tamanho do arquivo enviado é maior que o limite!';
                // Não houveram erros, move o arquivo
            } else {
                if ($this->permissao != null)
                    chmod($this->pasta, 0777);

                $this->mover();

                if ($this->permissao != null)
                    chmod($this->pasta, 0775);
            }
        }
    }

    private function strtoupperExtensoes()
    {
        foreach ($this->extensoes as $i => $ext) {
            $this->extensoes[$i] = strtoupper($ext);
        }
    }

    function __construct($pasta, $tamanho, $extensoes, $arquivo, $renomear, $permissao = null)
    {

        $this->pasta = $pasta;
        $this->tamanho = $tamanho;
        $this->extensoes = $extensoes;
        $this->arquivo = $arquivo;
        // O nome original do arquivo no computador do usuário
        $this->arqName = $arquivo['name'];
        $arq = pathinfo($this->pasta . DIRECTORY_SEPARATOR . $this->arqName);
        // O tipo mime do arquivo. Um exemplo pode ser "image/gif"
        //$tmpType = strtolower(end(explode(".", $this->arqName)));

        if ($this->arqName != '') {
            $tmpType = $arq['extension'];
        } else {
            $tmpType = array();
        }

        $this->arqType = $tmpType;
        // O tamanho, em bytes, do arquivo
        $this->arqSize = $arquivo['size'];
        // O nome temporário do arquivo, como foi guardado no servidor
        $this->arqTemp = $arquivo['tmp_name'];
        // O código de erro associado a este upload de arquivo
        $this->arqError = $arquivo['error'];
        $this->renomear($renomear);
        $this->strtoupperExtensoes();
        $this->msgError[2] = 'O tipo de arquivo enviado é inválido!';
        $this->msgError[1] = 'O tamanho do arquivo enviado é maior que o limite!';
        $this->valida();
    }
}
