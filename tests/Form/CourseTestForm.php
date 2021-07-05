<?php


namespace App\Tests\Form;

use Symfony\Component\Form\Test\TypeTestCase;
use App\Form\CourseType;
use App\Tests\AbstractTest;

class CourseTestForm extends AbstractTest
{
    public function testNotFound(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', "http://study-on.local:81/coursexdfh/");

        AbstractTest::assertResponseNotFound();

    }
}