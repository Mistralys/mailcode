<?php
/**
 * Example file for the command highligting color scheme.
 *
 * @package Mailcode
 * @subpackage Localization
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */

    use Mailcode\Mailcode;
    use function AppLocalize\pt;
    use function AppLocalize\pts;
    use Mailcode\Mailcode_Factory;
    
    $root = __DIR__;
    
    $autoload = realpath($root.'/../vendor/autoload.php');
    
    // we need the autoloader to be present
    if($autoload === false) 
    {
        die('<b>ERROR:</b> Autoloader not present. Run composer update first.');
    }
    
    /**
     * The composer autoloader
     */
    require_once $autoload;

    $mailcode = new Mailcode();
    $styler = $mailcode->createStyler();

    
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php pt('Highlighting examples') ?></title>
        <?php echo $styler->getStyleTag() ?>
        <style>
            BODY{
            font-family:monospace;
            background:#fdf6e3;
            padding:20px;
            }
        </style>
    </head>
    <body>
    	<p>
    		<?php 
    		    pts('This showcases the command\'s syntax highlighting.');
    		    pts(
    		        'It is based on the %1$s color scheme%2$s:',
    		        '<a href="https://ethanschoonover.com/solarized/">"Solarized"',
    		        '</a>'
		        );
    		?>
		</p>
		<br>
    	<div class="commands">
            <?php 
            
                $commands = array(
                    Mailcode_Factory::showVar('VARIABLE.NAME'),
                    Mailcode_Factory::comment('Some comments here'),
                    Mailcode_Factory::showSnippet('snippet_name'),
                    Mailcode_Factory::showDate('DATE.VARIABLE'),
                    Mailcode_Factory::if('1 + 1 == 2'),
                    Mailcode_Factory::ifContains('CUSTOMER.NAME', 'John'),
                    Mailcode_Factory::ifVarEquals('NUMBER', 124),
                );
                
                $and = Mailcode_Factory::elseIfVarEqualsString('STRINGVAR', 'John');
                $and->getLogicKeywords()->appendOR('$STRINGVAR == "Steve"', "variable");
                
                $commands[] = $and;
                
                $commands[] = Mailcode_Factory::end();
                
                foreach($commands as $command)
                {
                    ?>
                    	<p>
                    		<?php echo $command->getHighlighted(); ?>
                		</p>
            		<?php 
                }
            ?>
        </div>
    </body>
</html>
