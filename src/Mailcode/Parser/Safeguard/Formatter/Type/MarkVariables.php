<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;

/**
 * Variable marker: Surrounds all show variable commands with markup 
 * to highlight them, independently of the command highlighting itself.
 * 
 * This is meant to be used to highlight variables in a document even
 * after it has been run through post-processing (for example once the
 * Apache Velocity template has been rendered). 
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables extends Mailcode_Parser_Safeguard_Formatter_FormatType
{
    use Mailcode_Traits_Formatting_HTMLHighlighting;
    
    const ERROR_INVALID_TAG_TEMPLATE = 66101;
    const ERROR_CANNOT_FIND_STYLESHEET = 66102;
    const ERROR_CANNOT_EXTRACT_STYLES = 66103;

    const TEMPLATE_MODE_CLASS = 'class';
    const TEMPLATE_MODE_INLINE = 'inline';
    const TEMPLATE_MODE_CUSTOM = 'custom';

    const DEFAULT_CLASS_NAME = 'mailcode-marked-variable';
    
    /**
     * @var array<string,string>
     */
    private $templates = array(
        self::TEMPLATE_MODE_CLASS => '<span class="'.self::DEFAULT_CLASS_NAME.'">%s</span>',
        self::TEMPLATE_MODE_INLINE => '<span style="__STYLES__">%s</span>',
        self::TEMPLATE_MODE_CUSTOM => ''
    );

    /**
     * @var string
     */
    private $templateMode = self::TEMPLATE_MODE_CLASS;

    protected function initFormatting() : void
    {
    }
    
    public function setTemplate(string $template) : Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    {
        if(substr_count($template, '%s') !== 1)
        {
            throw new Mailcode_Exception(
                'Invalid tag template',
                'The template string must contain the placeholder [%s] exactly 1 time.',
                self::ERROR_INVALID_TAG_TEMPLATE
            );
        }

        $this->templates[self::TEMPLATE_MODE_CUSTOM] = $template;

        return $this->setTemplateMode(self::TEMPLATE_MODE_CUSTOM);
    }

    public function makeInline() : Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    {
        return $this->setTemplateMode(self::TEMPLATE_MODE_INLINE);
    }

    public function makeClassBased() : Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    {
        return $this->setTemplateMode(self::TEMPLATE_MODE_CLASS);
    }

    private function setTemplateMode(string $mode) : Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    {
        $this->templateMode = $mode;

        return $this;
    }
    
    public function getTemplate() : string
    {
        return $this->templates[$this->templateMode];
    }
    
    public function getPrependTag() : string
    {
        $parts = explode('%s', $this->getTemplate());
        $tag = array_shift($parts);

        if($this->templateMode === self::TEMPLATE_MODE_INLINE)
        {
            $tag = str_replace('__STYLES__', $this->getInlineStyles(), $tag);
        }

        return $tag;
    }

    public function getAppendTag() : string
    {
        $parts = explode('%s', $this->getTemplate());
        return array_pop($parts);
    }

    public function getInlineStyles() : string
    {
        $styleString = $this->extractStyleString();

        $parts = ConvertHelper::explodeTrim(';', $styleString);

        return implode(';', $parts).';';
    }

    private function extractStyleString() : string
    {
        $styles = '';
        $regex = '/\.'.self::DEFAULT_CLASS_NAME.'{([^}]+)}/';

        if(preg_match($regex, $this->getCSS(), $matches))
        {
            $styles = $matches[1];
        }

        if(empty($styles))
        {
            throw new Mailcode_Exception(
                'Cannot extract styles.',
                sprintf(
                    'Tried extracting the styles from the CSS string using the regex [%s].',
                    $regex
                ),
                self::ERROR_CANNOT_EXTRACT_STYLES
            );
        }

        return $styles;
    }

    /**
     * Retrieves the raw CSS used to highlight the marked variables.
     *
     * @return string
     * @throws Mailcode_Exception
     * @throws \AppUtils\FileHelper_Exception
     * @see Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables::getStyleTag()
     */
    public function getCSS() : string
    {
        return FileHelper::readContents($this->getStylesheetPath());
    }

    /**
     * Retrieves the required CSS with a style tag, to insert it into
     * the target document.
     *
     * @return string
     * @throws Mailcode_Exception
     * @throws \AppUtils\FileHelper_Exception
     * @see Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables::getCSS()
     */
    public function getStyleTag() : string
    {
        return sprintf(
            '<style>%s</style>',
            $this->getCSS()
        );
    }

    /**
     * Retrieves the path to the stylesheet used to highlight the
     * marked variables, when using the "class" template mode.
     *
     * @return string
     * @throws Mailcode_Exception
     */
    public function getStylesheetPath() : string
    {
        $file = MAILCODE_INSTALL_FOLDER.'/css/marked-variables.css';
        $path = realpath($file);

        if($path !== false)
        {
            return $path;
        }

        throw new Mailcode_Exception(
            'Cannot find a required stylesheet.',
            sprintf(
                'Tried finding the file [%s] in path: [%s].',
                basename($file),
                $file
            ),
            self::ERROR_CANNOT_FIND_STYLESHEET

        );
    }
}
