<?php


namespace esportz\addons\abios\dtos;


class Tournament
{
    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $title;

    /**
     * @var
     */
    public $start;

    /**
     * @var
     */
    public $prizepoolStringTotal;

    /**
     * @var
     */
    public $prizepoolStringFirst;

    /**
     * @var
     */
    public $prizepoolStringSecond;

    /**
     * @var
     */
    public $prizepoolStringThird;

    /**
     * @var
     */
    public $banner;

    /**
     * @var
     */
    public $imageSquare;

    /**
     * @var string
     */
    public $gameLongTitle;

    /**
     * @var string
     */
    public $gameImageCircle;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $format;

    /**
     * @var
     */
    public $stages;

    /**
     * Tournament constructor.
     *
     * @param $id
     * @param $start
     * @param $prizepoolStringTotal
     * @param $prizepoolStringFirst
     * @param $prizepoolStringSecond
     * @param $prizepoolStringThird
     * @param $banner
     * @param $imageSquare
     * @param $gameLongTitle
     * @param $gameImageCircle
     * @param $description
     * @param $country
     * @param $city
     * @param $format
     * @param $stages
     */
    public function __construct(
        $id,
        $title,
        $start, 
        $prizepoolStringTotal,
        $prizepoolStringFirst,
        $prizepoolStringSecond,
        $prizepoolStringThird, 
        $banner,
        $imageSquare,
        $gameLongTitle,
        $gameImageCircle,
        $description,
        $country,
        $city,
        $format,
        $stages
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->start = $start;
        $this->prizepoolStringTotal = $prizepoolStringTotal;
        $this->prizepoolStringFirst = $prizepoolStringFirst;
        $this->prizepoolStringSecond = $prizepoolStringSecond;
        $this->prizepoolStringThird = $prizepoolStringThird;
        $this->banner = $banner;
        $this->imageSquare = $imageSquare;
        $this->gameLongTitle = $gameLongTitle;
        $this->gameImageCircle = $gameImageCircle;
        $this->description = $description;
        $this->country = $country;
        $this->city = $city;
        $this->format = $format;
        $this->stages = $stages;
    }
}