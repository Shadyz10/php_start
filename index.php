<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

// Получение полного имени из ФИО

function getFullnameFromParts($surname, $name, $patronymic){
    return "$surname $name $patronymic";
};

// Получение частей ФИО из полного имени

function getPartsFromFullname($fullname){
    $parts = explode(' ', $fullname);
    return [
        'surname'=> $parts[0],
        'name'=> $parts[1],
        'patronymic'=> $parts[2],
    ];
};

foreach ($example_persons_array as $person){
    $parts = getPartsFromFullname($person['fullname']);
    $fullname = getFullnameFromParts($parts['surname'], $parts['name'], $parts['patronymic']);
};

// Сокращение до фамилии и инициала имени

function getShortName($fullname){
    $short = explode(' ', $fullname);
    $shortSurname = $short[0];
    $shortName = mb_substr($short[1], 0, 1) . '.' ;
    return $shortSurname . ' ' . $shortName;
};

foreach ($example_persons_array as $person){
    $shortName = getShortName($person['fullname']);
};

// Определение пола по ФИО

function getGenderFromName($fullname){
    $parts = getPartsFromFullname($fullname);
    $gender = 0;
    if (mb_substr($parts['patronymic'], -3, 3) === 'вна'){
        $gender--;
    }
    if (mb_substr($parts['name'], -1, 1) === 'а'){
        $gender--;
    }
    if (mb_substr($parts['surname'], -2, 2) === 'ва'){
        $gender--;
    };
    
    // мужской пол
    if (mb_substr($parts['patronymic'], -2, 2) === 'ич'){
        $gender++;
    };
    if (mb_substr($parts['name'], -1, 1) === 'й' || 'н'){
        $gender++;
    };
    if (mb_substr($parts['surname'], -1, 1) === 'в'){
        $gender++;
    };
    
    if($gender > 0){
        return 1; // мужской пол
    } else if ($gender < 0){
        return -1; // женский пол
    } else {
        return 0; // Неопределенный пол
    }
    
};

// Определение пола

function getGenderDescription($persons_array){
    $count = count($persons_array);
    $maleCount = 0;
    $femaleCount = 0;
    $undefinedCount = 0;
    
    foreach($persons_array as $person){
        $gender = getGenderFromName($person['fullname']);
        if($gender === 1){
            $maleCount++;
        } else if ($gender === -1){
            $femaleCount++;
        } else {
            $undefinedCount++;
        }
    }

    // Получение кол-ва в процентах

    $totalMale = round(($maleCount / $count) * 100, 1);
    $totalFemale = round(($femaleCount / $count) * 100, 1);
    $totalUndefined = round(($undefinedCount / $count) * 100, 1);
    
    $result = "Гендерный состав аудитории:\n";
    $result .= "---------------------------\n";
    $result .= "Мужчины - $totalMale%\n";
    $result .= "Женщины - $totalFemale%\n";
    $result .= "Не удалось определить - $totalUndefined%\n";

    return $result;
};

// Поиск идеального партнера

function getPerfectPartner($surname, $name, $patronymic, $persons_array){
    $transformSurname = mb_convert_case($surname, MB_CASE_TITLE);
    $transformName = mb_convert_case($name, MB_CASE_TITLE);
    $transformPatronymic = mb_convert_case($patronymic, MB_CASE_TITLE);
    
    $fullname = getFullnameFromParts($transformSurname, $transformName, $transformPatronymic);
    $shortFullname = getShortName($fullname);
    $gender = getGenderFromName($fullname);
    
    $partners = array_filter($persons_array, function ($person) use ($gender){
        $personGender = getGenderFromName($person['fullname']);
        return $personGender === -$gender;
    });
    $randPartner = $partners[array_rand($partners)];
    $compatibility_percentage = round(mt_rand(5000, 10000) / 100, 2);
    $partnerShortName = getShortName($randPartner['fullname']);
    
    $result = $shortFullname . ' + ' . $partnerShortName . ' =' . PHP_EOL;
    $result .= '♡ Идеально на ' . $compatibility_percentage . '% ♡';
    return $result;
};