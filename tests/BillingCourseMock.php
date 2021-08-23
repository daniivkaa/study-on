<?php

namespace App\Tests;

use App\Service\BillingCourse;

class BillingCourseMock extends BillingCourse
{
    public function checkCourse(string $token = null, string $code = null){
        $result = ["check" => true];
        return $result;
    }

    public function getCourseByCode(string $code){
        $result = [
            "code" => $code,
            "price" => 123,
            "type" => 1
        ];
        return $result;
    }

    public function deleteCourse(string $token = null, string $code){
        $result = ["Message" => "Курс удален"];
        return $result;
    }

    public function courseCreate(array $data){
        $result = [
            'message' => 'Create course',
        ];
        return $result;
    }

    public function payCourse(string $token = null, string $code){

    }
}