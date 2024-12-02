<?php
class ResponseHelper {

    public static function jsonResponse($status, $message, $data = null) {
        // Set the correct header
        header('Content-Type: application/json');
        header('Accept: application/json');

        // Create the response structure
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        // Send the JSON response
        echo json_encode($response);
        exit;
    }
}
?>