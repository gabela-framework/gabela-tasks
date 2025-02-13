<?php
function genRiskReturns_FundAnalysis($pdf, $arPortfolio, bool $blnCombined, $arPIndex)
{
    global $strPageOrient, $arFillColr, $intFColrCnt;
    global $arPracticeInfo;
    global $arAssetLocn, $arAssetType;
    global $intDP, $intPercDP, $intFracDP, $strBrdrCss, $strTHcss, $strDataCss;

    /** @var MYPDF $pdf
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    // Set the line style for the PDF
    $pdf->SetLineStyle([
        'width' => 0.5 / $pdf->getScaleFactor(),
        'cap' => 'butt',
        'join' => 'miter',
        'dash' => 0,
        'color' => PDF_LINE_COLR_RGBar
    ]);

    $arPgDim = $pdf->getPageDimensions();
    $arWidth = getColWidths_FundAnalysis($blnCombined);
    $arFundValu = array();
    $fundNameHTML = '<table border="1" cellspacing="0" width="100%" style="padding:' . PDF_TBLCELL_PADDING . ';font-size:1.3em;">';

    $fundNameHTML .= '<tr>
        <th style="font-weight: bold;">Fund Name</th>
        <th style="font-weight: bold;">Perc %</th>
        <th style="font-weight: bold;">MaxDrawdown %</th>
        <th style="font-weight: bold;">Objective ID</th>
        <th style="font-weight: bold;">Objective Name</th>
        <th style="font-weight: bold;"><strong>Total</strong></th>
    </tr>';

    $intMaxFundRows = 20;
    $totalRisk = 0;
    $totalPercentage = 0;
    $objectiveTotals = [];

    foreach ($arPortfolio['arFund'] as $intFundID => $arFund) {
        if (! (floatval($arFund['apnTotlAmntZAR']) > 0)) continue;

        $arFundValu[$intFundID] = $arFund['apnTotlAmntZAR'];

        // Decrement max number of funds shown if fund name is more than one line
        if (strlen($arFund['strFundName']) > 50) $intMaxFundRows--;
    }

    arsort($arFundValu, SORT_NUMERIC);
    $arFundIdx = array_keys($arFundValu);
    $apnOthrsAmntZAR = strval(0);
    $arPieSector = [];

    foreach ($arFundIdx as $intIdx => $intFundID) {
        $arFund = &$arPortfolio['arFund'][$intFundID];
        $strZARvalue = number_format($arFund['apnTotlAmntZAR'], 0, NUMBER_DECPNT, NUMBER_THUSEP);
        $apnFraction = bcdiv($arFund['apnTotlAmntZAR'], $arPortfolio['apnTotlAmntZAR'], $intFracDP);
        $apnPercentg = bcmul($apnFraction, '100', $intPercDP);
        $strPercentg = number_format($apnPercentg, $intPercDP, NUMBER_DECPNT, NUMBER_THUSEP);

        if ($intIdx < $intMaxFundRows) {
            $FC = $intIdx % $intFColrCnt; // modulo
            $strFillCss = ';background-color:rgb(' . implode(',', $arFillColr[$FC]) . ')';
            $arPieSector[$intIdx] = $apnPercentg;

            // Calculating the risk percentage
            $investPercentage = (float)$strPercentg;
            $maxDrawdown = (float)$arFund['MaxDrawdown'];
            $investPercentageDecimal = $investPercentage / 100;
            $maxDrawdownDecimal = $maxDrawdown / 100;
            $riskTotals = $investPercentageDecimal * $maxDrawdownDecimal;
            $riskTotalsPercentage = $riskTotals * 100;
            $riskTotalsPercentage = number_format($riskTotalsPercentage, 2, ".", "");

            // Update the totals
            $totalRisk += $riskTotalsPercentage;
            $totalPercentage += $investPercentage;

            // Track objective name totals
            $objectiveID = $arFund['ObjectiveTypeID'];
            $objectiveName = $arFund['ObjectiveTypeName'];

            if (!isset($objectiveTotals[$objectiveName])) {
                $objectiveTotals[$objectiveName] = 0;
            }

            $objectiveTotals[$objectiveName] += $riskTotalsPercentage;

            $fundNameHTML .= '<tr><td>' . $arFund['strFundName'] .
                '</td><td>' . $strPercentg .
                '%</td><td>' . $arFund['MaxDrawdown'] .
                '%</td><td>' . $arFund['ObjectiveTypeID'] .
                '</td><td>' . $arFund['ObjectiveTypeName'] .
                '</td><td>' . $riskTotalsPercentage .
                '%</td></tr>';
        } else {
            $apnOthrsAmntZAR = bcadd($apnOthrsAmntZAR, $arFund['apnTotlAmntZAR'], $intDP);
        }
    }

    $fundNameHTML .= '</tbody></table>';

    // Prepare the dynamic HTML content for the table
    $HTML = '<h2>Investment Risk/Returns Analysis</h2>';
    $HTML .= '<table border="0" cellspacing="0" width="60%" style="padding:' . PDF_TBLCELL_PADDING . ';font-size:1.3em;">';
    $HTML .= '<tbody>';

    // Fund Weighting Risk section with dynamic values
    $HTML .= '<tr><td colspan="2"><strong>Fund Weighting Risk:</strong></td></tr>';

    foreach ($objectiveTotals as $objectiveName => $totalRisk) {
        $HTML .= '<tr><td>- ' . $objectiveName . '</td><td>' . number_format($totalRisk, 2) . '%</td></tr>';
    }

    $HTML .= '<tr><td><strong>Total</strong></td><td><strong>' . number_format($totalPercentage, 2) . '%</strong></td></tr>';

    // Money Map return objective section
    $HTML .= '<tr><td colspan="2">&nbsp;</td></tr>'; // Blank row for spacing
    $HTML .= '<tr><td><strong>Money Map return objective:</strong></td><td>Inflation plus 3-5%</td></tr>';

    // Aggregate drawdown risk section with calculated total
    $HTML .= '<tr><td colspan="2">&nbsp;</td></tr>'; // Blank row for spacing
    $HTML .= '<tr><td><strong>Aggregate drawdown risk:</strong></td><td>' . number_format($totalRisk, 2) . '%</td></tr>';

    // Client signature section
    $HTML .= '<tr><td colspan="2">&nbsp;</td></tr>'; // Blank row for spacing
    $HTML .= '<tr><td><strong>Client signature:</strong></td><td>___________________________</td></tr>';

    $HTML .= '</tbody></table>';

    $pdf->writeHTML($fundNameHTML, LFEED_Y, FILL_N, RSETH_N, CELL_N, ALIGN_L);

    // Output the prepared HTML to the PDF
    $pdf->writeHTML($HTML, LFEED_Y, FILL_N, RSETH_N, CELL_N, ALIGN_L);
}

return [
    'TasksController' => [
        'namespace' => 'Gabela\\Tasks\\Controller\\',
        'path' => TASKS_PATH . '/controllers/',
    ],
    'TasksDeleteController' => [
        'namespace' => 'Gabela\\Tasks\\Controller\\',
        'path' => TASKS_PATH . '/controllers/',
    ],
    'TasksEditController' => [
        'namespace' => 'Gabela\\Tasks\\Controller\\',
        'path' => TASKS_PATH . '/controllers/',
    ],
    'TasksCreateSubmitController' => [
        'namespace' => 'Gabela\\Tasks\\Controller\\',
        'path' => TASKS_PATH . '/controllers/',
    ],
    'TasksCreateController' => [
        'namespace' => 'Gabela\\Tasks\\Controller\\',
        'path' => TASKS_PATH . '/controllers/',
    ],
    'TasksSubmitController' => [
        'namespace' => 'Gabela\\Tasks\\Controller\\',
        'path' => TASKS_PATH . '/controllers/',
    ],
];
