<?php
    if (!file_exists(__DIR__ . '/../helpers/response.php')) {
        die('Response Helper file not found');
    }

    require_once __DIR__ . '/../helpers/response.php';

    if (!file_exists(__DIR__ . '/../services/CampaignService.php')) {
        die('CampaignService file not found');
    }

    require_once __DIR__ . '/../services/CampaignService.php';

    class BidController {
        
        public function handleBidRequest() {
            $input = file_get_contents('php://input');
            
            // Decode the JSON request
            $bidRequest = json_decode($input, true);

            //ResponseHelper::jsonResponse(200, "Bid response generated", $bidRequest['device']['geo']['country']);
        
            //Check for JSON errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                ResponseHelper::jsonResponse(400, "Invalid JSON");
                return;
            }
        
            // Process the bid request with the CampaignService
            $service = new CampaignService();
            $matchedCampaign = $service->matchCampaign($bidRequest);
        
            // If a matching campaign is found
            if ($matchedCampaign) {
                $response = [
                    'id' => uniqid(),
                    'bidid' => $bidRequest['id'],
                    'seatbid' => [
                        [
                            'bid' => [
                                [
                                    'price' => $matchedCampaign['price'],
                                    'adm' => json_encode([
                                        'native' => [
                                            'assets' => [
                                                ['id' => 101, 'title' => ['text' => $matchedCampaign['native_title']]],
                                                ['id' => 104, 'img' => ['url' => $matchedCampaign['image_url'], 'w' => 100, 'h' => 100]],
                                                ['id' => 105, 'img' => ['url' => $matchedCampaign['image_url'], 'w' => 640, 'h' => 640, 'type' => 3]],
                                                ['id' => 102, 'data' => ['value' => $matchedCampaign['native_data_value'], 'type' => 2]],
                                                ['id' => 103, 'data' => ['value' => $matchedCampaign['native_data_cta'], 'type' => 12]],
                                            ]
                                        ]
                                    ]),
                                    'impid' => $bidRequest['imp'][0]['id'],
                                    'crid' => $matchedCampaign['creative_id'],
                                    'cid' => $matchedCampaign['code']
                                ]
                            ]
                        ]
                    ]
                ];
        
                // Send a 200 OK response with the bid response data
                ResponseHelper::jsonResponse(200, "Bid response generated", $response);
            } else {
                // If no matching campaign is found, return a 204 No Content response
                ResponseHelper::jsonResponse(204, "No matching campaign found");
            }

        }
    }
?>