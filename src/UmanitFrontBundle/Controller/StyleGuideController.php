<?php

declare(strict_types=1);

namespace Umanit\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StyleGuideController extends AbstractController
{
    public function __invoke(string $template)
    {  return $this->render('style_guide/' . $template . '.html.twig');
    }
}