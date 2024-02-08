<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController
{
    #[Route('/', name: 'index_redirect')]
    public function indexRedirect(): RedirectResponse
    {
        return new RedirectResponse('/login');
    }
}
