<?php
namespace Bgy\CoreTracker;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
use Bgy\CoreTracker\Storage\StorageInterface;

class CoreTracker
{
    /**
     * The decorated autoloader.
     * @var object
     */
    protected $autoloader;

    protected $coreDump;

    /**
     * @var StorageInterface
     */
    protected $coreStorage;

    public function __construct($autoloader, StorageInterface $coreStorage)
    {
        if (!is_object($autoloader) || !method_exists($autoloader, 'loadClass')) {
            throw new DomainException('The first argument must be an autoloader object with \'loadClass\' method.');
        }
        $this->autoloader = $autoloader;

        $this->coreStorage = $coreStorage;

        $this->coreDump = $this->coreStorage->fetch();
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
        if ($result) {
            $this->coreDump[$class]->calls++;
        }

        return $result;
    }

    public function __destruct()
    {
        $this->coreStorage->store($this->coreDump);
    }
}
