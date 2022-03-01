<?php

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Interfaces_Commands_Validation_URLDecode;
use Mailcode\Mailcode_Interfaces_Commands_Validation_URLEncode;

interface URLEncodingInterface
    extends
    Mailcode_Interfaces_Commands_Validation_URLDecode,
    Mailcode_Interfaces_Commands_Validation_URLEncode
{

}
