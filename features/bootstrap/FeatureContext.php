<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use PaulGibbs\WordpressBehatExtension\Context\RawWordpressContext;

/**
 * Define application features from the specific context.
 */
class FeatureContext extends RawWordpressContext implements SnippetAcceptingContext
{
    /**
     * Initialise context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @Given I am on the login page
     */
    public function iAmOnTheLoginPage()
    {
        $this->visitPath('wp-login.php');
    }

    /**
     * @When I enter :username as my username and :password as my password
     */
    public function iEnterMyUsernameAndMyPassword($username, $password)
    {
        $page = $this->getSession()->getPage();

        $loginField = $page->findField('user_login');
        $loginField->setValue($username);

        $passwordField = $page->findField('user_pass');
        $passwordField->setValue($password);
    }

    /**
     * @Then I should see a login error saying :error
     */
    public function iShouldSeeALoginErrorSaying($error)
    {
        $this->assertSession()->elementTextContains('css', '#login_error', $error);
    }
}
