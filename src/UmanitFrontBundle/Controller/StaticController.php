<?php

declare(strict_types=1);

namespace Umanit\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StaticController
 */
final class StaticController extends AbstractController
{
    public function __invoke(string $path, string $basePath = ''): Response
    {
        $extension = '.html' === mb_substr($path, -5) ? '' : '.html';

        return $this->render($basePath.$path.$extension.'.twig');
    }
}
