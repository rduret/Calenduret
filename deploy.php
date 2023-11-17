<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:rduret/advent-calendars.git');
set('keep_releases', 5);

add('shared_files', []);
add('shared_dirs', ['public/files']);
add('writable_dirs', []);

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
