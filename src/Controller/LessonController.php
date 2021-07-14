<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lesson")
 */
class LessonController extends AbstractController
{
    /**
     * @Route("/new/{course}", name="lesson_new", methods={"GET","POST"})
     */
    public function new(Request $request, Course $course): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $lesson = $form->getData();
            $lesson->setCourse($course);
            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('course_show', ['id' => $course->getId()]);
        }

        return $this->render('lesson/new.html.twig', [
            'lesson' => $lesson,
            'lessonForm' => $form->createView(),
            'course'    => $course,
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_show", methods={"GET"})
     */
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="lesson_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lesson $lesson): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('course_show', ['id' => $lesson->getCourse()->getId()]);
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'lessonForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="lesson_delete", methods={"POST"})
     */
    public function delete(Request $request, Lesson $lesson): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('course_show', ['id' => $lesson->getCourse()->getId()]);
    }
}
