<?php
/*
 * This file is part of the fyrts/psr-4-case-checker library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PSR4CaseChecker;

final class IndexDirectory
{
    protected $path;
    protected $name;
    protected $contents;
    
    public function __construct(string $path, ?string $name = null)
    {
        $this->path = $path;
        $this->name = $name;
    }
    
    public function getTruePath(array $path): ?string
    {
        if (is_null($this->contents)) {
            $this->contents = [];
            $directory = new \FilesystemIterator($this->path);
            foreach ($directory as $file) {
                $filename = $file->getFilename();
                if ($file->isDir()) {
                    $this->contents[strtolower($filename)] = new IndexDirectory($file->getPathname(), $filename);
                } else if (strtolower($file->getExtension()) === 'php') {
                    $this->contents[strtolower($filename)] = $filename;
                }
            }
        }
        $part = array_shift($path);
        if (!isset($this->contents[$part])) {
            return null;
        } else if (is_string($this->contents[$part])) {
            $part = $this->contents[$part];
        } else {
            $part = $this->contents[$part]->getTruePath($path);
        }
        return is_null($this->name) ? $part : $this->name . DIRECTORY_SEPARATOR . $part;
    }
}