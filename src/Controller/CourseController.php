<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\BillingClient;
use App\Service\BillingCourse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/course")
 */
class CourseController extends AbstractController
{

    private BillingCourse $billingCourse;
    private CourseRepository $courseRepository;
    private BillingClient $billingClient;

    public function __construct(BillingCourse $billingCourse, CourseRepository $courseRepository, BillingClient $billingClient)
    {
        $this->billingCourse = $billingCourse;
        $this->courseRepository = $courseRepository;
        $this->billingClient = $billingClient;
    }
    /**
     * @Route("/", name="course_index", methods={"GET"})
     */
    public function index(): Response
    {
        $coursesInformation = $this->billingCourse->getCourses();

        $coursesCode = [];
        foreach($coursesInformation as $courseInf){
            $coursesCode[] = $courseInf['code'];
        }

        $couses = $this->courseRepository->findBy(["code" => $coursesCode]);



        return $this->render('course/index.html.twig', [
            'courses' => $couses,
        ]);
    }

    /**
     * @Route("/new", name="course_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $course->setName($form->get('name')->getData());
            $course->setDescription($form->get('description')->getData());
            $course->setCode($form->get('code')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($course);
            $entityManager->flush();

            $data = [
                "token" => $this->getUser()->getApiToken(),
                "code" => $form->get('code')->getData(),
                "price" => $form->get('price')->getData(),
                "type" => $form->get('type')->getData(),
            ];

            $error = $this->billingCourse->courseCreate($data);

            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'courseForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="course_show", methods={"GET"})
     */
    public function show(Course $course): Response
    {
        if(!$this->getUser()) {
            return $this->render('course/show_buy.html.twig', [
                'status' => "Войдите, чтобы зыйти на курс",
                'courseId' => $course->getId(),
                'courseName' => $course->getName(),
                "disabled" => true
            ]);
        }
            $check = $this->billingCourse->checkCourse($this->getUser()->getApiToken(), $course->getCode());
            $courseInformation = $this->billingCourse->getCourseByCode($course->getCode());
            if ($check['check']) {
                if (!isset($courseInformation["code"])) {
                    return $this->redirectToRoute('course_index');
                }

                return $this->render('course/show.html.twig', [
                    'course' => $course,
                    "courseInf" => $courseInformation,
                ]);
            }
        else{
            $balance = $this->billingClient->getBalance($this->getUser());
            $disabled = false;
            if($courseInformation['price'] > $balance){
                $disabled = true;
            }
            return $this->render('course/show_buy.html.twig', [
                'status' => $check['message'],
                'courseId' => $course->getId(),
                'courseName' => $course->getName(),
                "disabled" => $disabled
            ]);
        }
    }

    /**
     * @Route("/edit/{id}", name="course_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('course_show', ['id' =>$course->getId()]);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'courseForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="course_delete", methods={"POST"})
     */
    public function delete(Request $request, Course $course): Response
    {
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($course);
            $entityManager->flush();
            $this->billingCourse->deleteCourse($this->getUser()->getApiToken(), $course->getCode());
        }

        return $this->redirectToRoute('course_index');
    }

    /**
     * @Route("/pay/{id}", name="course_pay", methods={"GET","POST"})
     */
    public function payCourse(Request $request, Course $course){
        $result = $this->billingCourse->payCourse($this->getUser()->getApiToken(), $course->getCode());
        return $this->redirectToRoute('course_show', ['id' =>$course->getId()]);
    }
}
