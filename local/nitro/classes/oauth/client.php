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
 * An OAuth client as the authorization and token endpoints see it, however it registered.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class client {
    /**
     * Constructor.
     *
     * @param string $clientid client ID, or the CIMD URL
     * @param string $name name declared by the client or given by the admin
     * @param string[] $redirecturis registered redirect URIs, already filtered by the allowlist
     * @param string $authmethod none, client_secret_post or client_secret_basic
     * @param string $origin cimd, dcr or admin
     * @param string|null $secrethash SHA-256 of the client secret, for confidential clients
     */
    public function __construct(
        /** @var string client ID, or the CIMD URL */
        public readonly string $clientid,
        /** @var string name declared by the client or given by the admin */
        public readonly string $name,
        /** @var string[] registered redirect URIs, already filtered by the allowlist */
        public readonly array $redirecturis,
        /** @var string none, client_secret_post or client_secret_basic */
        public readonly string $authmethod,
        /** @var string cimd, dcr or admin */
        public readonly string $origin,
        /** @var string|null SHA-256 of the client secret, for confidential clients */
        public readonly ?string $secrethash = null,
    ) {
    }

    /**
     * Whether the client must authenticate at the token endpoint.
     *
     * @return bool
     */
    public function is_confidential(): bool {
        return $this->authmethod !== 'none';
    }

    /**
     * Whether a redirect URI presented in a request is one of this client's.
     *
     * @param string $uri
     * @return bool
     */
    public function has_redirect_uri(string $uri): bool {
        foreach ($this->redirecturis as $registered) {
            if (redirect_uris::matches($uri, $registered)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Whether every redirect URI of the client is a loopback address.
     *
     * @return bool
     */
    public function is_loopback_only(): bool {
        return $this->redirecturis && count(array_filter(
            $this->redirecturis,
            [redirect_uris::class, 'is_loopback']
        )) === count($this->redirecturis);
    }
}
