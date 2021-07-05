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
}
