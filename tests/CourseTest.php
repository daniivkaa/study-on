<?php

namespace App\Tests;

use App\Entity\Course;
use App\Entity\Lesson;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourseTest extends AbstractTest
{
    public function testIndex(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', 'http://study-on.local:81/course/');

        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        $countCourse = count($courses);
        $this->assertEquals(3, $countCourse);

        AbstractTest::assertResponseOk();
    }

    public function testShow(): void
    {
        $client = AbstractTest::getClient();
        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $id = $course->getId();

            $lessons = $em->getRepository(Lesson::class)->findBy(['course' => $id]);
            $countLesson = count($lessons);
            $this->assertEquals(3, $countLesson);

            $crawler = $client->request('GET', "http://study-on.local:81/course/show/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testNewGet(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', 'http://study-on.local:81/course/new');

        AbstractTest::assertResponseOk();
    }

    public function testEditGet(): void
    {
        $client = AbstractTest::getClient();
        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $id = $course->getId();
            $crawler = $client->request('GET', "http://study-on.local:81/course/$id/edit");

            AbstractTest::assertResponseOk();
        }
    }

    public function testNotFound(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', "http://study-on.local:81/coursexdfh/");

        AbstractTest::assertResponseNotFound();

    }

    public function testNewPost(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', "http://study-on.local:81/coursexdfh/");

        AbstractTest::assertResponseNotFound();

    }
}
