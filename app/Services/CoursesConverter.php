<?php


namespace App\Services;


use App\Course;
use App\Enums\Currency;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class CoursesConverter
{
    /**
     * @var string 212.40.192.49 - www.cbr.ru
     * На момей машине очень медленно выполнялся curl если указывать домен
     */
    private const API = "http://212.40.192.49/scripts/XML_daily_eng.asp";

    public static function getCourseModifier($date, $currency) {

        $needleCourse = self::getCourse($date, $currency);
        $nominal = $needleCourse->getAttributeValue('nominal');
        $value = $needleCourse->getAttributeValue('value');
        return $value/$nominal;
    }

    public static function getCourse($date, $currency) {
        /** @var Model $course */
        $course = Course::class;
        $needleCourse = $course::query()->where('date', $date)->where('code', $currency)->first();
        if (!$needleCourse) {
            $requestDate = date_format(date_create_from_format('Y-m-d', $date), 'd/m/Y');

            $client = new Client();

            $response = $client->get(self::API."?date_req=$requestDate");
            $xml = simplexml_load_string($response->getBody());
            $json = json_encode($xml);
            $dataArray = json_decode($json,true);

            foreach ($dataArray['Valute'] as $data) {
                $charCode = $data['CharCode'];
                $nominal = intval($data['Nominal']);
                $value =  floatval(str_replace(',','.',$data['Value']));

                if ($charCode == "BYN") $charCode = Currency::BYR;

                if ($currency == $charCode) {
                    $needleCourse = new Course();
                    $needleCourse->setAttribute('code', $charCode);
                    $needleCourse->setAttribute('nominal', $nominal);
                    $needleCourse->setAttribute('value', $value);
                    $needleCourse->setAttribute('date', $date);
                    $needleCourse->save();
                }
            }
        }
        return $needleCourse;
    }
}
