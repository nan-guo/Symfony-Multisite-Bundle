<?php

namespace Prodigious\MultisiteBundle\Command;

/**
 * Validator functions.
 */
class Validators
{
    /**
     * Validates that the given site name is a valid format.
     *
     *
     * @param $name
     *
     * @return string
     */
    public static function validateSiteName($name)
    {
       
        if (!preg_match('/^[a-zA-Z0-9_]*$/', $name)) {
            throw new \InvalidArgumentException('The site name contains invalid characters.');
        }

        $name = strtolower($name);

        // validate reserved keywords
        $reserved = self::getReservedWords();
        if (in_array($name, $reserved)) {
            throw new \InvalidArgumentException(sprintf('The sitename cannot contain reserved words ("%s").', $name));
        }

        return $name;
    }

    /**
     * Performs basic checks in host .
     *
     * @param string $host
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function validateHost($host)
    {
        if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?$/', $host)) {
            throw new \InvalidArgumentException(sprintf('The host name isn\'t valid ("%s" given, expecting something like site.demo.com)', $host));
        }

        return $host;
    }

    public static function getReservedWords()
    {
        return array(
            'app',
            '__CLASS__',
            '__DIR__',
            '__FILE__',
            '__LINE__',
            '__FUNCTION__',
            '__METHOD__',
            '__NAMESPACE__',
            '__TRAIT__',
        );
    }
}
