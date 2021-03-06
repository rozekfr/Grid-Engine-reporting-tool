<?php

    /**Funkce převádějící filtry na WHERE příkaz do databáze
     * @param $db string Databázová tabulka
     * @param $where string Filtry ve formatu: nazev:podminka-nazev2:podminka2-...
     * @return string Vrací příkaz WHERE do SQL dotazu
     */
    function make_where_statement($db,$where){
        if($where != "1"){
            $tmp_where = explode("|", $where);
            $where = "";
            foreach ($tmp_where as $tmp){
                $filtr = explode(":", $tmp);
                if(isset($filtr[0]) and isset($filtr[1])) {
	                $col = $filtr[0];
	                $condition = $filtr[1];
	                $matches = array();
	                if($col == "uzivatel"){
		                $col = $db.".".$col; //aby bylo jasné z které tabulky
	                }
	                if ($col == "skupina") {
						if (preg_match_all("/^[^(<|>|<=|>=|=|!=|,)]+(, ?[^<|>|<=|>=|=|!=|,)]+)*$/", $condition, $matches)) {
			                $skupina = "id_skupiny IN";
			                $Vskupina = "vychozi_skupina IN";
			                $c = explode(",", $condition);
			                $condition = "";
			                $len = count($c);
			                $i = 0;
			                foreach ($c as $c1) {
				                $c1 = trim($c1);
				                $condition .= ($i < ($len - 1) ? "'$c1'," : "'$c1'");
				                $i++;
			                }
			                $skupina .= " ($condition)";
			                $Vskupina .= " ($condition)";

			                if(empty($where)){
				                $where = "($db.uzivatel IN (SELECT uzivatele.uzivatel FROM uzivatele LEFT JOIN rozpis_uzivatele_skupiny ON uzivatele.uzivatel=rozpis_uzivatele_skupiny.uzivatel WHERE $skupina OR $Vskupina))";
			                }
			                else{
				                $where .= " AND ($db.uzivatel IN (SELECT uzivatele.uzivatel FROM uzivatele LEFT JOIN rozpis_uzivatele_skupiny ON uzivatele.uzivatel=rozpis_uzivatele_skupiny.uzivatel WHERE $skupina OR $Vskupina))";
			                }
		                }
	                }
	                else {
		                //podmínky
		                if (preg_match_all("/^(<|>|<=|>=|=|!=) [^( |AND|OR|<|>|<=|>=|=|!=||'|\")]+(( AND | OR )(<|>|<=|>=|=|!=) [^( |AND|OR|<|>|<=|>=|=|!=|'|\")]+)*$/", $condition, $matches)) {
			                $condition = explode(" ", $condition);
			                $i = 0;
			                $len = count($condition);
			                foreach ($condition as $c) {
				                //začátek podmínky
				                if ($i == 0) {
					                if (empty($where)) {
						                $where = "(";
					                } else {
						                $where .= " AND (";
					                }
				                }

				                //vnitřek podmínky
				                //operátory
				                if ($c == "<" or $c == ">" or $c == "<=" or $c == ">=" or $c == "=" or $c == "!=") {
					                $where .= "$col $c";
				                }
				                //čas
				                else if (preg_match_all("/^(\d+r)?(\d{1,3}d)?(\d{1,2}h)?(\d{1,2}m)?(\d{1,2}s)?$/", $c, $matches)) {
					                $time = 0;
					                $time_string = str_split($c);
					                $val = "";
					                foreach ($time_string as $char) {
						                if (is_numeric($char)) {
							                $val .= $char;
						                } else {
							                switch ($char) {
								                case 'r':
									                $val = intval($val);
									                $time += $val * 365 * 24 * 60 * 60;
									                break;

								                case 'd':
									                $val = intval($val);
									                $time += $val * 24 * 60 * 60;
									                break;

								                case 'h':
									                $val = intval($val);
									                $time += $val * 60 * 60;
									                break;

								                case 'm':
									                $val = intval($val);
									                $time += $val * 60;
									                break;

								                case 's':
									                $val = intval($val);
									                $time += $val;
									                break;
							                }
							                $val = "";
						                }
					                }
					                $where .= " $time";
				                }
				                //paměť
				                else if(preg_match_all("/(\d+|\d+(,|\.)\d+)(kB|MB|GB)/",$c,$matches)){
					                $string = str_split($c);
					                $val = "";
					                $unit = "";
					                foreach($string as $ch){
						                if(is_numeric($ch) or $ch == "," or $ch == "."){
							                $val .= $ch;
						                }
						                else{
							                $unit .= $ch;
						                }
					                }
					                $value = floatval($val);
					                switch($unit){
						                case 'kB':
							                $value = $value/1024;
							                break;

						                case 'GB':
							                $value = $value*1024;
							                break;

						                default:
							                break;
					                }
					                $where .= " $value";
				                }
				                //spotřeba
				                else if(preg_match_all("/(\d+|\d+(,|\.)\d+)(mWh|Wh|kWh)/",$c,$matches)){
					                $string = str_split($c);
					                $val = "";
					                $unit = "";
					                foreach($string as $ch){
						                if(is_numeric($ch) or $ch == "," or $ch == "."){
							                $val .= $ch;
						                }
						                else{
							                $unit .= $ch;
						                }
					                }
					                $value = floatval($val);
					                switch($unit){
						                case 'mWh':
							                $value = $value/1000;
							                break;

						                case 'kWh':
							                $value = $value*1000;
							                break;

						                default:
							                break;
					                }
					                $where .= " $value";
				                }
				                //AND
				                else if ($c == "AND") {
					                $where .= " AND ";
				                }
				                //OR
				                else if ($c == "OR") {
					                $where .= " OR ";
				                } //čísla
				                else if (is_numeric($c)) {
					                $where .= " $c";
				                } //zbytek
				                else {
					                //desetinná čísla
					                if (strpos($c, ',') !== false) {
						                $c = str_replace(",", ".", $c);
						                $where .= " $c";
					                } else {
						                $where .= " '$c'";
					                }
				                }

				                //konec podmínky
				                if ($i == $len - 1) {
					                $where .= ")";
				                }
				                $i++;
			                }
		                }
		                //seznam
		                else if (preg_match_all("/^[^(<|>|<=|>=|=|!=|\s|,)]+(, ?[^<|>|<=|>=|=|!=|\s|,)]+)*$/", $condition, $matches)) {
			                if (empty($where)) {
				                $where = "$col IN ";
			                } else {
				                $where .= " AND $col IN ";
			                }
			                $c = explode(",", $condition);
			                $condition = "";
			                $len = count($c);
			                $i = 0;
			                foreach ($c as $c1) {
				                $c1 = trim($c1);
				                $condition .= ($i < ($len - 1) ? "'$c1'," : "'$c1'");
				                $i++;
			                }
			                $where .= "($condition)";
		                }
	                }
                }
            }
        }

        return $where;
    }


    /** Převede čas v sekundách do čitelného formátu roky dny hodiny minuty
     * @param $time čas v sekundách
     * @return čas ve formátu r d h min
     */
    function transform_time($time){
        //roky
        $roky = $time/(60*60*24*365);
        $intpart = floor($roky);
        $fraction = $roky - $intpart;
        $roky = $intpart;
        //dny
        $dny = $fraction * 365;
        $intpart = floor($dny);
        $fraction = $dny - $intpart;
        $dny = $intpart;
        //hodiny
        $hodiny = $fraction * 24;
        $intpart = floor($hodiny);
        $fraction = $hodiny - $intpart;
        $hodiny = $intpart;
        //minuty
        $minuty = $fraction * 60;
        $intpart = floor($minuty);
        $fraction = $minuty - $intpart;
        $minuty = $intpart;
        //sekundy
        $sekundy = $fraction * 60;
        $intpart = round($sekundy);
        $sekundy = $intpart;

        return "<span>{$roky}r</span><span>{$dny}<i>d</i></span><span>{$hodiny}<i>h</i></span><span>{$minuty}<i>m</i></span><span>{$sekundy}<i>s</i></span>";
    }

    /**Funkce převádějící jednotky paměti pro čitelnější výpis.
     * @param $val Hodnota, k převedění.
     * @return string Vrací hodnotu s příslušnou jednotkou.
     */
    function write_memory($val){
        $val = round($val,0);
        if($val > 1024){
            $val /= 1024;
            $val = number_format($val, 0, ",", " ")." GB";
        }
        else if($val == 0){
            $val *= 1024;
            $val = number_format($val, 0, ",", " ")." KB";
        }
        else{
            $val = number_format($val, 0, ",", " ")." MB";
        }
        return $val;
    }

	/**Funke převádějící jednotky spotřeby na čitelnější tvar.
	 * @param $val Hodnota.
	 * @return string Převedená hodnota s příslušnou jednotkou.
	 */
	function write_consumption($val){
		if($val > 1000){
			$val /= 1000;
			$val = number_format($val, 2, ",", " ")." kWh";
		}
		else if($val < 1){
			$val *= 1000;
			$val = number_format($val, 2, ",", " ")." mWh";
		}
		else{
			$val = number_format($val, 2, ",", " ")." Wh";
		}
		return $val;
	}
?>