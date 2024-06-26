<?php

require_once 'controllers/StudentsController.php';

use Controllers\StudentsController;

function StudentRoutes(IStudentService $studentService)
{
  return function (string $method, ?string $id, ?array $params) use ($studentService) {
    $studentsController = new StudentsController($studentService);

    $response = array('status' => 'failed', 'message' => 'Invalid Request');

    $json_data = file_get_contents('php://input');

    $data = ($json_data && !empty($json_data)) ? json_decode($json_data, true) : null;

    $nombres = null;
    $apellidos = null;
    $carnet = null;
    $pages = 0;

    if (!is_null($params)) {
      $nombres = (!$params && array_key_exists('nombres', $params)) ? $params['nombres'] : null;
      $apellidos = (!$params && array_key_exists('apellidos', $params)) ? $params['apellidos'] : null;
      $carnet = (!$params && array_key_exists('carnet', $params)) ? $params['carnet'] : null;
      $pages = (!$params && array_key_exists('pages', $params)) ? $params['pages'] : 0;
    }

    $response = match (true) {
      $method === 'GET' && $id => $studentsController->getStudentByID($id),
      $method === 'GET' && $nombres && $apellidos => $studentsController->getStudentByName($nombres, $apellidos),
      $method === 'GET' && $carnet => $studentsController->getStudentByCarnet($carnet),
      $method === 'GET' => $studentsController->getStudents(),
      $method === 'POST' => $studentsController->createStudent($data),
      $method === 'PUT' => $studentsController->updateStudent($data),
      $method === 'DELETE' => $studentsController->deleteStudent($data)
    };

    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode($response);
  };
}
