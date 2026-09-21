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

namespace local_nitro\oauth;

/**
 * URLs and discovery documents of nitro's OAuth authorization server and MCP resource.
 *
 * The issuer is the metadata script itself, so the issuer-path-appended discovery form
 * `<issuer>/.well-known/openid-configuration` is served by the plugin without web server changes.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadata {
    /** @var string[] Scopes nitro knows. */
    public const SCOPES = ['nitro', 'offline_access'];

    /**
     * The MCP endpoint URL, which is also the OAuth resource identifier.
     *
     * @return string
     */
    public static function resource(): string {
        global $CFG;
        return $CFG->wwwroot . '/local/nitro/mcp.php';
    }

    /**
     * The authorization server's issuer identifier.
     *
     * @return string
     */
    public static function issuer(): string {
        global $CFG;
        return $CFG->wwwroot . '/local/nitro/oauth/metadata.php';
    }

    /**
     * URL of the protected resource metadata document, as sent in the 401 challenge.
     *
     * @return string
     */
    public static function resource_metadata_url(): string {
        return self::issuer() . '/.well-known/oauth-protected-resource';
    }

    /**
     * URL of one of the OAuth endpoint scripts.
     *
     * @param string $name authorize, token or register
     * @return string
     */
    public static function endpoint(string $name): string {
        global $CFG;
        return $CFG->wwwroot . '/local/nitro/oauth/' . $name . '.php';
    }

    /**
     * Protected resource metadata (RFC 9728) for the MCP endpoint.
     *
     * @return array
     */
    public static function protected_resource(): array {
        return [
            'resource' => self::resource(),
            'authorization_servers' => [self::issuer()],
            'scopes_supported' => self::SCOPES,
            'bearer_methods_supported' => ['header'],
            'resource_name' => 'nitro',
        ];
    }

    /**
     * Authorization server metadata (RFC 8414).
     *
     * It is also served as OpenID discovery, so it carries the OpenID fields a strict client may
     * check (`subject_types_supported`), without claiming ID token support.
     *
     * @return array
     */
    public static function authorization_server(): array {
        $data = [
            'issuer' => self::issuer(),
            'authorization_endpoint' => self::endpoint('authorize'),
            'token_endpoint' => self::endpoint('token'),
        ];
        if (get_config('local_nitro', 'dcrenabled')) {
            $data['registration_endpoint'] = self::endpoint('register');
        }
        return $data + [
            'scopes_supported' => self::SCOPES,
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'code_challenge_methods_supported' => ['S256'],
            'token_endpoint_auth_methods_supported' => ['none', 'client_secret_post', 'client_secret_basic'],
            'client_id_metadata_document_supported' => true,
            'authorization_response_iss_parameter_supported' => true,
            'subject_types_supported' => ['public'],
        ];
    }

    /**
     * Which document a request path asks for.
     *
     * The root rewrite and the 401 pointer send `.../oauth-protected-resource[/...]`; every other
     * form (bare, `/.well-known/openid-configuration`, `/.well-known/oauth-authorization-server`)
     * gets the authorization server metadata.
     *
     * @param string $path the path after the script name
     * @return array the document
     */
    public static function for_path(string $path): array {
        if (str_contains($path, 'oauth-protected-resource')) {
            return self::protected_resource();
        }
        return self::authorization_server();
    }
}
