<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Hungarian strings for local_nitrosandbox.
 *
 * @package    local_nitrosandbox
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['category'] = 'A bemutató kurzusok kategóriája';
$string['category_desc'] = 'Annak a kurzuskategóriának az azonosítója, amelyben a személyes bemutató kurzusok létrejönnek.';
$string['enabled'] = 'Bemutató kurzus regisztrációkor';
$string['enabled_desc'] = 'Amikor egy megerősített felhasználó először bejelentkezik, létrejön a személyes bemutató kurzusa kitalált hallgatókkal. Csak a gyakorlóoldalon.';
$string['onboardfrom'] = 'Csak az ezután létrehozott fiókok';
$string['onboardfrom_desc'] = 'Unix időbélyeg: csak az ennél később létrehozott fiókok kapnak bemutató kurzust. Állítsa arra a pillanatra, amikor a regisztrációs folyamatot bekapcsolja, így az oldal meglévő felhasználói érintetlenek maradnak. A 0 minden fiókot jelent.';
$string['pluginname'] = 'nitro gyakorlóoldal – regisztráció';
$string['privacy:metadata:preference:courseid'] = 'A felhasználónak létrehozott személyes bemutató kurzus azonosítója.';
$string['restorefailed'] = 'A bemutató kurzus sablonját nem sikerült visszaállítani: {$a}';
$string['students'] = 'Kitalált hallgatók száma bemutató kurzusonként';
$string['template'] = 'Bemutató kurzus sablonja';
$string['template_desc'] = 'Minden új bemutató kurzushoz ez a mentés (.mbz, felhasználói adatok nélkül) kerül visszaállításra. A local/nitrosandbox/cli/build_template.php készíti el.';
$string['templatemissing'] = 'Nincs bemutató kurzus sablon. Előbb futtassa a local/nitrosandbox/cli/build_template.php parancsot.';
$string['usergone'] = 'A fiók már nem létezik, ezért nem jött létre bemutató kurzus.';
