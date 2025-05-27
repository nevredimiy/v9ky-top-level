<?php



/**
 * Функция дополняет первый массив. все массивы вида [ player_id => [ matches_id => [ name_static => value_static, ... all_statics ] ] ]
 * @param array - массив из БД со статистикой без учета абитых голов
 * @param array - массив и БД только с забитыми голами
 * @return array - массив со всей статистикой.
 */
function megreTwoMainArrays($firstArray, $secondArray, $column = 'count_goals'){

  foreach ($secondArray as $playerId => $matches) {
    if (!isset($firstArray[$playerId])) {
        // Если игрок отсутствует в первом массиве
        $firstArray[$playerId] = array();
    }

    foreach ($matches as $matchId => $stats) {
        if (isset($firstArray[$playerId][$matchId])) {
            // Если матч присутствует у игрока
            $firstArray[$playerId][$matchId][$column] = $stats[$column];
        } else {
            // Если матч отсутствует, создаем с $column = 0
            $firstArray[$playerId][$matchId] = array(
              $column => 0,
            );
        }
    }
  }

  // Проверка для игроков из первого массива, у которых отсутствует $column
  foreach ($firstArray as $playerId => &$matches) {
      foreach ($matches as $matchId => &$matchStats) {
          if (!isset($matchStats[$column])) {
              $matchStats[$column] = 0;
          }
      }
  }
  unset($matches, $matchStats);

  return $firstArray;

}

/**
 * Преобразует массив и сортирует по убыванию по критерию $keySort. Для всех рубрик Топ.
 * @param array - Статистика игроков. Массив вида [ player_id => [match_id => [ name_static => value ] ] ]
 * @param array - Данные игроков. Массив вида [ player_id => [ name_data => value ] ]
 * @param string - $keySort определяет одну и восьми номинаций.
 * @param int - для сортировки в таблице в случае одинаковых начений и количество матчей
 * @return array - 
 */
function getTopPlayers($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0){  

   // Преобразование массива
  $topPlayers = [];

  foreach ($allStaticPlayers as $playerId => $matches) {
      $matchCount = count($matches); // Количество матчей
      $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сумма всех значений по ключу $keySort
      $countGoals = array_sum(array_column($matches, 'count_goals'));
      $countAsists = array_sum(array_column($matches, 'count_asists'));
      $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
      $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
      $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
      $countRed_cards = array_sum(array_column($matches, 'red_cards'));

      $matchIdKeys = array_keys($matches);
      // Ищем количество лучших игроков матча.
      $countBPM = array_count_values( array_column( $matches, 'count_best_player_of_match' ) );
      $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;
      
      
      
      
      // Инициализируем строку для таблицы. Для Бомбардиров и Асистентов
      if(!is_array($totalKeySort)) {
        
        $keySortPerMatch = $matchCount > 0 ? $totalKeySort / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
            'player_id' => $playerId,
            'match_count' => $matchCount,
            'total_key' => $totalKeySort,
            'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
            'match_ids' => implode(" ", $matchIdKeys),
 
            'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
            'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
            'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
            'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
            'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
            'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',

            'count_goals' => $countGoals,
            'count_asists' => $countAsists,
            'golevoypas' => $countGolevoypas, 
            'yellow_cards' => $countYellowCards,
            'yellow_red_cards' => $countYellowRedCards,
            'red_cards' => $countRed_cards,
            'count_best_player_of_match' => $countBestPlayerOfMatch,
        ];

      } 
      
      // Дріблінг
      if ($keySort == "dribling" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'obvodkaplus' => $totalKeySort['obvodka_plus'],
          'obvodkaminus' => $totalKeySort['obvodka_minus'],

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
        ];

        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ])) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'obvodkaplus' ] - $stats[ 'obvodkaminus' ];
          }

        }
        
      }

      // Удар
      if ($keySort == "udar" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'udarplus' => $totalKeySort['udar_plus'],
          'udarminus' => $totalKeySort['udar_minus'],

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
        ];

        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if( isset( $stats[ 'tur' ] ) ) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'vstvor' ] - $stats[ 'mimo' ];
          }
        }
        
      }

      // Пас
      if ($keySort == "pas" && is_array($totalKeySort)) {

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'obvodkaplus' => isset($totalKeySort['obvodka_plus']) ? $totalKeySort['obvodka_plus'] : 0,
          'obvodkaminus' => isset($totalKeySort['obvodka_minus']) ? $totalKeySort['obvodka_minus'] : 0,
          'zagostrennia' => isset($totalKeySort['zagostrennia']) ? $totalKeySort['zagostrennia'] : 0,
          'pasplus' => isset($totalKeySort['pasplus']) ? $totalKeySort['pasplus'] : 0,
          'pasminus' => isset($totalKeySort['pasminus']) ? $totalKeySort['pasminus'] : 0,

          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
        ];
        
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ])){
            $row["match_{$stats[ 'tur' ]}_key"] = ($stats[ 'zagostrennia' ] * 5 + $stats[ 'pasplus' ]) - $stats[ 'pasminus' ] * 3;
          }
        }
        
      }      

      // Асист
      if( $keySort == 'count_goals' || $keySort == 'golevoypas' || $keySort == 'count_asists'){
        
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if(isset($stats[ 'tur' ] )) {
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ $keySort ];         
          }
        }

      }

      // Захисник
      if( $keySort == 'zahusnuk') {
        // Добавляем значение для каждого матча
        foreach ($matches as $matchId => $stats) {
          if( isset( $stats[ 'tur' ] ) ) {            
            $row["match_{$stats[ 'tur' ]}_key"] = $stats[ 'otbor' ] + $stats[ 'blok' ];                            
          }
        }
      }


      // Топ гравець
      if( $keySort == 'topgravetc' ) {
        
        // Добавляем значение для каждого матча
        $i = 1;
        foreach ($matches as $matchId => $stats) {
          if(isset($stats['tur'])){
            $row["match_{$stats[ 'tur' ]}_key"] = 
            $stats['count_goals'] * 15 
            + $stats['count_asists'] * 10
            + $stats['zagostrennia'] * 10
            + $stats['pasplus'] * 3 
            - $stats['pasminus'] * 3 
            - $stats['vtrata'] * 3
            + $stats['vstvor'] * 7 
            - $stats['mimo'] * 4 
            + $stats['obvodkaplus'] * 5
            - $stats['obvodkaminus'] * 3 
            + $stats['otbor'] * 8 
            - $stats['otbormin'] * 5
            + $stats['blok'] * 4 
            + $stats['seyv'] * 15 
            - $stats['seyvmin'] * 7;
          }

            $i++;
        }

      }

      // Тренер
      if( $keySort == 'trainer' ){

        $row = [
          'player_id' => $playerId,
          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
          'amplua' => isset($dataAllPlayers[$playerId]['amplua']) ? $dataAllPlayers[$playerId]['amplua'] : '',
        ];

      }

      // Голкіпер      
      if ($keySort == "golkiper" && is_array($totalKeySort)) {

        if(($totalKeySort['seyv'] + $totalKeySort['seyvmin']) > 10) {
          continue;
        }

        $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Среднее значений по ключу $keySort за матч
        
        $row = [
          'player_id' => $playerId,
          'match_count' => $matchCount,
          'total_key' => $totalKeySort['total_value'],
          'key_per_match' => round($keySortPerMatch, 2), // Округляем до 2 знаков
          'seyv' => $totalKeySort['seyv'],
          'seyvmin' => $totalKeySort['seyvmin'],
          'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
          'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
          'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
          'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
          'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
          'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
        ];

        // Добавляем значение для каждого матча        
        foreach ($matches as $matchId => $stats) {
          $seyv = isset($stats['seyv']) ? $stats['seyv'] : 0;
          $seyvmin = isset($stats['seyvmin']) ? $stats['seyvmin'] : 0;
          $denominator = $seyv + $seyvmin; // Знаменатель
          
          if(isset($stats[ 'tur' ])) {
            $row["match_{$stats[ 'tur' ]}_key"] = $denominator == 0
              ? 0 
              : round(( 100 / $denominator ) * $stats[ 'seyv' ], 1);
          }
        }

        
        
      }
      
      $topPlayers[] = $row;
  }
    
  // Сортируем игроков
  usort($topPlayers, function ($a, $b) use ($lastTur) {
    // 1. Сортировка по (total_key)
    if ($a['total_key'] != $b['total_key']) {
        return ($b['total_key'] > $a['total_key']) ? 1 : -1; // По убыванию
    }

    // 2. Сортировка по «Матчів» (count_matches)
    if ($a['match_count'] != $b['match_count']) {
        return ($b['match_count'] > $a['match_count']) ? 1 : -1; // По убыванию
    }
    // 3. Сортировка по последнему сыгранному матчу (total_3_match)
    if(isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {

      if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
          return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1; // По убыванию
      }

    }

    // Если все значения равны, оставить текущий порядок
    return 0;
  });

  // Присваиваем позиции
  $rank = 1; // Начальный порядковый номер
  foreach ($topPlayers as $index => &$player) {

    // если в последнем туре не играли оба сравниваемых игрока
    if( isset( $topPlayers[$index - 1]["match_{$lastTur}_key"] ) && isset( $player["match_{$lastTur}_key"] ) ) {
      
      // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
      if (
          $index > 0 &&
          $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
          $topPlayers[$index - 1]['match_count'] === $player['match_count'] &&
          $topPlayers[$index - 1]["match_{$lastTur}_key"] === $player["match_{$lastTur}_key"]
      ) {
          $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
      } else {
          $player['rank'] = $rank; // Новый ранг
      }

    } else {
      // Если это не первый игрок и текущий игрок имеет те же значения, что и предыдущий
      if (
        $index > 0 &&
        $topPlayers[$index - 1]['total_key'] === $player['total_key'] &&
        $topPlayers[$index - 1]['match_count'] === $player['match_count']
      ) {
          $player['rank'] = isset( $topPlayers[$index - 1]['rank'] ) ? $topPlayers[$index - 1]['rank'] : $rank; // Присваиваем тот же ранг
      } else {
          $player['rank'] = $rank; // Новый ранг
      }
    }
      $rank++; // Увеличиваем счетчик
  }

  

  return $topPlayers;
}

/**
 * Расчитывает рейтинг игрока по заданному критерию. Для рубрик Топ-Бомбардир, Топ-Ассистент и т.д.
 * @param string
 * @param array
 * @return integer|array
 */
function calculateArrayByColumn($column, $array) {

  if (empty($array)) {
    throw new Exception("The input array is empty");
  }

  $totalValue = 0;
  if ( $column == 'count_goals' || $column == 'golevoypas' || $column == 'count_asists' ){
    $totalValue = array_sum(array_column($array, $column));
  }

  if( $column == 'zahusnuk' ) {
    $totalOtbor = array_sum(array_column($array, 'otbor'));
    $totalBlok = array_sum(array_column($array, 'blok'));
    $totalValue = $totalOtbor + $totalBlok;
  }

  if( $column == "dribling") {
    $totalObvodkaplus = array_sum(array_column($array, 'obvodkaplus'));
    $totalObvodkaminus = array_sum(array_column($array, 'obvodkaminus'));
    $totalValue = $totalObvodkaplus - $totalObvodkaminus;
    $totalValue = ['total_value' => $totalValue, 'obvodka_plus' => $totalObvodkaplus, 'obvodka_minus' => $totalObvodkaminus];
  }

  if( $column == "udar") {
    $totalObvodkaplus = array_sum(array_column($array, 'vstvor'));
    $totalObvodkaminus = array_sum(array_column($array, 'mimo'));
    $totalValue = $totalObvodkaplus - $totalObvodkaminus;
    $totalValue = ['total_value' => $totalValue, 'udar_plus' => $totalObvodkaplus, 'udar_minus' => $totalObvodkaminus];
  }

  if( $column == "pas") {
    $totalZagostrennia = array_sum(array_column($array, 'zagostrennia'));
    $totalPasplus = array_sum(array_column($array, 'pasplus'));
    $totalPasminus = array_sum(array_column($array, 'pasminus'));
    $totalValue = ( $totalZagostrennia * 5 + $totalPasplus ) - $totalPasminus * 3 ;
    $totalValue = ['total_value' => $totalValue, 'zagostrennia' => $totalZagostrennia, 'pasplus' => $totalPasplus, 'pasminus' => $totalPasminus];
  }

  if( $column == "golkiper") {
    $totalSeyv = array_sum(array_column($array, 'seyv'));
    $totalSeyvmin = array_sum(array_column($array, 'seyvmin'));
    $totalValue = $totalSeyv + $totalSeyvmin == 0 ? 0 : 100 / ( $totalSeyv + $totalSeyvmin ) * $totalSeyv ;
    $totalValue = ['total_value' => round($totalValue, 1), 'seyv' => $totalSeyv, 'seyvmin' => $totalSeyvmin];
  }

  if( $column == "topgravetc") {
    $totalGoals = array_sum(array_column($array, 'count_goals'));
    $totalGolevoypas = array_sum(array_column($array, 'golevoypas'));
    $totalZagostrennia = array_sum(array_column($array, 'zagostrennia'));
    $totalPasplus = array_sum(array_column($array, 'pasplus'));
    $totalPasminus= array_sum(array_column($array, 'pasminus'));
    $totalVtrata = array_sum(array_column($array, 'vtrata'));
    $totalVstvor = array_sum(array_column($array, 'vstvor'));
    $totalMimo = array_sum(array_column($array, 'mimo'));
    $totalObvodkaplus = array_sum(array_column($array, 'obvodkaplus'));
    $totalObvodkaminus = array_sum(array_column($array, 'obvodkaminus'));
    $totalOtbor = array_sum(array_column($array, 'otbor'));
    $totalOtbormin = array_sum(array_column($array, 'otbormin'));
    $totalBlok = array_sum(array_column($array, 'blok'));
    $totalSeyv = array_sum(array_column($array, 'seyv'));
    $totalSeyvmin = array_sum(array_column($array, 'seyvmin'));

    $totalValue = $totalGoals * 15 
      + $totalGolevoypas * 10 
      + $totalZagostrennia * 10
      + $totalPasplus * 3 
      - $totalPasminus * 3 
      - $totalVtrata * 3 
      + $totalVstvor * 7 
      - $totalMimo * 4 
      + $totalObvodkaplus * 5 
      - $totalObvodkaminus * 3 
      + $totalOtbor * 8 
      - $totalOtbormin * 5 
      + $totalBlok * 4 
      + $totalSeyv * 15 
      - $totalSeyv * 7;
  }
  
  return $totalValue;
}

/**
 * Находит сумму статистики всех игроков в команде по одному показателю. Например, сума забитых голов в команде.
 * @param array - массив статистики по заданному показателю. Реультат функции getTopPlayers() - Например, TopBombardir, можно любой топ
 * @param int - идентификато команды
 * @return int 
 */
function getTotalStaticByTeam($staticPlayers, $teamId, $column = 'total_key'){

  $countStatic = 0;
  foreach ($staticPlayers as $player) {
    if ($player['team_id'] === $teamId){
        $countStatic += $player[$column];
      }
  }
  return $countStatic;
}

/**
 * находит лучший показатель команды/игрока в рейтинге. Место в рейтинге. Например лучший бомбардир в команде занимает 5 место. Все остальные ниже. Выводится цыфра 5
 * @param array - массив статистики по заданому показателю. Реультат функции getTopPlayers(). Например, TopBombardir
 * @param int - идентификато команды или игрока
 * @param string - ключ элемента искомого поля. Если это колманда - team_id, если это игрок - player_id
 * @return int 
 */
function getBestPlayer($staticPlayers, $id, $column = 'team_id') {
  
  $position = 1000; // сппецально задано большое число для поииска найменьшего

  // Фильтрация массива для получения позиций
  $filteredKeys = array_keys(array_column($staticPlayers, $column), $id);


  // Проверка наличия элемента
  if (!empty($filteredKeys)) {
      // $position = array_search($filteredKeys[0], array_keys($staticPlayers));
      $position = $filteredKeys[0] + 1;
  } 

  if ($column == 'player_id' && $position == 1000){
    $position = 0;
  }

  return $position;

}


/**
 * определяет лучший показатель игрока и 8 номинаций. 
 * @param int
 * @return int
 */
function getCategoryPlayerBest ($player_id) {
  // Отсортированный массив по рубрике Топ-Бомбардир
  $topBombardi = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_goals');

  // Отсортированный массив по рубрике Топ-Асистент
  $topAsists = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golevoypas');

  // Отсортированный массив по рубрике Топ-Захистник
  $topZhusnuk = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'zahusnuk');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topUdar = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'udar');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topGolkiper = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golkiper');

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc');

  // Лучшие в команде 
  $bestGravetc = getBestPlayer($topGravetc, $player_id);
  $bestGolkiper = getBestPlayer($topGolkiper, $player_id);
  $bestBombardi = getBestPlayer($topBombardi, $player_id);
  $bestAssist = getBestPlayer($topAsists, $player_id);
  $bestZhusnuk = getBestPlayer($topZhusnuk, $player_id);
  $bestDribling = getBestPlayer($topDribling, $player_id);
  $bestUdar = getBestPlayer($topUdar, $player_id);
  $bestPas = getBestPlayer($topPas, $player_id);

  $arr = [$bestGravetc, $bestGolkiper, $bestBombardi, $bestAssist, $bestZhusnuk, $bestDribling, $bestUdar, $bestPas ];

  

  // Найти наименьшее число
  $minValue = min($arr);

  // Найти индекс наименьшего числа
  $minIndex = array_search($minValue, $arr);

  return $minIndex;

}

/**
 * Получение индивидуальной статистики игрока по всем матчам турнира (лиги)
 * @param array Статистика игроков. Массив вида [ player_id => [match_id => [ name_static => value ] ] ]
 * @param string - идентификатор игрока
 * @return array - Массив со всей статистикой игрока
 */
function getIndStaticPlayer($allStaticPlayers, $player_id){

  $indStatPlayer = [];

  if(isset($allStaticPlayers[$player_id]) && is_array($allStaticPlayers[$player_id])) {
    
    $countGoals = 0;
    // Иконка зведочка
    $countNominationPlayerOfMatch = 0;

    $countBestPlayerOfMatch = 0;
    
    // Иконка футболка
    // $countInTour = 0;

    // Иконка бутса
    $countAsists = 0;
    // Иконка поле
    $countMatches = 0;
    $yellowCards = 0;
    $yellowRedCards = 0;
    $redCards = 0;

    // Точность удара
    $accuracyOfKicking = 0;
    //Точность паса
    $accuracyOfPassing = 0;
    //Удачные обводки
    $accuracyOfDribbles = 0;
    //Количество обострений за матч
    $countOfAggravations = 0;
    //Количество отборов
    $accuracyOfTackles = 0;

    $vstvor = 0;
    $mimo = 0;
    $pasplus = 0;
    $pasminus = 0;
    $obvodkaplus = 0;
    $obvodkaminus = 0;
    $zagostrennia = 0;
    $otbor = 0;
    $blok = 0;


    foreach($allStaticPlayers[$player_id] as $matches => $stats){      

        // Индивид. статистика игрока. Для карточки игрока где иконки, мяч, звездочка и т.д.
        // Иконка мяч
        $countGoals += $stats['count_goals'];
        // Иконка зведочка
        $countBestPlayerOfMatch += isset($stats['count_best_player_of_match']) && $stats['count_best_player_of_match'] == $player_id ? 1 : 0;

        // Иконка футболка
        // $countInTour += isset($stats['count_in_tour']) ? $stats['count_in_tour'] : 0;

        // Иконка бутса
        $countAsists += $stats['count_asists'];
        // иконка поле
        $countMatches++;
        $yellowCards += $stats['yellow_cards'];
        $yellowRedCards += $stats['yellow_red_cards'];
        $redCards += $stats['red_cards'];
        
        // Точность удара
        if ( isset($stats['vstvor']) && isset($stats['mimo'])) {
          $vstvor += $stats['vstvor'];
          $mimo += $stats['mimo'];
          $accuracyOfKicking = $vstvor + $mimo == 0 ? 0 : ( 100 / ( $vstvor + $mimo ) ) * $vstvor ;
        }

        //Точность паса
        if ( isset($stats['pasplus']) && isset($stats['pasminus'])) {
          $pasplus += $stats['pasplus'];
          $pasminus += $stats['pasminus'];
          $accuracyOfPassing = $pasplus +  $pasminus == 0 ? 0 : ( 100 / ( $pasplus + $pasminus ) ) * $pasplus ;
        }

        //Удачные обводки
        if ( isset($stats['obvodkaplus']) && isset($stats['obvodkaminus'])) {
          $obvodkaplus += $stats['obvodkaplus'];
          $obvodkaminus += $stats['obvodkaminus'];
          $accuracyOfDribbles = $obvodkaplus +  $obvodkaminus == 0 ? 0 : ( 100 / ( $obvodkaplus + $obvodkaminus ) ) * $obvodkaplus ;
        }

        //Количество обострений за матч
        if ( isset($stats['zagostrennia'])) {
          $zagostrennia += $stats['zagostrennia'];
          $countOfAggravations = number_format( round( $zagostrennia / $countMatches ), 1 );
        }

        //Количество отборов
        if ( isset($stats['otbor']) && isset($stats['blok'])) {
          $otbor += $stats['otbor'];
          $blok += $stats['blok'];
          $accuracyOfTackles = number_format( round( ( $otbor +  $blok ) / $countMatches, 1 ), 1  );
        }
      }
      
    } 

    $indStatPlayer = [
      'count_goals' => isset($countGoals) && $countGoals != '' ? $countGoals : 0,
      'count_best_player_of_match' => isset($countBestPlayerOfMatch) && $countBestPlayerOfMatch > 0 ? $countBestPlayerOfMatch : 0,
      // 'count_in_tour' => $countInTour != '' ? $countInTour : 0,
      'count_asists' => isset($countAsists) && $countAsists != '' ? $countAsists : 0,
      'count_matches' => isset($countMatches) && $countMatches != '' ? $countMatches : 0,
      'yellow_cards' => isset($yellowCards) && $yellowCards != '' ? $yellowCards : 0,
      'yellow_red_cards' => isset($yellowRedCards) && $yellowRedCards != '' ? $yellowRedCards : 0,
      'red_cards' => isset($redCards) && $redCards != '' ? $redCards : 0,
      'accuracy_of_kicking' => isset($accuracyOfKicking) ? round($accuracyOfKicking, 1, PHP_ROUND_HALF_UP) : 0,
      'accuracy_of_passing' => isset($accuracyOfPassing) ? round($accuracyOfPassing, 1, PHP_ROUND_HALF_UP) : 0,
      'accuracy_of_dribbles' => isset($accuracyOfDribbles) ? round($accuracyOfDribbles, 1, PHP_ROUND_HALF_UP) : 0,
      'count_of_aggravations' => isset($countOfAggravations) && $countOfAggravations != '' ? $countOfAggravations : 0,
      'accuracy_of_tackles' => isset($accuracyOfTackles) && $accuracyOfTackles != '' ? $accuracyOfTackles : 0,
    ];

  return $indStatPlayer;

}

/**
 * Возвращает статистику игрока по матчу в одной из восьми рубрик. Для отображения таблицы Топ-игроков.
 * @param integer
 * @param integer
 * @param integer|string
 * @param string
 * @return string
 */

function checkingCurrentTur( $indexIteration, $lastTur=0, $totalValue=0, $sufix='' ){
  // если матч состоялся
  if($indexIteration <= $lastTur) {
    // возвращаем значение или пропуск
    return $totalValue ? $totalValue . $sufix : "-";
    // если матча еще не было
  } else {
    return '?';
  }
  
}

/**
 * 
 */

 function getBestPlayerOfTur($allStaticPlayers, $lastTur, $teamId){

  $countInTour = [];
  $playerOfTur = [];

  for( $i = 1; $i <= $lastTur; $i++ ){
    foreach ( $allStaticPlayers as $player_id => $match ){
      foreach ( $match as $match_id => $value ){
        if ( $value['tur'] == $i) {
          $countInTour[] = $value;
          
        }
      }
    }
  
    usort($countInTour, function ($a, $b) {
      // Сортировка по (total_key)
      if ($a['count_goals'] != $b['count_goals']) {
          return ($b['count_goals'] > $a['count_goals']) ? 1 : -1; // По убыванию
      }
  
      // Если все значения равны, оставить текущий порядок
      return 0;
    });
    $playerOfTur[] = $countInTour[0];
  }
  
  $arrK = 1000;
  foreach($playerOfTur as $key => $value){
    if($value['team'] == $teamId){
      $arrK = $key;
    }
  }
 
  if($arrK != 1000) {
    return $playerOfTur[$arrK];
  }  

  return $playerOfTur;

 }

 /**
  * Переводит месяц и день недели с англ на укр
  * 
  */
  function date_translate($date){

    $translate = array(
      "Monday" => "Понеділок",
      "Mon" => "Пн",
      "Tuesday" => "Вівторок",
      "Tue" => "Вт",
      "Wednesday" => "Середа",
      "Wed" => "Ср",
      "Thursday" => "Четвер",
      "Thu" => "Чт",
      "Friday" => "П'ятниця",
      "Fri" => "Пт",
      "Saturday" => "Субота",
      "Sat" => "Сб",
      "Sunday" => "Неділя",
      "Sun" => "Нд",
      "January" => "Січня",
      "Jan" => "Січ",
      "February" => "Лютого",
      "Feb" => "Лют",
      "March" => "Березня",
      "Mar" => "Бер",
      "April" => "Квітня",
      "Apr" => "Кві",
      "May" => "Травня",
      "June" => "Червня",
      "Jun" => "Чер",
      "July" => "Липня",
      "Jul" => "Лип",
      "August" => "Серпня",
      "Aug" => "Сер",
      "September" => "Вересня",
      "Sep" => "Вер",
      "October" => "Жовтня",
      "Oct" => "Жов",
      "November" => "Листопада",
      "Nov" => "Лис",
      "December" => "Грудня",
      "Dec" => "Гру",
      "st" => "ое",
      "nd" => "ое",
      "rd" => "е",
      "th" => "ое"
      );

    // Проверяем, есть ли месяц или день недели в массиве
    if (array_key_exists($date, $translate)) {
        return $translate[$date];
    } else {
        return "Месяц не найден"; // Если месяц не найден
    }

  }

  /**
   * 
   */
  function getCountMatchesOfTurnir($allStaticPlayers, $teamId) {

    $countMatches = 0;
    foreach($allStaticPlayers as $matches){
      foreach ($matches as $staticMatch) {
        if($staticMatch['team'] == $teamId) {
          if($staticMatch['tur'] > $countMatches){
            $countMatches = $staticMatch['tur'];
          }
        }
      }
    }
    return $countMatches;
  }

  /**
   * Определяет "сборную тура" по номеру тура
   */
  function getPlayersOfTur( $allStaticPlayers, $tur ){
    
    //  Массив игроков текущего тура
    $playerOfTur = [];

    // Массив для лучших игроков тура
    $bestPlayer = [];
    
    // --- Основной массив ---
    // Находим игроков текущего тура и записываем в массив с который будем фильтровать для 8 рубрик
    foreach( $allStaticPlayers as $matches ){
      foreach ( $matches as $staticMatch ) {
        if( $staticMatch['tur'] == $tur ){
          $playerOfTur[] = $staticMatch;
        }        
      }
    }

    // добавление элемента с ключом 'player_total' в массив $playerOfTur
    foreach ($playerOfTur as $key => $item) {
      $playerOfTur[$key]['player_total'] = $item['count_goals'] * 15 
      + $item['count_asists'] * 10 
      + $item['zagostrennia'] * 10
      + $item['pasplus'] * 3 
      - $item['pasminus'] * 3 
      - $item['vtrata'] * 3 
      + $item['vstvor'] * 7 
      - $item['mimo'] * 4 
      + $item['obvodkaplus'] * 5 
      - $item['obvodkaminus'] * 3 
      + $item['otbor'] * 8 
      - $item['otbormin'] * 5 
      + $item['blok'] * 4 
      + $item['seyv'] * 15 
      - $item['seyvmin'] * 7;
    }
  

    // --- Топ Игрок ---
    // Получаем массив лучших 
    $topgravetcs = getBestPlayers($playerOfTur, 'topgravetc');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($topgravetcs)) {
      $maxPlayerTotal = max(array_column($topgravetcs, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $topgravetcs = array_filter($topgravetcs, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });


    // берем только первого бомбардира
    $topgravetcs = array_slice($topgravetcs, 0, 1);

    // Результат записываем в основной массив
    foreach ($topgravetcs as $topgravetc) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($topgravetc['count_points'] > 0) {
        $bestPlayer[] = $topgravetc;
      }
    }

    

    // --- Голкипер ---
    // Получаем массив лучших 
    $golkipers = getBestPlayers($playerOfTur, 'golkiper');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($golkipers)) {
      $maxPlayerTotal = max(array_column($golkipers, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $golkipers = array_filter($golkipers, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого бомбардира
    $golkipers = array_slice($golkipers, 0, 1);
   
    // Результат записываем в основной массив
    foreach ($golkipers as $golkiper) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($golkiper['count_points'] > 0) {
        $bestPlayer[] = $golkiper;
      }
    }


    // --- Бомбардир ---
    // Находим максимальное значение count_goals. 
    if(empty($playerOfTur)){
      $maxGoals = 0;
    } else {
      $maxGoals = max(array_column($playerOfTur, 'count_goals'));
    }
    
    // Отбираем все элементы с максимальным значением count_goals
    $bombardirs = array_filter($playerOfTur, function ($item) use ($maxGoals) {
        return $item['count_goals'] == $maxGoals;
    });

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($bombardirs)) {
      $maxPlayerTotal = max(array_column($bombardirs, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $bombardirs = array_filter($bombardirs, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });

    // Добавляем игроку ключ что он лучший в туре бомбардир
    foreach($bombardirs as $key => $res){
      $bombardirs[$key]['best_player'] = 'bombardir';
      $bombardirs[$key]['count_points'] = $res['count_goals'];
    }

    // берем только первого бомбардира
    $bombardirs = array_slice($bombardirs, 0, 1);
    
    // Результат записываем в основной массив
    foreach ($bombardirs as $bombardir) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($bombardir['count_points'] > 0) {
        $bestPlayer[] = $bombardir;
      }
    }    

    // --- Асистент ---
    // Находим максимальное значение count_asists. 
    if(empty($playerOfTur)){
      $maxAsist = 0;
    } else {
      $maxAsist = max(array_column($playerOfTur, 'count_asists'));
    }

    // Отбираем все элементы с максимальным значением count_goals
    $asists = array_filter($playerOfTur, function ($item) use ($maxAsist) {
        return $item['count_asists'] == $maxAsist;
    });

   
    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($asists)) {
      $maxPlayerTotal = max(array_column($asists, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $asists = array_filter($asists, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });

    // Добавляем игроку ключ что он лучший в туре бомбардир
    foreach($asists as $key => $res){
      $asists[$key]['best_player'] = 'asistent';
      $asists[$key]['count_points'] = $res['count_asists'];
    }

    // берем только первого ассиста
    $asists = array_slice($asists, 0, 1);

    // Результат записываем в основной массив
    foreach ($asists as $asist) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($asist['count_points'] > 0) {
        $bestPlayer[] = $asist;
      }
    }

    // --- Захисник ---
    // Получаем массив лучших защитников
    $zahusnuks = getBestPlayers($playerOfTur, 'zahusnuk');

     // Находим максимальное значение player_total среди отобранных бомбардиров
     if (!empty($zahusnuks)) {
      $maxPlayerTotal = max(array_column($zahusnuks, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $zahusnuks = array_filter($zahusnuks, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });

    
    // берем только первого ассиста
    $zahusnuks = array_slice($zahusnuks, 0, 1);

    // Результат записываем в основной массив
    foreach ($zahusnuks as $zahusnuk) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($zahusnuk['count_points'] > 0) {
        $bestPlayer[] = $zahusnuk;
      }
    }

    // --- Дриблинг ---
    // Получаем массив лучших 
    $driblings = getBestPlayers($playerOfTur, 'dribling');

     // Находим максимальное значение player_total среди отобранных бомбардиров
     if (!empty($driblings)) {
      $maxPlayerTotal = max(array_column($driblings, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $driblings = array_filter($driblings, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });


    // Результат записываем в основной массив
    foreach ($driblings as $dribling) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($dribling['count_points'] > 0) {
        $bestPlayer[] = $dribling;
      }
    }

    // --- Удар ---
    // Получаем массив лучших 
    $udars = getBestPlayers($playerOfTur, 'udar');

    // Находим максимальное значение player_total среди отобранных бомбардиров
    if (!empty($udars)) {
      $maxPlayerTotal = max(array_column($udars, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $udars = array_filter($udars, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого ассиста
    $udars = array_slice($udars, 0, 1);

    // Результат записываем в основной массив
    foreach ($udars as $udar) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($udar['count_points'] > 0) {
        $bestPlayer[] = $udar;
      }
    }

    // --- Пас ---
    // Получаем массив лучших 
    $pases = getBestPlayers($playerOfTur, 'pas');

     // Находим максимальное значение player_total среди отобранных бомбардиров
     if (!empty($pases)) {
      $maxPlayerTotal = max(array_column($pases, 'player_total'));
    } else {
      $maxPlayerTotal = 0;
    }

    // Отбираем всех игроков, у которых и count_goals, и player_total являются максимальными
    $pases = array_filter($pases, function ($item) use ($maxPlayerTotal) {
      return $item['player_total'] == $maxPlayerTotal;
    });

    // берем только первого ассиста
    $pases = array_slice($pases, 0, 1);

    // Результат записываем в основной массив
    foreach ($pases as $pas) {
      // Если у игрока количество очков ноль, то это не топ игрок
      if($pas['count_points'] > 0) {
        $bestPlayer[] = $pas;
      }
    } 

    return $bestPlayer;
  
  }

  
  /**
   * Соединяет массив статистики с данными игрока. Данные это ФИО, фото, навание команды и т. д.
   * @param array - массив статистики
   * @param array - массив данных
   * @return array - объединенный массив
   */
  function mergeStaticAndData($playersOfTur, $dataPlayer){
    $bestPlayer = [];

    foreach($playersOfTur as $playerStats) {
      $playerId = $playerStats['player'];
      if(isset($dataPlayer[$playerId])) {
        $bestPlayer[] = array_merge($playerStats, $dataPlayer[$playerId]);
      } else {
        $bestPlayer[] = $playerStats;
      }
    }

    return $bestPlayer;
  }


  /**
   * Находим лучших игроков тура. Например, защитников, бомбардиров и т.д.
   * @param array - все игроки тура
   * @param string - например zahusnuk, dribling, udar
   * @return array - массив лучших игроков тура по рубрике Захисник
   */
  function getBestPlayers($playerOfTur, $playerRole){
    // Массив для хранения сумм
    $total = [];

    $result = [];

    if($playerRole == 'topgravetc') {
      
      // Шаг 1: Вычисляем тотал для каждого элемента
      foreach ($playerOfTur as $key => $item) {
          $total[$key] = $item['count_goals'] * 15 
          + $item['count_asists'] * 10 
          + $item['zagostrennia'] * 10
          + $item['pasplus'] * 3 
          - $item['pasminus'] * 3 
          - $item['vtrata'] * 3 
          + $item['vstvor'] * 7 
          - $item['mimo'] * 4 
          + $item['obvodkaplus'] * 5 
          - $item['obvodkaminus'] * 3 
          + $item['otbor'] * 8 
          - $item['otbormin'] * 5 
          + $item['blok'] * 4 
          + $item['seyv'] * 15 
          - $item['seyvmin'] * 7;
      }
      
      // Шаг 2: Находим максимальное значение тотала
      if(empty($total)){
        $maxTotal = 0;
      } else {
        $maxTotal = max($total);
      }
  
      // Шаг 3: Отбираем всех игроков, у которых maxTotal равна максимальной
      $result = array_filter($playerOfTur, function ($item) use ($maxTotal) {
          return ( $item['count_goals'] * 15 
          + $item['count_asists'] * 10 
          + $item['zagostrennia'] * 10 
          + $item['pasplus'] * 3 
          - $item['pasminus'] * 3 
          - $item['vtrata'] * 3 
          + $item['vstvor'] * 7 
          - $item['mimo'] * 4 
          + $item['obvodkaplus'] * 5 
          - $item['obvodkaminus'] * 3 
          + $item['otbor'] * 8 
          - $item['otbormin'] * 5 
          + $item['blok'] * 4 
          + $item['seyv'] * 15 
          - $item['seyvmin'] * 7 ) == $maxTotal;
      });
  
      // Добавляем игроку ключ что он лучший в туре
      foreach($result as $key => $res){
        $result[$key]['best_player'] = 'topgravetc';
        $result[$key]['count_points'] = $maxTotal;
      }

    }

    if($playerRole == 'golkiper') {

      // Фильтруем только тех голкиперов, у которых минимум 3 сэйва
      $filtered = array_filter($playerOfTur, function ($item) {
          return $item['seyv'] >= 4;
      });

      // Шаг 1: Вычисляем тотал для каждого элемента
      foreach ($filtered as $key => $item) {
          $total[$key] = ($item['seyv'] + $item['seyvmin'] == 0) ? 0 : (100 / ($item['seyv'] + $item['seyvmin'])) * $item['seyv'];
      }
      

      // Шаг 2: Находим максимальное значение тотала
      $maxTotal = empty($total) ? 0 : max($total);

      // dump($maxTotal);

      // Шаг 3: Отбираем все элементы, у которых сумма равна максимальной
      $result = array_filter($filtered, function ($item) use ($maxTotal) {
          return ($item['seyv'] + $item['seyvmin'] == 0 ? 0 : (100 / ($item['seyv'] + $item['seyvmin'])) * $item['seyv']) == $maxTotal;
      });

      // Добавляем игроку ключ что он лучший в туре
      foreach ($result as $key => $res) {
          $result[$key]['best_player'] = 'golkiper';
          $result[$key]['count_points'] = round($maxTotal, 0);
      }
  }




    if($playerRole == 'zahusnuk') {
      
      // Шаг 1: Вычисляем сумму otbor + blok для каждого элемента
      foreach ($playerOfTur as $key => $item) {
          $total[$key] = $item['otbor'] + $item['blok'];
      }
  
      // Шаг 2: Находим максимальное значение суммы
      if(empty($total)){
        $maxTotal = 0;
      } else {
        $maxTotal = max($total);
      }
  
      // Шаг 3: Отбираем все элементы, у которых сумма равна максимальной
      $result = array_filter($playerOfTur, function ($item) use ($maxTotal) {
          return ($item['otbor'] + $item['blok']) == $maxTotal;
      });
  
      // Добавляем игроку ключ что он лучший в туре
      foreach($result as $key => $res){
        $result[$key]['best_player'] = 'zahusnuk';
        $result[$key]['count_points'] = $maxTotal;
      }

    }

    if($playerRole == 'dribling') {
      
      // Шаг 1: Вычисляем тотал для каждого элемента
      foreach ($playerOfTur as $key => $item) {
          $total[$key] = $item['otbor'] - $item['otbormin'];
      }
  
      // Шаг 2: Находим максимальное значение тотала
      if(empty($total)){
        $maxTotal = 0;
      } else {
        $maxTotal = max($total);
      }
  
      // Шаг 3: Отбираем все элементы, у которых сумма равна максимальной
      $result = array_filter($playerOfTur, function ($item) use ($maxTotal) {
          return ($item['otbor'] - $item['otbormin']) == $maxTotal;
      });
  
      // Добавляем игроку ключ что он лучший в туре
      foreach($result as $key => $res){
        $result[$key]['best_player'] = 'dribling';
        $result[$key]['count_points'] = $maxTotal;
      }

    }

    if($playerRole == 'udar') {
      
      // Шаг 1: Вычисляем тотал для каждого элемента
      foreach ($playerOfTur as $key => $item) {
          $total[$key] = $item['vstvor'] - $item['mimo'];
      }
  
      // Шаг 2: Находим максимальное значение тотала
      if(empty($total)){
        $maxTotal = 0;
      } else {
        $maxTotal = max($total);
      }
  
      // Шаг 3: Отбираем все элементы, у которых сумма равна максимальной
      $result = array_filter($playerOfTur, function ($item) use ($maxTotal) {
          return ($item['vstvor'] - $item['mimo']) == $maxTotal;
      });
  
      // Добавляем игроку ключ что он лучший в туре
      foreach($result as $key => $res){
        $result[$key]['best_player'] = 'udar';
        $result[$key]['count_points'] = $maxTotal;
      }

    }

    if($playerRole == 'pas') {
      
      // Шаг 1: Вычисляем тотал для каждого элемента
      foreach ($playerOfTur as $key => $item) {
          $total[$key] = ( $item['zagostrennia']*5 + $item['pasplus'] ) - $item['pasminus']*3;
      }
  
      // Шаг 2: Находим максимальное значение тотала
      if(empty($total)){
        $maxTotal = 0;
      } else {
        $maxTotal = max($total);
      }
  
      // Шаг 3: Отбираем все элементы, у которых сумма равна максимальной
      $result = array_filter($playerOfTur, function ($item) use ($maxTotal) {
          return ( ( $item['zagostrennia']*5 + $item['pasplus'] ) - $item['pasminus']*3 ) == $maxTotal;
      });
  
      // Добавляем игроку ключ что он лучший в туре
      foreach($result as $key => $res){
        $result[$key]['best_player'] = 'pas';
        $result[$key]['count_points'] = $maxTotal;
      }

    }


    return $result;
  }

  /**
   * Получает всех лучших игроков из все туров.
   * @param array - Статистика всех игроков
   * @param int - колчиство сыграных туров
   * @return array - массив игроков призеров всех туров по отдельности. 
   */
  function getPlayersOfAllTurs($allStaticPlayers, $lastTur) {
    $bestPlayerOfAllTurs = [];
    for ($i = 1; $i <= $lastTur; $i++) {
        $playersOfTur = getPlayersOfTur($allStaticPlayers, $i); // Получаем результат функции
        foreach ($playersOfTur as $player) {
            $bestPlayerOfAllTurs[] = $player; // Добавляем элементы без вложений
        }
    }
    return $bestPlayerOfAllTurs;
}

/**
 * Находит количество раз попаданий в сборную тура
 * @param array - массив всех игроков, которые пападали в сборную тура. Реультат функции getPlayersOfAllTurs()
 * @param integer - идентификатор игрока
 * @return integer 
 */
function getCountInTur($palyersOfAllTurs, $playerId){
  $count = 0;

  foreach( $palyersOfAllTurs as $player) {
    if($player['player'] == $playerId) {
      $count ++;
    }
  }

  return $count;
}

/**
 * Получает количество ра попаданий в сборную тура всех игроков команды.
 * @param array - массив всех игроков, которые пападали в сборную тура. Реультат функции getPlayersOfAllTurs()
 * @param array - массив идентификатор всех игроков команды
 * @return integer
 */
function getCountInTurOfTeam($palyersOfAllTurs, $playerIds){
  // Счетчик совпадений
  $matchCount = 0;

  // Проходим по массиву и считаем совпадения
  foreach ($palyersOfAllTurs as $item) {
    if (in_array($item['player'], $playerIds)) {
        $matchCount++;
    }
  }

  return $matchCount;
}

/**
 * получает идентификатор всех  игроков команды
 */
function getPlayerIds($dataAllPlayers, $teamId) {
  $playerIds = [];
  foreach ($dataAllPlayers as $key => $value){
    if($value['team_id'] === $teamId) {
      $playerIds[] = $key;
    }    
  }
  return $playerIds;
}

/**
 * @param string - дата и баы данных в формате  '2024-12-01 12:30:00'
 * @return array - с двумя элементами - Например, Array( [0] => 24 листопад (нд), [1] => 11:35)
 */
function getFormattedDate($dateFromDB) {
  
  // Преобразуем дату в объект DateTime
  $date = new DateTime($dateFromDB);

  // Устанавливаем локаль для русского языка
  setlocale(LC_TIME, 'uk_UA.UTF-8');

  // Форматируем дату
  $formattedDate = strftime('%e %B (%a)', $date->getTimestamp());
  $formattedTime = strftime('%H:%M', $date->getTimestamp());

  // Вывод результата  
  return [$formattedDate, $formattedTime];
}

/**
 * Добавляем в массив два элемента с форматированной датой для выода на страницу
 * @param array
 * @return array
 */
function getArrayWithFormattedDate($dataCurrentTur){
  foreach ($dataCurrentTur as $key => $match) {
    $formattedDate = getFormattedDate($match['date']);
    $dataCurrentTur[$key]['match_day'] = $formattedDate[0];
    $dataCurrentTur[$key]['match_time'] = $formattedDate[1];
  }
  return $dataCurrentTur;
}