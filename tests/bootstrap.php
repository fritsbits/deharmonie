<?php

// Load the Composer autoloader (may be symlinked in worktrees)
$loader = require __DIR__ . '/../vendor/autoload.php';

// Ensure App\ namespace resolves to this project's app/ directory,
// not to a symlink-resolved parent project. Safe no-op when not in a worktree.
$loader->setPsr4('App\\', [realpath(__DIR__ . '/../app')]);

// Application::inferBasePath() falls back to the Composer ClassLoader's registered
// path, which follows the symlink and points to the parent project. Override it here
// so Laravel bootstraps from this worktree's root instead.
$worktreeRoot = realpath(__DIR__ . '/..');
$_ENV['APP_BASE_PATH'] = $worktreeRoot;
$_SERVER['APP_BASE_PATH'] = $worktreeRoot;
