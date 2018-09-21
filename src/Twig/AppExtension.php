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

    public function adminPanelSourceCodeFilter(string $sourceCode, string $idScript, $currentDashBoard)
    {
        $sourceCode = str_replace("\"", "'", $sourceCode);
        $sourceCode = str_replace($idScript, $idScript . "&adminPanel=1", $sourceCode);
        if ($currentDashBoard != null) {
            $sourceCode = str_replace("&adminPanel=1", "&adminPanel=1&dashboardWindow=" . $currentDashBoard, $sourceCode);
        }
        return $sourceCode;
    }
}