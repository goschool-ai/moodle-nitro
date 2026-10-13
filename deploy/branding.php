<?php
// GoSchool branding and the sandbox notices for moodle.goschool.ai. Run inside the web container:
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
    'fullname' => 'GoSchool Nitro – gyakorlóoldal',
    'shortname' => 'GoSchool Nitro',
    'summary' => '<p>Ez a GoSchool <strong>Nitro</strong> gyakorlóoldala: kipróbálhatja, milyen a Moodle-kurzusát '
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
// The login and signup notice: HTML (FORMAT_MOODLE does not render markdown), one language at a time
// through the multilang filter.
filter_set_global_state('multilang', TEXTFILTER_ON);
set_config('auth_instructions',
    '<span lang="hu" class="multilang"><strong>Új itt?</strong> Kattints az <em>Új fiók</em> gombra. A regisztráció '
    . 'után e-mailben kapsz egy megerősítő linket: <strong>erre rá kell kattintani</strong>, különben a belépés nem '
    . 'működik (nézd meg a spam mappát is). Belépés után egy percen belül megjelenik a saját próbakurzusod, '
    . 'kitalált hallgatókkal. Valódi hallgatói adatot ne vigyél fel.</span>'
    . '<span lang="en" class="multilang"><strong>New here?</strong> Click <em>Create new account</em>. Moodle then '
    . 'emails you a confirmation link: <strong>you have to click it</strong> before you can log in (check your spam '
    . 'folder too). After you log in, your own trial course with fictitious students appears within a minute. Do '
    . 'not add real student data.</span>');
purge_all_caches();
cli_writeln('Branding applied.');
