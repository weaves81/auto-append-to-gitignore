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
     * @param array    $gitignore_extra
     * @param Composer $composer
     */
    public function __construct(Array $gitignore_extra, Composer $composer)
    {
        $this->composer            = $composer;
        $this->repositoryManager   = $this->composer->getRepositoryManager();
        $this->installationManager = $composer->getInstallationManager();
        $this->gitignore_path      = $gitignore_extra['path'];
        $this->baseDir             = $this->normalizePath(getcwd(), '');
        $this->requiredTypes       = $gitignore_extra['modules'];
    }

    /**
     * @return array
     */
    public function getModules()
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
     * @param $folder
     *
     * @return string
     */
    private function normalizePath($path, $folder)
    {
        $search  = array('\\', '\\\\', '//', $this->baseDir);
        $replace = array('/', '/', '/', '');
        $path    = str_replace($search, $replace, $path);

        $updated_path = str_replace('%' . $folder . '%', '', $path);

        return trim($updated_path, '/');
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
        $packagePath                           = $this->normalizePath(
            $this->installationManager->getInstallPath($package),
            $this->gitignore_path
        );
        $packages[$package->getName()]["info"] = $package;
        $packages[$package->getName()]["path"] = $packagePath;

        return $packages;
    }
}
