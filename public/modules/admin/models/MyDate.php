<?php
namespace app\modules\admin\models;

use DateTime;
use yii\helpers\ArrayHelper;

date_default_timezone_set('Asia/Yekaterinburg');

/**
 * Работа с датами
 *
 * @property int date Дата для форматирования
 * @property int date_to Начальная дата
 * @property int date_do Конечная дата
 * @property int status Статус заявки
 */

class MyDate extends \yii\db\ActiveRecord
{

    public $date;

    public $date_to, $date_do,  $status;

    public function rules()
    {
        return[
            ['date', 'date']
        ];
    }


    public function afterFind()
    {
        $this->date_to = strtotime('now');
    }

    /*
     * date - дата которую необходимо перевети
     * перевод в юникс дату
     */
    public static function getTimestamp($date)
    {
        $date = new \DateTime($date);
        return $date->getTimestamp();
    }

    /*
     * dates - дата которую необходимо перевети
     * перевод в дату в обычном виде
     */
    public static function getDate($dates, $format=null)
    {
        $date = new \DateTime();
        $date->setTimestamp($dates);
        if(isset($format)){
            if($format == 100){
                return $date->format('Y-m-d');
            }else{
                return $date->format('H:i | Y-m-d');
            }
        }else{
            $datetime1 = new \DateTime(date('Y-m-d'));
            $datetime2 = new \DateTime($date->format('Y-m-d'));
            $interval = $datetime1->diff($datetime2);
            if($interval->format('%a') == 0){
                return $date->format('Сегодня H:i');
            }elseif ($interval->format('%a') == 1){
                return $date->format('Вчера H:i');
            }
            return $date->format('H:i | d-m-Y');
        }
    }

    public function getWeek($w){
        switch ($w) {
            case 0: echo "Вс"; break;
            case 1: echo "Пн"; break;
            case 2: echo "Вт"; break;
            case 3: echo "Ср"; break;
            case 4: echo "Чт"; break;
            case 5: echo "Пт"; break;
            case 6: echo "Сб"; break;
        }
    }


    /**
     * Общее количество минут между датами
     * в секундах
     */
    public function getTotalTime(){
//        $_begin = strtotime(date('Y-m-d 8:00', $this->date_to)); // Начальное время
//        $_end =  strtotime(date('Y-m-d 17:00', $this->date_do)); // Конечное время
//
//        if($_begin > $this->date_to){
//            $this->date_to = $_begin;
//        }elseif ($_begin > $this->date_do ){
//            $this->date_do = $_begin;
//        }
//
//
//        if($_end < $this->date_do){
//            $this->date_to = $_end;
//        }
        $time_sub = $this->date_do - $this->date_to;
        $result = $time_sub < 60 ? 1 : (int)($time_sub / 60);
        return $result;
    }

    /**
     * Вычисляем время между рабочими днями
     * Если начальное время и окненчо время выходят за границы рабочего времени, то это время учитываем как рабочее.
     */
    public function getDataTime(){
        $count = $this->getCountDay();

        $time_end = $time_begin = $weekend = $w_count = 0;

        $_begin = strtotime(date('Y-m-d 8:00', $this->date_to)); // Начальное время по history
        $_end =  strtotime(date('Y-m-d 17:00', $this->date_do)); // Конечное время берем как конец рабочего дня.

        $day_begin = $this->getDay($this->date_to);
        $day_end = $this->getDay($this->date_do);

        while($day_begin <= $day_end){
            $ww= date('Y-m-'.$day_begin, $this->date_to);
            $w = date('w', strtotime($ww));
//            echo $ww.'-'.date('w', strtotime($ww)).'<br>';
            $day_begin++;
            if ($w == 0 or $w == 6){
                $weekend = $weekend + 24;
                $w_count++;
            }
        }
        if ($_begin > $this->date_to){
            $time_begin = (int)(($_begin - $this->date_to) / 60);
        }
        if ($_end < $this->date_do){
            $time_end = (int)(($this->date_do - $_end) / 60);
        }

//        echo '$weekend '. $weekend.'<br>';
        return (($count - $w_count) * 15 + $weekend) * 60 - ($time_begin + $time_end);
    }

    /* Получаем время не учитываемое в обед в начальную дату */
    public function startData(){
        $date = $this->date_to;
        $_begin = strtotime(date('Y-m-d H:i',$date)); // Начальное время по history
        $_end =  strtotime(date('Y-m-d 17:00',$date)); // Конечное время берем как конец рабочего дня.

        $lunch = $this->getLunch($_begin, $_end);

//        echo 'lunch '.(int)($lunch / 60).'<br>';
//        echo 'begin '.' '.date('Y-m-d H:i',$date).'<br>';
//        echo 'end '.' '.date('Y-m-d 17:00',$date).'<br>';
//
        return (int)($lunch / 60);


    }

    /* Получаем время не учитываемое в обед в конечную дату */
    public function endData(){
        $date = $this->date_do;

        $date_do = date('Y-m-d H:i',$date);
        $date_to = date('Y-m-d 8:00',$date);
        $_begin = strtotime($date_to); // Начальное время по history
        if ($_begin > $date){
            $date_do = date('Y-m-d 8:00',$date);
        }
        $_end =  strtotime($date_do); // Конечное время берем как конец рабочего дня.

        $lunch = $this->getLunch($_begin, $_end);
//        echo 'lunch '.(int)($lunch / 60).'<br>';
//        echo 'begin '.' '.$date_to.'<br>';
//        echo 'end '.' '.$date_do.'<br>';
        return (int)($lunch / 60);
    }

    /* Проверяем обеду в промежуток времени. Если входит вычисляем неучиываемое время */
    public function getLunch($_to, $_do){
        $lunch_begin = strtotime(date('Y-m-d 12:00',$_to));
        $lunch_end = strtotime(date('Y-m-d 13:00',$_to));
        $ss = null;
        if ($_to < $lunch_begin and $lunch_end < $_do){
            $ss = 60*60;
        }elseif ($_to < $lunch_begin and $lunch_end > $_do){
            $ss = $_do - $lunch_begin;
        }elseif($_to > $lunch_begin and $lunch_end < $_do){
            $ss = $lunch_end - $_to;
        }else{
            $ss = 0;
        }
//        $result = $this->getTotalTime() - (int)($ss / 60);
        return $ss > 0 ? $ss : 0;
    }

    /* Приводим время в нормальный вид. Минуты переводим в часы */
    public static function normalizeTime($time){
        $min = $time;
        if($min > 60){
            $clock = (int)($min / 60);
            $min = $min - $clock * 60;
            return $clock.'ч. '.str_pad($min, 2, '0', STR_PAD_LEFT).' мин.';
        }
        return $min.' мин.';
    }

    /* Выводим ДЕНЬ из даты */
    public function getDay($date){
        return date('d', $date);
    }

    /**
     * Вычесляем колчиество дней между датами
     * @property int $date_to Начальная дата
     * @property int $date_do Конечная дата
     */
    public function getCountDay(){
        $day_to = date_create(date('Y-m-d',$this->date_to));
        $day_do = date_create(date('Y-m-d',$this->date_do));

        $dateFrom = '2012-01-01';
        $dateTo = '2013-01-01';


        $interval = date_diff($day_to, $day_do);
        return $interval->days;
//        return $datetimeFrom->diff( $datetimeTo )->format( '%a days' ); # 366 days
    }

    public function ramki(){
        $begin = strtotime(date('Y-m-d 8:00', $this->date_to)); // Начальное время
        $end =  strtotime(date('Y-m-d 17:00', $this->date_to)); // Конечное время

        if ($this->date_to > $end){
            $result = $this->date_to - $end;
        }elseif($this->date_to < $begin){
            $result = $begin - $this->date_to;
        }else{
            return 0;
        }

        $_begin = strtotime(date('Y-m-d 8:00', $this->date_do)); // Начальное время
        $_end =  strtotime(date('Y-m-d 17:00', $this->date_do)); // Конечное время

        if ($this->date_do > $_end ){
            $result = $this->date_do - $end;
        }elseif($this->date_do < $_begin){
            $result = $begin - $this->date_do;
        }else{
            return 0;
        }

        return $result;
    }


    /* Выводим неучитываемое время */
    public function getSum(){
        $total = $this->getTotalTime(); //общее время
        $count_day = $this->getCountDay();

        if ($count_day == 0){
//            echo '1 - 1 <br>';
            $lunch = $this->getLunch($this->date_to, $this->date_do); // Обеденное время. Которые не должно учитываться
            $result = $total - (int)($lunch / 60); // Вычитаем неучитываемое время от общего времени
            return $total < 0 ? 0 : $total;
        }elseif ($count_day == 1){
//            echo 'date_to '.date('Y-m-d H:i', $this->date_to).'<br>';
//            echo 'date_do '.date('Y-m-d H:i', $this->date_do).'<br>';
//            echo '1 - 2 <br>';

            $_day = $this->getDataTime(); // Время между рабочими днями

            $_start = $this->startData(); // Обеденное время для начала даты. Которые не должно учитываться
            $_end = $this->endData(); // Обеденное время для конца даты. Которые не должно учитываться

//            echo $_day.'<br>';
//            echo $_start.'<br>';
//            echo $_end .'<br>';
            $_res = $total - ($_day + $_start + $_end);
            return $_res;

        }else{
//            echo '1 - 3 <br>';

            $_lunch = ($count_day - 1)  * 1 * 60;
            $_day = $this->getDataTime(); // Время между рабочими днями
            $_start = $this->startData(); // Обеденное время для начала даты. Которые не должно учитываться
            $_end = $this->endData(); // Обеденное время для конца даты. Которые не должно учитываться

            $r = $_day + $_start + $_end + $_lunch + $this->ramki();

//            echo '$count_day '.$count_day.'<br>';
//            echo '$total '.self::normalizeTime($total).'<br>';
//            echo '$r '.$r.'<br>';
//            echo '$_day '.$_day.'<br>';
//            echo '$_start '.$_start.'<br>';
//            echo '$_end '.$_end.'<br>';
//            echo '$_lunch '.$_lunch.'<br>';
            $_res = $total - ($_day + $_start + $_end + $_lunch + $this->ramki());
//            echo '$res  '.self::normalizeTime($_res).'<br>';

            return  $_res;
        }

        return true;
    }


















        private $offtimes = array (
            0, 1, 2, 3, 4, 5, 6, 7, // утром
            12,  // обед
            17, 18, 19, 20, 21, 22, 23); // вечером


        private $holydays = array (
            "2018-01-01",  "2018-01-02",  "2018-01-03",
            "2018-01-04",  "2018-01-05",  "2018-01-06",
            "2018-01-07",  "2018-01-08",  "2018-02-23");

        private $weekends = array ("Sat", "Sun");



        public function isOfftime ($time)
        {
            return in_array (date ("G", $time),  // час без ведущего нуля
                $this -> offtimes);  // список нерабочих часов
        }

        public function isWeekend ($time)
        {
            return in_array (date ("D", $time), // буквенный код дня недели
                $this -> weekends); // список выходных дней недели
        }

        public function isHolyday ($time)
        {
            return in_array (date ("Y-m-d", $time), // конвертируем в дату
                $this -> holydays); // ищем в списке праздников
        }

        public function isDate($time, $date_do){
            return $time < $date_do ? false : true;
        }

        public function addHour ($time)
        {
            do
                $time += 3600;
            while ($this -> isWeekend ($time) // выходной
            || $this -> isHolyday ($time) // праздник
            || $this -> isOfftime ($time)); // нерабочий час
            return $time;
        }

        public function addHours ($date, $hours)
        {
            $time = strtotime ($date); // переводим дату в секунды
            for ($j = 0; $j < $hours; $j ++) // в цикле добавляем по часу
                $time = $this -> addHour ($time);
            return $time; // конвертируем в дату
        }

        public function addMin ($time)
        {
            do
                $time += 60;
            while ($this -> isWeekend ($time) // выходной
            || $this -> isHolyday ($time) // праздник
            || $this -> isOfftime ($time)); // нерабочий час
            return $time;
        }

        public function addMins ($date, $hours)
        {
            $time = strtotime ($date); // переводим дату в секунды
            for ($j = 0; $j < $hours; $j ++) // в цикле добавляем по часу
                $time = $this -> addMin ($time);
            return $time; // конвертируем в дату
        }


        /**
         * @param $time
         * @param $date_do
         * @return float|int
         * Рабочее время между датами
         */
        public function betweenTime(){
            $i = 0;
            $sum = 0;

            $time = $this->date_to;



            $runtime = (int)(($this->date_do - $time) / 60);

//            $clock = (int)($runtime / 60);
//            $min = (int)(($runtime - $clock * 60));
//
//
//            for ($j = 0; $j < $clock; $j ++) { // в цикле добавляем по часу
//                echo 'clk - '.$i."<br>";
//                if($time >  $this->date_do){
////                    echo '+ '.$time.'->'. $this->date_do . '<br>';
//                    goto sum;
//                    echo 2;
//
//                }
//                $time = $this->addHour($time);
//
//                $i++;
//            }
//
//            sum:
//            $sum += $i * 60;

            $i = 0;
            for ($j = 0; $j < $runtime; $j ++) { // в цикле добавляем по часу
//                echo '+ '.$time.'->'. $this->date_do . '<br>';
//                echo 'min - '.$i."<br>";
                if($time >  $this->date_do){
//                    echo 2;
                    goto sum2;
                }
                $time = $this->addMin($time);

                $i++;
            }
            sum2:
        $sum += $i;


//        echo "<br><br><br>";
        return $sum; // конвертируем в дату

    }
}
