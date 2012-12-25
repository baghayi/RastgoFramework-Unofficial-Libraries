<?php
namespace root\library\Pdate\index;

final class Pdate
{
    private $pdateWeekName = array(
        "شنبه",
        "یکشنبه",
        "دوشنبه",
        "سه شنبه",
        "چهارشنبه",
        "پنج شنبه",
        "جمعه"
    );

    private $pdateMonthName = array(
        "",
        "فروردین",
        "اردیبهشت",
        "خرداد",
        "تیر",
        "مرداد",
        "شهریور",
        "مهر",
        "آبان",
        "آذر",
        "دی",
        "بهمن",
        "اسفند"
    );   

    private $MonthDays = array(0,31,31,31,31,31,31,30,30,30,30,30,29);

    public function pdate($format,$timestamp=""){
        if($timestamp===""){
            $timestamp=time();
        }
        
        // Create need date parametrs
        $date=date("Y-m-d-w",$timestamp);
        list( $gYear, $gMonth, $gDay ,$gWeek) = explode ('-',$date);
        list( $pYear, $pMonth, $pDay ) = $this->gregorian_to_jalali($gYear, $gMonth, $gDay);
        $pWeek=$gWeek+1;
        if($pWeek==7)$pWeek=0;
        
        $lenghFormat=strlen($format);
        $i=0;
        $result="";
        while($i<$lenghFormat){
            $par=$format{$i};
            if($par=='\\'){
                $result.=$format{++$i};
                $i++;
                continue;
            }
            switch($par){
                //Day
                case 'd':
                    $result.=($pDay<10)?"0".$pDay:$pDay;
                break;
                
                case 'D':
                    $result.=substr($this->pdateWeekName[$pWeek],0,2);
                break;
                
                case 'j':
                    $result.=$pDay;
                break;
                
                case 'l':
                    $result.=$this->pdateWeekName[$pWeek];
                break;
                
                case 'N':
                    $result.=$pWeek+1;
                break;
                
                case 'w':
                    $result.=$pWeek;
                break;
                
                case 'z':
                    $result.=$this->DayOfYear($pYear,$pMonth,$pDay);
                break;
                
                case 'S':
                    $result.="م";
                break;
                
                //Week
                case 'W':
                    $result.=ceil($this->DayOfYear($pYear,$pMonth,$pDay)/7);
                break;
                
                //Month
                case 'F':
                    $result.=$this->pdateMonthName[$pMonth];
                break;
                
                case 'm':
                    $result.=($pMonth<10)?"0".$pMonth:$pMonth;
                break;
                
                case 'M':
                    $result.=substr($this->pdateMonthName[$pMonth],0,6);
                break;
                
                case 'n':
                    $result.=$pMonth;
                break;
                
                case 't':
                    $result.=(isKabise($pYear) and $pMonth==12)?30:$this->MonthDays[$pMonth];
                break;
                
                //Years
                case 'L':
                    $result.=(int)isKabise($pYear);
                break;
                
                case 'Y':
                case 'o':
                    $result.=$pYear;
                break;
                
                case 'y':
                    $result.=substr($pYear,2);
                break;
                
                //Time
                case 'a':
                case 'A':
                    if(date('a',$timestamp)=='am'){
                        $result.=($par=='a')?'ق.ظ':'قبل از ظهر';
                    }
                    else{
                        $result.=($par=='a')?'ب.ظ':'بعد از ظهر';
                    }
                break;
                
                case 'B':
                case 'g':
                case 'G':
                case 'h':
                case 'H':
                case 's':
                case 'u':
                case 'i':

                //Timezone
                case 'e':
                case 'I':
                case 'O':
                case 'P':
                case 'T':
                case 'Z':
                        $result.=date($par,$timestamp);
                break;
                
                //Full Date/Time
                
                case 'c':
                        $result.=$pYear."-".$pMonth."-".$pDay."T".date("H::i:sP",$timestamp);
                break;
                
                case 'r':
                        $result.=substr($this->pdateWeekName[$pWeek],0,2)."، ".$pDay." ".substr($this->pdateMonthName[$pMonth],0,6)." ".$pYear." ".date("H::i:s P",$timestamp);
                break;
                case 'U':
                        $result.=$timestamp;
                break;
                default:
                $result.=$par;
                
                
                
            }
            $i++;
        }
        return $result;
    }

    public function pstrftime($format,$timestamp=""){
        if($timestamp===""){
            $timestamp=time();
        }
        // Create need date parametrs
        $date=date("Y-m-d-w",$timestamp);
        list( $gYear, $gMonth, $gDay ,$gWeek) = explode ('-',$date);
        list( $pYear, $pMonth, $pDay ) = gregorian_to_jalali($gYear, $gMonth, $gDay);
        $pWeek=$gWeek+1;
        if($pWeek==7)$pWeek=0;
        
        $lenghFormat=strlen($format);
        $i=0;
        $result="";
        while($i<$lenghFormat){
                $par=$format{$i};
                if($par=="%"){
                    $type=$format{++$i};
                    switch($type){
                        //Day
                        case 'a':
                            $result.=substr($this->pdateWeekName[$pWeek],0,2) ;
                        break;
                        
                        case 'A':
                            $result.=$this->pdateWeekName[$pWeek];
                        break;
                        
                        case 'd':
                            $result.=($pDay<10)?"0".$pDay:$pDay;
                        break;
                        
                        case 'e':
                            $result.=$pDay;
                        break;
                        
                        case 'j':
                            $dayinM=$this->DayOfYear($pYear,$pMonth,$pDay);
                            $result.=($dayinM<10)?"00".$dayinM:(($dayinM<100)?"0".$dayinM:$dayinM);
                        break;
                        
                        case 'u':
                            $result.=$pWeek+1;
                        break;
                        
                        case 'w':
                            $result.=$pWeek;
                        break;
                        
                        //Week
                        case 'U':
                            $result.=floor($this->DayOfYear($pYear,$pMonth,$pDay)/7);
                        break;
                        
                        case 'V':
                        case 'W':
                            $result.=ceil($this->DayOfYear($pYear,$pMonth,$pDay)/7);
                        break;
                        
                        //Month
                        case 'b':
                        case 'h':
                            $result.=substr($this->pdateMonthName[$pMonth],0,6);
                        break;
                        
                        case 'B':
                            $result.=$this->pdateMonthName[$pMonth];
                        break;
                        
                        case 'm':
                            $result.=($pMonth<10)?"0".$pMonth:$pMonth;
                        break;
                        
                        //Year
                        case 'C':
                            $result.=ceil($pYear/100);
                        break;
                        
                        case 'g':
                        case 'y':
                            $result.=substr($pYear,2);
                        break;
                        
                        case 'G':
                        case 'Y':
                            $result.=$pYear;
                        break;
                        
                        //Time
                        case 'H':
                        case 'I':
                        case 'l':
                        case 'M':
                        case 'R':
                        case 'S':
                        case 'T':
                        case 'X':
                        case 'z':
                        case 'Z':
                            $result.=strftime("%".$type,$timestamp);
                        break;
                        case 'p':
                        case 'P':
                        case 'r':
                            if(date('a',$timestamp)=='am'){
                                $result.=($type=='p')?'ق.ظ':(($type=='P')?'قبل از ظهر':strftime("%I:%M:%S قبل از ظهر",$timestamp));
                            }
                            else{
                                $result.=($type=='p')?'ب.ظ':(($type=='P')?'بعد از ظهر':strftime("%I:%M:%S بعد از ظهر",$timestamp));
                            }
                        break;
                        
                        //Time and Date Stamps
                        case 'c':
                            $result.=substr($this->pdateWeekName[$pWeek],0,2)." ".substr($this->pdateMonthName[$pMonth],0,6)." ".$pDay." ".strftime("%T",$timestamp)." ".$pYear;
                        break;
                        
                        case 'D':
                        case 'x':
                            $result.=(($pMonth<10)?"0".$pMonth:$pMonth)."/".(($pDay<10)?"0".$pDay:$pDay)."/".substr($pYear,2);
                        break;
                        
                        case 'F':
                            $result.=$pYear."-".(($pMonth<10)?"0".$pMonth:$pMonth)."-".(($pDay<10)?"0".$pDay:$pDay);
                        break;
                        
                        case 's':
                            $result.=$timestamp;
                        break;
                        
                        //Miscellaneous
                        case 'n':
                            $result.="\n";
                        break;
                        
                        case 't':
                            $result.="\t";
                        break;
                        
                        case '%':
                            $result.="%";
                        break;
                        
                        default: $result.="%".$type;
                        
                        
                    }
                }
                else{
                    $result.=$par;
                }
                $i++;
        }
        return $result;
    }

    public function DayOfYear($pYear,$pMonth,$pDay){
        $days=0;
        for($i=1;$i<$pMonth;$i++){
            $days+=$this->MonthDays[$i];
        }
        return $days+$pDay;
    }

    public function isKabise($year)
    {
        $mod=$year%33;
        if($mod==1 or $mod==5 or $mod==9 or $mod==13 or $mod==17 or $mod==22 or $mod==26 or $mod==30)return true;
        return false;
    }

    public function pmktime($hour=0,$minute=0,$second=0,$month=0,$day=0,$year=0,$is_dst=-1){
        
        if($hour==0 && $minute==0 && $second==0 && $month==0 && $day==0 && $year==0) return time();
        
        list($year,$month,$day)=jalali_to_gregorian($year,$month,$day);
        
        return mktime($hour,$minute,$second,$month,$day,$year,$is_dst);
    }

    public function pcheckdate($month, $day, $year){
        if($month < 1 || $month > 12 || $year < 1  || $year > 32767 || $day < 1){
            return false;
        }
        if($day>$this->MonthDays[$month]){
            if($month!=12 || $day!=30 || !isKabise($year)){
                return false;
            }
        }
        return true;
    }

    public function pgetdate($timestamp="")
    {
        if($timestamp==="")
            $timestamp=mktime();
        list($seconds,$minutes,$hours,$mday,$wday,$mon,$year,$yday,$weekday,$month)=explode("-",$this->pdate("s-i-G-j-w-n-Y-z-l-F",$timestamp));
        return array(
            0=>$timestamp,  
            "seconds"=>$seconds,
            "minutes"=>$minutes,
            "hours"=>$hours,
            "mday"=>$mday,
            "wday"=>$wday,
            "mon"=>$mon,
            "year"=>$year,
            "yday"=>$yday,
            "weekday"=>$weekday,        
            "month"=>$month,
        );
    }

    public function div($a,$b) {
        return (int) ($a / $b);
    }

    public function gregorian_to_jalali ($g_y, $g_m, $g_d) 
    {
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);     

        $gy = $g_y-1600; 
        $gm = $g_m-1; 
        $gd = $g_d-1; 

        $g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400); 

        for ($i=0; $i < $gm; ++$i) 
            $g_day_no += $g_days_in_month[$i]; 
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))) 
          /* leap and after Feb */ 
          $g_day_no++; 
        $g_day_no += $gd; 

        $j_day_no = $g_day_no-79; 

        $j_np = $this->div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */ 
        $j_day_no = $j_day_no % 12053; 

        $jy = 979+33*$j_np+4*$this->div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */ 

        $j_day_no %= 1461; 

        if ($j_day_no >= 366) { 
            $jy += $this->div($j_day_no-1, 365); 
            $j_day_no = ($j_day_no-1)%365; 
        } 

        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) 
            $j_day_no -= $j_days_in_month[$i]; 
        $jm = $i+1; 
        $jd = $j_day_no+1; 

        return array($jy, $jm, $jd); 
    } 


    public function jalali_to_gregorian($j_y, $j_m, $j_d) 
    { 
        $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
        $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);


        $jy = $j_y-979; 
        $jm = $j_m-1; 
        $jd = $j_d-1; 

        $j_day_no = 365*$jy + $this->div($jy, 33)*8 + $this->div($jy%33+3, 4); 
        for ($i=0; $i < $jm; ++$i) 
            $j_day_no += $j_days_in_month[$i]; 

        $j_day_no += $jd; 

        $g_day_no = $j_day_no+79; 

        $gy = 1600 + 400*$this->div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */ 
        $g_day_no = $g_day_no % 146097; 

        $leap = true; 
        if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */ 
        { 
            $g_day_no--; 
            $gy += 100*$this->div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */ 
            $g_day_no = $g_day_no % 36524; 

            if ($g_day_no >= 365) 
                $g_day_no++; 
            else 
                $leap = false; 
        } 

        $gy += 4*$this->div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */ 
        $g_day_no %= 1461; 

        if ($g_day_no >= 366) { 
            $leap = false; 

            $g_day_no--; 
            $gy += $this->div($g_day_no, 365); 
            $g_day_no = $g_day_no % 365; 
        } 

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++) 
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap); 
        $gm = $i+1; 
        $gd = $g_day_no+1; 

        return array($gy, $gm, $gd); 
        }

}
