@local @local_nitro
Feature: Users see and disconnect their connected AI tools
  In order to stay in control of what can act as me
  As a teacher
  I need to see the AI tools I approved and disconnect them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Anna      | Kovács   |
      | teacher2 | Csaba     | Tóth     |
    And the following "local_nitro > grants" exist:
      | user     | clientname  | clientid | redirecthost       |
      | teacher1 | Claude      | c1       | claude.ai          |
      | teacher2 | Other tool  | c2       | teams.microsoft.com |

  Scenario: A teacher disconnects Claude from their profile
    Given I log in as "teacher1"
    And I follow "Profile" in the user menu
    When I follow "Connected AI tools"
    Then I should see "Claude" in the "local_nitro_connections" "table"
    And I should see "claude.ai" in the "local_nitro_connections" "table"
    And I should see "Never" in the "local_nitro_connections" "table"
    And I should not see "Other tool"
    And I press "Disconnect"
    And I press "Disconnect"
    And I should see "Claude was disconnected."
    And I should see "No AI tools are connected to your account."
