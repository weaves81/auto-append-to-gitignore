<?php namespace Weaves81\AutoAppendToGitIgnore;

class AutoGitIgnoreSaveFailedException extends \Exception
{
    public function __construct()
    {
        parent::__construct();
    }
}
