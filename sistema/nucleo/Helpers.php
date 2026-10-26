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
    public static function extrairEntreMarcadores(string $texto, string $marcadorInicial, ?string $marcadorFinal = null): string
    {
        if ($marcadorFinal === null) {
            return Helpers::obterTrechoAPartirDe($texto, $marcadorInicial);
        }

        $posicaoInicial = Helpers::obterPosicaoMarcador($texto, $marcadorInicial);

        // tem diferença entre caracters especiais entre strpos e stripos?
        $iniciaMarcadorFinal = strpos($texto, $marcadorFinal, $posicaoInicial);
        $tamanhoMarcadorFinal = strlen($marcadorFinal);

        $posicaoFinal = $iniciaMarcadorFinal + $tamanhoMarcadorFinal;

        $tamanhoTrecho = $posicaoFinal === false
            ? null
            : $posicaoFinal - $posicaoInicial;

        return substr($texto, $posicaoInicial, $tamanhoTrecho);
    }

    // extrairEntreRev() seria mais intuitivo?
    public static function extrairAteMarcadorAnterior(string $texto, string $marcadorChave, ?string $marcadorAnterior = null): string
    {
        if ($marcadorAnterior === null) {
            return Helpers::obterTrechoAPartirDe($texto, $marcadorChave, true);
        } else {
            $inicioMarcadorChave = Helpers::obterPosicaoMarcador($texto, $marcadorChave);
            $tamanhoMarcadorChave = strlen($marcadorChave);

            $posicaoFinalTrecho = $inicioMarcadorChave + $tamanhoMarcadorChave;

            $posicaoMarcadorAnterior = Helpers::obterPosicaoMarcadorAnterior($texto, $marcadorAnterior, $posicaoFinalTrecho);

            $tamanhoTrecho = $posicaoFinalTrecho - $posicaoMarcadorAnterior;

            return substr($texto, $posicaoMarcadorAnterior, $tamanhoTrecho);
        }
    }

    private static function obterTrechoAPartirDe(string $texto, string $marcador, bool $antesDoMarcador = false): string
    {
        $trecho = $antesDoMarcador
            ? strstr($texto, $marcador, true)
            : strstr($texto, $marcador, true);

        if ($trecho === false) {
            throw new \InvalidArgumentException('O marcador informado não foi encontrado no texto.');
        }

        return $trecho;
    }
    private static function obterPosicaoMarcador(string $texto, string $marcador): int
    {
        $posicaoMarcador = stripos($texto, $marcador);

        if ($posicaoMarcador === false) {
            throw new \InvalidArgumentException('O marcador não foi encontrado no texto.');
        }

        return $posicaoMarcador;
    }

    private static function obterPosicaoMarcadorAnterior(string $texto, string $marcador, int $posicaoLimite): int
    {
        $posicaoEncontrada = null;
        $tamanhoMarcador = strlen($marcador);

        for ($i = $posicaoLimite; $i >= 0 ; $i--) {
            if (substr($texto, $i, $tamanhoMarcador) === $marcador) {
                $posicaoEncontrada = $i;
                break;
            }
        }

        if (is_int($posicaoEncontrada)) {
            return $posicaoEncontrada;
        }

        throw new \InvalidArgumentException('O marcador não foi encontrado no texto.');

    }
}
