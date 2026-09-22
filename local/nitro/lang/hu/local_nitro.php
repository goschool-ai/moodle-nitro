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
 * Hungarian strings for local_nitro.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['autherror_disabled'] = 'Ezen az oldalon a nitrón keresztüli AI-hozzáférés ki van kapcsolva.';
$string['autherror_missingclient'] = 'Az AI-kliens hiányos kérést küldött (hiányzik a kliensazonosító vagy az átirányítási URI). Próbáljon újra csatlakozni az AI-kliensből.';
$string['autherror_redirecturi'] = 'Az AI-kliens olyan címre kért visszairányítást, amelyet ez az oldal nem engedélyez. Semmilyen adat nem került megosztásra. Ha úgy gondolja, hogy ezt a klienst engedélyezni kellene, forduljon a Moodle rendszergazdájához.';
$string['autherror_unknownclient'] = 'Ez az oldal nem ismeri az AI-klienst, amely ide küldte. Semmilyen adat nem került megosztásra. Próbáljon újra csatlakozni az AI-kliensből, vagy forduljon a Moodle rendszergazdájához.';
$string['cachedef_cimd'] = 'AI-kliensek kliensleíró dokumentumai';
$string['cimdhosts'] = 'Kliensleíró dokumentumok hosztjai';
$string['cimdhosts_desc'] = 'Soronként egy hoszt, ahonnan az AI-kliensek kliensleíró dokumentum URL-jével azonosíthatják magukat (például a Claude). A nitro csak ezekről a hosztokról tölt le kliensadatokat.';
$string['clientadd'] = 'Kliens regisztrálása';
$string['clientconfidential'] = 'Bizalmas';
$string['clientconfidential_label'] = 'Klienstitok kiadása (a Microsoft 365 Copilotnak szüksége van rá)';
$string['clientdelete'] = '{$a} törlése';
$string['clientdeleteconfirm'] = 'Törli a(z) „{$a}” klienst? Minden rajta keresztül létrejött kapcsolat azonnal megszűnik.';
$string['clientdeleted'] = 'A(z) „{$a}” kliens törölve.';
$string['clientfirstused'] = 'Első használat';
$string['clientid'] = 'Kliensazonosító';
$string['clientname'] = 'Név';
$string['clientorigin'] = 'Regisztráció';
$string['clientorigin_admin'] = 'Rendszergazda által';
$string['clientorigin_dcr'] = 'Dinamikusan';
$string['clientredirectnotallowed'] = 'Ez az oldal nem engedélyezi a(z) {$a} átirányítási URI-t. Előbb vegye fel az engedélyezett átirányítási URI-k közé a nitro beállításaiban.';
$string['clientredirecturis'] = 'Átirányítási URI-k';
$string['clientredirecturis_help'] = 'Soronként egy; mindegyiknek szerepelnie kell a nitro beállításaiban megadott engedélyezett átirányítási URI-k között.';
$string['clientregister'] = 'Regisztrálás';
$string['clientregistered'] = 'A(z) „{$a}” kliens regisztrálva. Ezeket az adatokat adja meg az AI-kliensben:';
$string['clients'] = 'OAuth-kliensek';
$string['clientsecret'] = 'Klienstitok';
$string['clientsecretonce'] = 'Most másolja ki a titkot: csak hash-ként tároljuk, később nem lehet újra megjeleníteni.';
$string['clientsnone'] = 'Nincs regisztrált kliens. A Claude regisztráció nélkül csatlakozik; a dinamikusan regisztrált kliensek itt jelennek meg.';
$string['confirmchanged'] = 'Nem történt semmi: az argumentumok eltérnek a tanár által jóváhagyott előnézettől. Hívja meg újra az eszközt confirmation_token nélkül egy új előnézetért, és kérje a tanár jóváhagyását.';
$string['confirmexpired'] = 'Nem történt semmi: a megerősítés lejárt (az előnézet 10 percig érvényes). Hívja meg újra az eszközt confirmation_token nélkül egy új előnézetért, és kérje a tanár jóváhagyását.';
$string['confirmunknown'] = 'Nem történt semmi: ez a megerősítő token érvénytelen. Hívja meg újra az eszközt confirmation_token nélkül egy új előnézetért, és kérje a tanár jóváhagyását.';
$string['confirmused'] = 'Nem történt meg újra: ezt a megerősítő tokent már felhasználták, a művelet már megtörtént. Az ismétléshez új előnézet és új jóváhagyás kell.';
$string['connectionapproved'] = 'Jóváhagyva';
$string['connectionclient'] = 'AI-eszköz';
$string['connectionlastused'] = 'Utolsó használat';
$string['connectionreturnsto'] = 'Visszatérési cím';
$string['connectionrevoke'] = 'Leválasztás';
$string['connectionrevokeconfirm'] = 'Leválasztja ezt: {$a}? Azonnal megszűnik a hozzáférése, és az újracsatlakozáshoz ismét az Ön jóváhagyását kell kérnie.';
$string['connectionrevoked'] = '{$a} leválasztva.';
$string['connections'] = 'Csatlakoztatott AI-eszközök';
$string['connectionsintro'] = 'Ezek az AI-eszközök a nitrón keresztül Önként járhatnak el a Moodle-ben. Válassza le azokat, amelyeket már nem használ vagy nem ismer fel.';
$string['connectionsnone'] = 'Nincs AI-eszköz csatlakoztatva a fiókjához.';
$string['consentapprove'] = 'Engedélyezem';
$string['consentconfirm'] = 'Üzenet, közlemény és értékelés csak azután megy ki, hogy Ön jóváhagyta az előnézetét.';
$string['consentdeny'] = 'Elutasítom';
$string['consentexplain'] = 'Ha engedélyezi, az alkalmazás Önként ({$a}) járhat el azokban a kurzusokban, ahol az AI-hozzáférés be van kapcsolva az Ön számára. Pontosan azt teheti, amit Ön is megtehet a Moodle-ben, többet nem:';
$string['consentheading'] = 'A(z) {$a} hozzáférést kér a Moodle-fiókjához';
$string['consentloopback'] = 'Ez az alkalmazás az Ön saját számítógépén fut. Csak akkor engedélyezze, ha a csatlakozást épp most Ön indította.';
$string['consentnotice'] = 'Közlemény a hozzájárulási oldalon';
$string['consentnotice_desc'] = 'A hozzájárulási oldalon, a jóváhagyás gomb fölött jelenik meg, például: „Ez egy gyakorlóoldal: valódi hallgatói adatot ne vigyen fel.” Ha üres, nincs közlemény.';
$string['consentoffline'] = 'Addig marad csatlakoztatva, amíg le nem választja, így nem kell óránként újra bejelentkeznie.';
$string['consentprovider'] = 'Amit a(z) {$a} itt elolvas, az AI-t üzemeltető céghez kerül, és ott, a Moodle-ön kívül, a beszélgetés előzményeiben maradhat.';
$string['consentread'] = 'láthatja a kurzusai tartalmát, a résztvevőket, a beadandókat és a jegyeket;';
$string['consentredirect'] = 'A döntése után visszakerül ide: {$a}.';
$string['consentrevoke'] = 'Bármikor leválaszthatja a profiljában, a Csatlakoztatott AI-eszközök oldalon.';
$string['consentselfregistered'] = 'Ez az alkalmazás saját maga regisztrált ezen az oldalon, ezért a neve nincs ellenőrizve. Csak akkor engedélyezze, ha a csatlakozást épp most Ön indította, abból az alkalmazásból, amelyre számít.';
$string['consenttitle'] = 'AI-asszisztens csatlakoztatása';
$string['consentwrite'] = 'oldalakat, feladatokat, kérdéseket és teszteket hozhat létre és módosíthat;';
$string['dcrcap'] = 'Soha nem használt kliensek felső határa';
$string['dcrcap_desc'] = 'Ha ennyi dinamikusan regisztrált kliens még soha nem kapott tokent, az új regisztrációkat a rendszer elutasítja, amíg a szám nem csökken. A nem használt klienseket a regisztráció után 7 nappal törli.';
$string['dcrcapreached'] = '{$a} dinamikusan regisztrált kliens még soha nem kapott tokent, ez eléri a felső határt: az új regisztrációkat a rendszer elutasítja, amíg a nem használt kliensek törlése meg nem történik.';
$string['dcrenabled'] = 'Dinamikus kliensregisztráció';
$string['dcrenabled_desc'] = 'Az AI-kliensek maguk regisztrálhatnak (a Microsoft 365 Copilotnak erre szüksége van). Kikapcsolva csak a rendszergazda által regisztrált és a kliensleíró dokumentummal rendelkező kliensek csatlakozhatnak.';
$string['enabled'] = 'A nitro bekapcsolása';
$string['enabled_desc'] = 'Kikapcsolva az MCP- és az OAuth-végpontok minden kérést elutasítanak, és a kiadott tokenek nem működnek.';
$string['eventtoolcalled'] = 'AI-eszköz hívása';
$string['feedbackemail'] = 'Visszajelzés címzettje';
$string['feedbackemail_desc'] = 'Ide megy a nitróról szóló visszajelzés. Írja át a saját címére, ha az intézményen belül szeretné tartani.';
$string['feedbackenabled'] = 'A tanárok küldhetnek visszajelzést';
$string['feedbackenabled_desc'] = 'Ad az AI-nak egy eszközt, amely a nitróról szóló visszajelzést elküldi az alábbi címre. A tanár mindig előre látja a pontos szöveget, és jóvá kell hagynia. Az üzenet csak ezt a szöveget, a webhely nevét és címét, valamint a verziószámokat tartalmazza, kurzus- vagy hallgatói adatot soha.';
$string['feedbackheading'] = 'Visszajelzés a nitróról';
$string['feedbackmailfailed'] = 'A visszajelzést nem sikerült elküldeni ide: {$a}. A Moodle újra megpróbálja.';
$string['feedbacknoaddress'] = 'Ezen az oldalon nincs beállítva érvényes visszajelzési cím. Kérje meg a tanárt, hogy a Moodle-adminisztrátorának írjon.';
$string['feedbackoff'] = 'Ezen az oldalon a visszajelzés küldése ki van kapcsolva. Javasolja a tanárnak, hogy írjon a Moodle rendszergazdájának.';
$string['feedbackqueued'] = 'A levelezőkiszolgáló most nem volt elérhető. A bejelentés elmentve, a Moodle tovább próbálja elküldeni, tehát nem veszett el.';
$string['importfailed'] = 'Semmi nem került importálásra: {$a} Javítsa a kérdéseket, és importáljon újra; az importálás mindent vagy semmit elven működik.';
$string['nitro:use'] = 'A Moodle használata AI-asszisztensen keresztül (nitro)';
$string['notenoughquestions'] = 'Semmi nem került hozzáadásra: a(z) „{$a->category}” kategóriában {$a->available} kérdés van, de {$a->requested} véletlen kérdést kért. Legfeljebb {$a->available} kérdést kérjen, vagy előbb importáljon további kérdéseket.';
$string['notpermitted'] = 'Az Ön fiókjában nincs bekapcsolva a nitrón keresztüli AI-hozzáférés. Ha használni szeretné, kérje meg a Moodle rendszergazdáját, hogy kapcsolja be a kurzusaiban.';
$string['notpermittedheading'] = 'Az AI-hozzáférés nincs bekapcsolva az Ön számára';
$string['oauthheading'] = 'AI-kliensek csatlakoztatása';
$string['pluginname'] = 'nitro';
$string['privacy:metadata:aiclient'] = 'A felhasználó által csatlakoztatott AI-kliens (például Claude vagy Microsoft 365 Copilot). Amit egy eszköz beolvas, az ehhez a klienshez, és így az azt üzemeltető céghez kerül, ahol a Moodle-ön kívül, a beszélgetés előzményeiben maradhat.';
$string['privacy:metadata:aiclient:coursecontent'] = 'Az eszköz által beolvasott kurzustartalom: szakaszok, tevékenységek, oldalak és dátumaik.';
$string['privacy:metadata:aiclient:grades'] = 'Jegyek és értékelő megjegyzések.';
$string['privacy:metadata:aiclient:participants'] = 'A kurzus résztvevőinek neve, szerepköre, csoportja és utolsó kurzuslátogatása.';
$string['privacy:metadata:aiclient:submissions'] = 'A beadások állapota, és kérésre a beadott szöveg, linkek és fájlnevek.';
$string['privacy:metadata:client'] = 'A webhelyen regisztrált OAuth-kliensek.';
$string['privacy:metadata:client:createdby'] = 'A klienst kézzel regisztráló rendszergazda.';
$string['privacy:metadata:code'] = 'Rövid életű hitelesítési kódok, amelyek akkor jönnek létre, amikor a felhasználó jóváhagy egy AI-klienst.';
$string['privacy:metadata:code:userid'] = 'A klienst jóváhagyó felhasználó.';
$string['privacy:metadata:confirm'] = 'Üzenetek, közlemények és értékelések függő megerősítései, amelyeket a felhasználó AI-kliensen keresztül tekintett meg előnézetben.';
$string['privacy:metadata:confirm:tool'] = 'Az eszköz, amelynek előnézete készült.';
$string['privacy:metadata:confirm:userid'] = 'Az előnézetet megtekintő felhasználó.';
$string['privacy:metadata:feedback'] = 'A nitróról szóló, tanár által jóváhagyott visszajelzés, amely az admin által megadott címre megy e-mailben.';
$string['privacy:metadata:feedback:message'] = 'A tanár által jóváhagyott szöveg, az oldal nevével, címével és a verziószámokkal.';
$string['privacy:metadata:grant'] = 'A felhasználó által jóváhagyott AI-kliensek (csatlakoztatott AI-eszközök).';
$string['privacy:metadata:grant:clientid'] = 'Az AI-kliens azonosítója.';
$string['privacy:metadata:grant:clientname'] = 'Az AI-kliens neve.';
$string['privacy:metadata:grant:redirecthost'] = 'Ahová az AI-kliens a jóváhagyás után visszatér.';
$string['privacy:metadata:grant:scope'] = 'Amit a felhasználó engedélyezett a kliensnek.';
$string['privacy:metadata:grant:timecreated'] = 'A jóváhagyás időpontja.';
$string['privacy:metadata:grant:timelastused'] = 'Mikor használta a kliens utoljára a kapcsolatot.';
$string['privacy:metadata:grant:userid'] = 'A klienst jóváhagyó felhasználó.';
$string['privacy:metadata:token'] = 'A kapcsolat hozzáférési és frissítési tokenjei, csak hash-ként tárolva.';
$string['privacy:metadata:token:expires'] = 'A token lejárata.';
$string['privacy:metadata:token:grantid'] = 'A kapcsolat, amelyhez a token tartozik.';
$string['quizattempted'] = 'Semmi nem került hozzáadásra: a hallgatók már kitöltötték ezt a tesztet, ezért a kérdései zárolva vannak, ugyanúgy, mint a Moodle webes felületén. Hozzon létre új tesztet, vagy kérje meg a tanárt, hogy előbb törölje a kitöltéseket a Moodle-ben.';
$string['quizneedsmoodle5'] = 'A teszteszközökhöz Moodle 5.0 vagy újabb szükséges; ezen az oldalon Moodle {$a} fut. Semmi nem változott.';
$string['redirecturis'] = 'Engedélyezett átirányítási URI-k';
$string['redirecturis_desc'] = 'Soronként egy. Minden kliens, bárhogyan regisztrál, csak a listán szereplő átirányítási URI-kat használhatja. A loopback címek (localhost, 127.0.0.1) bármely porton egyeznek.';
$string['selfcheck'] = 'Felderítési ellenőrzés';
$string['selfcheck_issuer'] = 'Hitelesítési szerver metaadatai, a bővítmény szolgálja ki';
$string['selfcheck_issuer_root'] = 'Hitelesítési szerver metaadatai a webhely gyökerében';
$string['selfcheck_resource'] = 'Védett erőforrás metaadatai, a bővítmény szolgálja ki';
$string['selfcheck_resource_root'] = 'Védett erőforrás metaadatai a webhely gyökerében';
$string['selfcheckinactive'] = 'A felderítési ellenőrzéshez kapcsolja be a nitrót, és mentse a beállításokat.';
$string['selfcheckmissing'] = 'Nem válaszol';
$string['selfcheckok'] = 'Működik';
$string['selfcheckpluginbroken'] = 'A bővítmény saját felderítési URL-jei nem válaszolnak helyesen, így az AI-kliensek nem tudnak csatlakozni. Ellenőrizze, hogy a webhelyen működnek-e a perjeles argumentumok (a Moodle a fájlok kiszolgálásához is használja), és hogy a Moodle előtt semmi nem blokkolja-e ezeket az URL-eket.';
$string['selfcheckrootoptional'] = 'A webhely gyökerében lévő felderítési URL-ek nem válaszolnak. Ez nem kötelező: azok az AI-kliensek, amelyek követik a 401-es válaszban kapott hivatkozást, nélküle is csatlakoznak. Egyes kliensek csak a webhely gyökerében keresnek; ha valamelyikük nem tud csatlakozni, kérje meg a webszerver üzemeltetőit, hogy vegyék fel az alábbi szabályok egyikét.';
$string['selfcheckrule_apache'] = 'Apache (virtuális hoszt beállítása)';
$string['selfcheckrule_htaccess'] = 'Apache (.htaccess a Moodle webgyökerében)';
$string['selfcheckrule_nginx'] = 'nginx (server blokk)';
$string['settings'] = 'Beállítások';
$string['taskcleanup'] = 'Nem használt AI-kliensek és lejárt tokenek törlése';
$string['tasksendfeedbackmail'] = 'Visszajelzés küldése a nitróról';
$string['tools'] = 'Engedélyezett eszközök';
$string['tools_desc'] = 'Az AI-kliensek csak ezeket az eszközöket látják és hívhatják.';
