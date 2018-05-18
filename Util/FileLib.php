<?php

namespace Prodigious\MultisiteBundle\Util;

use Symfony\Component\Filesystem\Filesystem;

class FileLib
{
    /**
     * @var Filesystem
     */
    private static $fileSystem;

    public static function fileSystem()
    {
        return self::$fileSystem = new Filesystem();
    }

    /**
     * Copy a directory to another location
     *
     * @param string $srcdir
     * @param string $dstdir
     */
    public static function sync($srcdir, $dstdir)
    {
        $dir = opendir($srcdir);
        @mkdir($dstdir);
        while ($file = readdir($dir)) {
            if ($file != '.' && $file != '..') {
                $src = $srcdir . '/' . $file;
                $dst = $dstdir . '/' . $file;
                if (is_dir($src)) {
                    self::sync($src, $dst);
                }
                else {
                    copy($src, $dst);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Dump file
     *
     * @param string $filename
     * @param string $content
     */
    public static function dumpFile($filename, $content)
    {
        self::fileSystem()->dumpFile($filename, $content);
    }

    /**
     * Copy file
     *
     * @param string $originFile
     * @param string $targetFile
     * @param bool $overwriteNewerFiles
     */
    public static function copy($originFile, $targetFile, $overwriteNewerFiles = false)
    {
        self::fileSystem()->copy($originFile, $targetFile, $overwriteNewerFiles);
    }

    /**
     * Create directory
     *
     * @param string|iterable $dirs The directory path
     * @param int             $mode The directory mode
     */
    public static function mkdir($dirs, $mode = 0777)
    {
        self::fileSystem()->mkdir($dirs, $mode);
    }

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files
     */
    public static function remove($files)
    {
        self::fileSystem()->remove($files);
    }
}