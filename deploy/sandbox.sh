#!/bin/sh
# Deploys local_nitro and local_nitrosandbox to the sandbox (moodle.tilosazai.org, host sbc).
#
# The sandbox image has Moodle's code baked in, so the plugins are bind-mounted read-only into the web
# and cron containers from /data/moodle/plugins, together with the root OAuth discovery rewrite.
# Run from the repository root:  sh deploy/sandbox.sh [--first]
#   --first  also adds the bind mounts to the compose file, builds the template course and sets the
#            sandbox settings (consent notice, onboarding, self-registration); run once.
set -eu
HOST=${NITRO_HOST:-sbc}
WRAPPER=/data/moodle/wrapper
PLUGINS=/data/moodle/plugins

ssh "$HOST" "mkdir -p $PLUGINS"
rsync -a --delete --exclude tests/ local/nitro/ "$HOST:$PLUGINS/nitro/"
rsync -a --delete --exclude tests/ local/nitrosandbox/ "$HOST:$PLUGINS/nitrosandbox/"
scp -q deploy/htaccess "$HOST:$PLUGINS/htaccess"

if [ "${1:-}" = "--first" ]; then
  ssh "$HOST" "cd $WRAPPER && cp docker-compose.yml docker-compose.yml.pre-nitro-\$(date +%Y%m%d-%H%M) && python3 - <<'PY'
p = 'docker-compose.yml'; s = open(p).read()
mounts = ('      - /data/moodle/plugins/nitro:/var/www/html/public/local/nitro:ro\n'
          '      - /data/moodle/plugins/nitrosandbox:/var/www/html/public/local/nitrosandbox:ro\n'
          '      - /data/moodle/plugins/htaccess:/var/www/html/public/.htaccess:ro\n')
anchor = '      - ./config.php:/var/www/html/config.php:ro\n'
if 'local/nitro' not in s:
    s = s.replace(anchor, anchor + mounts)
open(p, 'w').write(s)
PY
docker compose up -d web cron"
fi

ssh "$HOST" "cd $WRAPPER && docker compose exec -T web php /var/www/html/admin/cli/upgrade.php --non-interactive | tail -1 \
  && docker compose exec -T web apache2ctl -k graceful"

if [ "${1:-}" = "--first" ]; then
  ssh "$HOST" "cd $WRAPPER && docker compose exec -T web sh -c '
    cfg=\"php /var/www/html/admin/cli/cfg.php\"
    \$cfg --component=local_nitro --name=active --set=1
    \$cfg --component=local_nitro --name=consentnotice --set=\"Ez egy gyakorlóoldal: valódi hallgatói adatot ne vigyen fel. / This is a sandbox: do not enter real student data.\"
    php /var/www/html/public/local/nitrosandbox/cli/build_template.php
    # Only accounts created from now on get a demo course; existing users are left alone.
    \$cfg --component=local_nitrosandbox --name=onboardfrom --set=\$(date +%s)
    \$cfg --component=local_nitrosandbox --name=enabled --set=1
  '"
fi

# Discovery from outside, through Cloudflare.
for p in /local/nitro/oauth/metadata.php/.well-known/openid-configuration \
         /.well-known/oauth-protected-resource/local/nitro/mcp.php \
         /.well-known/oauth-authorization-server/local/nitro/oauth/metadata.php; do
  printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code}' "https://moodle.tilosazai.org$p")" "$p"
done
curl -s -o /dev/null -w '%{http_code} POST mcp.php without token (expect 401)\n' -X POST https://moodle.tilosazai.org/local/nitro/mcp.php
