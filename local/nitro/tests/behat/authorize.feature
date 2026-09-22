@local @local_nitro
Feature: Teachers approve an AI client on the consent screen
  In order to connect an AI assistant to Moodle without copying a token
  As a teacher
  I need to log in normally and approve the client

  Background:
    Given the following config values are set as admin:
      | active        | 1                                                   | local_nitro |
      | consentnotice | This is a sandbox: do not enter real student data. | local_nitro |
    And the following "local_nitro > clients" exist:
      | name    | clientid | redirecturis              |
      | Test AI | testai   | http://localhost/callback |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Anna      | Kovács   |
      | student1 | Béla      | Nagy     |
    And the following "roles" exist:
      | shortname | name       | archetype |
      | nitrouser | nitro user |           |
    And the following "role capability" exists:
      | role            | nitrouser |
      | local/nitro:use | allow     |
    And the following "role assigns" exist:
      | user     | role      | contextlevel | reference |
      | teacher1 | nitrouser | System       |           |

  Scenario: A teacher who is not logged in logs in, sees who asks, and approves
    When I visit "/local/nitro/oauth/authorize.php?response_type=code&client_id=testai&redirect_uri=http%3A%2F%2Flocalhost%2Fcallback&code_challenge=E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM&code_challenge_method=S256&state=xyz"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    And I press "Log in"
    Then I should see "Test AI wants to access your Moodle account"
    And I should see "you return to localhost"
    And I should see "This application runs on your own computer"
    And I should see "This is a sandbox: do not enter real student data."
    And I should see "read your course content, participants, submissions and grades"
    And I should see "Whatever Test AI reads here goes to the company that runs this AI"
    And I press "Allow"
    And the url should match "^http://localhost/callback\?code=[A-Za-z0-9_-]{43}&state=xyz&iss="

  Scenario: The statement on where the data goes does not depend on the admin notice
    Given the following config values are set as admin:
      | consentnotice |  | local_nitro |
    And I log in as "teacher1"
    When I visit "/local/nitro/oauth/authorize.php?response_type=code&client_id=testai&redirect_uri=http%3A%2F%2Flocalhost%2Fcallback&code_challenge=E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM&code_challenge_method=S256&state=xyz"
    Then I should see "Whatever Test AI reads here goes to the company that runs this AI"
    And I should see "may stay in the history of your conversation there, outside Moodle"
    And I should see "sent only after you approve a preview"
    And I should not see "This is a sandbox"

  Scenario: A teacher denies the request
    Given I log in as "teacher1"
    When I visit "/local/nitro/oauth/authorize.php?response_type=code&client_id=testai&redirect_uri=http%3A%2F%2Flocalhost%2Fcallback&code_challenge=E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM&code_challenge_method=S256&state=xyz"
    And I press "Deny"
    Then the url should match "^http://localhost/callback\?error=access_denied&state=xyz"

  Scenario: A user without AI access gets an explanation and no code
    Given I log in as "student1"
    When I visit "/local/nitro/oauth/authorize.php?response_type=code&client_id=testai&redirect_uri=http%3A%2F%2Flocalhost%2Fcallback&code_challenge=E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM&code_challenge_method=S256&state=xyz"
    Then I should see "AI access is not enabled for you"
    And I should not see "Allow"

  Scenario: An unknown client is refused without redirecting
    Given I log in as "teacher1"
    When I visit "/local/nitro/oauth/authorize.php?response_type=code&client_id=nope&redirect_uri=http%3A%2F%2Flocalhost%2Fcallback&code_challenge=E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM&code_challenge_method=S256"
    Then I should see "This site does not know the AI client"
    And the url should match "/local/nitro/oauth/authorize\.php"
