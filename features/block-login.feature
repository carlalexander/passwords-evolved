Feature: Block login for compromised passwords
    In order to login to the WordPress admin
    As a WordPress administrator
    I need to use a password that hasn't been compromised

    Scenario: Cannot login using a compromised password as an administrator
        Given I am on the login page
        When I enter "admin" as my username and "password" as my password
        And I press "Log In"
        Then I should see a login error saying "This password has appeared in a published data breach on another site and cannot be used to log in. Please reset your password to log back in."
