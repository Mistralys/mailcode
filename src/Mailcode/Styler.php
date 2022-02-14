<?php
/**
 * File containing the {@see Mailcode} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\FileHelper;

/**
 * Generat
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Styler
{
    public const ERROR_CSS_FILE_NOT_FOUND = 60901;
    
   /**
    * @var string
    */
    private $path;
    
   /**
    * @var string
    */
    private $fileName = 'highlight.css';
    
    public function __construct()
    {
        $folder = sprintf(__DIR__.'/../../css/%s', $this->fileName);
        $path = realpath($folder);
        
        if($path === false)
        {
            throw new Mailcode_Exception(
                'Could not find the highlight CSS file',
                sprintf(
                    'Tried looking in folder [%s].',
                    $folder
                ),
                self::ERROR_CSS_FILE_NOT_FOUND
            );
        }
        
        $this->path = $path;
    }
   
   /**
    * Retrieves the raw CSS source for the highlighting.
    * 
    * @return string
    */
    public function getCSS() : string
    {
        return FileHelper::readContents($this->path);
    }
    
   /**
    * Retrieves a fully formed `code` tag with the CSS,
    * to inject inline into an HTML document.
    * 
    * @return string
    */
    public function getStyleTag() : string
    {
        return sprintf(
            '<!-- Mailcode highlight CSS --><style>%s</style>',
            $this->getCSS()
        );
    }
    
   /**
    * Retrieves the path to the stylesheet file.
    * 
    * @return string
    */
    public function getStylesheetPath() : string
    {
        return $this->path;
    }
    
   /**
    * Retrieves the URL to the stylesheet file, given the
    * local URL to the application's vendor folder.
    *  
    * @param string $vendorURL The URL to the vendor folder (must be accessible in the webroot).
    * @return string
    */
    public function getStylesheetURL(string $vendorURL) : string
    {
        return sprintf(
            '%s/mistralys/mailcode/css/%s',
            rtrim($vendorURL, '/'),
            $this->fileName
        );
    }
    
    public function getStylesheetTag(string $vendorURL) : string
    {
        return sprintf(
            '<link rel="stylesheet" src="%s">',
            $this->getStylesheetURL($vendorURL)
        );
    }
}
