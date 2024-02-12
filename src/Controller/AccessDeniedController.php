<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessDeniedController extends AbstractController
{
    #[Route('/access-denied', name: 'access_denied')]
    public function index(): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl('app_home'));
    }
}
