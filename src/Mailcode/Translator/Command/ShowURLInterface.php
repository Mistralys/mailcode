<?php

declare(strict_types=1);

namespace Mailcode\Translator\Command;

use Mailcode\Mailcode_Commands_Command_ShowURL;

interface ShowURLInterface
{
    public function translate(Mailcode_Commands_Command_ShowURL $command) : string;
}
