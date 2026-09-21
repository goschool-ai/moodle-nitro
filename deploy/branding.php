<?php
// GoSchool branding and the sandbox notices for moodle.tilosazai.org. Run inside the web container:
//   php /var/www/html/public/../branding.php --assets=/tmp/branding
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/course/lib.php');

[$options] = cli_get_params(['assets' => '/tmp/branding']);
\core\session\manager::set_user(get_admin());
$fs = get_file_storage();
$context = context_system::instance();

// Site name: the site course cannot go through update_course().
$DB->update_record('course', (object) [
    'id' => SITEID,
    'fullname' => 'GoSchool nitro – gyakorlóoldal',
    'shortname' => 'GoSchool nitro',
    'summary' => '<p>Ez a GoSchool <strong>nitro</strong> gyakorlóoldala: kipróbálhatja, milyen a Moodle-kurzusát '
        . 'AI-asszisztensből (Claude) vezetni. Kitalált hallgatókkal dolgozik; valódi hallgatói adatot ne vigyen fel.</p>',
    'summaryformat' => FORMAT_HTML,
    'timemodified' => time(),
]);
rebuild_course_cache(SITEID, true);

// Logos and favicon: core_admin file areas.
foreach (['logo' => 'logo-color.png', 'logocompact' => 'favicon.svg', 'favicon' => 'favicon.svg'] as $area => $name) {
    $path = $options['assets'] . '/' . $name;
    if (!file_exists($path)) {
        cli_problem("missing {$path}");
        continue;
    }
    $fs->delete_area_files($context->id, 'core_admin', $area);
    $fs->create_file_from_pathname([
        'contextid' => $context->id, 'component' => 'core_admin', 'filearea' => $area,
        'itemid' => 0, 'filepath' => '/', 'filename' => $name,
    ], $path);
    set_config($area, '/' . $name, 'core_admin');
    cli_writeln("{$area}: {$name}");
}

// Boost brand colour (Electric Scholar Purple) and the notices.
set_config('brandcolor', '#5926eb', 'theme_boost');
set_config('auth_instructions', 'Ez a GoSchool nitro gyakorlóoldala. Kitalált hallgatókkal dolgozik: '
    . '**valódi hallgatói adatot ne vigyen fel.** Regisztráció után néhány másodpercen belül megkapja a saját '
    . 'bemutató kurzusát. / This is the GoSchool nitro sandbox: fictitious students only, no real student data.');
purge_all_caches();
cli_writeln('Branding applied.');
