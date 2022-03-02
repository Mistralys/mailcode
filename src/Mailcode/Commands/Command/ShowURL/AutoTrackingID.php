<?php
/**
 * File containing the class {@see \Mailcode\Commands\Command\ShowURL\AutoTrackingID}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Commands\Command\ShowURL\AutoTrackingID
 */

declare(strict_types=1);

namespace Mailcode\Commands\Command\ShowURL;

use Mailcode\Interfaces\Commands\Validation\TrackingIDInterface;

/**
 * Generates a tracking ID for a URL, used when a
 * `showurl` command's tracking is enabled, but no
 * tracking ID is specified.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class AutoTrackingID
{
    public const AUTO_ID_TEMPLATE = 'link-%03d';

    private static int $linkCounter = 0;

    /**
     * @var array<int,string>
     */
    private static array $generated = array();

    /**
     * @var callable|NULL
     */
    private static $customGenerator = null;

    /**
     * Generates an ID for the specified command.
     * Subsequent calls for the same command will
     * return the same ID.
     *
     * @param TrackingIDInterface $command
     * @return string
     */
    public static function generate(TrackingIDInterface $command) : string
    {
        $instanceID = $command->getInstanceID();

        if(isset(self::$generated[$instanceID]))
        {
            return self::$generated[$instanceID];
        }

        $trackingID = self::generateIDString($command);

        self::$generated[$instanceID] = $trackingID;

        return $trackingID;
    }

    /**
     * Sets the callback used to auto-generate tracking IDs
     * for URLs. It gets the mailcode command as sole parameter,
     * and must return a tracking ID string.
     *
     * @param callable $generator
     * @return void
     */
    public static function setGenerator(callable $generator) : void
    {
        self::$customGenerator = $generator;
    }

    public static function resetGenerator() : void
    {
        self::$customGenerator = null;
    }

    public static function hasCustomGenerator() : bool
    {
        return isset(self::$customGenerator);
    }

    private static function generateIDString(TrackingIDInterface $command) : string
    {
        if(isset(self::$customGenerator))
        {
            $result = call_user_func(self::$customGenerator, $command);

            if(is_string($result))
            {
                return $result;
            }
        }

        return self::generateDefault();
    }

    public static function resetLinkCounter() : void
    {
        self::$linkCounter = 0;
    }

    public static function generateDefault() : string
    {
        self::$linkCounter++;

        return sprintf(self::AUTO_ID_TEMPLATE, self::$linkCounter);
    }
}
