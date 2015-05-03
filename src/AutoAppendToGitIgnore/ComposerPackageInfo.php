<?php namespace Weaves81\AutoAppendToGitIgnore;

use \Composer\Composer as Composer;

class ComposerPackageInfo
{
    /**
     * Path to current projects root directory
     * @var string
     */
    protected $baseDir = "";

    /**
     * @var \Composer\Composer
     */
    protected $composer = "";

    /**
     * @var Composer\Repository\RepositoryManager
     */
    protected $repositoryManager = "";

    /**
     * @var Composer\Installer\InstallationManager
     */
    protected $installationManager = "";

    /**
     * @param          $gitignore_modules
     * @param Composer $composer
     */
    public function __construct($gitignore_modules, Composer $composer)
    {
        $this->composer = $composer;
        $this->repositoryManager = $this->composer->getRepositoryManager();
        $this->installationManager = $composer->getInstallationManager();
        $this->baseDir = $this->NormalizePath(getcwd());
        $this->requiredTypes = $this->setGitignoreRequiredTypes($gitignore_modules);
    }

    /**
     * @return array
     */
    public function GetModules()
    {
        $packages = array();

        foreach ($this->repositoryManager->getLocalRepository()->getPackages() as $package) {

            if ($this->isPackage($packages, $package)) {

                if ($this->isPackageTypeListed($package)) {

                    $packages = $this->addToPackagesArray($package, $packages);
                }
            }
        }

        return $packages;
    }

    /**
     * @param $path
     *
     * @return string
     */
    private function NormalizePath($path)
    {
        $search  = array('\\', '\\\\', '//', $this->baseDir);
        $replace = array('/', '/', '/', '');

        return trim(str_replace($search, $replace, $path), '/');
    }

    /**
     * @param $gitignore_modules
     *
     * @return string
     */
    private function setGitignoreRequiredTypes($gitignore_modules)
    {
        $ignore_modules = '';
        $end            = end($gitignore_modules);

        foreach ($gitignore_modules as $module) {
            $ignore_modules .= $module;

            if ($module !== $end) {
                $ignore_modules .= ", ";
            }
        }

        return $ignore_modules;
    }

    /**
     * @param $packages
     * @param $package
     *
     * @return bool
     */
    private function isPackage($packages, $package)
    {
        return ! isset($packages[$package->getName()]) ||
               ! is_object($packages[$package->getName()]) ||
               version_compare($packages[$package->getName()]->getVersion(), $package->getVersion(), '<');
    }

    /**
     * @param $package
     *
     * @return bool
     */
    private function isPackageTypeListed($package)
    {
        return in_array($package->getType(), $this->requiredTypes);
    }

    /**
     * @param $package
     * @param $packages
     *
     * @return mixed
     */
    private function addToPackagesArray($package, $packages)
    {
        $packagePath = $this->NormalizePath($this->installationManager->getInstallPath($package));
        $packages[$package->getName()]["info"] = $package;
        $packages[$package->getName()]["path"] = $packagePath;

        return $packages;
    }
}
