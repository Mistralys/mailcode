<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_URLAnalyzer} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_URLAnalyzer
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

/**
 * Detects all URLs in the subject string, and tells all placeholders
 * that are contained in URLs, that they are in an URL. This allows
 * those commands to adjust themselves automatically if necessary.
 *
 * Example: showvar commands that automatically turn on URL encoding.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_URLAnalyzer
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var Mailcode_Parser_Safeguard
     */
    private $safeguard;

    public function __construct(string $subject, Mailcode_Parser_Safeguard $safeguard)
    {
        $this->subject = $subject;
        $this->safeguard = $safeguard;
    }

    public function analyze() : void
    {
        $urls = ConvertHelper::createURLFinder($this->subject)
            ->includeEmails(false)
            ->getURLs();

        foreach ($urls as $url)
        {
            $this->analyzeURL($url);
        }
    }

    private function analyzeURL(string $url) : void
    {
        if(stristr($url, 'tel:') !== false)
        {
            return;
        }

        $placeholders = $this->safeguard->getPlaceholdersCollection()->getAll();

        foreach($placeholders as $placeholder)
        {
            $command = $placeholder->getCommand();

            if(!$command->supportsURLEncoding())
            {
                continue;
            }

            if(strstr($url, $placeholder->getReplacementText()) !== false && !$command->isURLDecoded())
            {
                $command->setURLEncoding(true);
                break;
            }
        }
    }
}
