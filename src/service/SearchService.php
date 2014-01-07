<?php

/**
 * Main class for Bigstock API Search Service
 * @url http://help.bigstockphoto.com/entries/20843622-api-overview#search
 * For licensing and examples:
 *
 * @see https://github.com/jacobemerick/bigstock-api-services
 *
 * @author jacobemerick (http://home.jacobemerick.com/)
 */

namespace BigstockAPI\Service;

// @todo autoloader
include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'AbstractService.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ServiceInterface.php';
include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Exception.php';

use BigstockAPI\AbstractService;
use BigstockAPI\ServiceInterface;
use BigstockAPI\Exception;

class SearchService extends AbstractService implements ServiceInterface
{

    /**
     * Search term(s) for the service
     * Search terms can be pipe-delimited to pass in multiple
     */
    protected $query_list = array();

    /**
     * Term to exclude from the search
     */
    protected $exclude;

    /**
     * Page number, a way to iterate through a large set of results
     * @default     1
     */
    protected $page;

    /**
     * A limit to the number of results per request
     * @default     50
     */
    protected $limit;

    /**
     * The order in which the results should come back in
     * @default     'popular'
     */
    protected $order;

    /**
     * List of acceptable order settings
     */
    public static $ACCEPTABLE_ORDER_LIST = array(
        'relevant',
        'popular',
        'new',
    );

    /**
     * Only return results that fit a certain orientation
     * @default     'no preference'
     */
    protected $orientation_constraint;

    /**
     * List of acceptable orientations
     */
    public static $ACCEPTABLE_ORIENTATION_LIST = array(
        'h',
        'v',
    );

    /**
     * Flag to either return illustrations or exclude illustrations
     * @default     'no preference'
     */
    protected $illustration_constraint;

    /**
     * List of acceptable illustration settings
     */
    public static $ACCEPTABLE_ILLUSTRATION_LIST = array(
        'y',
        'n',
    );

    /**
     * Flag to either return vectors or exclude vectors
     * @default     'no preference'
     */
    protected $vector_constraint;

    /**
     * List of acceptable vector settings
     */
    public static $ACCEPTABLE_VECTOR_LIST = array(
        'y',
        'n',
    );

    /**
     * Category to constrain search results to
     */
    protected $category;

    /**
     * Flag to set a preference based on size
     * Note: vectors are oblivious to this setting
     * @default     'no preference'
     */
    protected $size_constraint;

    /**
     * List of acceptable size settings
     */
    public static $ACCEPTABLE_SIZE_LIST = array(
        'm',
        'l',
        'xl',
    );

    /**
     * Boolean to allow mature images through
     * Unlike the other 'flags', this is either 'no mature images' or 'normal + mature images'
     * @default     'y'
     */
    protected $is_safe_search;

    /**
     * Filter to specific contributor id
     */
    protected $contributor;

    /**
     * Filter to a certain language
     * Note: if a search in 'English' returns 0 results, the API will attempt to guess the correct language
     * @default     'en'
     */
    protected $language;

    /**
     * List of acceptable language settings
     */
    public static $ACCEPTABLE_LANGUAGE_LIST = array(
        'de', // german
        'en', // english
        'es', // spanish
        'fr', // french
        'it', // italian
        'ja', // japanese
        'nl', // dutch
        'pt', // portugese
        'ru', // russian
        'zh', // chinese
    );

    /**
     * Name of the service for endpoint creation
     *
     * @return  string  acceptable service name for the request
     */
    public function getServiceName()
    {
        return 'search';
    }

    /**
     * Add term for the search
     * Note: apparently you cannot have crossover between the search list and exclude term
     *
     * @param   string  $term   term for the search
     */
    public function addTerm($term)
    {
        if (strlen($term) < 1) {
            throw new Exception('The search term must be longer than 0 characters');
        }
        if ($term == $this->exclude) {
            throw new Exception('You cannot add a search term for something that is already excluded');
        }
        
        array_push($this->query_list, $term);
    }

    /**
     * Add term for exclusion
     * Note: apparently you cannot have crossover between the search list and exclude term
     *
     * @param   string  $term   term for exclusion
     */
    public function excludeTerm($term)
    {
        if (strlen($term) < 1) {
            throw new Exception('The exclude term must be longer than 0 characters');
        }
        if (in_array($term, $this->query_list)) {
            throw new Exception('You cannot add an exclusion term for something in the search list');
        }
        
        $this->exclude = $term;
    }

    /**
     * Return a new 'page' of results
     * Helpful if you need to process more than 200 results (which is the limit) and are willing to have multiple requests
     * @default     1
     *
     * @param       int     $page   number of page that you want returned
     */
    public function setPage($page)
    {
        if (!is_int($page) || $page < 1) {
            throw new Exception('The page requested must be an integer larger than 1');
        }
        
        $this->page = $page;
    }

    /**
     * Limit the results to a certain amount
     * @default     50
     *
     * @param       int     $limit  number of results that you want to limit to
     */
    public function setLimit($limit)
    {
        if (!is_int($limit) || $limit < 1 || $limit > 200) {
            throw new Exception('The limit must be an integer between 1 and 200');
        }
        
        $this->limit = $limit;
    }

    /**
     * Return the results in a specific order
     * @default     'popular'
     *
     * @param       string  $order  the order that you'd like the results returned in
     */
    public function setOrder($order)
    {
        if ($order != '' && !in_array($order, self::$ACCEPTABLE_ORDER_LIST)) {
            throw new Exception('An unacceptable order setting was passed in');
        }
        
        $this->order = $order;
    }

    /**
     * Flag to set a preference for orientation
     * @default     'no preference'
     
     * @param       string  $orientation    constraining orientation setting
     */
    public function setOrientationConstraint($orientation)
    {
        if ($orientation != '' && !in_array($orientation, self::$ACCEPTABLE_ORIENTATION_LIST)) {
            throw new Exception('An unacceptable orientation setting was passed in');
        }
        
        $this->orientation_constraint = $orientation;
    }

    /**
     * Flag to set a preference for illustration
     * @default     'no preference'
     *
     * @param       string  $illustration   constraining illustration setting
     */
    public function setIllustrationConstraint($illustration)
    {
        if ($illustration != '' && !in_array($illustration, self::$ACCEPTABLE_ILLUSTRATION_LIST)) {
            throw new Exception('An unacceptable illustration setting was passed in');
        }
        
        $this->illustration_constraint = $illustration;
    }

    /**
     * Flag to set a preference for vector
     * @default     'no preference'
     *
     * @param       string  $vector     constraining illustration setting
     */
    public function setVectorConstraint($vector)
    {
        if ($vector != '' && !in_array($vector, self::$ACCEPTABLE_VECTOR_LIST)) {
            throw new Exception('An unacceptable vector setting was passed in');
        }
        
        $this->vector_constraint = $vector;
    }

    /**
     * Category name to constrain results from
     * @default     'no category preference'
     *
     * @param       string  $category   constraining category
     */
    public function setCategory($category)
    {
        if (strlen($category) < 1) {
            throw new Exception('The category must be longer than 0 characters');
        }
        
        $this->category = $category;
    }

    /**
     * Flag to set a preference for size
     * @default     'no preference'
     *
     * @param       string  $size   constraining size setting
     */
    public function setSizeConstraint($size)
    {
        if ($size != '' && !in_array($size, self::$ACCEPTABLE_SIZE_LIST)) {
            throw new Exception('An unacceptable size setting was passed in');
        }
        
        $this->size_constraint = $size;
    }

    /**
     * Flag to set whether or not this is a safe search
     * @default     'y'
     *
     * @param       string  $is_safesearch  flag for safe search
     */
    public function isSafeSearch($is_safe)
    {
        if (!is_bool($is_safe)) {
            throw new Exception('Only a boolean can be passed into the safe search method');
        }
        
        $this->is_safe_search = $is_safe;
    }

    /**
     * Only search for images under a certain contributor
     * @default     'y'
     *
     * @param       string  $is_safesearch  flag for safe search
     */
    public function setContributor($contributor)
    {
        if (strlen($contributor) < 1) {
            throw new Exception('The contributor must be a string longer than 0 characters');
        }
        
        $this->contributor = $contributor;
    }

    /**
     * Only return images that are tagged with a specific language
     * @default     'en'
     *
     * @param       string  $language   language setting
     */
    public function setLanguageConstraint($language)
    {
        if ($language != '' && !in_array($language, self::$ACCEPTABLE_LANGUAGE_LIST)) {
            throw new Exception('The language parameter must be one of the acceptable values');
        }
        
        $this->language_constraint = $language;
    }

    /**
     * Format the URL endpoint with all the parameters
     *
     * @return  string  endpoint string for the request
     */
    public function getEndpoint()
    {
        $query_parameters = array();
        
        if (count($this->query_list) < 1) {
            throw new Exception('You must enter at least one search term before making a request');
        }
        
        $query_parameters['q'] = implode('&', $this->query_list);
        
        if (isset($this->exclude)) {
            $query_parameters['exclude'] = $this->exclude;
        }
        if (isset($this->page)) {
            $query_parameters['page'] = $this->page;
        }
        if (isset($this->limit)) {
            $query_parameters['limit'] = $this->limit;
        }
        if (isset($this->order)) {
            $query_parameters['order'] = $this->order;
        }
        if (isset($this->orientation_constraint)) {
            $query_parameters['orientation'] = $this->orientation_constraint;
        }
        if (isset($this->illustration_constraint)) {
            $query_parameters['illustrations'] = $this->illustration_constraint;
        }
        if (isset($this->vector_constraint)) {
            $query_parameters['vectors'] = $this->vector_constraint;
        }
        if (isset($this->category)) {
            $query_parameters['category'] = $this->category;
        }
        if (isset($this->size_constraint)) {
            $query_parameters['size'] = $this->size_constraint;
        }
        if (isset($this->is_safe_search)) {
            $query_parameters['safesearch'] = ($this->is_safe_search) ? 'y' : 'n';
        }
        if (isset($this->contributor)) {
            $query_parameters['contributor'] = $this->contributor;
        }
        if (isset($this->language_constraint)) {
            $query_parameters['language'] = $this->language_constraint;
        }
        
        $domain = $this->getEndpointDomain();
        $query_string = http_build_query($query_parameters);
        
        return "{$domain}?{$query_string}";
    }

}
