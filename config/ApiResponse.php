<?php

class ApiResponse
{
    /**
     * REST Header and body statusCode parameter
     * 200 OK
     * 201 Created
     * 204 No Content
     * 401 Unauthorized
     * 404 Not Found
     * 422 Unprocessable Entity
     * 304 Not Modified
     */
    CONST HTTP_STATUS_CODE_OK = 200;
    CONST HTTP_STATUS_CODE_CREATED = 201;
    CONST HTTP_STATUS_CODE_NO_CONTENT = 204;
    CONST HTTP_STATUS_CODE_UNAUTHORISED = 401;
    CONST HTTP_STATUS_CODE_NOT_FOUND = 404;
    CONST HTTP_STATUS_CODE_UNPROCESSABLE_ENTITY = 422;
    CONST HTTP_STATUS_CODE_NOT_MODIFIED = 304;

    public function data($status, $data, $httpStatusCode)
    {
        http_response_code($httpStatusCode);
        echo json_encode(['status' => $status, 'data' => $data]);
    }

    public function error($status, $error, $httpStatusCode)
    {
        http_response_code($httpStatusCode);
        echo json_encode(['status' => $status, 'error' => $error]);
    }

    /**
     * @param $status int
     * @param $data array|string
     */
    public function ok($status, $data)
    {
        $this->data($status, $data, self::HTTP_STATUS_CODE_OK);
    }

    /**
     * @param $status int
     * @param $data array
     */
    public function created($status, $data)
    {
        $this->data($status, $data, self::HTTP_STATUS_CODE_CREATED);
    }

    public function noContent()
    {
        http_response_code(self::HTTP_STATUS_CODE_NO_CONTENT);
    }

    public function unauthorised()
    {
        http_response_code(self::HTTP_STATUS_CODE_UNAUTHORISED);
    }

    /**
     * @param $status int
     * @param $error String|array
     */
    public function unprocessableEntity($status, $error)
    {
        $this->error($status, $error, self::HTTP_STATUS_CODE_UNPROCESSABLE_ENTITY);
    }
}