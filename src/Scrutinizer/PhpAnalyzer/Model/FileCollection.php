<?php

/*
 * Copyright 2013 Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Scrutinizer\PhpAnalyzer\Model;

use Scrutinizer\PhpAnalyzer\Exception\MaxMemoryExceededException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\Iterator\ExcludeDirectoryFilterIterator;
use Symfony\Component\Finder\Iterator\FilenameFilterIterator;
use Symfony\Component\Finder\SplFileInfo;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("ALL")
 * @Serializer\XmlRoot("analyzer")
 */
class FileCollection implements \IteratorAggregate, \Countable
{
    /**
     * @Serializer\Expose
     * @Serializer\XmlList(inline=true, entry="file")
     */
    private $files = array();
    private $size = 0;

    public static function createFromContent($fileName, $content)
    {
        return new self(array(File::create($fileName, $content)));
    }

    /**
     * @param string      $zipFile
     * @param string|null $namePattern
     *
     * @return FileCollection
     */
    public static function createFromZipFile($zipFile, $namePattern = null, array $filter = array())
    {
        if ( ! is_file($zipFile)) {
            throw new \InvalidArgumentException('The zip file "%s" does not exist.');
        }

        $iterator = new ZipFileIterator($zipFile);
        $iterator = new ExcludeDirectoryFilterIterator($iterator, array('.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg'));

        if (null !== $namePattern) {
            $iterator = new FilenameFilterIterator($iterator, array($namePattern), array());
        }

        $paths = isset($filter['paths']) ? (array) $filter['paths'] : array();
        $excludedPaths = isset($filter['excluded_paths']) ? (array) $filter['excluded_paths'] : array();
        if ($paths || $excludedPaths) {
            $iterator = new \CallbackFilterIterator($iterator, self::createFilterCallback($paths, $excludedPaths));
        }

        return self::createFromTraversable($iterator);
    }

    /**
     * @param string      $dir
     * @param string|null $namePattern
     */
    public static function createFromDirectory($dir, $namePattern = null, array $filter = array())
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }

        $dir = realpath($dir);
        $finder = Finder::create()->in($dir)->ignoreVCS(true)->files();
        if (null !== $namePattern) {
            $finder->name($namePattern);
        }

        $paths = isset($filter['paths']) ? (array) $filter['paths'] : array();
        $excludedPaths = isset($filter['excluded_paths']) ? (array) $filter['excluded_paths'] : array();
        if ($paths || $excludedPaths) {
            $finder->filter(self::createFilterCallback($paths, $excludedPaths));
        }

        return self::createFromTraversable($finder, $dir.'/');
    }

    private static function createFilterCallback(array $paths, array $excludedPaths)
    {
        return function(SplFileInfo $file) use ($paths, $excludedPaths) {
            $relPathname = $file->getRelativePathname();

            if ($paths) {
                $matches = false;
                foreach ($paths as $path) {
                    if ( ! fnmatch($path, $relPathname)) {
                        continue;
                    }

                    $matches = true;
                    break;
                }

                if (! $matches) {
                    return false;
                }
            }

            foreach ($excludedPaths as $path) {
                if (fnmatch($path, $relPathname)) {
                    return false;
                }
            }

            return true;
        };
    }

    public static function createFromTraversable(\Traversable $traversable, $basePath = '')
    {
        $prefixLength = strlen($basePath);
        $col = new self();

        $i = 0;
        $memoryThreshold = self::getMemoryThreshold();
        foreach ($traversable as $file) {
            assert($file instanceof SplFileInfo);
            $col->add(File::create(substr($file->getRealPath(), $prefixLength), $file->getContents()));

            if ($i % 50 === 0 && $memoryThreshold < $memoryUsage = memory_get_usage()) {
                throw MaxMemoryExceededException::create($memoryUsage, $memoryThreshold);
            }

            $i += 1;
        }

        if ($memoryThreshold < $memoryUsage = memory_get_usage()) {
            throw MaxMemoryExceededException::create($memoryUsage, $memoryThreshold);
        }

        return $col;
    }

    /**
     * Determines the maximum allowed memory usage.
     *
     * The return value depends on the underlying system. We try to determine
     * the maximum value in such a way that the analysis is just using RAM
     * without going to SWAP as this would severely slow down everything.
     *
     * TODO: We probably need a better place for this.
     */
    public static function getMemoryThreshold()
    {
        if (0 === stripos(PHP_OS, 'win')) {
            return 1024 * 1024 * 1024 * 1.8; // 1.8 GB
        }

        exec('free -m', $output, $returnVar);
        if (0 === $returnVar) {
            if (preg_match('#buffers/cache:\s+[^\s]+\s+([^\s]+)#', implode("\n", $output), $match)) {
                // Values are in mega bytes.
                list(, $freeMemory) = $match;

                // Allow it to use all free memory, but at least 512M.
                return max(1024 * 1024 * $freeMemory, 1024 * 1024 * 512);
            }
        }

        // If we cannot infer something about the free memory, then we
        // gonna use 3 GB as this is fulfilled by all machines this is
        // running on at the moment.
        return 1024 * 1024 * 1024 * 3; // 3 GB
    }

    public function __construct(array $initialFiles = array())
    {
        foreach ($initialFiles as $file) {
            $this->add($file);
        }
    }

    public function getSize()
    {
        return $this->size;
    }

    public function add(File $file)
    {
        if (isset($this->files[$name = $file->getName()])) {
            throw new \InvalidArgumentException(sprintf('There already exists a file named "%s".', $name));
        }

        $this->files[$name] = $file;
        $this->size += strlen($file->getContent());
    }

    public function all()
    {
        return $this->files;
    }

    public function count()
    {
        return count($this->files);
    }

    public function get($name)
    {
        if (!isset($this->files[$name])) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $name));
        }

        return $this->files[$name];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }

    /**
     * Get a FileCollection with just the commented files.
     *
     * @return FileCollection
     */
    public function getCommentedFiles()
    {
        $commentedFiles = array();

        foreach ($this->files as $file) {
            if ($file->hasComments()) {
                $commentedFiles[] = $file;
            }
        }

        return new FileCollection($commentedFiles);
    }

    /**
     * Merge FileCollection with another file collection and return a new one.
     *
     * @param FileCollection $other
     *
     * @return FileCollection
     */
    public function merge(FileCollection $other)
    {
        return new FileCollection(array_merge($this->files, $other->files));
    }

    /**
     * Filter files in the collection by expression and return new file collection.
     *
     * @param string $expression
     *
     * @return FileCollection
     */
    public function filter($expression)
    {
        $filteredFiles = array();

        foreach ($this->files as $file) {
            if (strpos($file->getName(), $expression) !== false) {
                $filteredFiles[] = $file;
            }
        }

        return new FileCollection($filteredFiles);
    }
}

class ZipFileIterator implements \Iterator
{
    private $file;
    private $archive;
    private $entry;
    private $key;

    public function __construct($file)
    {
        if ( ! is_file($file)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $file));
        }

        $this->file = $file;
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return false !== $this->entry;
    }

    public function current()
    {
        return new CompressedZipFile($this->entry);
    }

    public function next()
    {
        while (false !== $this->entry = zip_read($this->archive)) {
            if ('/' !== substr(zip_entry_name($this->entry), -1)) {
                break;
            }
        }

        $this->key += 1;
    }

    public function rewind()
    {
        if (null !== $this->archive) {
            zip_close($this->archive);
            $this->archive = null;
        }

        if ( ! is_resource($archive = zip_open($this->file))) {
            throw new \RuntimeException('Could not open zip file.');
        }
        $this->archive = $archive;
        $this->key = -1;

        $this->next();
    }

    public function isDir()
    {
        return false;
    }

    public function getSubPath()
    {
        return dirname(zip_entry_name($this->entry));
    }

    public function getFilename()
    {
        return basename(zip_entry_name($this->entry));
    }
}

class CompressedZipFile extends SplFileInfo
{
    private $entry;
    private $name;

    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->name = zip_entry_name($entry);
    }

    public function getATime()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getBasename($suffix = null)
    {
        return basename($this->name, $suffix);
    }

    public function getCTime()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getExtension()
    {
        if ( ! preg_match('#\.([^\.]+)$#', $this->name, $match)) {
            return null;
        }

        return $match[1];
    }

    public function getFileInfo($className = null)
    {
        throw new \LogicException('Not implemented.');
    }

    public function getFilename()
    {
        return basename($this->name);
    }

    public function getGroup()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getINode()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getLinkTarget()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getMTime()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getOwner()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getPath()
    {
        return dirname($this->name);
    }

    public function getPathInfo($className = null)
    {
        throw new \LogicException('Not implemented.');
    }

    public function getPathname()
    {
        return $this->name;
    }

    public function getPerms()
    {
        throw new \LogicException('Not implemented.');
    }

    public function getRealPath()
    {
        return $this->name;
    }

    public function getSize()
    {
        return zip_entry_filesize($this->entry);
    }

    public function getType()
    {
        throw new \LogicException('Not implemented.');
    }

    public function isDir()
    {
        return false;
    }

    public function isExecutable()
    {
        return false;
    }

    public function isFile()
    {
        return true;
    }

    public function isLink()
    {
        return false;
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return false;
    }

    /**
     * @param boolean $useIncludePath
     */
    public function openFile($openMode = 'r', $useIncludePath = false, $context = null)
    {
        throw new \LogicException('Not implemented.');
    }

    public function setFileClass($className = null)
    {
        throw new \LogicException('Not implemented.');
    }

    public function setInfoClass($className = null)
    {
        throw new \LogicException('Not implemented.');
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getRelativePath()
    {
        return $this->getPath();
    }

    public function getRelativePathname()
    {
        return $this->getPathname();
    }

    public function getContents()
    {
        return zip_entry_read($this->entry, zip_entry_filesize($this->entry));
    }
}
