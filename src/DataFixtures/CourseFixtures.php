<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $courses = [
            "names" => [
                "Изучение верстки",
                "Основы PHP за 24 часа",
                "Symfony: От новичка до мастера",
            ],
            "descriptions" => [
                "Обучение основам HTML и CSS. А так же научим верстать ваше портфолио",
                "Обучим самым осовным фещам в php. Изучим циклы, массивы, функции, а так же затронем ООП",
                "Обучим всему необходимому, чтобы написать свой первый сайт на symfony",
            ],
            "codes" => [
                "1523533",
                "2356236",
                "2362623",
            ],
        ];

        $lessons = [
            [
                "names" => [
                    "Основы HTML",
                    "Основы CSS",
                    "Пишем совой сайт портфолио",
                ],
                "contents" => [
                    "Мы изучим тег div, a, table, span, hr... Мы все изучили, удачи в следующем уроке ",
                    "Сегодня мы изучаем основные стили background, color... Мы изучили основные стили, переходим к следующему уроку",
                    "Теперь мы всезнаем, осталось только написать сайт, делать мы будем это так... Молодцы, теперь у вас есть свой сайт",
                ],
            ],

            [
                "names" => [
                    "Изучение циклов",
                    "Изучение массивов",
                    "Функции и чутка ООП",
                ],
                "contents" => [
                    "Циклы работают таким образом... Вот мы и изучили циклы",
                    "Массивы это очень важная тема и они часно используются вместе с циклами. Вот как они работают... Вот мы и изучили массивы",
                    "Функции сложная тема и они работают вот так... Вот мы и изучили функциии. И сейчас немного про ООП...",
                ],
            ],

            [
                "names" => [
                    "Зачем нужен фреймворк",
                    "Сущности, контроллеры, представления ",
                    "Простенький сайт на симфони",
                ],
                "contents" => [
                    "Фреймворк ускоряет работу над сайтом, и упрощает поддержку в будующем, а самое главное... Теперь мы знаем зачем нужен фреймворк.",
                    "Сейчас мы изучим сущности, контроллеры и представления... Отлично, теперь мы сможем написать первый сайт",
                    "Сейчас я расскажу как написать сайт, чтобы он не лагал и работал всегда прекрасно... Отлично, теперь вы знаете как написать сайт",
                ],
            ],
        ];



        $course = [];
        $lesson = [];
        for ($i = 0; $i < 3; $i++) {
            $course[$i] = new Course();
        }

        for ($i = 0; $i < 9; $i++) {
            $lesson[$i] = new Lesson();
        }

        foreach ($course as $courseKey => $itemCourse) {
            $itemCourse->setName($courses['names'][$courseKey]);
            $itemCourse->setDescription($courses['descriptions'][$courseKey]);
            $itemCourse->setCode($courses['codes'][$courseKey]);

            for ($i = $courseKey * 3; $i < ($courseKey + 1) * 3; $i++) {
                $itemLesson = $lesson[$i];

                $lessonKey = $i - $courseKey * 3;
                $itemLesson->setName($lessons[$courseKey]['names'][$lessonKey]);
                $itemLesson->setContent($lessons[$courseKey]['contents'][$lessonKey]);
                $itemLesson->setNumber($lessonKey + 1);
                $itemLesson->setCourse($itemCourse);
                $itemCourse->addLesson($itemLesson);

                $manager->persist($itemLesson);
                $manager->persist($itemCourse);
            }
        }

        $manager->flush();
    }
}
