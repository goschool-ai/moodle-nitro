@local @local_nitro
Feature: Admins register OAuth clients by hand
  In order to connect an AI client on a site where dynamic registration is off
  As an admin
  I need to register, list and delete OAuth clients

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Local plugins > nitro > OAuth clients" in site administration

  Scenario: Register a confidential client, see its secret once, then delete it
    When I set the following fields to these values:
      | Name          | Copilot at the faculty                                      |
      | Redirect URIs | https://teams.microsoft.com/api/platform/v1.0/oAuthRedirect |
      | Confidential  | 1                                                           |
    And I press "Register"
    Then I should see "The client \"Copilot at the faculty\" was registered"
    And I should see "Client secret"
    And I should see "Copilot at the faculty" in the "local_nitro_clients" "table"
    And I should see "By an admin" in the "local_nitro_clients" "table"
    And I reload the page
    And I should not see "Client secret"
    And I click on "Delete" "link" in the "Copilot at the faculty" "table_row"
    And I press "Continue"
    And I should see "The client \"Copilot at the faculty\" was deleted."
    And I should see "No clients are registered."

  Scenario: A redirect URI outside the allowlist is refused
    When I set the following fields to these values:
      | Name          | Somebody               |
      | Redirect URIs | https://evil.example/cb |
    And I press "Register"
    Then I should see "This site does not allow the redirect URI https://evil.example/cb"
    And I should see "No clients are registered."
