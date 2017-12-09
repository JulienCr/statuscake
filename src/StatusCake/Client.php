<?php

namespace StatusCake;

use Exception;

class Client extends Call
{
    /**
     * @var bool check API response for success status
     */
    public $checkForSuccess;

    /**
     * Set the API credentials
     *
     * @param string $username
     * @param string $token
     * @param bool $checkForSuccess
     */
    public function __construct($username, $token, $checkForSuccess = true)
    {
        $this->checkForSuccess = $checkForSuccess;

        $credentials = new Credentials();
        $credentials->user = $username;
        $credentials->token = $token;
        
        $this->registerCredentials($credentials);
    }

    /**
     * Returns a list overview of all tests
     */
    public function getTests()
    {
        $response = $this->callApi('Tests');

        // Check for success
        if (!is_array($response)) {

            throw new Exception('StatusCake API Error - No list response on getTests.');
        }

        $testList = array();

        foreach ($response as $testData) {

            $testItem = new Test($this->credentials);

            foreach ($testData as $key => $testDataValue) {

                $testItem->{lcfirst($key)} = $testDataValue;
            }

            $testList[] = $testItem;
        }

        return $testList;
    }
    
    /**
     * updates or creates a test
     *
     * @param \StatusCake\Test $test
     *
     * @return mixed
     * @throws \Exception
     */
    public function updateTest(Test $test)
    {

        $data = array();

        // convert Test data to API structure
        foreach ($test as $key => $value) {

            if ($value != null) {

                $data[ucfirst($key)] = $value;
            }
        }

        $response = $this->callApi('Tests/Update', 'PUT', $data);

        // Check for success
        if (is_object($response) && (!$this->checkForSuccess || $response->Success == true)) {
            
            if(property_exists($response, 'InsertID'))
            {
                $test->contactID = $response->InsertID;
                $test->registerCredentials($this->credentials);
            }
            
            return $response;
        }

        throw new Exception('StatusCake API Error - Test update failed.');

    }

    /**
     * Deletes a test
     *
     * @param \StatusCake\Test $test
     *
     * @return mixed
     * @throws \Exception
     */
    public function deleteTest(Test $test)
    {
        if ((int)$test->testID == 0) {

            throw new Exception('Illegal Test/TestID.');
        }

        $response = $this->callApi(
            'Tests/Details/?TestID=' . (int)$test->testID,
            'DELETE'
        );

        // Check for success
        if (is_object($response) && (!$this->checkForSuccess || $response->Success == true)) {

            return $response;
        }
    
        throw new Exception('StatusCake API Error - Test deletion failed.');
    }
    
    /**
     * Return user info
     *
     * @return mixed
     */
    public function account()
    {
        try {
            $response = $this->callApi('Auth');
        
            if(is_object($response) && $response->Success)
            {
                return $response->Details;
            }
    
            throw new Exception('StatusCake API Error - Account authentication failed.');
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Simply test the credentials.
     *
     * @return mixed
     */
    public function validAccount()
    {
        try {
            $response = $this->account();
            
            return is_object($response);
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Get the contact groups.
     *
     * @return array
     * @throws \Exception
     */
    public function getContactGroups()
    {
        $groups = [];
        
        $data = $this->callApi('ContactGroups');
    
    
        // Check for success
        if (!is_array($data)) {
        
            throw new Exception('StatusCake API Error - No list response on getContactGroups.');
        }
        
        // No groups found
        if(count($data) == 0)
            return [];
        
        // Loop over existing groups
        foreach($data as $contactGroup)
        {
            // Initiate
            $group = new ContactGroup();
            
            // Set attributes
            foreach($contactGroup as $key => $val)
            {
                $group->{ucfirst($key)} = $val;
            }
            
            // Add to list
            $groups[] = $group;
        }
        
        return $groups;
    }
    
    /**
     * Create/update a contact group.
     *
     * @param \StatusCake\ContactGroup $contactGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function updateContactGroup(ContactGroup $contactGroup)
    {
        $data = $contactGroup->toArray();
        
        $response = $this->callApi('ContactGroups/Update', 'PUT', $data);
        
        // Check for success
        if (is_object($response) && (!$this->checkForSuccess || $response->Success == true)) {
            
            return $response;
        }
        
        throw new Exception('StatusCake API Error - ContactGroup update failed.');
    }
    
    /**
     * Deletes a ContactGroup
     *
     * @param \StatusCake\ContactGroup $contactGroup
     *
     * @return mixed
     * @throws \Exception
     */
    public function deleteContactGroup(ContactGroup $contactGroup)
    {
        if ($contactGroup->isNew() || (int)$contactGroup->contactID == 0) {
            throw new Exception('Illegal Contact/ContactID.');
        }
        
        $response = $this->callApi(
            'ContactGroups/Update/?ContactID=' . (int)$contactGroup->contactID,
            'DELETE'
        );
        
        // Check for success
        if (is_object($response) && (!$this->checkForSuccess || $response->Success == true)) {
            
            return $response;
        }
        
        throw new Exception('StatusCake API Error - ContactGroup deletion failed.');
    }

    
}
