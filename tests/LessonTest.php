<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Lesson;
use App\Entity\Course;

class LessonTest extends AbstractTest
{
   public function testShow(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "user@user.com", "123456");
        $client->followRedirect();

        $em = AbstractTest::getEntityManager();
        $lessons = $em->getRepository(Lesson::class)->findAll();
        foreach ($lessons as $lesson) {
            $id = $lesson->getId();
            $crawler = $client->request('GET', "/lesson/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testEditGet(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $em = AbstractTest::getEntityManager();
        $lessons = $em->getRepository(Lesson::class)->findAll();
        foreach ($lessons as $lesson) {
            $id = $lesson->getId();
            $crawler = $client->request('GET', "/lesson/edit/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testNewGet(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $id = $course->getId();
            $crawler = $client->request('GET', "/lesson/new/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testFormNewError(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('??????????????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink('???????????????? ????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "P",
            "lesson[content]"  => "P",
            "lesson[number]" => "sg",
        ));

        $crawler = $client->submit($form);

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('???????????????? ???????????? ???????? ???????????? 5 ????????????????', $messege);

        $messege = $crawler->filter('li')->eq(1)->text();
        $this->assertSame('???????????????? ???????????? ???????? ???????????? 10 ????????????????', $messege);

        $messege = $crawler->filter('li')->eq(2)->text();
        $this->assertSame('This value is not valid.', $messege);

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $this->assertEquals($countLesson, $countLessonNew);


    }

    public function testFormNewOk(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('??????????????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $urlExpected = $crawler->getUri();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink('???????????????? ????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "?????????? ????????",
            "lesson[content]"  => "?????????? ??????????????",
            "lesson[number]" => "1",
        ));

        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame($urlExpected, $crawler->getUri());

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));
        $this->assertEquals($countLesson + 1, $countLessonNew);

        $lessons = $em->getRepository(Lesson::class)->findBy(['course' => $idCourse], ['id' => 'DESC'], 1);
        $lesson = array_shift($lessons);
        $this->assertSame("?????????? ????????", $lesson->getName());
        $this->assertSame("?????????? ??????????????", $lesson->getContent());
        $this->assertEquals(1, $lesson->getNumber());
    }

    public function testFormDeleteOk(): void
    {
        $em = AbstractTest::getEntityManager();
        $countCourse = count($em->getRepository(Course::class)->findAll());

        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('??????????????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $urlExpected = $crawler->getUri();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink("???????????? HTML")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $form = $crawler->filter('form')->form();


        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame($urlExpected, $crawler->getUri());

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $this->assertEquals($countLesson - 1, $countLessonNew);
    }

    public function testFormEditOk(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('??????????????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $urlExpected = $crawler->getUri();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $link = $crawler->selectLink("???????????? HTML")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idLesson = $segments[4];

        $link = $crawler->selectLink("???????????????? ????????")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "?????????????????????? ????????",
            "lesson[content]"  => "?????????????????????? ??????????????",
            "lesson[number]" => "1",
        ));

        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame($urlExpected, $crawler->getUri());

        $em = AbstractTest::getEntityManager();
        $lesson = $em->getRepository(Lesson::class)->find($idLesson);
        $this->assertSame("?????????????????????? ????????", $lesson->getName());
        $this->assertSame("?????????????????????? ??????????????", $lesson->getContent());
        $this->assertSame(1, $lesson->getNumber());
    }

    public function testFormEditError(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('??????????????????')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink("???????????? HTML")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idLesson = $segments[4];

        $link = $crawler->selectLink("???????????????? ????????")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "??",
            "lesson[content]"  => "??",
            "lesson[number]" => "sdh",
        ));

        $crawler = $client->submit($form);

        $this->assertResponseOk();

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('???????????????? ???????????? ???????? ???????????? 5 ????????????????', $messege);

        $messege = $crawler->filter('li')->eq(1)->text();
        $this->assertSame('???????????????? ???????????? ???????? ???????????? 10 ????????????????', $messege);

        $messege = $crawler->filter('li')->eq(2)->text();
        $this->assertSame('This value is not valid.', $messege);

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $this->assertEquals($countLesson, $countLessonNew);

    }
}
