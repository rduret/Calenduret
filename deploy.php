<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:rduret/advent-calendars.git');

add('shared_files', ['.env.local']);
add('shared_dirs', ['var/log', 'public/files']);
add('writable_dirs', ['var/log', 'public/files']);

// Hosts
host('production')
    ->set('hostname', '62.72.16.63')
    ->set('remote_user', 'rduret')
    ->set('deploy_path', '~/htdocs/calenduret.com');

// Hooks
task('build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
});

after('deploy:update_code', 'build');
after('deploy:vendors', 'database:migrate');
after('deploy:failed', 'deploy:unlock');
