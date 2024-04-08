<?php
/**
 * Utility script that can translate a text with Mailcode commands
 * to any of the available translation syntaxes. Meant to be opened
 * in a browser.
 *
 * @package Mailcode
 * @subpackage Tools
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */

declare(strict_types=1);

use AppUtils\Request;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator_Exception;
use function AppLocalize\pt;
use function AppLocalize\pts;
use function AppLocalize\t;

require_once 'prepend.php';

$request = new Request();
$mailcode = Mailcode::create();
$translator = $mailcode->createTranslator();
$syntaxes = $translator->getSyntaxes();

$commandsText = '';
$translated = '';
$error = null;
$activeSyntax = $syntaxes[0]->getTypeID();

if($request->getBool('translate'))
{
    $commandsText = $request->registerParam('mailcode')->getString();
    $activeSyntax = $request->registerParam('syntax')
        ->setEnum($translator->getSyntaxNames())
        ->getString($activeSyntax);

    try
    {
        $translated = translateMailcode($commandsText, $activeSyntax);
    }
    catch (Mailcode_Exception $e)
    {
        $translated = '';
        $error = $e->getMessage();

        $collection = $e->getCollection();
        if($collection)
        {
            $first = $collection->getFirstError();
            $error = $first->getMessage();
            $matched = $first->getMatchedText();
            if(!empty($matched)) {
                $error .= '<br>'.t('In command:').' <code>'.$matched.'</code>';
            }
        }
    }
}

/**
 * Translates the specified text to Mailcode.
 *
 * @param string $subject
 * @param string $syntax
 * @return string
 * @throws Mailcode_Exception
 * @throws Mailcode_Translator_Exception
 */
function translateMailcode(string $subject, string $syntax) : string
{
    $mailcode = Mailcode::create();
    $translator = $mailcode->createTranslator()->createSyntax($syntax);

    $safeguard = $mailcode->createSafeguard($subject);
    return $translator->translateSafeguard($safeguard);
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php pts('Syntax translator'); ?> - <?php echo Mailcode::getName(); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link href="main.css" rel="stylesheet">
    <style>
        <?php echo $mailcode->createStyler()->getCSS() ?>
    </style>
</head>
<body>
    <div class="container">
        <p>
            <a href="./">&laquo; <?php pts('Back to overview'); ?></a>
        </p>
        <h1><?php pt('Commands translation') ?></h1>
        <p>
            <?php
                pts('Enter the text that contains the mailcode commands to convert, and choose the output language to convert them to.');
                pts('Can be any kind of text, including HTML/XML.');
            ?>
        </p>
        <p class="text-warning">
            <strong><?php pt('Note:') ?></strong>
            <?php pt('The Mailcode commands must be valid.') ?>
        </p>
        <form method="post">
            <p>
                <textarea class="form-control" name="mailcode" rows="10"><?php echo htmlspecialchars($commandsText) ?></textarea>
            </p>
            <p>
                <select name="syntax" class="form-control">
                    <?php
                        foreach ($syntaxes as $syntax)
                        {
                            $selected = '';
                            $typeID = $syntax->getTypeID();

                            if($typeID === $activeSyntax) {
                                $selected = ' selected';
                            }

                            ?>
                                <option value="<?php echo $syntax->getTypeID() ?>>" <?php echo $selected ?>>
                                    <?php echo $syntax->getTypeID() ?>
                                </option>
                            <?php
                        }
                    ?>
                </select>
            </p>
            <button type="submit" name="translate" value="yes" class="btn btn-primary">
                <?php pt('Translate commands') ?>
            </button>
        </form>
        <p></p><br>
        <h2><?php pt('Translated commands') ?></h2>
        <?php
            if(empty($commandsText))
            {
                ?>
                    <div class="alert alert-info">
                        <?php pt('No commands specified.') ?>
                    </div>
                <?php
            }
            else if($error)
            {
                ?>
                    <div class="alert alert-danger">
                        <strong><?php pt('Error parsing commands:') ?></strong>
                        <?php echo $error ?>
                    </div>
                <?php
            }
            else
            {
                ?>
                    <textarea class="form-control" rows="10"><?php echo htmlspecialchars($translated) ?></textarea>
                <?php
            }
        ?>
    </div>
</body>
</html>
