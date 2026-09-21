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
 * English strings for local_nitro.
 *
 * @package    local_nitro
 * @copyright  2026 GoSchool
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['autherror_disabled'] = 'AI access through nitro is switched off on this site.';
$string['autherror_missingclient'] = 'The AI client sent an incomplete request (client ID or redirect URI missing). Try connecting again from the AI client.';
$string['autherror_redirecturi'] = 'The AI client asked to return to an address this site does not allow. Nothing was shared. Ask your Moodle administrator if you believe this client should be allowed.';
$string['autherror_unknownclient'] = 'This site does not know the AI client that sent you here. Nothing was shared. Try connecting again from the AI client, or ask your Moodle administrator.';
$string['cachedef_cimd'] = 'Client metadata documents of AI clients';
$string['cimdhosts'] = 'Client metadata document hosts';
$string['cimdhosts_desc'] = 'Hosts, one per line, from which AI clients may identify themselves with a client metadata document URL (for example Claude). nitro fetches client metadata only from these hosts.';
$string['clientadd'] = 'Register a client';
$string['clientconfidential'] = 'Confidential';
$string['clientconfidential_label'] = 'Issue a client secret (needed by Microsoft 365 Copilot)';
$string['clientdelete'] = 'Delete {$a}';
$string['clientdeleteconfirm'] = 'Delete the client "{$a}"? Every connection made through it stops working immediately.';
$string['clientdeleted'] = 'The client "{$a}" was deleted.';
$string['clientfirstused'] = 'First used';
$string['clientid'] = 'Client ID';
$string['clientname'] = 'Name';
$string['clientorigin'] = 'Registered';
$string['clientorigin_admin'] = 'By an admin';
$string['clientorigin_dcr'] = 'Dynamically';
$string['clientredirectnotallowed'] = 'This site does not allow the redirect URI {$a}. Add it to the allowed redirect URIs in the nitro settings first.';
$string['clientredirecturis'] = 'Redirect URIs';
$string['clientredirecturis_help'] = 'One per line, each from the allowed redirect URIs in the nitro settings.';
$string['clientregister'] = 'Register';
$string['clientregistered'] = 'The client "{$a}" was registered. Enter these values in the AI client:';
$string['clients'] = 'OAuth clients';
$string['clientsecret'] = 'Client secret';
$string['clientsecretonce'] = 'Copy the secret now: it is stored only as a hash and cannot be shown again.';
$string['clientsnone'] = 'No clients are registered. Claude connects without registering; dynamically registered clients appear here.';
$string['confirmchanged'] = 'Nothing was done: the arguments differ from the preview the teacher approved. Call the tool again without confirmation_token to get a new preview, and ask the teacher to approve it.';
$string['confirmexpired'] = 'Nothing was done: the confirmation expired (previews are valid for 10 minutes). Call the tool again without confirmation_token to get a new preview, and ask the teacher to approve it.';
$string['confirmunknown'] = 'Nothing was done: this confirmation token is not valid. Call the tool again without confirmation_token to get a new preview, and ask the teacher to approve it.';
$string['confirmused'] = 'Nothing was done again: this confirmation token was already used, and the action already happened. To do it once more, get a new preview and a new approval.';
$string['connectionapproved'] = 'Approved';
$string['connectionclient'] = 'AI tool';
$string['connectionlastused'] = 'Last used';
$string['connectionreturnsto'] = 'Returns to';
$string['connectionrevoke'] = 'Disconnect';
$string['connectionrevokeconfirm'] = 'Disconnect {$a}? It stops working immediately, and it has to ask for your approval again to reconnect.';
$string['connectionrevoked'] = '{$a} was disconnected.';
$string['connections'] = 'Connected AI tools';
$string['connectionsintro'] = 'These AI tools can act as you in Moodle through nitro. Disconnect any you no longer use or do not recognise.';
$string['connectionsnone'] = 'No AI tools are connected to your account.';
$string['consentapprove'] = 'Allow';
$string['consentconfirm'] = 'Messages, announcements and grades are sent only after you approve a preview.';
$string['consentdeny'] = 'Deny';
$string['consentexplain'] = 'If you allow it, the application can act as you ({$a}) in the courses where AI access is enabled for you. It can do exactly what you can do in Moodle, no more:';
$string['consentheading'] = '{$a} wants to access your Moodle account';
$string['consentloopback'] = 'This application runs on your own computer. Only allow it if you started the connection yourself just now.';
$string['consentnotice'] = 'Consent screen notice';
$string['consentnotice_desc'] = 'Shown on the consent screen above the approve button, for example "This is a sandbox: do not enter real student data." Leave empty for no notice.';
$string['consentoffline'] = 'It stays connected until you disconnect it, so you do not have to log in again every hour.';
$string['consentread'] = 'read your courses, participants and submissions;';
$string['consentredirect'] = 'After you decide, you return to {$a}.';
$string['consentrevoke'] = 'You can disconnect it at any time on your profile, under Connected AI tools.';
$string['consentselfregistered'] = 'This application registered itself with this site, so its name is not verified. Only allow it if you started the connection yourself just now, from the application you expect.';
$string['consenttitle'] = 'Connect an AI assistant';
$string['consentwrite'] = 'create and update pages, assignments, questions and quizzes;';
$string['dcrcap'] = 'Limit of never-used clients';
$string['dcrcap_desc'] = 'When this many dynamically registered clients have never obtained a token, new registrations are refused until the number drops. Unused clients are deleted 7 days after registration.';
$string['dcrcapreached'] = '{$a} dynamically registered clients have never obtained a token, which reaches the limit: new registrations are refused until unused clients are cleaned up.';
$string['dcrenabled'] = 'Dynamic client registration';
$string['dcrenabled_desc'] = 'Let AI clients register themselves (needed by Microsoft 365 Copilot). When off, only clients registered by an admin and clients with a metadata document can connect.';
$string['enabled'] = 'Enable nitro';
$string['enabled_desc'] = 'When off, the MCP endpoint and the OAuth endpoints refuse every request and existing tokens stop working.';
$string['eventtoolcalled'] = 'AI tool called';
$string['feedbackemail'] = 'Feedback address';
$string['feedbackemail_desc'] = 'Where feedback about nitro is sent. Change it to your own address to keep it inside the institution.';
$string['feedbackenabled'] = 'Let teachers send feedback';
$string['feedbackenabled_desc'] = 'Gives the AI a tool that mails feedback about nitro to the address below. The teacher always sees the exact message first and has to approve it. The message carries only that text, the site name and URL and the version numbers, never course or student data.';
$string['feedbackheading'] = 'Feedback about nitro';
$string['feedbackmailfailed'] = 'Could not mail the feedback to {$a}. Moodle will try again.';
$string['feedbacknoaddress'] = 'No valid feedback address is configured on this site. Tell the teacher to write to their Moodle administrator instead.';
$string['feedbackoff'] = 'Feedback is switched off on this site. Tell the teacher to write to their Moodle administrator instead.';
$string['feedbackqueued'] = 'The mail server could not be reached just now. The report has been saved and Moodle keeps trying to send it, so it is not lost.';
$string['importfailed'] = 'Nothing was imported: {$a} Fix the questions and import again; the import is all or nothing.';
$string['nitro:use'] = 'Use Moodle through an AI assistant (nitro)';
$string['notenoughquestions'] = 'Nothing was added: the category "{$a->category}" holds {$a->available} questions, but {$a->requested} random questions were requested. Ask for at most {$a->available}, or import more questions first.';
$string['notpermitted'] = 'AI access through nitro is not enabled for your account. If you would like to use it, ask your Moodle administrator to enable it for your courses.';
$string['notpermittedheading'] = 'AI access is not enabled for you';
$string['oauthheading'] = 'Connecting AI clients';
$string['pluginname'] = 'nitro';
$string['privacy:metadata:client'] = 'OAuth clients registered on the site.';
$string['privacy:metadata:client:createdby'] = 'The admin who registered the client by hand.';
$string['privacy:metadata:code'] = 'Short-lived authorization codes issued when the user approves an AI client.';
$string['privacy:metadata:code:userid'] = 'The user who approved the client.';
$string['privacy:metadata:confirm'] = 'Pending confirmations of messages, announcements and grades the user previewed through an AI client.';
$string['privacy:metadata:confirm:tool'] = 'The tool that was previewed.';
$string['privacy:metadata:confirm:userid'] = 'The user who previewed the action.';
$string['privacy:metadata:grant'] = 'The AI clients the user approved (connected AI tools).';
$string['privacy:metadata:grant:clientid'] = 'The identifier of the AI client.';
$string['privacy:metadata:grant:clientname'] = 'The name of the AI client.';
$string['privacy:metadata:grant:redirecthost'] = 'Where the AI client returns after approval.';
$string['privacy:metadata:grant:scope'] = 'What the user allowed the client to do.';
$string['privacy:metadata:grant:timecreated'] = 'When the user approved it.';
$string['privacy:metadata:grant:timelastused'] = 'When the client last used the connection.';
$string['privacy:metadata:grant:userid'] = 'The user who approved the client.';
$string['privacy:metadata:token'] = 'Access and refresh tokens of a connection, stored only as hashes.';
$string['privacy:metadata:token:expires'] = 'When the token expires.';
$string['privacy:metadata:token:grantid'] = 'The connection the token belongs to.';
$string['quizattempted'] = 'Nothing was added: students have already attempted this quiz, so its questions are locked, as in the Moodle web interface. Create a new quiz, or ask the teacher to delete the attempts in Moodle first.';
$string['quizneedsmoodle5'] = 'The quiz tools need Moodle 5.0 or later; this site runs Moodle {$a}. Nothing was changed.';
$string['redirecturis'] = 'Allowed redirect URIs';
$string['redirecturis_desc'] = 'One per line. Every client, however it registers, may only use redirect URIs from this list. Loopback addresses (localhost, 127.0.0.1) match any port.';
$string['selfcheck'] = 'Discovery check';
$string['selfcheck_issuer'] = 'Authorization server metadata, served by the plugin';
$string['selfcheck_issuer_root'] = 'Authorization server metadata at the site root';
$string['selfcheck_resource'] = 'Protected resource metadata, served by the plugin';
$string['selfcheck_resource_root'] = 'Protected resource metadata at the site root';
$string['selfcheckinactive'] = 'Switch nitro on and save to run the discovery check.';
$string['selfcheckmissing'] = 'Not answering';
$string['selfcheckok'] = 'Working';
$string['selfcheckpluginbroken'] = 'The plugin\'s own discovery URLs do not answer correctly, so AI clients cannot connect. Check that slash arguments work on this site (Moodle needs them for file serving too) and that nothing in front of Moodle blocks these URLs.';
$string['selfcheckrootoptional'] = 'The site-root discovery URLs do not answer. This is optional: AI clients that follow the pointer in the 401 response connect without it. Some clients look only at the site root; if one of them cannot connect, ask the web server team to add one of these rules.';
$string['selfcheckrule_apache'] = 'Apache (virtual host configuration)';
$string['selfcheckrule_htaccess'] = 'Apache (.htaccess in the Moodle web root)';
$string['selfcheckrule_nginx'] = 'nginx (server block)';
$string['settings'] = 'Settings';
$string['taskcleanup'] = 'Delete unused AI clients and expired tokens';
$string['tasksendfeedbackmail'] = 'Send feedback about nitro';
$string['tools'] = 'Allowed tools';
$string['tools_desc'] = 'Only these tools are listed to AI clients and can be called.';
