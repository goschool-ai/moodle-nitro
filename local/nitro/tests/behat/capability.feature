@local @local_nitro
Feature: The nitro access gate is a normal Moodle capability
  In order to decide who may use Moodle through an AI assistant
  As an admin
  I need to grant local/nitro:use through role management, and no role has it by default

  Scenario: The capability is listed in role definitions and not allowed by default
    Given I log in as "admin"
    When I navigate to "Users > Permissions > Define roles" in site administration
    And I follow "Teacher"
    Then I should not see "local/nitro:use"
    And I press "Edit"
    And I should see "local/nitro:use"
    And the field "local/nitro:use" matches value ""
