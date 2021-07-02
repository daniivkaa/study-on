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
}
