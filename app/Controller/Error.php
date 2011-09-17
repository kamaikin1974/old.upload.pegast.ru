<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Response;

class Error
{
    public function __invoke(\Exception $e, $code)
    {
        switch ($code) {
            case 404:
                $message = 'The requested page could not be found.';
                break;
            default:
                $message = $e->getMessage();
        }

        return new Response($message, $code);
    }
}
