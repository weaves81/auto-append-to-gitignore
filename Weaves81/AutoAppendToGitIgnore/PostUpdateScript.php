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
        $GIT_IGNORE_PATH = getenv('GIT_IGNORE_PATH');
        $package_array = array();

        $composerPackageInfo = new ComposerPackageInfo($event->getComposer());
        $git_ignore_editor = new EditGitIgnoreFile(getcwd() . $GIT_IGNORE_PATH);

        $event->getIO()->writeError('<info>Generating .gitignore: </info>', false);

        foreach ($composerPackageInfo->GetModules() as $value) {
            $package_array[] = "/" . $value["path"] . "/";
        }

        sort($package_array);

        $git_ignore_editor->createGitIgnoreLines($package_array);

        $git_ignore_editor->save();

        $event->getIO()
            ->writeError('<info>Complete - added ' . count($package_array) . ' packages to git ignore file</info>');
    }
}
