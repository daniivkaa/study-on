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
        $em = AbstractTest::getEntityManager();
        $lessons = $em->getRepository(Lesson::class)->findAll();
        foreach ($lessons as $lesson) {
            $id = $lesson->getId();
            $crawler = $client->request('GET', "http://study-on.local:81/lesson/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testEditGet(): void
    {
        $client = AbstractTest::getClient();
        $em = AbstractTest::getEntityManager();
        $lessons = $em->getRepository(Lesson::class)->findAll();
        foreach ($lessons as $lesson) {
            $id = $lesson->getId();
            $crawler = $client->request('GET', "http://study-on.local:81/lesson/$id/edit");

            AbstractTest::assertResponseOk();
        }
    }

    public function testNewGet(): void
    {
        $client = AbstractTest::getClient();
        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $id = $course->getId();
            $crawler = $client->request('GET', "http://study-on.local:81/lesson/new/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testFormNewError(): void
    {
        $client = AbstractTest::getClient();
        $url = 'http://study-on.local:81/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink('Добавить урок')->link();
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
        $this->assertSame('Название должно быть больше 5 символов', $messege);

        $messege = $crawler->filter('li')->eq(1)->text();
        $this->assertSame('Конотент должен быть больше 10 символов', $messege);

        $messege = $crawler->filter('li')->eq(2)->text();
        $this->assertSame('This value is not valid.', $messege);

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $this->assertEquals($countLesson, $countLessonNew);


    }

    public function testFormNewOk(): void
    {
        $client = AbstractTest::getClient();
        $url = 'http://study-on.local:81/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink('Добавить урок')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "Новый урок",
            "lesson[content]"  => "Новый контент",
            "lesson[number]" => "1",
        ));

        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame("http://study-on.local:81/course/show/$idCourse", $crawler->getUri());

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));
        $this->assertEquals($countLesson + 1, $countLessonNew);

        $lessons = $em->getRepository(Lesson::class)->findBy(['course' => $idCourse], ['id' => 'DESC'], 1);
        $lesson = array_shift($lessons);
        $this->assertSame("Новый урок", $lesson->getName());
        $this->assertSame("Новый контент", $lesson->getContent());
        $this->assertEquals(1, $lesson->getNumber());
    }

    public function testFormDeleteOk(): void
    {
        $em = AbstractTest::getEntityManager();
        $countCourse = count($em->getRepository(Course::class)->findAll());

        $client = AbstractTest::getClient();
        $url = 'http://study-on.local:81/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink("Основы HTML")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $form = $crawler->filter('form')->form();


        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame("http://study-on.local:81/course/show/$idCourse", $crawler->getUri());

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $this->assertEquals($countLesson - 1, $countLessonNew);
    }

    public function testFormEditOk(): void
    {
        $client = AbstractTest::getClient();
        $url = 'http://study-on.local:81/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $link = $crawler->selectLink("Основы HTML")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idLesson = $segments[4];

        $link = $crawler->selectLink("Обновить урок")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "Обновленный урок",
            "lesson[content]"  => "Обновленный контент",
            "lesson[number]" => "1",
        ));

        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame("http://study-on.local:81/course/show/$idCourse", $crawler->getUri());

        $em = AbstractTest::getEntityManager();
        $lesson = $em->getRepository(Lesson::class)->find($idLesson);
        $this->assertSame("Обновленный урок", $lesson->getName());
        $this->assertSame("Обновленный контент", $lesson->getContent());
        $this->assertSame(1, $lesson->getNumber());
    }

    public function testFormEditError(): void
    {
        $client = AbstractTest::getClient();
        $url = 'http://study-on.local:81/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idCourse = $segments[5];

        $em = AbstractTest::getEntityManager();
        $countLesson = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $link = $crawler->selectLink("Основы HTML")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $idLesson = $segments[4];

        $link = $crawler->selectLink("Обновить урок")->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "lesson[name]" => "О",
            "lesson[content]"  => "О",
            "lesson[number]" => "sdh",
        ));

        $crawler = $client->submit($form);

        $this->assertResponseOk();

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('Название должно быть больше 5 символов', $messege);

        $messege = $crawler->filter('li')->eq(1)->text();
        $this->assertSame('Конотент должен быть больше 10 символов', $messege);

        $messege = $crawler->filter('li')->eq(2)->text();
        $this->assertSame('This value is not valid.', $messege);

        $countLessonNew = count($em->getRepository(Lesson::class)->findBy(['course' => $idCourse]));

        $this->assertEquals($countLesson, $countLessonNew);

    }
}
