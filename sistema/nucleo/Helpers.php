<?php

namespace sistema\nucleo;

use sistema\nucleo\Configuracao;

//use Exception;

class Helpers
{
    public static function redirecionar(?string $url): void
    {
        header('HTTP/1.1 302 Found');

        $local = ($url ? self::url($url) : self::url());

        header("Location: {$local}");
        exit();
    }

    public static function url(?string $url = null): string
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_UNSAFE_RAW);

        $ambiente = ($servidor === 'localhost' ? Configuracao::URL_DEV : Configuracao::URL_PROD);

        return $ambiente . ltrim((string) $url, '/');
    }

    public static function localhost(): bool
    {
        $servidor = filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_UNSAFE_RAW);

        if ($servidor === 'localhost') {
            return true;
        }
        return false;
    }

    public static function flash(): ?string
    {
        $sessão = new Sessao();
        if ($flash = $sessão->flash()) {
            echo $flash;
        }
        return null;
    }

    /**
     * Extrai o trecho de um texto a partir de um marcador inicial.
     *
     * Retorna o texto a partir do marcador inicial, incluindo-o. Quando um
     * marcador final é informado, o trecho termina imediatamente antes dele.
     * Se o marcador final não for encontrado, retorna o texto até o fim.
     *
     * A busca pelo marcador inicial diferencia maiúsculas de minúsculas quando não há marcador final ou o mesmo não é encontrado
     *
     * Exemplos:
     * - extrairTrecho('denomination, id, observation', 'id') retorna 'id, observation'.
     * - extrairTrecho('Inicio: valor; fim', 'Inicio:', ';') retorna 'Inicio: valor'.
     *
     * @param string      $texto          Texto no qual o trecho será localizado.
     * @param string      $marcadorInicial Marcador a partir do qual a extração começa.
     * @param string|null $marcadorFinal   Marcador opcional que encerra a extração.
     *
     * @return string Trecho extraído do texto.
     *
     * @throws \InvalidArgumentException Se o marcador inicial não for encontrado.
     */
    public static function extrairTrecho(string $texto, string $marcadorInicial, ?string $marcadorFinal = null): string
    {
        if ($marcadorFinal === null) {
            $trecho = strstr($texto, $marcadorInicial);
            if ($trecho === false) {
                throw new \InvalidArgumentException('O marcador inicial não foi encontrado no texto.');
            }

            return $trecho;
        }

        $posicaoInicial = stripos($texto, $marcadorInicial);
        if ($posicaoInicial === false) {
            throw new \InvalidArgumentException('O marcador inicial não foi encontrado no texto.');
        }

        $posicaoFinal = strpos($texto, $marcadorFinal, $posicaoInicial);
        $comprimentoTrecho = $posicaoFinal === false
            ? null
            : $posicaoFinal - $posicaoInicial;

        return substr($texto, $posicaoInicial, $comprimentoTrecho);
    }
}
