<?php

define("tranUrl", "https://api.transparent.ly");

class tran extends Provider
{

    public function translationDataSend()
    {
        /* START COLLECT AND TRANSFORM DATA */

            /* Start Collect Data */

                $zip     = $this->funnelFormData['zip'] ?? $this->errorData("zip");
                $its_id  = $this->funnelFormData['its_id'] ?? $this->errorData("its_id");
                $channel = $this->funnelFormData['ch'] ?? $this->errorData("ch");
                
                $firstName  = $this->funnelFormData['firstName'] ?? $this->errorData("firstName");
                $lastName   = $this->funnelFormData['lastName']  ?? $this->errorData("lastName");
                $email      = $this->funnelFormData['email']     ?? $this->errorData("email");

                $device = Helper::getDevice();

                $tranDeviceArray = [
                    "Desktop"   => 1,
                    "Mobile"    => 2,
                    "Tablet"    => 3
                ];

                $tran_DeviceType = $tranDeviceArray[$device] ?? $tranDeviceArray["Desktop"]; 

                $tran_ColVehicles = (!empty($this->funnelFormData["vehicleMake_1"])? 1 : 0);
                $tran_ColVehicles += (!empty($this->funnelFormData['vehicleMake_2']) ? 1 : 0);


                $tran_SourceValue = Helper::getSourceValue($channel, 'tran');

                $tran_license = ($this->funnelFormData['licenseStatus'] == 'Valid' ? '1' : '5');

                $day    = $this -> funnelFormData["birthDay"];
                $month  = $this -> funnelFormData["birthMonth"];
                $year   = $this -> funnelFormData["birthYear"];
        
                $birthday = Helper::getAgeByBirthParams($day,$month,$year);
                $tran_AgeGroup = $this->getTranAgeGroup($birthday) ?? "";

                $tran_HomeOwner = $this->funnelFormData['ownHome'] == 'Yes' ? "1" : "0";

                if (!empty($this->funnelFormData['creditRating'])) {
                    switch ($this->funnelFormData['creditRating']) {
                        case 'Good':
                            $tran_credit_rating = "2";
                            break;
                        case 'Excellent':
                            $tran_credit_rating = "1";
                            break;
                        case 'Poor':
                            $tran_credit_rating = "4";
                            break;
                        case 'Fair':
                            $tran_credit_rating = "2";
                            break;
                        default:
                            $tran_credit_rating = "3";
                    }
                }

                if (!empty($this->funnelFormData['gender'])) {
                    switch ($this->funnelFormData['gender']) {
                        case 'M':
                            $tran_gender = "1";
                            break;
                        case 'F':
                            $tran_gender = "2";
                            break;
                        case 'NB':
                            $tran_gender = "3";
                            break;
                        default:
                            $tran_gender = "3";
                    }
                }

                if (!empty($this->funnelFormData['currentlyInsured'])) {
                    $tran_CurrentlyInsured = $this->funnelFormData['currentlyInsured'] == "YES"
                        ? "1" : "0"; 
                }

                $tran_maritial_status = $this->funnelFormData['maritalStatus'] == "Yes" ? "2" : "1";
                
                $tran_sr22 = $this->funnelFormData['doesRequireSR22'] == 'Y' ? "1" : "2";

                $tran_incidents_count = "";
                $tran_respondent = array();
                $tran_ArrayOfVehicles = array();

                $tran_driverArray = array();
                $tran_currentPolicy = array();

            /* End  Collect Data*/

            /* Start respondent */

                $threeYearIncidentCount = '1';
                if (isset($this->funnelFormData['hasAccidents']) && $this->funnelFormData['hasAccidents'] == 'Yes') {
                    if (isset($this->funnelFormData['secondIncident']) && $this->funnelFormData['secondIncident'] == 'Yes') {
                        $threeYearIncidentCount = '3';
                    } else {
                        $threeYearIncidentCount = '2';
                    }
                }

                $tran_respondent[0] = array();
                $tran_respondent[0]['consumerIP'] = Helper::getIpAdress();
                $tran_respondent[0]['consumerUserAgent'] = Helper::getUserAgent();

                $tran_respondent[0]['firstName']    = $firstName;
                $tran_respondent[0]['lastName']     = $lastName;
                $tran_respondent[0]['email']        = $email;

                if (!empty($this->funnelFormData['phoneNumber'])) {
                    $phoneNumberConst = '#[^0-9]#';
                    $cleanPhone = preg_replace($phoneNumberConst, '', ($this->funnelFormData['phoneNumber'] ?? ''));
                    if (strlen($cleanPhone) == 10)
                        $tran_respondent[0]['phone'] = '(' . substr($cleanPhone, 0, 3) . ') ' . substr($cleanPhone, 3, 3) . '-' . substr($cleanPhone, 6, 9);
                }

                if (!empty($this->funnelFormData['streetAddress'])) {
                    $tran_respondent[0]['address'] = $this->funnelFormData['streetAddress'] ?? '';
                }

                $tran_respondent[0]['zipCode']  = $zip;
                $tran_respondent[0]['city']     = ($this->funnelFormData['city'] ?? "");
                $tran_respondent[0]['state']    = ($this->funnelFormData['state'] ?? '');
                $tran_respondent[0]['address']  = ($this->funnelFormData['address']??"");
                $tran_respondent[0]['country']  = "United States";
                $tran_respondent[0]['sr22']     = $tran_sr22;
                $tran_respondent[0]['homeowner']= $tran_HomeOwner;
                $tran_respondent[0]['threeYearIncidentCount'] = $threeYearIncidentCount;
                $tran_respondent[0]['deviceType'] = $tran_DeviceType;

            /* End respondent */

            /* Start vehicles */

                $tran_ArrayOfVehicles[0] = array();
                $tran_ArrayOfVehicles[0]['vehicleYear'] = "";
                if (!empty($this->funnelFormData['vehicleYear_1'])) {
                    $tran_ArrayOfVehicles[0]['vehicleYear'] = $this->funnelFormData['vehicleYear_1'];
                }

                $tran_ArrayOfVehicles[0]['make'] = "";
                if (!empty($this->funnelFormData['vehicleMake_1'])) {
                    $tran_ArrayOfVehicles[0]['make'] = $this->funnelFormData['vehicleMake_1'];
                }
                $tran_ArrayOfVehicles[0]['model'] = "";
                if (!empty($this->funnelFormData['vehicleModel_1'])) {
                    $tran_ArrayOfVehicles[0]['model'] = $this->funnelFormData['vehicleModel_1'];
                }
                $tran_ArrayOfVehicles[0]['submodel'] = "";
                if (!empty($this->funnelFormData['vehicleSubModel_1'])) {
                    $tran_ArrayOfVehicles[0]['submodel'] = $this->funnelFormData['vehicleSubModel_1'];
                }

                if (!empty($this->funnelFormData['moreThenOneVehicle']) && $this->funnelFormData['moreThenOneVehicle'] == 'Yes') {
                    $tran_ArrayOfVehicles[1] = array();
                    if (!empty($this->funnelFormData['vehicleYear_2'])) {
                        $tran_ArrayOfVehicles[1]['vehicleYear'] = $this->funnelFormData['vehicleYear_2'];
                    }
                    $tran_ArrayOfVehicles[1]['make'] = "";
                    if (!empty($this->funnelFormData['vehicleMake_2'])) {
                        $tran_ArrayOfVehicles[1]['make'] = $this->funnelFormData['vehicleMake_2'];
                    }
                    $tran_ArrayOfVehicles[1]['model'] = "";
                    if (!empty($this->funnelFormData['vehicleModel_2'])) {
                        $tran_ArrayOfVehicles[1]['model'] = $this->funnelFormData['vehicleModel_2'];
                    }
                    $tran_ArrayOfVehicles[1]['submodel'] = "";
                    if (!empty($this->funnelFormData['vehicleSubModel_2'])) {
                        $tran_ArrayOfVehicles[1]['submodel'] = $this->funnelFormData['vehicleSubModel_2'];
                    }
                }
            /* End vehicles */

            /* Start Incidents */
                $tran_haveAccidesnts = false;

                $tran_incidents_count = "1"; //no incidents
                if (isset($this->funnelFormData['hasAccidents']) && $this->funnelFormData['hasAccidents'] == 'Yes') {
                    if (isset($this->funnelFormData['secondIncident']) && $this->funnelFormData['secondIncident'] == 'Yes') {
                        $tran_incidents_count = "3";
                    } else {
                        $tran_incidents_count = "2";
                    }
                }
                if (!empty($this->funnelFormData['hasAccidents']) && $this->funnelFormData['hasAccidents'] == 'Yes') {
                    $tran_haveAccidesnts = true;
                    $tran_accidentsArray = array();
                    $tran_accidentsArray[0] = array();
                    $tran_accidentsArray[0]['drivers'] = '1';
                    if (!empty($this->funnelFormData['incidentTypeName_1'])) {
                        switch ($this->funnelFormData['incidentTypeName_1']) {
                            case 'violation':
                                $tran_accidentsArray[0]['incidentType'] = '2'; //ticket
                                if (!empty($this->funnelFormData['incidentType_1']) && $this->funnelFormData['incidentType_1'] == "InfluenceOfAlcohol") {
                                    $tran_accidentsArray[0]['ticketType'] = '3'; // Ticket: DUI/DWI 
                                } elseif (!empty($this->funnelFormData['incidentType_1']) && $this->funnelFormData['incidentType_1'] == "Speeding") {
                                    $tran_accidentsArray[0]['ticketType'] = '8'; // Ticket: speeding 
                                } else
                                    $tran_accidentsArray[0]['ticketType'] = '10'; // Ticket: other 
                                break;
                            case 'accident':
                                $tran_accidentsArray[0]['incidentType'] = '3'; // accident
                                if (!empty($this->funnelFormData['incidentAmountPaid_1']) && $this->funnelFormData['incidentAmountPaid_1'] !== "Unknown")
                                    $tran_accidentsArray[0]['amountPaid'] = $this->funnelFormData['incidentAmountPaid_1'];
                                if (!empty($this->funnelFormData['incidentType_1']) && $this->funnelFormData['incidentType_1'] == "NotAtFaultNotListed")
                                    $tran_accidentsArray[0]['driverAtFault'] = '2'; // Not At Fault
                                else
                                    $tran_accidentsArray[0]['driverAtFault'] = '1'; // At Fault

                                $tran_accidentsArray[0]['accidentType'] = '4'; // Other
                                break;
                            case 'claim':
                                $tran_accidentsArray[0]['incidentType'] = '1'; // claim
                                if (!empty($this->funnelFormData['incidentAmountPaid_1']) && $this->funnelFormData['incidentAmountPaid_1'] !== "Unknown")
                                    $tran_accidentsArray[0]['amountPaid'] = $this->funnelFormData['incidentAmountPaid_1'];
                                if (!empty($this->funnelFormData['incidentType_1']) && $this->funnelFormData['incidentType_1'] == "VehicleStolen")
                                    $tran_accidentsArray[0]['claimType'] = '1';  // Claim: theft
                                else
                                    $tran_accidentsArray[0]['claimType'] = '5'; // Other
                                break;
                        }
                    }
                    $tran_accidentsArray[0]['incidentDate'] = "";
                    if (!empty($this->funnelFormData['incidentDate_1'])) {
                        $tran_accidentsArray[0]['incidentDate'] = $this->funnelFormData['incidentDate_1'];
                    }

                    if (!empty($this->funnelFormData['secondIncident']) && $this->funnelFormData['secondIncident'] == 'Yes') {
                        $tran_accidentsArray[1] = array();
                        $tran_accidentsArray[1]['drivers'] = '1';
                        if (!empty($this->funnelFormData['incidentTypeName_2'])) {
                            switch ($this->funnelFormData['incidentTypeName_2']) {
                                case 'violation':
                                    $tran_accidentsArray[1]['incidentType'] = '2'; //ticket
                                    if (!empty($this->funnelFormData['incidentType_2']) && $this->funnelFormData['incidentType_2'] == "InfluenceOfAlcohol") {
                                        $tran_accidentsArray[1]['ticketType'] = '3'; // Ticket: DUI/DWI 
                                    } elseif (!empty($this->funnelFormData['incidentType_2']) && $this->funnelFormData['incidentType_2'] == "Speeding") {
                                        $tran_accidentsArray[1]['ticketType'] = '8'; // Ticket: speeding 
                                    } else
                                        $tran_accidentsArray[1]['ticketType'] = '10'; // Ticket: other 
                                    break;
                                case 'accident':
                                    $tran_accidentsArray[1]['incidentType'] = '3'; // accident
                                    if (!empty($this->funnelFormData['incidentAmountPaid_2']) && $this->funnelFormData['incidentAmountPaid_2'] !== "Unknown")
                                        $tran_accidentsArray[1]['amountPaid'] = $this->funnelFormData['incidentAmountPaid_2'];
                                    if (!empty($this->funnelFormData['incidentType_2']) && $this->funnelFormData['incidentType_2'] == "NotAtFaultNotListed")
                                        $tran_accidentsArray[1]['driverAtFault'] = '2'; // Not At Fault
                                    else
                                        $tran_accidentsArray[1]['driverAtFault'] = '1'; // At Fault

                                    $tran_accidentsArray[1]['accidentType'] = '4'; // Other
                                    break;
                                case 'claim':
                                    $tran_accidentsArray[1]['incidentType'] = '1'; // claim
                                    if (!empty($this->funnelFormData['incidentAmountPaid_2']) && $this->funnelFormData['incidentAmountPaid_2'] !== "Unknown")
                                        $tran_accidentsArray[1]['amountPaid'] = $this->funnelFormData['incidentAmountPaid_2'];
                                    if (!empty($this->funnelFormData['incidentType_2']) && $this->funnelFormData['incidentType_2'] == "VehicleStolen")
                                        $tran_accidentsArray[1]['claimType'] = '1';  // Claim: theft
                                    else
                                        $tran_accidentsArray[1]['claimType'] = '5'; // Other
                                    break;
                            }
                        }
                        $tran_accidentsArray[1]['incidentDate'] = "";
                        if (!empty($this->funnelFormData['incidentDate_2'])) {
                            $tran_accidentsArray[1]['incidentDate'] = $this->funnelFormData['incidentDate_2'];
                        }
                    }
                }
            /* End Incidents */

            /* Start drivers */

                $fullName = $firstName . " " . $lastName;

                $educMatchArr = [
                    'Other' => '10',
                    'High school' => '3',
                    'Some college' => '4',
                    'Associate' => '6',
                    'Bachelor' => '7',
                    'Master' => '8',
                    'PhD' => '9',
                ];

                $tran_driverArray[0] = [
                    'relationship'  => '1',
                    'FullName'      => $fullName,
                    'gender'        => $tran_gender,
                    'age'           => $tran_AgeGroup,
                    'primaryVehicle'=> 1,
                    'licenseStatus' => $tran_license,
                    'licenseState'  => isset($this->funnelFormData['state']) ? $this->funnelFormData['state'] : '',
                    'sr22'          => ($this->funnelFormData['doesRequireSR22'] == 'Y' ? '1' : '2'),
                    'maritalStatus' => isset($tran_maritial_status) ?  $tran_maritial_status : '1',
                    'education'     => !empty($this->funnelFormData['education']) 
                            && !empty($educMatchArr[$this->funnelFormData['education']]) 
                                    ? $educMatchArr[$this->funnelFormData['education']] : 10,
                    'creditEvaluation' => isset($tran_credit_rating) ?  $tran_credit_rating : '',
                    'dob' => Helper::getAlphaDOBByBirthParams($this->funnelFormData), 
                    
                ];

            /* End drivers */

            /* Start tran_currentInsurar */
                $tran_currentInsurar = "";

                if (!empty($this->funnelFormData['currentlyInsured'])) {
                    if ($this->funnelFormData['currentlyInsured'] == "Yes")
                        $tran_currentInsurar = $this->getTranInsurarCode($this->funnelFormData['insuranceCarrier'] ?? '');
                    else
                        $tran_currentInsurar = "0";
                }

                $tran_currentPolicy['currentInsuranceProvider'] = $tran_currentInsurar;

                if ($tran_currentInsurar !== "0") {
                    $tran_CurrentInsurTime = "";

                    if (!empty($this->funnelFormData['insuredTimeframe']) && $this->funnelFormData['currentlyInsured'] == "Yes") {
                        switch ($this->funnelFormData['insuredTimeframe']) {
                            case 'SixToElevenMonths':
                                $tran_CurrentInsurTime = "9";
                                break;
                            case 'TwelveOrMoreMonths':
                                $tran_CurrentInsurTime = "18";
                                break;
                            case 'TwotoThreeYears':
                                $tran_CurrentInsurTime = "30";
                                break;
                            case 'FiveYearsorMore':
                                $tran_CurrentInsurTime = "60";
                                break;
                            default:
                                $tran_CurrentInsurTime = "";
                        }
                    }

                    $tran_currentPolicy['currentCustomer'] = $tran_CurrentInsurTime;
                }
            /* End tran_currentInsurar */

        /* END COLLECT AND TRANSFORM DATA */

        /* Start  $requestLead */
            $mS1Val = "";
            $mS2Val = "";
            $mS3Val = "";
            $mS4Val = $its_id;

            $data = [
                "respondent"    => $tran_respondent,
                "vehicles"      => $tran_ArrayOfVehicles,
                "drivers"       => $tran_driverArray,
                "currentPolicy" => $tran_currentPolicy
            ];
            if ($tran_haveAccidesnts) {
                $data['incidents'] = $tran_accidentsArray;
            }
            if ($this->test == true)
                $this->requestLead["no_tracking"] = true;

            $result = [
                "state"                     => (!empty($this->funnelFormData['state']) ? $this->funnelFormData['state'] : ''),
                "pubcampaignid"             => $tran_SourceValue,
                "vertical"                  => "2",
                "zipcode"                   => $zip,
                "threeyearincidentcount"    => $tran_incidents_count,
                "age"                       => $tran_AgeGroup,
                "creditevaluation"          => $tran_credit_rating,
                "currentlyinsured"          => $tran_CurrentlyInsured,
                "devicetype"                => $tran_DeviceType,
                "gender"                    => $tran_gender,
                "homeowner"                 => $tran_HomeOwner,
                "vehiclecount"              => (string) $tran_ColVehicles,
                "maritalstatus"         => $tran_maritial_status,
                "SR22"  => $tran_sr22,
                "mS1"   => $mS1Val,
                "mS2"   => (!empty($mS2Val) ? $mS2Val : $device),
                "mS3"   => (!empty($mS3Val) ? $mS3Val : $mS4Val),
                "mS4"   => $mS4Val,
                "data" => json_encode($data)
            ];

            $this->requestLead = array_merge($this->requestLead, $result);

        /* End $requestLead */
    }

    public function impressionRequest()
    {

        /* Start GET Impression */

        $url            = tranUrl . "/search/blue/green";

        $request        = 'POST';
        $postFields     = json_encode($this->requestLead, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        $httpHeader     = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent:' . Helper::getUserAgent()
        ];

        $this->providerImpression = (array) json_decode(Helper::sendPostApi($url, $request, $postFields, $httpHeader));

        /* End GET Impression */
    }

    public function translationDataRequest()
    {
        $this->providerData = [];

        if (!empty($this->providerImpression['result'])) {
            foreach ($this->providerImpression['result'] as $key => $row) {
                $row        = (array) $row;

                $list = [
                    "rank"          => (string)$row['position'] ?? '',
                    "title"         => $row['title'] ?? '',
                    "description"   => $row['bulletedDescription'] ?? '',
                    "clickurl"      => $row['clickUrl'] ?? '',
                    "sitehost"      => $row['displayUrl'] ?? '',
                    "logo"          => $row['logoUrl'] ?? '',
                    "raw_logo"      => $row['logoUrl'] ?? '',
                    "imp_pixel"     => "", // ?
                    "impressionid"  => $this->providerImpression['adsRequestId'] ?? '',
                    "accountid"     => "",
                    "customerid"    => $row['advertiserId'] ?? '',
                    "company"       => $row['brandName'] ?? '',
                    "displayname"   => $row['brandName'] ?? '',
                    "revenue"       => (string)$row['publisherRevenue'] ?? ''
                ];

                if (!empty($this->test)) {
                    $list['prov'] = 'tran';
                }

                //publisherRevenue
                $_SESSION[$list['impressionId'] ?? ''] = (string)($row['publisherRevenue'] ?? '');

                /* start feathes and filters */

                if ($key == 0 || $key == 1) {
                    $list['rankName'] = 'featured';
                } else {
                    $list['rankName'] = 'standard';
                }

                if (!empty($row['paragraphDescription']) && gettype($row['paragraphDescription']) == 'string') {
                    $list['description'] = $row['paragraphDescription'];
                }

                if (!empty($row['bulletedDescription']) && is_array($row['bulletedDescription'])) {
                    $description = "<ul>";
                    foreach ($row['bulletedDescription'] as $line) {
                        $description .= "<li>" . $line . "</li>";
                    }
                    $description .= "</ul>";

                    $list['description'] = $description;
                }

                $listing [] = $list;
                /* end feathes and filters */
            }
        }

        $this->providerData = [
            'response'     => [
                'listingset' => [
                    "Date"                  => date("d/m/Y h:i:s A"),
                    "numListingsReturned"   => count($listing ?? []),
                    "statecode"             => $this->funnelFormData['state'] ?? '',
                    "accountid"             => $this->requestLead["pubcampaignid"],
                    "state"                 => $this->funnelFormData['city'] ?? '',
                    "zipcode"               => $this->funnelFormData['zip'] ?? '',
                    'listing'               => $listing ?? [],
                ]
            ]
        ];
    }

    function getTranInsurarCode($companyName)
    {
        switch ($companyName) {
            case 'Progressive':
                return "1";
            case 'Geico':
                return "2";
            case 'StateFarm':
                return "3";
            case 'State Farm':
                return "3";
            case 'Allstate':
                return "4";
            case 'Nationwide':
                return "5";
            case 'FarmersInsurance':
                return "6";
            case 'LibertyMutual':
                return "7";
            case 'Liberty Mutual':
                return "7";
            case 'Elephant':
                return "8";
            case 'Mercury':
                return "9";
            case 'SafeAuto':
                return "10";
            case 'MetLife':
                return "11";
            case 'Infinity':
                return "12";
            case 'Dairyland':
                return "13";
            case 'TheGeneral':
                return "14";
            case 'The General':
                return "14";
            case '21st Century':
                return "15";
            case 'DirectGeneral':
                return "16";
            case 'Direct General':
                return "16";
            case 'other':
                return "17";
            case 'Other':
                return "17";
            case 'Esurance':
                return "18";
            case 'Travelers':
                return "19";
            case 'USAA':
                return "20";
            default:
                return "17";
        }
    }

    function getTranAgeGroup($explicitAge)
    {
        if ($explicitAge <= "17")
            return "1";
        elseif ($explicitAge >= "18" && $explicitAge <= "24")
            return "2";
        elseif ($explicitAge >= "25" && $explicitAge <= "34")
            return "3";
        elseif ($explicitAge >= "35" && $explicitAge <= "49")
            return "4";
        elseif ($explicitAge >= "50" && $explicitAge <= "64")
            return "5";
        else return "6"; //over 65 years old
    }

}
