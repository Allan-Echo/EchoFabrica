<?php

namespace sistema\nucleo;

use sistema\nucleo\Helpers;

class TratadorExcecao
{
    private function __construct(
        private readonly bool $debug
    ) {
    }

    public static function registrar(?bool $debug = null): void
    {
        $debug ??= Helpers::localhost();

        $tratador = new self($debug);
        set_exception_handler([$tratador, 'tratar']);
    }

    public function tratar(\Throwable $excecao): void
    {
        error_log(sprintf(
            "[%s] %s em %s:%d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $excecao->getMessage(),
            $excecao->getFile(),
            $excecao->getLine(),
            $excecao->getTraceAsString()
        ));

        if ($excecao instanceof \Pecee\SimpleRouter\Exceptions\NotFoundHttpException) {
            http_response_code(404);
            if ($this->debug) {
                (new Sessao())->criar('erro_debug', [
                    'mensagem' => $excecao->getMessage(),
                    'arquivo'  => $excecao->getFile(),
                    'linha'    => $excecao->getLine(),
                    'trace'    => $excecao->getTraceAsString(),
                ]);
                Helpers::redirecionar('erro/debug');
                return;
            }
            Helpers::redirecionar('erro404');
            return;
        }

        // fallback para 500
        http_response_code(500);
        if ($this->debug) {
            (new Sessao())->criar('erro_debug', [
                'mensagem' => $excecao->getMessage(),
                'arquivo'  => $excecao->getFile(),
                'linha'    => $excecao->getLine(),
                'trace'    => $excecao->getTraceAsString(),
            ]);
            Helpers::redirecionar('erro/debug');
            return;
        }

        (new Mensagem())->erro('Ocorreu um erro interno. Tente novamente.')->flash();
        Helpers::redirecionar('erro500');
    }
}
