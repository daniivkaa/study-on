<?php

namespace App\Tests;

use App\Entity\Course;
use App\Entity\Lesson;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Form\CourseType;

class CourseTest extends AbstractTest
{
    public function testIndex(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $this->doAuth($client, "user@user.com", "123456");

        $client->followRedirect();
        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        $countCourse = count($courses);
        $this->assertEquals(3, $countCourse);

        $this->assertResponseOk();
    }

    public function testShow(): void
    {
        $client = AbstractTest::getClient();
        $this->doAuth($client, "user@user.com", "123456");
        $client->followRedirect();

        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $id = $course->getId();

            $lessons = $em->getRepository(Lesson::class)->findBy(['course' => $id]);
            $countLesson = count($lessons);
            $this->assertEquals(3, $countLesson);

            $crawler = $client->request('GET', "/course/show/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testNewGet(): void
    {

        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $crawler = $client->request('GET', '/course/new');

        AbstractTest::assertResponseOk();
    }

    public function testEditGet(): void
    {
        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $em = AbstractTest::getEntityManager();
        $courses = $em->getRepository(Course::class)->findAll();
        foreach ($courses as $course) {
            $id = $course->getId();
            $crawler = $client->request('GET', "/course/edit/$id");

            AbstractTest::assertResponseOk();
        }
    }

    public function testNotFound(): void
    {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', "/coursexdfh/");

        AbstractTest::assertResponseNotFound();

    }

    public function testFormNewError(): void
    {
        $url = '/course';

        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();
        $client->followRedirects();

        $em = AbstractTest::getEntityManager();
        $coursesCountBefore = count($em->getRepository(Course::class)->findAll());


        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();

        $link = $crawler->selectLink('Создать курс')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $form = $crawler->filter('form')->form();


        $form->setValues(array(
            "course[name]" => "P",
            "course[description]" => "P",
            "course[code]" => 1
        ));

        $crawler = $client->submit($form);

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('Название должно быть больше 5 символов', $messege);

        $messege = $crawler->filter('li')->eq(1)->text();
        $this->assertSame('Описание должно быть больше 10 символов', $messege);

        $messege = $crawler->filter('li')->eq(2)->text();
        $this->assertSame('код должен быть больше 3 символов', $messege);

        $form = $crawler->filter('form')->form();

        $coursesCountAfter = count($em->getRepository(Course::class)->findAll());

        $this->assertEquals($coursesCountBefore, $coursesCountAfter);


        $form->setValues(array(
            "course[name]" => "Новое имя курса",
            "course[description]" => "Новое описание к курсу",
            "course[code]" => "2362623"
        ));

        $crawler = $client->submit($form);

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('Курс с таким кодом уже существует', $messege);

    }

    public function testFormNewOk(): void
    {
        $em = AbstractTest::getEntityManager();
        $countCourse = count($em->getRepository(Course::class)->findAll());

        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();
        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $urlExpected = $crawler->getUri();

        $link = $crawler->selectLink('Создать курс')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "course[name]" => "Namedh",
            "course[description]"  => "Description testd",
            "course[code]" => "222",
        ));

        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame($urlExpected, $crawler->getUri());

        $countCourseNew = count($em->getRepository(Course::class)->findAll());
        $this->assertEquals($countCourse + 1, $countCourseNew);

    }

    public function testFormDeleteOk(): void
    {
        $em = AbstractTest::getEntityManager();
        $countCourse = count($em->getRepository(Course::class)->findAll());

        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $urlExpected = $crawler->getUri();
        
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();


        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame($urlExpected, $crawler->getUri());

        $countCourseNew = count($em->getRepository(Course::class)->findAll());
        $this->assertEquals($countCourse - 1, $countCourseNew);

    }

    public function testFormEditOk(): void
    {
        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();

        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $urlExpected = $crawler->getUri();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $id = $segments[5];

        $link = $crawler->selectLink('Обновить курс')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "course[name]" => "Новое название",
            "course[description]"  => "Новое описание",
            "course[code]" => "2222",
        ));

        $crawler = $client->submit($form);
        $this->assertResponseRedirect();
        $crawler = $client->followRedirect();
        $this->assertSame($urlExpected, $crawler->getUri());

        $em = AbstractTest::getEntityManager();
        $course= $em->getRepository(Course::class)->find($id);
        $this->assertSame("Новое название", $course->getName());
        $this->assertSame("Новое описание", $course->getDescription());
        $this->assertSame("2222", $course->getCode());
    }

    public function testFormEditError(): void
    {
        $em = AbstractTest::getEntityManager();
        $client = AbstractTest::getClient();
        $this->doAuth($client, "admin@admin.com", "123456");
        $client->followRedirect();

        $url = '/course/';

        $crawler = $client->request('GET', $url);
        $this->assertResponseOk();
        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $uri = $crawler->getUri();
        $segments = explode('/', $uri);
        $id = $segments[5];

        $course= $em->getRepository(Course::class)->find($id);
        $name = $course->getName();
        $description = $course->getDescription();
        $code = $course->getCode();

        $link = $crawler->selectLink('Обновить курс')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();
        $form = $crawler->filter('form')->form();

        $form->setValues(array(
            "course[name]" => "Н",
            "course[description]"  => "Н",
            "course[code]" => "2",
        ));
        $course= $em->getRepository(Course::class)->findAll();

        $crawler = $client->submit($form);

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('Название должно быть больше 5 символов', $messege);

        $messege = $crawler->filter('li')->eq(1)->text();
        $this->assertSame('Описание должно быть больше 10 символов', $messege);

        $messege = $crawler->filter('li')->eq(2)->text();
        $this->assertSame('код должен быть больше 3 символов', $messege);


        $form = $crawler->filter('form')->form();


        $form->setValues(array(
            "course[name]" => "Новое имя курса",
            "course[description]" => "Новое описание к курсу",
            "course[code]" => "2362623"
        ));

        $crawler = $client->submit($form);

        $messege = $crawler->filter('li')->eq(0)->text();
        $this->assertSame('Курс с таким кодом уже существует', $messege);

        $client->request('GET', $url);
        $this->assertResponseOk();

        $course= $em->getRepository(Course::class)->find($id);
        $this->assertSame($name, $course->getName());
        $this->assertSame($description, $course->getDescription());
        $this->assertSame($code, $course->getCode());
    }


}
