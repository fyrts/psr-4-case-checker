<?php
/*
 * This file is part of the fyrts/psr-4-case-checker library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PSR4CaseChecker;

use Composer\Autoload\ClassLoader;

final class PSR4CaseChecker
{
	protected static $initialized = false;
    protected static $classLoader;
    protected static $rootPath;
    protected static $vendorPath;
    protected static $fileIndex;
    protected static $namespaces = [];
    
    private function __construct() {}
    
    public static function init(): bool
    {
		if (self::$initialized) return true;
		
		// Detect namespaces through Composer
		if (!class_exists('Composer\\Autoload\\ClassLoader')) return false;
		$composer_path = self::getRootPath() . DIRECTORY_SEPARATOR . 'composer.json';
		if (!file_exists($composer_path)) return false;
		$composer_json = json_decode(file_get_contents($composer_path), true);
		if (isset($composer_json['autoload']['psr-4'])) self::initNamespaces($composer_json['autoload']['psr-4']);
		if (isset($composer_json['autoload-dev']['psr-4'])) self::initNamespaces($composer_json['autoload-dev']['psr-4']);
		
		// Preload IndexDirectory class
		self::getClassLoader()->loadClass(IndexDirectory::class);
		
		self::$initialized = spl_autoload_register([self::class, 'autoload'], false, true);
		
        return self::$initialized;
    }

    public static function autoload(string $classname): void
    {
        $class_path = null;
        foreach (self::$namespaces as $path => $namespace) {
            if (strpos($classname, $namespace) !== 0) continue;
            if (is_null($class_path)) $class_path = self::getRelativePath(self::getClassLoader()->findFile($classname));
            if (strpos($class_path, $path) !== 0) continue;

            if (is_null(self::$fileIndex)) self::$fileIndex = new IndexDirectory(self::getRootPath());
            $true_path = self::$fileIndex->getTruePath(explode(DIRECTORY_SEPARATOR, strtolower($class_path)));
            $short_path = substr($true_path, strlen($path));
            $short_classname = substr($classname, strlen($namespace));
            $expected_path = preg_replace('/[\\/\\\]/', DIRECTORY_SEPARATOR, $short_classname) . '.php';
            
            if ($short_path !== $expected_path) {
                throw new ClassnameCasingException('File \'' . $true_path . '\' does not match the casing of class \'' . $classname . '\'');
            }
            break;
        }
    }

    protected static function getRootPath(): string
    {
        if (is_null(self::$rootPath)) {
            $reflector = new \ReflectionClass(ClassLoader::class);
            self::$vendorPath = realpath(dirname($reflector->getFileName(), 2));
            self::$rootPath = dirname(self::$vendorPath);
        }
        return self::$rootPath;
    }
    
    protected static function initNamespaces(array $namespace_map): void
    {
        foreach ($namespace_map as $namespace => $paths) {
            foreach (is_array($paths) ? $paths : [$paths] as $path) {
                $path = preg_replace('/[\\/\\\]/', DIRECTORY_SEPARATOR, $path);
                self::$namespaces[$path] = $namespace;
            }
        }
    }

    protected static function getClassLoader(): ClassLoader
    {
        if (is_null(self::$classLoader)) {
            self::$classLoader = require(self::$vendorPath . DIRECTORY_SEPARATOR . 'autoload.php');
        }
        return self::$classLoader;
    }

    protected static function getRelativePath(string $path): string
    {
        $realpath = realpath($path);
        return ltrim(substr($realpath, strlen(self::getRootPath())), DIRECTORY_SEPARATOR);
    }
}
