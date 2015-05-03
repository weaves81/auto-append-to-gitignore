# AutoAppendGitIgnore
Composer post-update-cmd script to automatically add Composer managed modules and themes to .gitignore

## Install instructions
### Add it to your project with:
`composer require weaves81/auto-append-to-gitigore`
### Add the following to your composer.json
Amend GIT_IGNORE_PATH and GIT_IGNORE_MODULES as required
```
"scripts": {
     "post-update-cmd": GIT_IGNORE_PATH="/wp-content/.gitignore"; GIT_IGNORE_MODULES='"wordpress-plugin", "wordpress-theme", "wordpress-muplugin"'; "Weaves81\\AutoAppendToGitIgnore\\PostUpdateScript::Run"
}
```
Originally based on https://github.com/guru-digital/SSAutoGitIgnore