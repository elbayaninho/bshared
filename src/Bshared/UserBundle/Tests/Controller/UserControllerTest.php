<?php

namespace Bshared\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr'));
        $crawler = $client->click($crawler->filter('.new_entry a')->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'utilisateur[username]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[usernameCanonical]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[email]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[emailCanonical]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[enabled]' => true,
            'utilisateur[salt]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[password]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[lastLogin]' => new \DateTime(),
            'utilisateur[locked]' => true,
            'utilisateur[expired]' => true,
            'utilisateur[expiresAt]' => new \DateTime(),
            'utilisateur[confirmationToken]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[passwordRequestedAt]' => new \DateTime(),
            'utilisateur[roles]' => 'Lorem ipsum dolor sit amet',
            'utilisateur[credentialsExpired]' => true,
            'utilisateur[credentialsExpireAt]' => new \DateTime(),
            'utilisateur[loginCount]' => 42,
            'utilisateur[firstLogin]' => new \DateTime(),
                    ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->filter('.record_actions a')->link());
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr'));
    }

    public function testCreateError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/new');
        $form = $crawler->filter('form button[type="submit"]')->form();
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('form div.has-error')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @depends testCreate
     */
    public function testEdit()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr:contains("First value")'));
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr:contains("Changed")'));
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(1)->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'utilisateur[username]' => 'Changed',
            'utilisateur[usernameCanonical]' => 'Changed',
            'utilisateur[email]' => 'Changed',
            'utilisateur[emailCanonical]' => 'Changed',
            'utilisateur[enabled]' => true,
            'utilisateur[salt]' => 'Changed',
            'utilisateur[password]' => 'Changed',
            'utilisateur[lastLogin]' => new \DateTime(),
            'utilisateur[locked]' => true,
            'utilisateur[expired]' => true,
            'utilisateur[expiresAt]' => new \DateTime(),
            'utilisateur[confirmationToken]' => 'Changed',
            'utilisateur[passwordRequestedAt]' => new \DateTime(),
            'utilisateur[roles]' => 'Changed',
            'utilisateur[credentialsExpired]' => true,
            'utilisateur[credentialsExpireAt]' => new \DateTime(),
            'utilisateur[loginCount]' => 42,
            'utilisateur[firstLogin]' => new \DateTime(),
            // ... adapt fields value here ...
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->click($crawler->filter('.record_actions a')->link());
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr:contains("First value")'));
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr:contains("Changed")'));
    }

    /**
     * @depends testCreate
     */
    public function testEditError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(1)->link());
        $form = $crawler->filter('form button[type="submit"]')->form(array(
            'utilisateur[field_name]' => '',
            // ... use a required field here ...
        ));
        $crawler = $client->submit($form);
        $this->assertGreaterThan(0, $crawler->filter('form div.has-error')->count());
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.records_list tbody tr'));
        $crawler = $client->click($crawler->filter('table.records_list tbody tr td .btn-group a')->eq(0)->link());
        $client->submit($crawler->filter('form#delete button[type="submit"]')->form());
        $crawler = $client->followRedirect();
        $this->assertCount(0, $crawler->filter('table.records_list tbody tr'));
    }

    /**
     * @depends testCreate
     */
    public function testFilter()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');
        $form = $crawler->filter('div#filter form button[type="submit"]')->form(array(
            'utilisateur_filter[username]' => 'First%',
            'utilisateur_filter[usernameCanonical]' => 'First%',
            'utilisateur_filter[email]' => 'First%',
            'utilisateur_filter[emailCanonical]' => 'First%',
            'utilisateur_filter[enabled]' => true,
            'utilisateur_filter[salt]' => 'First%',
            'utilisateur_filter[password]' => 'First%',
            'utilisateur_filter[lastLogin]' => new \DateTime(),
            'utilisateur_filter[locked]' => true,
            'utilisateur_filter[expired]' => true,
            'utilisateur_filter[expiresAt]' => new \DateTime(),
            'utilisateur_filter[confirmationToken]' => 'First%',
            'utilisateur_filter[passwordRequestedAt]' => new \DateTime(),
            'utilisateur_filter[roles]' => 'First%',
            'utilisateur_filter[credentialsExpired]' => true,
            'utilisateur_filter[credentialsExpireAt]' => new \DateTime(),
            'utilisateur_filter[loginCount]' => 42,
            'utilisateur_filter[firstLogin]' => new \DateTime(),
            // ... maybe use just one field here ...
        ));
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $crawler = $client->click($crawler->filter('div#filter a')->link());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
    }    /**
     * @depends testCreate
     */
    public function testSort()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/utilisateurs/');
        $this->assertCount(1, $crawler->filter('table.records_list th')->eq(0)->filter('a i.fa-sort'));
        $crawler = $client->click($crawler->filter('table.records_list th a')->link());
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.records_list th a i.fa-sort-up'));
    }
}
