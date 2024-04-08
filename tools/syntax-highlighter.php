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
use function \AppLocalize\pt;
use function \AppLocalize\pts;use function AppLocalize\t;

require_once 'prepend.php';

$request = new Request();
$mailcode = Mailcode::create();

$commandsText = '';
$translated = '';
$error = null;
$highlighted = '';

if($request->getBool('highlight'))
{
    $commandsText = $request->registerParam('mailcode')->getString();

    try
    {
        $safeguard = $mailcode->createSafeguard($commandsText);
        $safe = htmlspecialchars($safeguard->makeSafe(), ENT_QUOTES, 'UTF-8');
        $highlighted = $safeguard->makeHighlighted($safe);
    }
    catch (Mailcode_Exception $e)
    {
        $error = $e->getMessage();

        $collection = $e->getCollection();
        if($collection)
        {
            $first = $collection->getFirstError();
            $error = $first->getMessage();
            $matched = $first->getMatchedText();
            if(!empty($matched)) {
                $error .= '<br>'. t('In command:').' <code>'.$matched.'</code>';
            }
        }
    }
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php pts('Syntax highlighter'); ?> - <?php echo Mailcode::getName(); ?></title>
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
        <h1><?php pt('Syntax highlighter') ?></h1>
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
            <button type="submit" name="highlight" value="yes" class="btn btn-primary">
                <?php pt('Highlight commands') ?>
            </button>
        </form>
        <p></p><br>
        <h2><?php pt('Highlighted commands') ?></h2>
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
                    <pre style="border: solid 1px #ccc;padding:12px;border-radius: 5px"><?php
                        echo $highlighted;
                    ?></pre>
                <?php
            }
        ?>
    </div>
</body>
</html>
