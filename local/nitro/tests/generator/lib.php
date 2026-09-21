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
 * Test data generator for local_nitro.
 *
 * @package    local_nitro
 * @category   test
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_nitro_generator extends component_generator_base {
    /**
     * Registers an OAuth client.
     *
     * @param array|stdClass $data name, redirecturis (array or newline-separated), optional clientid,
     *      authmethod (default none), origin (default admin)
     * @return array{0: \local_nitro\oauth\client, 1: ?string} the client and its plain secret
     */
    public function create_client($data): array {
        global $DB;
        $data = (array) $data;
        $uris = $data['redirecturis'] ?? ['http://localhost/callback'];
        if (is_string($uris)) {
            $uris = array_filter(array_map('trim', preg_split('/[\n,]/', $uris)));
        }
        [$client, $secret] = \local_nitro\oauth\clients::register(
            $data['origin'] ?? 'admin',
            $data['name'] ?? 'Test client',
            array_values($uris),
            $data['authmethod'] ?? 'none'
        );
        if (!empty($data['clientid'])) {
            $DB->set_field('local_nitro_client', 'clientid', $data['clientid'], ['clientid' => $client->clientid]);
            $client = \local_nitro\oauth\clients::find($data['clientid']);
        }
        return [$client, $secret];
    }

    /**
     * Creates a grant (a user's approval of a client) with an access token.
     *
     * @param array|stdClass $data userid, clientid, clientname, redirecthost, optional scope
     * @return array{0: stdClass, 1: string} the grant record and its plain access token
     */
    public function create_grant($data): array {
        global $DB;
        $data = (array) $data;
        $grant = (object) [
            'userid' => $data['userid'],
            'clientid' => $data['clientid'] ?? 'https://claude.ai/oauth/mcp-oauth-client-metadata',
            'clientname' => $data['clientname'] ?? 'Claude',
            'redirecthost' => $data['redirecthost'] ?? 'claude.ai',
            'scope' => $data['scope'] ?? 'nitro offline_access',
            'timelastused' => null,
            'timecreated' => time(),
        ];
        $grant->id = $DB->insert_record('local_nitro_grant', $grant);
        $token = \local_nitro\oauth\clients::random_token();
        $DB->insert_record('local_nitro_token', ['grantid' => $grant->id, 'tokentype' => 'access',
            'tokenhash' => hash('sha256', $token), 'used' => 0, 'expires' => time() + HOURSECS, 'timecreated' => time()]);
        return [$grant, $token];
    }
}
