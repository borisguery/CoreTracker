<?php
namespace Bgy\CoreTracker;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class CoreTracker
{
    /**
     * The decorated autoloader.
     * @var object
     */
    protected $autoloader;
    /**
     * The core file name, where the results should be dumped.
     * @var string
     */
    protected $coreFileName;
    /**
     * The core file resource.
     * @var resource
     */
    protected $coreFile;
    /**
     * The current core layout.
     * @var array
     */
    protected $core;
    /**
     * The current scan.
     * @var array
     */
    protected $currentScan = array();

    /**
     * @var array
     */
    protected $collectedData = array();
    /**
     * Whether we are generating the initial core or reducing the possibilities?
     * @var integer
     */
    protected $mode;

    public function __construct($autoloader, $coreFile)
    {
        if (!is_object($autoloader) || !method_exists($autoloader, 'loadClass')) {
            throw new DomainException('The first argument must be an autoloader object with \'loadClass\' method.');
        }
        $this->autoloader = $autoloader;

        $this->coreFileName = (string) $coreFile;

        if (!file_exists($this->coreFileName)) {
            touch($this->coreFileName);
        }
        $this->coreFile = fopen($this->coreFileName, 'r+');

        $content = '';

        while (!feof($this->coreFile)) {
            $content .= fread($this->coreFile, 2048);
        }

        $this->core = @unserialize($content);

        if (empty($content)) {
            $this->mode = 0;
        } else {
            $this->mode = 1;
        }

        if (is_array($this->core) && array_key_exists('collectedData', $this->core)) {
            $this->collectedData = $this->core['collectedData'];
        }

        $this->currentScan = array();
    }

    /**
     * Returns the decorated autoloader.
     *
     * @return object
     */
    public function getAutoloader()
    {
        return $this->autoloader;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    public function loadClass($class)
    {
        $result = $this->autoloader->loadClass($class);
        $this->currentScan[] = $class;
        $calls = 0;
        if (isset($this->collectedData[sha1($class)])) {
            $calls = $this->collectedData[sha1($class)]['calls'];
        }
        $this->collectedData[sha1($class)] = array('className' => $class, 'calls' => ++$calls);

        return $result;
    }

    public function __destruct()
    {
        ftruncate($this->coreFile, 0);
        rewind($this->coreFile);

        if (0 === $this->mode) {
            fwrite(
                $this->coreFile,
                serialize(array(
                    'dump'          => $this->currentScan,
                    'collectedData' => $this->collectedData,
                ))
            );
        } else {
            fwrite(
                $this->coreFile,
                serialize(
                    array(
                        'dump' => array_intersect($this->core['dump'], $this->currentScan),
                        'collectedData' => $this->collectedData,
                    )
                )
            );
        }

        fclose($this->coreFile);
    }
}
