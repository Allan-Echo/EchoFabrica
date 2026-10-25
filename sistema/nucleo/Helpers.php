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
            return Helpers::buscarTrecho($texto, $marcadorInicial);
        }

        $posicaoInicial = Helpers::buscarPosicao($texto, $marcadorInicial);
        $posicaoFinal = strpos($texto, $marcadorFinal, $posicaoInicial);
        $comprimentoTrecho = $posicaoFinal === false
            ? null
            : $posicaoFinal - $posicaoInicial;

        return substr($texto, $posicaoInicial, $comprimentoTrecho);
    }

    // extrairEntreRev() seria mais intuitivo?
    public static function extrairTrechoReverso(string $texto, string $marcadorChave, ?string $marcadorAnterior = null): string
    {
        if ($marcadorAnterior === null) {
            return Helpers::buscarTrecho($texto, $marcadorChave, true);
        } else {
            $posicaoChave = Helpers::buscarPosicao($texto, $marcadorChave);
            $posicaoMarcadorAnterior = Helpers::buscarPosicaoReverso($texto, $marcadorAnterior, $posicaoChave);
            $comprimentoTrecho = $posicaoChave - $posicaoMarcadorAnterior;

            return substr($texto, $posicaoMarcadorAnterior, $comprimentoTrecho);
        }
    }

    private static function buscarTrecho(string $texto, string $marcadorInicial, bool $antesDoMarcador = false): string
    {
        if ($antesDoMarcador) {
            $trecho = strstr($texto, $marcadorInicial, $antesDoMarcador);
        } else {
            $trecho = strstr($texto, $marcadorInicial);
        }

        if ($trecho === false) {
            throw new \InvalidArgumentException('O marcador inicial não foi encontrado no texto.');
        }

        return $trecho;
    }
    private static function buscarPosicao(string $texto, string $marcadorChave, bool $inverterDirecao = false): int
    {
        if ($inverterDirecao) {
            $posicaoMarcador = strrpos($texto, $marcadorChave);
        } else {
            $inicioMarcadorChave = stripos($texto, $marcadorChave);
            $comprimentoMarcadorChave = strlen($marcadorChave);
            $posicaoMarcador = $inicioMarcadorChave + $comprimentoMarcadorChave;
        }

        if ($posicaoMarcador === false) {
            throw new \InvalidArgumentException('O marcador não foi encontrado no texto.');
        }

        return $posicaoMarcador;
    }

    private static function buscarPosicaoReverso(string $texto, string $marcadorBuscado, int $posicaoChave): int
    {
        $posicaoBuscada = null;
        $comprimentoBuscado = strlen($marcadorBuscado);

        for ($i = $posicaoChave; $i >= 0 ; $i--) {
            if (substr($texto, $i, $comprimentoBuscado) === $marcadorBuscado) {
                $posicaoBuscada = $i;
                break;
            }
        }

        if (is_int($posicaoBuscada)) {
            return $posicaoBuscada;
        } else {
            throw new \InvalidArgumentException('O marcador não foi encontrado no texto.');
        }

    }
}
