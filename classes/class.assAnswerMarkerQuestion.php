<?php

require_once './Modules/Test/classes/inc.AssessmentConstants.php';

/**
 * Class for error text answers
 *
 * @author Mohammed Helwani <mohammed.helwani@llz.uni-halle.de>
 * @version $Id: $
 * @ingroup    ModulesTestQuestionPool
 *
 */
class assAnswerMarkerQuestion
{
    /**
     * Array consisting of one errortext-answer
     * E.g. array('name' => 'Point1', 'points_answer' => 1, 'points_position' => 1, 'coordinates' => [[[-93.51562500000011,201.09375],[-190.5468750000001,90],[27.421874999999886,43.59375],[-93.51562500000011,201.09375]]])
     *
     * @var array Array consisting of one errortext-answer
     */
    protected $arrData;

    /**
     * assAnswerErrorTextL constructor
     *
     * @param string $name Name
     * @param double $points_answer Points answer
     * @param double $points_position Points position
     * @param string $coordinates Coordinates
     *
     */
    public function __construct($name = "", $points_answer = 0.0, $points_position = 0.0, $coordinates = "")
    {
        $this->arrData = [
            'name' => $name,
            'points_answer' => $points_answer,
            'points_position' => $points_position,
            'coordinates' => $coordinates
        ];
    }

    /**
     * Object getter
     */
    public function __get($value)
    {
        switch ($value) {
            case "name":
            case "points_answer":
            case "points_position":
            case "coordinates":
                return $this->arrData[$value];
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Object setter
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case "name":
            case "points_answer":
            case "points_position":
            case "coordinates":
                $this->arrData[$key] = $value;
                break;
            default:
                break;
        }
    }
}