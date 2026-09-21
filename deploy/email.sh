#!/bin/sh
# Outbound email for the sandbox through Resend, and email self-registration.
#
# The API key is read from /data/moodle/.resend on the host (RESEND_KEY=...), never from the repository.
# It must belong to the Resend team that has the sender's domain verified (goschool.ai).
#   sh deploy/email.sh [sender-address] [test-recipient]
set -eu
HOST=sbc
WRAPPER=/data/moodle/wrapper
FROM=${1:-info@goschool.ai}
TEST=${2:-}

ssh "$HOST" "set -eu; . /data/moodle/.resend
cd $WRAPPER
# Moodle must be allowed to send at all: drop the noemailever line (a backup is kept).
if grep -q 'noemailever' config.php; then
  cp config.php config.php.pre-email-\$(date +%Y%m%d-%H%M)
  sed -i '/noemailever/d' config.php
  # sed writes a new file, and the containers bind-mount the old inode: recreate them.
  docker compose up -d --force-recreate web cron >/dev/null
fi
docker compose exec -T -u www-data -e KEY=\"\$RESEND_KEY\" -e FROM='$FROM' web sh -c '
  cfg=\"php /var/www/html/admin/cli/cfg.php\"
  # noemailever is also kept in the database, not only in config.php.
  \$cfg --name=noemailever --set=0
  \$cfg --name=smtphosts --set=smtp.resend.com:587
  \$cfg --name=smtpsecure --set=tls
  \$cfg --name=smtpuser --set=resend
  \$cfg --name=smtppass --set=\"\$KEY\"
  \$cfg --name=smtpmaxbulk --set=10
  \$cfg --name=noreplyaddress --set=\"\$FROM\"
  \$cfg --name=supportemail --set=\"\$FROM\"
  \$cfg --name=registerauth --set=email
  \$cfg --name=authloginviaemail --set=1
  php /var/www/html/admin/cli/cfg.php --name=auth --set=email,manual,nologin
  php /var/www/html/admin/cli/purge_caches.php
'"

if [ -n "$TEST" ]; then
  ssh "$HOST" "cd $WRAPPER && docker compose exec -T -u www-data web php -r '
    define(\"CLI_SCRIPT\", true); require(\"/var/www/html/config.php\");
    \$user = core_user::get_user_by_email(\"$TEST\") ?: (object) [\"id\" => -99, \"email\" => \"$TEST\",
        \"firstname\" => \"nitro\", \"lastname\" => \"test\", \"username\" => \"nitrotest\", \"auth\" => \"manual\",
        \"suspended\" => 0, \"deleted\" => 0, \"mailformat\" => 1, \"maildisplay\" => 1, \"firstnamephonetic\" => \"\",
        \"lastnamephonetic\" => \"\", \"middlename\" => \"\", \"alternatename\" => \"\"];
    var_dump(email_to_user(\$user, core_user::get_support_user(), \"nitro sandbox test\",
        \"Ez a GoSchool nitro gyakorlooldal teszt levele.\"));
  '"
fi
