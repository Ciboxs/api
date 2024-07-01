<?php




# -- проверка правильности формата даты
# dateFormatValid('1990-01-01')
# dateFormatValid('1990-01-01 09:30:00')
if (!function_exists('dateFormatValid')) {
    function dateFormatValid($date, $pattern = 'Y-m-d'): bool
    {
        # datetime pattern: Y-m-d H:i:s
        return (\DateTime::createFromFormat($pattern, $date) !== false);
    }
}


# -- процент
# percent($price, 10)
if (!function_exists('percent')) {
    function percent(float $price, float $percent): int | float
    {
        if (!$price || !$percent) {
            return 0;
        }
        return $price * ($percent / 100);
    }
}


?>