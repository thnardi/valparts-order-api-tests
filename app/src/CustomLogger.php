<?php

namespace Farol360\Ancora;
/**
 *
 */
class CustomLogger
{

  public static function ModelErrorLog($message, $modelExceptionData) {

    $file = __DIR__ . '/../../logs/modelerror.' . date('Y-m-d') . '.log';

    // data atual
    $date = date( 'Y-m-d H:i:s' );



    // formata a mensagem do log
    // 1o: data atual
    // 2o: nível da mensagem (INFO, WARNING ou ERROR)
    // 3o: a mensagem propriamente dita
    // 4o: uma quebra de linha
    $msg = sprintf( "[%s] [ERROR-CODE: %s] [%s] [ERROR-INFO: %s] [TABLE: %s] [FUNCTION: %s] %s", $date, $modelExceptionData->errorCode, $message, $modelExceptionData->errorInfo[2], $modelExceptionData->table, $modelExceptionData->function, PHP_EOL );

    // escreve o log no arquivo
    // é necessário usar FILE_APPEND para que a mensagem seja escrita no final do arquivo, preservando o conteúdo antigo do arquivo
    file_put_contents( $file, $msg, FILE_APPEND );
    $result = chmod($file, 0640);

    //var_dump($result);
    //die;
  }

  public static function ADMINLog($method, $info, $content, $level = 'info') {
    // variável que vai armazenar o nível do log (INFO, WARNING ou ERROR)
    $levelStr = '';
    $methodStr = '';

    $file = __DIR__ . '/../../logs/admin.' . date('Y-m-d') . '.log';

    // verifica o método http
    switch ( $method )
    {
        case 'request':
            // nível de informação
            $methodStr = 'REQUEST';
            break;

        case 'response':
            // nível de aviso
            $methodStr = 'RESPONSE';
            break;
    }

    // verifica o nível do log
    switch ( $level )
    {
        case 'info':
            // nível de informação
            $levelStr = 'INFO';
            break;

        case 'warning':
            // nível de aviso
            $levelStr = 'WARNING';
            break;

        case 'error':
            // nível de erro
            $levelStr = 'ERROR';
            break;
    }

    // data atual
    $date = date( 'Y-m-d H:i:s' );

    // formata a mensagem do log
    // 1o: data atual
    // 2o: nível da mensagem (INFO, WARNING ou ERROR)
    // 3o: a mensagem propriamente dita
    // 4o: uma quebra de linha
    $msg = sprintf( "[%s] [%s] [%s] [%s]: %s%s", $date, $levelStr, $info, $methodStr, serialize($content), PHP_EOL );

    // escreve o log no arquivo
    // é necessário usar FILE_APPEND para que a mensagem seja escrita no final do arquivo, preservando o conteúdo antigo do arquivo
    file_put_contents( $file, $msg, FILE_APPEND );
    chmod($file, 0640);
  }
}
