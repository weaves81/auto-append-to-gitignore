<?php namespace Weaves81\AutoAppendToGitIgnore;

use Composer\Script\Event;

class PostUpdateScript
{
    /**
     * @param Event $event
     *
     * @throws AutoGitIgnoreInvalidParameterException
     * @throws AutoGitIgnoreSaveFailedException
     */
    public static function Run(Event $event)
    {
        $package_array = array();

        $extras = $event->getComposer()->getPackage()->getExtra();
        $gitignore_extra = $extras['git-ignore'];

        $composerPackageInfo = new ComposerPackageInfo($gitignore_extra, $event->getComposer());
        $git_ignore_editor = new EditGitIgnoreFile(getcwd() . $gitignore_extra['path']);

        $event->getIO()->writeError('<info>Generating .gitignore: </info>', false);

        foreach ($composerPackageInfo->GetModules() as $value) {
            $path = str_replace($gitignore_extra['path'], '', $value["path"]);

            $package_array[] = "/" . $path . "/";
        }
        sort($package_array);

        $git_ignore_editor->createGitIgnoreLines($package_array);
        $git_ignore_editor->save();

        $event->getIO()
            ->writeError('<info>Complete - added ' . count($package_array) . ' packages to git ignore file</info>');
    }
}
