<?php

namespace App\Tests;

use App\Security\User;


class UserTest extends AbstractTest
{

    public function testRegisterOk(): void
    {
        $client = AbstractTest::getClient();

        $crawler = $client->request('POST', '/register');


        $form = $crawler->filter('form')->form();

        $email = "dani@gmail.com";
        $pass = "123456";

        $form->setValues(array(
            "register[email]" => $email,
            "register[password][first]" => $pass,
            "register[password][second]" => $pass,
            "register[agreeTerms]" => true,
        ));

        $crawler = $client->submit($form);

        $this->assertResponseRedirect();

        $crawler = $client->followRedirect();
        $this->assertSame('http://localhost/course/', $crawler->getUri());
    }

    public function testLoginOk(): void
    {
        $client = AbstractTest::getClient();
        $this->doAuth($client, "user@user.com", "123456");

        $this->assertResponseRedirect();

        $crawler = $client->followRedirect();
        $this->assertSame('http://localhost/course/', $crawler->getUri());
    }
}
