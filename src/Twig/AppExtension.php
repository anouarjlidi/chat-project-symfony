<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('adminPanelSourceCode', array($this, 'adminPanelSourceCodeFilter')),
        );
    }

    public function adminPanelSourceCodeFilter(string $sourceCode, string $idScript)
    {
        $sourceCode = str_replace("'", "\'", $sourceCode);
        $sourceCode = str_replace($idScript, $idScript . "&adminPanel=1", $sourceCode);
        return $sourceCode;
    }
}