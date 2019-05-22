<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function create_rules() {

	$options = get_option('ia_styles_options');
	$rules = ":root{";
	/*this is for holding the color rules that should be added at the end. 
	We need to go through the color array to get the values for the fonts at the beginning and this way we only need to iterate once.*/
	$c_rules = ''; 
	$c = array();

	//Color Declarations
	if (is_array($options['colors']) && !empty($options['colors'])){
		foreach ($options['colors'] as $color) {
			if ($color['color_name'] !== "" && $color['color_hexcode'] !== "") {
				$c_rules .= ".".$color['color_name']."-text{ color: ".$color['color_hexcode']."}";
				$c_rules .= ".".$color['color_name']."-background{ background-color: ".$color['color_hexcode']."}";
				$c[$color['color_name']] = $color['color_hexcode'];
				
				$rules .= "--" . $color['color_name'] . ": " . $color['color_hexcode'] . "; ";
			}
		}
		$rules .= "--black: #000000; ";
		$rules .= "--white: #FFFFFF; ";
		$c_rules .= ".black-text{ color: #000000;}";
		$c_rules .= ".black-background{ background-color: #000000;}";
		$c_rules .= ".white-text{ color: #FFFFFF;}";
		$c_rules .= ".white-background{ background-color: #FFFFFF;}";
		$c['black'] = '#000000';
		$c['white'] = '#FFFFFF';
	}

	$rules .= "}";

	function create_font_rule($opt_array){
			$opt_rules = '';
			foreach ($opt_array as $key => $value) {
			    if ($key === 'color') {
			    	$v = preg_replace('/\s+/', '', $value);
			    	$opt_rules .= "color: ". $c[$v].';';
			    } elseif ($key === 'font-size') {
			    	$opt_rules .= $key . ": ". $value .";";
			    	if (strpos($value, 'px')){
			    		$num = substr($value, 0, strpos($value, 'px'));
			    		$num = round((int)$num / 12, 4);
			    		$value2 = $num . 'vw';
			    	}
			    	$opt_rules .= $key . ": ". $value2 .";";
			    } else {
			    	$opt_rules .= $key . ": ". $value .";";
			    }
			}
			return $opt_rules;
	}

	function create_font_responsive($opt_px, $declare){
			$opt_rules = '';
			if ($opt_px) {
				if (strpos($opt_px, 'px')){
		    		$num = substr($opt_px, 0, strpos($opt_px, 'px'));
		    		$num = round((int)$num * 1.54167);
		    		$value = $num . 'px';
		    	}
				$opt_rules .= $declare. ": ". $value .";";
			}
			
			return $opt_rules;
	}

	//p rules 
	$rules .= "p, .p, div, li, body, input, textarea {";
		if (is_array($options['p']) && !empty($options['p'])) {
			$rules .= create_font_rule($options['p']);
		}
	$rules .= "}";

	//.p1 rules 
	$rules .= ".p1 {";
		if (is_array($options['p1']) && !empty($options['p1'])) {
			$rules .= create_font_rule($options['p1']);
		}
	$rules .= "}";

	//H1 rules
	$rules .= "h1, .h1 { ";
		if (is_array($options['h1']) && !empty($options['h1'])) {
			$rules .= create_font_rule($options['h1']);
		}
	$rules .= "}";

	//H2 rules
	$rules .= "h2, .h2 {";
		if (is_array($options['h2']) && !empty($options['h2'])) {
			$rules .= create_font_rule($options['h2']);
		}
	$rules .= "}";

	//H3 rules 
	$rules .= "h3, .h3 {";
		if (is_array($options['h3']) && !empty($options['h3'])) {
			$rules .= create_font_rule($options['h3']);
		}
	$rules .= "}";

	//H4 rules 
	$rules .= "h4, .h4 {";
		if (is_array($options['h4']) && !empty($options['h4'])) {
			$rules .= create_font_rule($options['h4']);
		}
	$rules .= "}";

	//h5 rules 
	$rules .= "h5, .h5 {";
		if (is_array($options['h5']) && !empty($options['h5'])) {
			$rules .= create_font_rule($options['h5']);
		}
	$rules .= "}";
	
	//h6 rules 
	$rules .= "h6, .h6 {";
		if (is_array($options['h6']) && !empty($options['h6'])) {
			$rules .= create_font_rule($options['h6']);
		}
	$rules .= "}";

	//a rules 
	$rules .= "a, .a {";
	    if (is_array($options['a']) && !empty($options['a'])) {
			$rules .= create_font_rule($options['a']);
		}
	$rules .= "}";

	//a_hover rules 
	$rules .= "a:hover, .a:hover, a:focus, .a:focus {";
	    if (is_array($options['a_hover']) && !empty($options['a_hover'])) {
			$rules .= create_font_rule($options['a_hover']);
		}
	$rules .= "}";
	
	//a1 rules 
	$rules .= ".a2 {";
	    if (is_array($options['a2']) && !empty($options['a2'])) {
			$rules .= create_font_rule($options['a2']);
		}
	$rules .= "}";

	//a1_hover rules 
	$rules .= ".a2:hover, .a2:focus {";
	    if (is_array($options['a2_hover']) && !empty($options['a2_hover'])) {
			$rules .= create_font_rule($options['a2_hover']);
		}
	$rules .= "}";

	//a3 rules 
	$rules .= ".a3 {";
		if (is_array($options['a3']) && !empty($options['a3'])) {
			$rules .= create_font_rule($options['a3']);
		}
	$rules .= "}";

	//a3_hover rules 
	$rules .= ".a3:hover, .a3:focus {";
	    if (is_array($options['a3_hover']) && !empty($options['a3_hover'])) {
			$rules .= create_font_rule($options['a3_hover']);
		}
	$rules .= "}";

	//.nav rules 
	$rules .= ".nav, .nav .menu>li, .nav .menu>li>a {";
	    if (is_array($options['nav']) && !empty($options['nav'])) {
			$rules .= create_font_rule($options['nav']);
		}
	$rules .= "}";
	
	//.nav_hover rules 
	$rules .= ".nav:hover, .nav:focus, .nav .menu>li:hover, .nav .menu>li:hover>a, ";
	$rules .= ".nav .menu>li:focus, .nav .menu>li:focus>a {";
		if (is_array($options['nav_hover']) && !empty($options['nav_hover'])) {
			$rules .= create_font_rule($options['nav_hover']);
		}
	$rules .= "}";
	
	//.nav_focus rules 
	$rules .= ".current-menu, .menu li.current-menu-item a, .current_page_item a, .current_page_ancestor {";
	    if (is_array($options['nav_current']) && !empty($options['nav_current'])) {
			$rules .= create_font_rule($options['nav_current']);
		}
	$rules .= "}";
	

	//.subnav rules 
	$rules .= ".subnav, .subnav .menu>li, .subnav .menu>li>a, .nav .sub-menu>li, .nav .sub-menu>li>a {";
	    if (is_array($options['subnav']) && !empty($options['subnav'])) {
			$rules .= create_font_rule($options['subnav']);
		}
	$rules .= "}";

	//.subnav_hover rules 
	$rules .= ".subnav:hover, .subnav:focus, .subnav .menu>li:hover, .subnav .menu>li:focus, .subnav .menu>li>a:hover, .subnav .menu>li>a:focus, .nav .sub-menu>li:hover, .nav .sub-menu>li:hover>a,";
	$rules .= " .nav .sub-menu>li:focus, .nav .sub-menu>li:focus>a {";
	    if (is_array($options['subnav_hover']) && !empty($options['subnav_hover'])) {
			$rules .= create_font_rule($options['subnav_hover']);
		}
	$rules .= "}";

	//.subnav_focus rules 
	$rules .= ".current-submenu, .current_page_ancestor .current_page_item, .subnav .current-menu{";
		if (is_array($options['subnav_focus']) && !empty($options['subnav_focus'])) {
			$rules .= create_font_rule($options['subnav_focus']);
		}
	$rules .= "}";

	//global style rules
	if (!empty($options['article-padding']['pixel-amount'])) {
		$rules .= ".grid-wrapper, article{";
			$rules .= "padding: ". $options['article-padding']['pixel-amount'] ." 0;";
	    	if (strpos($options['article-padding']['pixel-amount'], 'px') > 0){
	    		$num = substr($options['article-padding']['pixel-amount'], 0, strpos($options['article-padding']['pixel-amount'], 'px'));
	    		$num = (int)$num / 12;
	    		$value2 = $num . 'vw';
	    		$rules .= "padding: ". $value2 ." 0;";
	    	}
		$rules .= "}";
	}
	if (!empty($options['module-title-bottom-margin']['pixel-amount'])) {
		$rules .= ".module-title{";
			$rules .= "margin-bottom: ". $options['module-title-bottom-margin']['pixel-amount'] .";";
	    	if (strpos($options['module-title-bottom-margin']['pixel-amount'], 'px') > 0){
	    		$num = substr($options['module-title-bottom-margin']['pixel-amount'], 0, strpos($options['module-title-bottom-margin']['pixel-amount'], 'px'));
	    		$num = (int)$num / 12;
	    		$value2 = $num . 'vw';
	    		$rules .= "margin-bottom: ". $value2 .";";
	    	}
		$rules .= "}";
	}
	if (!empty($options['column-gutter']['pixel-amount'])) {
		if (strpos($options['column-gutter']['pixel-amount'], 'px') > 0){
    		$num = substr($options['column-gutter']['pixel-amount'], 0, strpos($options['column-gutter']['pixel-amount'], 'px'));
    		$num = (int)$num / 12;
    		$value2 = $num . 'vw';
    	}
		$rules .= ".row, .gutter-row{";
			$rules .= "margin-right: -". $num * 6 ."px;";
			$rules .= "margin-left: -". $num * 6 ."px;";
			$rules .= "margin-right: -". $num / 2 ."vw;";
			$rules .= "margin-left: -". $num / 2 ."vw;";
		$rules .= "}";
		$rules .= '.gutter-col, .row [class*="col-"]{';
			$rules .= "margin-bottom: ". $options['column-gutter']['pixel-amount'] .";";
			$rules .= "padding-right: ". $num * 6 ."px;";
			$rules .= "padding-left: ". $num * 6 ."px;";
			$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "padding-right: ". $num / 2 ."vw;";
			$rules .= "padding-left: ". $num / 2 ."vw;";
		$rules .= "}";
	}
	if (!empty($options['small-margin']['pixel-amount'])) {
		if (strpos($options['small-margin']['pixel-amount'], 'px') > 0){
    		$num = substr($options['small-margin']['pixel-amount'], 0, strpos($options['small-margin']['pixel-amount'], 'px'));
    		$num = (int)$num / 12;
    		$value2 = $num . 'vw';
			$rules .= ".small-margin-bottom{";
				$rules .= "margin-bottom: ". $options['small-margin']['pixel-amount'] .";";
				$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".small-margin-top{";
				$rules .= "margin-top: ". $options['small-margin']['pixel-amount'] .";";
				$rules .= "margin-top: ". $value2 .";";
			$rules .= "}";
			$rules .= ".small-padding-bottom{";
				$rules .= "padding-bottom: ". $options['small-margin']['pixel-amount'] .";";
				$rules .= "padding-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".small-padding-top{";
				$rules .= "padding-top: ". $options['small-margin']['pixel-amount'] .";";
				$rules .= "padding-top: ". $value2 .";";
			$rules .= "}";
    	}
	}
	if (!empty($options['medium-margin']['pixel-amount'])) {
		if (strpos($options['medium-margin']['pixel-amount'], 'px') > 0){
    		$num = substr($options['medium-margin']['pixel-amount'], 0, strpos($options['medium-margin']['pixel-amount'], 'px'));
    		$num = (int)$num / 12;
    		$value2 = $num . 'vw';
			$rules .= ".medium-margin-bottom{";
				$rules .= "margin-bottom: ". $options['medium-margin']['pixel-amount'] .";";
				$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".medium-margin-top{";
				$rules .= "margin-top: ". $options['medium-margin']['pixel-amount'] .";";
				$rules .= "margin-top: ". $value2 .";";
			$rules .= "}";
			$rules .= ".medium-padding-bottom{";
				$rules .= "padding-bottom: ". $options['medium-margin']['pixel-amount'] .";";
				$rules .= "padding-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".medium-padding-top{";
				$rules .= "padding-top: ". $options['medium-margin']['pixel-amount'] .";";
				$rules .= "padding-top: ". $value2 .";";
			$rules .= "}";
    	}
	}
	if (!empty($options['large-margin']['pixel-amount'])) {
		if (strpos($options['large-margin']['pixel-amount'], 'px') > 0){
    		$num = substr($options['large-margin']['pixel-amount'], 0, strpos($options['large-margin']['pixel-amount'], 'px'));
    		$num = (int)$num / 12;
    		$value2 = $num . 'vw';
			$rules .= ".large-margin-bottom{";
				$rules .= "margin-bottom: ". $options['large-margin']['pixel-amount'] .";";
				$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".large-margin-top{";
				$rules .= "margin-top: ". $options['large-margin']['pixel-amount'] .";";
				$rules .= "margin-top: ". $value2 .";";
			$rules .= "}";
			$rules .= ".large-padding-bottom{";
				$rules .= "padding-bottom: ". $options['large-margin']['pixel-amount'] .";";
				$rules .= "padding-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".large-padding-top{";
				$rules .= "padding-top: ". $options['large-margin']['pixel-amount'] .";";
				$rules .= "padding-top: ". $value2 .";";
			$rules .= "}";
    	}
	}
	if (!empty($options['button-padding']) && is_array($options['button-padding'])) {
		$rules .= ".a3, p.a3, .button, button{";
	    	$px = '';
	    	$vw = '';
	    	foreach ($options['button-padding'] as $value) {
	    		if (strpos($value, 'px')){
		    		$num = substr($value, 0, strpos($value, 'px'));
		    		$num = (int)$num / 12;
		    		$vw .= ' ' . $num . 'vw';
		    		$px .= ' ' . $value;
		    	}
	    	}
	    	$rules .= "padding:". $px .";";
			$rules .= "padding:". $vw .";";
		$rules .= "}";
	}

	// smaller screens
	$rules .= "@media screen and (max-width: 1200px) {";

		//p rules 
		$rules .= "p, .p, div, li, body, input, textarea {";
			if ($options['p']['font-size']) {
				$rules .= "font-size: ". $options['p']['font-size'] .";";
			}
		$rules .= "}";

		//.p1 rules 
		$rules .= ".p1 {";
			if ($options['p1']['font-size']) {
				$rules .= "font-size: ". $options['p1']['font-size'] .";";
			}
		$rules .= "}";

		//H1 rules
		$rules .= "h1, .h1 { ";
			if ($options['h1']['font-size']) {
				$rules .= "font-size: ". $options['h1']['font-size'] .";";
			}
		$rules .= "}";

		//H2 rules
		$rules .= "h2, .h2 {";
			if ($options['h2']['font-size']) {
				$rules .= "font-size: ". $options['h2']['font-size'] .";";
			}
		$rules .= "}";

		//H3 rules 
		$rules .= "h3, .h3 {";
			if ($options['h3']['font-size']) {
				$rules .= "font-size: ". $options['h3']['font-size'] .";";
			}
		$rules .= "}";

		//H4 rules 
		$rules .= "h4, .h4 {";
			if ($options['h4']['font-size']) {
				$rules .= "font-size: ". $options['h4']['font-size'] .";";
			}
		$rules .= "}";

		//h5 rules 
		$rules .= "h5, .h5 {";
			if ($options['h5']['font-size']) {
				$rules .= "font-size: ". $options['h5']['font-size'] .";";
			}
		$rules .= "}";
		
		//h6 rules 
		$rules .= "h6, .h6 {";
			if ($options['h6']['font-size']) {
				$rules .= "font-size: ". $options['h6']['font-size'] .";";
			}
		$rules .= "}";

		//a rules 
		$rules .= "a, .a {";
		    if ($options['a']['font-size']) {
				$rules .= "font-size: ". $options['a']['font-size'] .";";
			}
		$rules .= "}";

		//a_hover rules 
		$rules .= "a:hover, .a:hover, a:focus, .a:focus {";
		    if ($options['a_hover']['font-size']) {
				$rules .= "font-size: ". $options['a_hover']['font-size'] .";";
			}
		$rules .= "}";
		
		//a1 rules 
		$rules .= ".a2 {";
		    if ($options['a2']['font-size']) {
				$rules .= "font-size: ". $options['a2']['font-size'] .";";
			}
		$rules .= "}";

		//a1_hover rules 
		$rules .= ".a2:hover, .a2:focus {";
		    if ($options['a2_hover']['font-size']) {
				$rules .= "font-size: ". $options['a2_hover']['font-size'] .";";
			}
		$rules .= "}";

		//a3 rules 
		$rules .= ".a3 {";
			if ($options['a3']['font-size']) {
				$rules .= "font-size: ". $options['a3']['font-size'] .";";
			}
		$rules .= "}";

		//a3_hover rules 
		$rules .= ".a3:hover, .a3:focus {";
			if ($options['a3_hover']['font-size']) {
				$rules .= "font-size: ". $options['a3_hover']['font-size'] .";";
			}
		$rules .= "}";

		//.nav rules 
		$rules .= ".nav, .nav .menu>li, .nav .menu>li>a {";
		    if ($options['nav']['font-size']) {
				$rules .= "font-size: ". $options['nav']['font-size'] .";";
			}
		$rules .= "}";
		
		//.nav_hover rules 
		$rules .= ".nav:hover, .nav:focus, .nav .menu>li:hover, .nav .menu>li:hover>a, ";
		$rules .= ".nav .menu>li:focus, .nav .menu>li:focus>a {";
			if ($options['nav_hover']['font-size']) {
				$rules .= "font-size: ". $options['nav_hover']['font-size'] .";";
			}
		$rules .= "}";
		
		//.nav_focus rules 
		$rules .= ".current-menu, .menu li.current-menu-item a, .current_page_item a, .current_page_ancestor {";
		    if ($options['nav_focus']['font-size']) {
				$rules .= "font-size: ". $options['nav_focus']['font-size'] .";";
			}
		$rules .= "}";
		

		//.subnav rules 
		$rules .= ".subnav, .subnav .menu>li, .subnav .menu>li>a, .nav .sub-menu>li, .nav .sub-menu>li>a {";
		    if ($options['subnav']['font-size']) {
				$rules .= "font-size: ". $options['subnav']['font-size'] .";";
			}
		$rules .= "}";

		//.subnav_hover rules 
		$rules .= ".subnav:hover, .subnav:focus, .subnav .menu>li:hover, .subnav .menu>li:focus, .subnav .menu>li>a:hover, .subnav .menu>li>a:focus, .nav .sub-menu>li:hover, .nav .sub-menu>li:hover>a,";
		$rules .= " .nav .sub-menu>li:focus, .nav .sub-menu>li:focus>a {";
		    if ($options['subnav_hover']['font-size']) {
				$rules .= "font-size: ". $options['subnav_hover']['font-size'] .";";
			}
		$rules .= "}";

		//.subnav_focus rules
		$rules .= ".current-submenu, .current_page_ancestor .current_page_item, .subnav .current-menu{";
			if ($options['subnav_focus']['font-size']) {
				$rules .= "font-size: ". $options['subnav_focus']['font-size'] .";";
			}
		$rules .= "}";
		//global style rules
		if (!empty($options['article-padding']['pixel-amount'])) {
			$rules .= ".grid-wrapper, article{";
				$rules .= "padding: ". $options['article-padding']['pixel-amount'] ." 0;";
			$rules .= "}";
		}
		if (!empty($options['module-title-bottom-margin']['pixel-amount'])) {
			$rules .= ".module-title{";
				$rules .= "margin-bottom: ". $options['module-title-bottom-margin']['pixel-amount'] .";";
			$rules .= "}";
		}
		if (!empty($options['column-gutter']['pixel-amount'])) {
			if (strpos($options['medium-margin']['pixel-amount'], 'px')){
	    		$num = substr($options['medium-margin']['pixel-amount'], 0, strpos($options['medium-margin']['pixel-amount'], 'px'));
	    		$num = (int)$num / 12;
	    	}
			$rules .= ".row, .gutter-row{";
				$rules .= "margin-right: -". $num * 6 ."px;";
				$rules .= "margin-left: -". $num * 6 ."px;";
			$rules .= "}";
			$rules .= '.gutter-col, .row [class*="col-"]{';
				$rules .= "margin-bottom: ". $options['column-gutter']['pixel-amount'] .";";
				$rules .= "padding-right: ". $num * 6 ."px;";
				$rules .= "padding-left: ". $num * 6 ."px;";
			$rules .= "}";
		}
		if (!empty($options['small-margin']['pixel-amount'])) {
			$rules .= ".small-margin-bottom{";
				$rules .= "margin-bottom: ". $options['small-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".small-margin-top{";
				$rules .= "margin-top: ". $options['small-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".small-padding-bottom{";
				$rules .= "padding-bottom: ". $options['small-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".small-padding-top{";
				$rules .= "padding-top: ". $options['small-margin']['pixel-amount'] .";";
			$rules .= "}";
		}
		if (!empty($options['medium-margin']['pixel-amount'])) {
			$rules .= ".medium-margin-bottom{";
				$rules .= "margin-bottom: ". $options['medium-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".medium-margin-top{";
				$rules .= "margin-top: ". $options['medium-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".medium-padding-bottom{";
				$rules .= "padding-bottom: ". $options['medium-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".medium-padding-top{";
				$rules .= "padding-top: ". $options['medium-margin']['pixel-amount'] .";";
			$rules .= "}";
		}
		if (!empty($options['large-margin']['pixel-amount'])) {
			$rules .= ".large-margin-bottom{";
				$rules .= "margin-bottom: ". $options['large-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".large-margin-top{";
				$rules .= "margin-top: ". $options['large-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".large-padding-bottom{";
				$rules .= "padding-bottom: ". $options['large-margin']['pixel-amount'] .";";
			$rules .= "}";
			$rules .= ".large-padding-top{";
				$rules .= "padding-top: ". $options['large-margin']['pixel-amount'] .";";
			$rules .= "}";
		}
		if (!empty($options['button-padding']) && is_array($options['button-padding'])) {
			$rules .= ".a3, p.a3, .button, button{";
		    	$px = '';
		    	foreach ($options['button-padding'] as $value) {
		    		if (strpos($value, 'px')){
			    		$px .= ' ' . $value;
			    	} else {
			    		$px .= ' ' . $value . 'px';
			    	}
		    	}
		    	$rules .= "padding:". $px .";";
			$rules .= "}";
		}

	$rules .= "}";

	//large screens
	$rules .= "@media screen and (min-width: 1850px) {";
		//p rules 
		$rules .= "p, .p, div, li, body, input, textarea {";
			$rules .= create_font_responsive($options['p']['font-size'], 'font-size');
		$rules .= "}";

		//.p1 rules 
		$rules .= ".p1 {";
			$rules .= create_font_responsive($options['p1']['font-size'], 'font-size');
		$rules .= "}";

		//H1 rules
		$rules .= "h1, .h1 { ";
			$rules .= create_font_responsive($options['h1']['font-size'], 'font-size');
		$rules .= "}";

		//H2 rules
		$rules .= "h2, .h2 {";
			$rules .= create_font_responsive($options['h2']['font-size'], 'font-size');
		$rules .= "}";

		//H3 rules 
		$rules .= "h3, .h3 {";
			$rules .= create_font_responsive($options['h3']['font-size'], 'font-size');
		$rules .= "}";

		//H4 rules 
		$rules .= "h4, .h4 {";
			$rules .= create_font_responsive($options['h4']['font-size'], 'font-size');
		$rules .= "}";

		//h5 rules 
		$rules .= "h5, .h5 {";
			$rules .= create_font_responsive($options['h5']['font-size'], 'font-size');
		$rules .= "}";
		
		//h6 rules 
		$rules .= "h6, .h6 {";
			$rules .= create_font_responsive($options['h6']['font-size'], 'font-size');
		$rules .= "}";

		//a rules 
		$rules .= "a, .a {";
		    $rules .= create_font_responsive($options['a']['font-size'], 'font-size');
		$rules .= "}";

		//a_hover rules 
		$rules .= "a:hover, .a:hover, a:focus, .a:focus {";
			$rules .= create_font_responsive($options['a_hover']['font-size'], 'font-size');
		$rules .= "}";
		
		//a1 rules 
		$rules .= ".a2 {";
		    $rules .= create_font_responsive($options['a2']['font-size'], 'font-size');
		$rules .= "}";

		//a1_hover rules 
		$rules .= ".a2:hover, .a2:focus {";
		    $rules .= create_font_responsive($options['a2_hover']['font-size'], 'font-size');
		$rules .= "}";

		//a3 rules 
		$rules .= ".a3 {";
			$rules .= create_font_responsive($options['a3']['font-size'], 'font-size');
		$rules .= "}";

		//a3_hover rules 
		$rules .= ".a3:hover, .a3:focus {";
		    $rules .= create_font_responsive($options['a3_hover']['font-size'], 'font-size');
		$rules .= "}";

		//.nav rules 
		$rules .= ".nav, .nav .menu>li, .nav .menu>li>a {";
		    $rules .= create_font_responsive($options['nav']['font-size'], 'font-size');
		$rules .= "}";
		
		//.nav_hover rules 
		$rules .= ".nav:hover, .nav:focus, .nav .menu>li:hover, .nav .menu>li:hover>a, ";
		$rules .= ".nav .menu>li:focus, .nav .menu>li:focus>a {";
			$rules .= create_font_responsive($options['nav_hover']['font-size'], 'font-size');
		$rules .= "}";
		
		//.nav_focus rules 
		$rules .= ".current-menu, .menu li.current-menu-item a, .current_page_item a, .current_page_ancestor {";
		    $rules .= create_font_responsive($options['nav_focus']['font-size'], 'font-size');
		$rules .= "}";
		

		//.subnav rules 
		$rules .= ".subnav, .subnav .menu>li, .subnav .menu>li>a, .nav .sub-menu>li, .nav .sub-menu>li>a {";
		    $rules .= create_font_responsive($options['subnav']['font-size'], 'font-size');
		$rules .= "}";

		//.subnav_hover rules 
		$rules .= ".subnav:hover, .subnav:focus, .subnav .menu>li:hover, .subnav .menu>li:focus, .subnav .menu>li>a:hover, .subnav .menu>li>a:focus, .nav .sub-menu>li:hover, .nav .sub-menu>li:hover>a,";
		$rules .= " .nav .sub-menu>li:focus, .nav .sub-menu>li:focus>a {";
		    $rules .= create_font_responsive($options['subnav_hover']['font-size'], 'font-size');
		$rules .= "}";

		//.subnav_focus rules
		$rules .= ".current-submenu, .current_page_ancestor .current_page_item, .subnav .current-menu{";
			$rules .= create_font_responsive($options['subnav_focus']['font-size'], 'font-size');
		$rules .= "}";
		//global style rules
		if (!empty($options['article-padding']['pixel-amount'])) {
			$rules .= ".grid-wrapper, article{";
				$rules .= create_font_responsive($options['article-padding']['pixel-amount'], 'padding');
			$rules .= "}";
		}
		if (!empty($options['module-title-bottom-margin']['pixel-amount'])) {
			$rules .= ".module-title{";
				$rules .= create_font_responsive($options['module-title-bottom-margin']['pixel-amount'], 'margin-bottom');
			$rules .= "}";
		}
		if (!empty($options['column-gutter']['pixel-amount'])) {
			if (strpos($options['column-gutter']['pixel-amount'], 'px')){
		    		$num = substr($options['column-gutter']['pixel-amount'], 0, strpos($options['column-gutter']['pixel-amount'], 'px'));
		    		$num = round((int)$num * 1.54167);
			    	$value2 = $num . 'px';
		    	}
			$rules .= ".row, .gutter-row{";
				$rules .= "margin-right: -". $num / 2 ."px;";
				$rules .= "margin-left: -". $num / 2 ."px;";
			$rules .= "}";
			$rules .= '.gutter-col, .row [class*="col-"]{';
				$rules .= "margin-bottom: ". $value2 .";";
				$rules .= "padding-right: ". $num / 2 ."px;";
				$rules .= "padding-left: ". $num / 2 ."px;";
			$rules .= "}";
		}
		if (!empty($options['small-margin']['pixel-amount'])) {
			if (strpos($options['small-margin']['pixel-amount'], 'px')){
	    		$num = substr($options['small-margin']['pixel-amount'], 0, strpos($options['small-margin']['pixel-amount'], 'px'));
	    		$num = round((int)$num * 1.54167);
			    $value2 = $num . 'px';
	    	}
			$rules .= ".small-margin-bottom{";
				$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".small-margin-top{";
				$rules .= "margin-top: ". $value2 .";";
			$rules .= "}";
			$rules .= ".small-padding-bottom{";
				$rules .= "padding-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".small-padding-top{";
				$rules .= "padding-top: ". $value2 .";";
			$rules .= "}";
		}
		if (!empty($options['medium-margin']['pixel-amount'])) {
			if (strpos($options['medium-margin']['pixel-amount'], 'px')){
	    		$num = substr($options['medium-margin']['pixel-amount'], 0, strpos($options['medium-margin']['pixel-amount'], 'px'));
	    		$num = round((int)$num * 1.54167);
			    $value2 = $num . 'px';
	    	}
			$rules .= ".medium-margin-bottom{";
				$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".medium-margin-top{";
				$rules .= "margin-top: ". $value2 .";";
			$rules .= "}";
			$rules .= ".medium-padding-bottom{";
				$rules .= "padding-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".medium-padding-top{";
				$rules .= "padding-top: ". $value2 .";";
			$rules .= "}";
		}
		if (!empty($options['large-margin']['pixel-amount'])) {
			if (strpos($options['large-margin']['pixel-amount'], 'px')){
	    		$num = substr($options['large-margin']['pixel-amount'], 0, strpos($options['large-margin']['pixel-amount'], 'px'));
	    		$num = round((int)$num * 1.54167);
			    $value2 = $num . 'px';
	    	}
			$rules .= ".large-margin-bottom{";
				$rules .= "margin-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".large-margin-top{";
				$rules .= "margin-top: ". $value2 .";";
			$rules .= "}";
			$rules .= ".large-padding-bottom{";
				$rules .= "padding-bottom: ". $value2 .";";
			$rules .= "}";
			$rules .= ".large-padding-top{";
				$rules .= "padding-top: ". $value2 .";";
			$rules .= "}";
		}
		if (!empty($options['button-padding']) && is_array($options['button-padding'])) {
			$rules .= ".a3, .button, button, p.a3{";
		    	$px = '';
		    	foreach ($options['button-padding'] as $value) {
		    		if (strpos($value, 'px')){
			    		$num = substr($value, 0, strpos($value, 'px'));
			    		$num = round((int)$num * 1.54167);
			    		$px .= ' ' .$num . 'px';
			    	}
		    	}
		    	$rules .= "padding:". $px .";";
			$rules .= "}";
		}

	$rules .= "}";
	

	$rules .= $c_rules;

	//button1 color rules
	if (is_array($options['button1']) && !empty($options['button1'])) {
		$bkgrd = str_replace(' ', '-', $options['button1']['background-name']);
		$rules .= ".a3." . $bkgrd. "-background, .button." . $bkgrd. "-background, button." . $bkgrd. "-background{";
			$rules .= "color: ". $options['button1']['text-color'] .";";
		$rules .= "}";
		$rules .= ".a3." . $bkgrd. "-background:hover{";
			$rules .= "background-color: ". $options['button1']['hover-background-color'] .";";
			$rules .= "color: ". $options['button1']['hover-text-color'] .";";
		$rules .= "}";
	}
	//button2 color rules
	if (is_array($options['button2']) && !empty($options['button2'])) {
		$bkgrd = str_replace(' ', '-', $options['button2']['background-name']);
		$rules .= ".a3." . $bkgrd. "-background, .button." . $bkgrd. "-background, button." . $bkgrd. "-background{";
			$rules .= "color: ". $options['button2']['text-color'] .";";
		$rules .= "}";
		$rules .= ".a3." . $bkgrd. "-background:hover{";
			$rules .= "background-color: ". $options['button2']['hover-background-color'] .";";
			$rules .= "color: ". $options['button2']['hover-text-color'] .";";
		$rules .= "}";
	}
	//button3 color rules
	if (is_array($options['button3']) && !empty($options['button3'])) {
		$bkgrd = str_replace(' ', '-', $options['button3']['background-name']);
		$rules .= ".a3." . $bkgrd. "-background, .button." . $bkgrd. "-background, button." . $bkgrd. "-background{";
			$rules .= "color: ". $options['button3']['text-color'] .";";
		$rules .= "}";
		$rules .= ".a3." . $bkgrd . "-background:hover{";
			$rules .= "background-color: ". $options['button3']['hover-background-color'] .";";
			$rules .= "color: ". $options['button3']['hover-text-color'] .";";
		$rules .= "}";
	}
	
	return $rules;
}