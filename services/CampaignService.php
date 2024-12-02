<?php

if (!file_exists(__DIR__ . '/../interfaces/CampaignInterface.php')) {
    die('CampaignInterface file not found');
}

require_once __DIR__ . '/../interfaces/CampaignInterface.php';

class CampaignService implements CampaignInterface {
    private $campaigns;

    public function __construct() {
        // Load campaigns array from campaigns.php
        $this->campaigns = include __DIR__ . '/../campaigns.php';

        // Check if campaigns were loaded
        if (!$this->campaigns || !is_array($this->campaigns)) {
            die('Error: Failed to load valid campaign data from campaigns.php');
        }

        // Optionally log campaigns data
        error_log('Campaigns: ' . print_r($this->campaigns, true));
    }

    public function matchCampaign($bidRequest) {

        // Check for necessary fields
        if (!isset($bidRequest['device']['geo']['country']) || !isset($bidRequest['imp'][0]['bidfloor'])) {
            return null; // Return null or handle this case as needed
        }

        // Ensure campaigns data exists
        if (empty($this->campaigns)) {
            return null; // Or handle this case differently if needed
        }

        // Log the campaigns to see what is passed to array_filter()
        error_log('Campaigns Data: ' . print_r($this->campaigns, true));

        // Filter campaigns based on matching conditions
        $matchedCampaigns = array_filter($this->campaigns, function ($campaign) use ($bidRequest) {
            // Match country
            $countryMatches = $campaign['country'] === $bidRequest['device']['geo']['country'];

            // Match bidfloor
            $bidfloorMatches = (float)$campaign['price'] >= (float)$bidRequest['imp'][0]['bidfloor'];

            // Match OS (ensure hs_os is valid)
            $osMatches = !empty($campaign['hs_os']) && in_array($bidRequest['device']['os'], explode(',', $campaign['hs_os']));

            return $countryMatches && $bidfloorMatches && $osMatches;
        });

        // If no campaigns match, return null
        if (empty($matchedCampaigns)) {
            return null;
        }

        // Sort the matched campaigns by price (descending order)
        usort($matchedCampaigns, function($a, $b) {
            return (float)$b['price'] <=> (float)$a['price'];
        });

        // Return the best matching campaign
        return $matchedCampaigns[0];
    }
}
?>